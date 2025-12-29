# TODO

- Repasar imagenes perdidas

## Configuracion del servidor (fuera del tema)

### Cache Headers en .htaccess de la raiz de WordPress

El `.htaccess` del tema solo aplica a archivos dentro del directorio del tema.
Para que el cache funcione correctamente en todo el sitio, agregar esto al `.htaccess` 
de la raiz de WordPress (donde esta wp-config.php):

```apache
# ===============================================
# Browser Caching - agregar ANTES de las reglas de WordPress
# ===============================================
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresDefault "access plus 1 month"
    
    # Imagenes
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType image/x-icon "access plus 1 year"
    
    # Fonts
    ExpiresByType font/woff2 "access plus 1 year"
    ExpiresByType font/woff "access plus 1 year"
    ExpiresByType application/font-woff2 "access plus 1 year"
    ExpiresByType application/font-woff "access plus 1 year"
    
    # CSS/JS
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType text/javascript "access plus 1 year"
    
    # HTML - no cachear
    ExpiresByType text/html "access plus 0 seconds"
</IfModule>

# Cache-Control headers
<IfModule mod_headers.c>
    # Archivos estaticos inmutables (con hash en nombre)
    <FilesMatch "\.(js|css|woff2|woff)$">
        Header set Cache-Control "public, max-age=31536000, immutable"
    </FilesMatch>
    
    # Imagenes
    <FilesMatch "\.(webp|jpg|jpeg|png|gif|svg|ico)$">
        Header set Cache-Control "public, max-age=31536000"
    </FilesMatch>
    
    # HTML - no cachear
    <FilesMatch "\.html?$">
        Header set Cache-Control "no-cache, no-store, must-revalidate"
    </FilesMatch>
</IfModule>

# Compresion GZIP
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css
    AddOutputFilterByType DEFLATE application/javascript application/x-javascript text/javascript
    AddOutputFilterByType DEFLATE application/json application/xml
    AddOutputFilterByType DEFLATE image/svg+xml
    AddOutputFilterByType DEFLATE font/woff font/woff2 application/font-woff application/font-woff2
</IfModule>
```

### Optimizar imagen del slider

La imagen del slider (en uploads) pesa 261KB y tiene dimensiones 1399x1397.
Se muestra a ~450x450 en el sitio.

Recomendaciones:
1. Redimensionar a 900x900 (2x para retina)
2. Convertir a WebP
3. Subir nueva version desde el admin de WordPress

### CDN (opcional)

Si se configura un CDN, agregar en wp-config.php:
```php
define('CARCAJ_CDN_URL', 'https://cdn.carcaj.cl');
```

El tema automaticamente usara el CDN para assets estaticos.
