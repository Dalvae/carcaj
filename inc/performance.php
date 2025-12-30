<?php
/**
 * Performance Optimizations
 * 
 * - Remove jQuery (we use Alpine.js)
 * - Defer non-critical scripts
 * - CDN support
 * - WebP conversion
 */

if (!defined('ABSPATH')) {
    exit;
}

// ============================================================================
// CDN Configuration
// ============================================================================

/**
 * CDN URL - set in wp-config.php or here
 * Example: define('CARCAJ_CDN_URL', 'https://cdn.carcaj.cl');
 */
if (!defined('CARCAJ_CDN_URL')) {
    define('CARCAJ_CDN_URL', ''); // Empty = no CDN
}

/**
 * Rewrite URLs to use CDN
 */
function carcaj_cdn_url($url) {
    if (empty(CARCAJ_CDN_URL) || is_admin()) {
        return $url;
    }
    
    // Only rewrite uploads and theme assets
    $site_url = site_url();
    if (strpos($url, '/wp-content/uploads/') !== false || 
        strpos($url, '/wp-content/themes/') !== false) {
        $url = str_replace($site_url, CARCAJ_CDN_URL, $url);
    }
    
    return $url;
}
add_filter('wp_get_attachment_url', 'carcaj_cdn_url');
add_filter('theme_file_uri', 'carcaj_cdn_url');
add_filter('style_loader_src', 'carcaj_cdn_url');
add_filter('script_loader_src', 'carcaj_cdn_url');

// ============================================================================
// jQuery Removal
// ============================================================================

/**
 * Remove jQuery completely on frontend (we use Alpine.js)
 */
function carcaj_remove_jquery() {
    if (!is_admin()) {
        wp_dequeue_script('jquery');
        wp_deregister_script('jquery');
        wp_dequeue_script('jquery-core');
        wp_deregister_script('jquery-core');
        wp_dequeue_script('jquery-migrate');
        wp_deregister_script('jquery-migrate');
    }
}
add_action('wp_enqueue_scripts', 'carcaj_remove_jquery', 1);

// ============================================================================
// Script Optimization
// ============================================================================

/**
 * Add defer to non-critical scripts
 */
function carcaj_defer_scripts($tag, $handle, $src) {
    // Skip in admin
    if (is_admin()) {
        return $tag;
    }
    
    $defer_scripts = ['avatar-manager'];
    
    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }
    
    return $tag;
}
add_filter('script_loader_tag', 'carcaj_defer_scripts', 10, 3);

/**
 * Dequeue unnecessary styles/scripts from plugins
 */
function carcaj_dequeue_unnecessary_assets() {
    if (is_admin()) {
        return;
    }
    
    // Avatar manager - remove completely, not needed on frontend
    wp_dequeue_style('avatar-manager-css');
    wp_deregister_style('avatar-manager-css');
    wp_dequeue_style('jeherve-avatar-manager-css');
    wp_deregister_style('jeherve-avatar-manager-css');
    wp_dequeue_script('avatar-manager');
    wp_deregister_script('avatar-manager');
    wp_dequeue_script('jeherve-avatar-manager');
    wp_deregister_script('jeherve-avatar-manager');
    
    // Co-Authors Plus styles - not needed
    wp_dequeue_style('co-authors-plus-css');
    wp_deregister_style('co-authors-plus-css');
    
    // Classic theme styles - not needed
    wp_dequeue_style('classic-theme-styles');
    
    // Block library - we use Tailwind
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('global-styles');
    
    // WordPress block scripts - not needed on frontend (Gutenberg editor only)
    wp_dequeue_script('wp-hooks');
    wp_deregister_script('wp-hooks');
    wp_dequeue_script('wp-i18n');
    wp_deregister_script('wp-i18n');
    wp_dequeue_script('wp-block-library');
    wp_deregister_script('wp-block-library');
    
    // Contact Form 7 - only load on contact page
    if (!is_page('contacto')) {
        wp_dequeue_style('contact-form-7');
        wp_dequeue_script('contact-form-7');
        wp_dequeue_style('wpcf7-recaptcha');
        wp_dequeue_script('wpcf7-recaptcha');
    }

}
add_action('wp_enqueue_scripts', 'carcaj_dequeue_unnecessary_assets', 100);

// ============================================================================
// WebP Support
// ============================================================================

/**
 * Allow WebP uploads
 */
add_filter('upload_mimes', function($mimes) {
    $mimes['webp'] = 'image/webp';
    return $mimes;
});

/**
 * Serve WebP images when available
 * Checks if .webp version exists and browser supports it
 */
