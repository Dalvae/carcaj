#!/bin/bash
set -e

echo "🚀 Initializing WordPress..."

# Llamar al entrypoint original de WordPress para copiar archivos
# El entrypoint original copia WordPress a /var/www/html si no existe
/usr/local/bin/docker-entrypoint.sh apache2-foreground &
WP_PID=$!

# Esperar a que WordPress esté instalado
echo "⏳ Esperando archivos de WordPress..."
until [ -f /var/www/html/wp-includes/version.php ]; do
    sleep 2
done
echo "✅ Archivos de WordPress listos"

# Matar el proceso de Apache que inició el entrypoint original
# (lo reiniciaremos después de la migración)
kill $WP_PID 2>/dev/null || true
sleep 2

echo "🔄 Esperando a que la base de datos esté disponible..."
# Extraer credenciales de wp-config.php
DB_HOST=$(grep "DB_HOST" /var/www/html/wp-config.php | cut -d"'" -f4)
DB_USER=$(grep "DB_USER" /var/www/html/wp-config.php | cut -d"'" -f4)
DB_PASSWORD=$(grep "DB_PASSWORD" /var/www/html/wp-config.php | sed "s/.*DB_PASSWORD'[^\"']*[\"']\\([^\"']*\\).*/\\1/")
DB_NAME=$(grep "DB_NAME" /var/www/html/wp-config.php | cut -d"'" -f4)

until mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" --ssl=0 -e "SELECT 1" "$DB_NAME" >/dev/null 2>&1; do
    echo "⏳ Esperando conexión con la base de datos..."
    sleep 5
done
echo "✅ Base de datos conectada"

# Perform URL migration if not already completed
MIGRATION_CHECK=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASSWORD" --ssl=0 -N -e "SELECT option_value FROM ${WORDPRESS_TABLE_PREFIX:-wp_}options WHERE option_name='migration_completed'" "$DB_NAME" 2>/dev/null || echo "")

if [ -z "$MIGRATION_CHECK" ] || [ "$MIGRATION_CHECK" != "1" ]; then
    echo "🔄 Iniciando migración de URLs..."
    
    target_url="http://localhost:8888"
    
    # Usar WP-CLI con la opción de saltar SSL verificación
    export MYSQL_PWD="$DB_PASSWORD"
    
    for search_url in "https://carcaj.cl" "http://carcaj.cl" "https://www.carcaj.cl" "http://www.carcaj.cl" "//carcaj.cl" "//www.carcaj.cl"; do
        echo "Reemplazando ${search_url} con ${target_url}"
        wp search-replace "${search_url}" "${target_url}" --all-tables --precise --skip-columns=guid --allow-root 2>/dev/null || echo "  (continuando...)"
    done

    wp option update home "${target_url}" --allow-root 2>/dev/null || true
    wp option update siteurl "${target_url}" --allow-root 2>/dev/null || true
    
    wp cache flush --allow-root 2>/dev/null || true
    wp rewrite flush --allow-root 2>/dev/null || true

    wp option add migration_completed 1 --allow-root 2>/dev/null || wp option update migration_completed 1 --allow-root 2>/dev/null || true
    echo "✅ Migración completada"
else
    echo "ℹ️ Migración ya realizada anteriormente"
fi

echo "🚀 Iniciando Apache..."
exec apache2-foreground
