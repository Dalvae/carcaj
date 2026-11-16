<?php
/**
 * Plugin Name: Carcaj Media (R2)
 * Description: Reescritura opcional de imagenes a media.carcaj.cl (R2). Apagada: las sirve el Worker carcaj-uploads.
 *
 * El respaldo diario (~/scripts/backup-r2.sh) copia uploads/AAAA/MM al bucket
 * carcaj-media. Si CARCAJ_MEDIA_URL tiene valor, reescribe esas URLs a ese host,
 * salvo el mes actual y el anterior (pueden no estar copiados aun).
 */

if (!defined('ABSPATH')) {
	exit;
}

// Desde 2026-11-16 las imagenes de R2 se sirven en el mismo dominio
// (carcaj.cl/wp-content/uploads/*) con el Worker workers/carcaj-uploads, asi
// que no se reescribe nada: un host aparte (media.carcaj.cl) sumaba ~1,3 s al
// LCP movil por la conexion extra (prueba A/B, 5 rondas).
// Para volver a servir desde media: define('CARCAJ_MEDIA_URL', 'https://media.carcaj.cl').
if (!defined('CARCAJ_MEDIA_URL')) {
	define('CARCAJ_MEDIA_URL', '');
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

/**
 * Preconnect a media: las imagenes vienen de otro host, y sin esto el navegador
 * abre la conexion (DNS+TCP+TLS) recien al encontrar la primera <img>, lo que
 * retrasaba el LCP ~0,6-1 s en movil. Va como <link> y como cabecera Link
 * (Cloudflare la usa para Early Hints 103).
 */
add_action('send_headers', function () {
	if (CARCAJ_MEDIA_URL !== '' && !is_admin()) {
		header('Link: <' . rtrim(CARCAJ_MEDIA_URL, '/') . '>; rel=preconnect', false);
	}
});
add_action('wp_head', function () {
	if (CARCAJ_MEDIA_URL !== '') {
		echo '<link rel="preconnect" href="' . esc_url(rtrim(CARCAJ_MEDIA_URL, '/')) . '">' . "\n";
	}
}, 0);

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
