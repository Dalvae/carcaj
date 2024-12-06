FROM wordpress:latest

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    less \
    mariadb-client \
    && rm -rf /var/lib/apt/lists/*

# Instalar WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp \
    && wp --info

# Copiar script personalizado
COPY docker-entrypoint-custom.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint-custom.sh

# Mantener el ENTRYPOINT original pero cambiar el CMD
ENTRYPOINT ["docker-entrypoint-custom.sh"]
CMD ["apache2-foreground"]