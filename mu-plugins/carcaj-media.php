<?php
/**
 * Plugin Name: Carcaj Media (R2)
 * Description: Sirve las imagenes de wp-content/uploads/AAAA/MM desde media.carcaj.cl (Cloudflare R2).
 *
 * El respaldo diario (~/scripts/backup-r2.sh) copia uploads/AAAA/MM al bucket
 * carcaj-media. Los meses recientes (mes actual y el anterior) se siguen
 * sirviendo desde el hosting, para no apuntar a archivos que aun no se copian.
 * Para desactivar: define('CARCAJ_MEDIA_URL', '') en wp-config.php o borrar este archivo.
 */

if (!defined('ABSPATH')) {
	exit;
}

if (!defined('CARCAJ_MEDIA_URL')) {
	define('CARCAJ_MEDIA_URL', 'https://media.carcaj.cl');
}

/** Meses que siguen en el hosting (actual y anterior). */
function carcaj_media_min_local_month(): string {
	return gmdate('Ym', strtotime('first day of last month', current_time('timestamp')));
}

/** Reescribe URLs de uploads/AAAA/MM/ anteriores al mes limite. */
function carcaj_media_rewrite(string $html): string {
	if (CARCAJ_MEDIA_URL === '' || stripos($html, 'uploads') === false) {
		return $html;
	}
	$limit = carcaj_media_min_local_month();
	$base  = rtrim(CARCAJ_MEDIA_URL, '/');

	// Absolutas (http/https, //, con o sin www, tambien escapadas en JSON) o relativas
	// a la raiz precedidas por comilla, "=", "(", espacio o coma (srcset).
	$pattern = '~(?:(?:https?:)?\\\\?/\\\\?/(?:www\.)?carcaj\.cl|(?<=["\'(=\s,]))(\\\\?/)wp-content\\\\?/uploads\\\\?/(\d{4})(\\\\?/)(\d{2})(\\\\?/)~i';

	return preg_replace_callback($pattern, function ($m) use ($limit, $base) {
		if (($m[2] . $m[4]) >= $limit) {
			return $m[0];
		}
		$slash = $m[1]; // "/" o "\/" (JSON)
		$b     = $slash === '/' ? $base : str_replace('/', '\\/', $base);
		return $b . $slash . $m[2] . $m[3] . $m[4] . $m[5];
	}, $html) ?? $html;
}

/** Portada, entradas, feeds, sitemaps: todo el HTML del frente pasa por aqui. */
add_action('template_redirect', function () {
	if (is_admin() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
		return;
	}
	ob_start('carcaj_media_rewrite');
}, 0);

/** Admin, REST y AJAX: URLs de adjuntos. Lo que quede con URL vieja lo resuelve uploads/.htaccess (302 a media). */
add_filter('wp_get_attachment_url', 'carcaj_media_rewrite', 99);
add_filter('wp_calculate_image_srcset', function ($sources) {
	if (!is_array($sources)) {
		return $sources;
	}
	foreach ($sources as $w => $s) {
		$sources[$w]['url'] = carcaj_media_rewrite($s['url']);
	}
	return $sources;
}, 99);