function carcaj_maybe_serve_webp($image, $attachment_id, $size) {
    // Only on frontend
    if (is_admin() || !is_array($image)) {
        return $image;
    }
    
    // Check browser support
    if (!isset($_SERVER['HTTP_ACCEPT']) || 
        strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') === false) {
        return $image;
    }
    
    $image_url = $image[0];
    
    // Skip if already webp
    if (strpos($image_url, '.webp') !== false) {
        return $image;
    }
    
    // Check for webp version
    $webp_url = preg_replace('/\.(jpe?g|png)$/i', '.webp', $image_url);
    $upload_dir = wp_upload_dir();
    $webp_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $webp_url);
    
    if (file_exists($webp_path)) {
        $image[0] = $webp_url;
    }
    
    return $image;
}
add_filter('wp_get_attachment_image_src', 'carcaj_maybe_serve_webp', 10, 3);

/**
 * Add WebP to srcset when available
 */
function carcaj_webp_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id) {
    if (!isset($_SERVER['HTTP_ACCEPT']) || 
        strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') === false) {
        return $sources;
    }
    
    $upload_dir = wp_upload_dir();
    
    foreach ($sources as $width => $source) {
        $webp_url = preg_replace('/\.(jpe?g|png)$/i', '.webp', $source['url']);
        $webp_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $webp_url);
        
        if (file_exists($webp_path)) {
            $sources[$width]['url'] = $webp_url;
        }
    }
    
    return $sources;
}
add_filter('wp_calculate_image_srcset', 'carcaj_webp_srcset', 10, 5);

// ============================================================================
// Image Optimization
// ============================================================================

/**
 * Custom image sizes
 */
function carcaj_custom_image_sizes() {
    // Slider - circular, needs to be square
    add_image_size('slider', 800, 800, true);
    
    // Card thumbnails
    add_image_size('card-thumb', 400, 280, true);
}
add_action('after_setup_theme', 'carcaj_custom_image_sizes');

/**
 * Add loading="lazy" and decoding="async" to images
 */
function carcaj_lazy_load_images($attr, $attachment, $size) {
    // Don't lazy load above-the-fold images
    if ($size === 'slider' || $size === 'full') {
        $attr['fetchpriority'] = 'high';
        $attr['loading'] = 'eager';
    } else {
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';
    }
    
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'carcaj_lazy_load_images', 10, 3);

// ============================================================================
// Resource Hints
// ============================================================================

/**
 * Preload critical assets
 */
function carcaj_preload_critical_assets() {
    $theme_uri = get_template_directory_uri();
    echo '<link rel="preload" href="' . esc_url($theme_uri) . '/img/logo.svg" as="image" type="image/svg+xml">' . "\n";
}
add_action('wp_head', 'carcaj_preload_critical_assets', 1);

/**
 * DNS prefetch and preconnect
 */
function carcaj_resource_hints($urls, $relation_type) {
    if ('dns-prefetch' === $relation_type) {
        $urls[] = '//www.googletagmanager.com';
        $urls[] = '//www.google-analytics.com';
        $urls[] = '//cdnjs.cloudflare.com'; // PDF.js CDN
        if (!empty(CARCAJ_CDN_URL)) {
            $urls[] = '//' . parse_url(CARCAJ_CDN_URL, PHP_URL_HOST);
        }
    }
    
    if ('preconnect' === $relation_type) {
        // PDF.js CDN - preconnect for faster PDF viewer loading
        $urls[] = ['href' => 'https://cdnjs.cloudflare.com', 'crossorigin' => true];
        if (!empty(CARCAJ_CDN_URL)) {
            $urls[] = ['href' => CARCAJ_CDN_URL, 'crossorigin' => true];
        }
    }
    
    return $urls;
}
add_filter('wp_resource_hints', 'carcaj_resource_hints', 10, 2);

// ============================================================================
// Cleanup
// ============================================================================

/**
 * Disable emoji scripts (~10KB savings)
 */
function carcaj_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    
    add_filter('tiny_mce_plugins', function($plugins) {
        return is_array($plugins) ? array_diff($plugins, ['wpemoji']) : [];
    });
    
    add_filter('wp_resource_hints', function($urls, $relation_type) {
        if ('dns-prefetch' === $relation_type) {
            $urls = array_filter($urls, function($url) {
                return strpos($url, 's.w.org/images/core/emoji/') === false;
            });
        }
        return $urls;
    }, 10, 2);
}
add_action('init', 'carcaj_disable_emojis');

/**
 * Remove unnecessary meta tags
 */
function carcaj_cleanup_head() {
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
}
add_action('init', 'carcaj_cleanup_head');
