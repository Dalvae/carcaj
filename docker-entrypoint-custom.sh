#!/bin/bash
set -e

# Función para esperar a que la base de datos esté disponible
wait_for_db() {
    echo "🔄 Esperando a que la base de datos esté disponible..."
    until wp db check --allow-root 2>/dev/null; do
        echo "⏳ Esperando conexión con la base de datos..."
        sleep 5
    done
    echo "✅ Base de datos conectada"
}

# Función para realizar la migración con múltiples variantes de URL
do_migration() {
    if ! wp option get migration_completed --allow-root 2>/dev/null; then
        echo "🔄 Iniciando migración de URLs..."
        
        # Array de URLs de origen y destino
        declare -a search_urls=(
            "https://carcaj.cl"
            "http://carcaj.cl"
            "https://www.carcaj.cl"
            "http://www.carcaj.cl"
            "//carcaj.cl"
            "//www.carcaj.cl"
        )
        
        # URL de destino
        local target_url="http://localhost:8888"
        
        # Realizar reemplazos para cada variante
        for search_url in "${search_urls[@]}"; do
            echo "Reemplazando ${search_url} con ${target_url}"
            wp search-replace "${search_url}" "${target_url}" --all-tables --precise --allow-root
        done

        # Actualizar las opciones de WordPress directamente
        wp option update home "${target_url}" --allow-root
        wp option update siteurl "${target_url}" --allow-root
        
        # Limpiar cachés
        wp cache flush --allow-root
        wp rewrite flush --allow-root

        # Marcar como completado
        wp option add migration_completed 1 --allow-root
        echo "✅ Migración completada"
    else
        echo "ℹ️ Migración ya realizada anteriormente"
    fi
}

# Inicializar WordPress usando el entrypoint original
echo "🚀 Iniciando WordPress..."
/usr/local/bin/docker-entrypoint.sh apache2-foreground &

# Esperar a que WordPress esté listo
echo "⏳ Esperando a que WordPress esté disponible..."
until curl -s http://localhost > /dev/null; do
    sleep 2
done

# Realizar la migración
wait_for_db
do_migration

# Mantener el contenedor ejecutándose
wait