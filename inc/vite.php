<?php

// Exit if accessed directly
if (! defined('ABSPATH'))
    exit;

/*
 * VITE & Tailwind JIT development
 * Inspired by https://github.com/andrefelipe/vite-php-setup
 *
 */

// dist subfolder - defined in vite.config.json
const DIST_DEF = 'dist';

// defining some base urls and paths
define('DIST_URI', get_template_directory_uri() . '/' . DIST_DEF);
define('DIST_PATH', get_template_directory() . '/' . DIST_DEF);

// js enqueue settings
const JS_DEPENDENCY = array(); // array('jquery') as example
const JS_LOAD_IN_FOOTER = false; // load in head for Turbo compatibility

// deafult server address, port and entry point can be customized in vite.config.json
const VITE_SERVER = 'http://localhost:3000';

define('IS_LOGIN_PAGE', in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php')));

if (IS_LOGIN_PAGE) {
    define("VITE_ENTRY_POINT", 'src/wp-login/login.js');
    define("HOOK_PREFIX", 'login');
} else {
    define("VITE_ENTRY_POINT", 'src/theme.js');
    define("HOOK_PREFIX", 'wp');
}

// Critical CSS helper (disabled for now - causes issues)
// require_once get_template_directory() . '/inc/critical-css.php';

// enqueue hook
add_action(HOOK_PREFIX . '_enqueue_scripts', function () {

    if (defined('IS_VITE_DEVELOPMENT') && IS_VITE_DEVELOPMENT === true && ((defined('DEVELOPMENT_IP') && $_SERVER['REMOTE_ADDR'] === DEVELOPMENT_IP) || !defined('DEVELOPMENT_IP'))) {

        // insert hmr into head for live reload
        function vite_head_module_hook()
        {
            echo '<script type="module" crossorigin src="' . VITE_SERVER . '/' . VITE_ENTRY_POINT . '"></script>';
        }
        add_action(HOOK_PREFIX . '_head', 'vite_head_module_hook');
    } else {

        // production version, 'npm run build' must be executed in order to generate assets
        // ----------

        // read manifest.json to figure out what to enqueue
        $manifest_path = DIST_PATH . '/.vite/manifest.json';

        if (!file_exists($manifest_path)) {
            return;
        }

        $manifest = json_decode(file_get_contents($manifest_path), true);

        // is ok
        if (is_array($manifest)) {

            $entry_point_manifest = isset($manifest[VITE_ENTRY_POINT]) ? $manifest[VITE_ENTRY_POINT] : null;

            if ($entry_point_manifest) {
                // enqueue CSS files (standard method - works reliably)
                if (!empty($entry_point_manifest['css'])) {
                    foreach ($entry_point_manifest['css'] as $css_file) {
                        wp_enqueue_style('theme', DIST_URI . '/' . $css_file);
                    }
                }

                // enqueue theme JS file
                if (!empty($entry_point_manifest['file'])) {
                    $js_file = $entry_point_manifest['file'];
                    wp_enqueue_script('theme', DIST_URI . '/' . $js_file, JS_DEPENDENCY, '', JS_LOAD_IN_FOOTER);
                }
            }

            // Add type="module" to theme script for Turbo compatibility
            add_filter('script_loader_tag', function ($tag, $handle, $src) {
                if ($handle === 'theme') {
                    return '<script type="module" src="' . esc_url($src) . '" id="theme-js"></script>' . "\n";
                }
                return $tag;
            }, 10, 3);

            // Preload critical fonts (subset versions)
            add_action('wp_head', function () {
                $fonts = [
                    'subset-Alegreya-Regular.woff2',
                    'subset-Alegreya-Medium.woff2', 
                    'subset-Alegreya-Bold.woff2',
                    'subset-Alegreya-Italic.woff2',
                ];
                foreach ($fonts as $font) {
                    echo '<link rel="preload" href="' . esc_url(DIST_URI . '/assets/fonts/' . $font) . '" as="font" type="font/woff2" crossorigin>' . "\n";
                }
            }, 1);
        }
    }
});
