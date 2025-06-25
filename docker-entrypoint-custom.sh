#!/bin/bash
set -e

# This script is designed to run before the main CMD.
# We run the original entrypoint with a temporary command to set up WordPress files.
echo "🚀 Initializing WordPress files..."
/usr/local/bin/docker-entrypoint.sh echo "WordPress initialized"

# Now that files are in place and wp-config.php is linked, we can use wp-cli.
echo "🔄 Esperando a que la base de datos esté disponible..."
until wp db check --allow-root --quiet; do
    echo "⏳ Esperando conexión con la base de datos..."
    sleep 5
done
echo "✅ Base de datos conectada"

# Perform URL migration if not already completed
if ! wp option get migration_completed --allow-root > /dev/null 2>&1; then
    echo "🔄 Iniciando migración de URLs..."
    
    declare -a search_urls=(
        "https://carcaj.cl" "http://carcaj.cl"
        "https://www.carcaj.cl" "http://www.carcaj.cl"
        "//carcaj.cl" "//www.carcaj.cl"
    )
    target_url="http://localhost:8888"
    
    for search_url in "${search_urls[@]}"; do
        echo "Reemplazando ${search_url} con ${target_url}"
        wp search-replace "${search_url}" "${target_url}" --all-tables --precise --skip-columns=guid --allow-root
    done

    wp option update home "${target_url}" --allow-root
    wp option update siteurl "${target_url}" --allow-root
    
    wp cache flush --allow-root
    wp rewrite flush --allow-root

    wp option add migration_completed 1 --allow-root
    echo "✅ Migración completada"
else
    echo "ℹ️ Migración ya realizada anteriormente"
fi

echo "🚀 Iniciando Apache..."
# Now, execute the CMD passed to the container (e.g., "apache2-foreground")
exec "$@"
