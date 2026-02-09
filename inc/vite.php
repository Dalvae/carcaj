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
const JS_LOAD_IN_FOOTER = true; // load in footer for better performance

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

// Critical CSS helper - inlines above-the-fold styles for instant render
require_once get_template_directory() . '/inc/critical-css.php';

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
                // Don't enqueue CSS normally - load it async to avoid render-blocking
                // Critical CSS is inlined for instant first paint

                // enqueue theme JS file
                if (!empty($entry_point_manifest['file'])) {
                    $js_file = $entry_point_manifest['file'];
                    wp_enqueue_script('theme', DIST_URI . '/' . $js_file, JS_DEPENDENCY, null, JS_LOAD_IN_FOOTER);
                }

                // Inline critical CSS + async load full CSS
                // Priority 4: after wp_enqueue_scripts (1), before wp_print_styles (8)
                add_action('wp_head', function () use ($entry_point_manifest) {
                    // 1. Inline critical CSS (above-the-fold styles for instant render)
                    carcaj_output_critical_css();

                    // 2. Load full CSS async (non-render-blocking)
                    // header.php already has <link rel="preload"> for early discovery
                    if (!empty($entry_point_manifest['css'])) {
                        foreach ($entry_point_manifest['css'] as $css_file) {
                            $css_url = esc_url(DIST_URI . '/' . $css_file);
                            echo '<link rel="stylesheet" href="' . $css_url . '" media="print" onload="this.media=\'all\'">' . "\n";
                            echo '<noscript><link rel="stylesheet" href="' . $css_url . '"></noscript>' . "\n";
                        }
                    }
                }, 4);
            }

            // Add type="module" and defer to theme script
            // type="module" is deferred by default, but explicit defer helps older parsers
            add_filter('script_loader_tag', function ($tag, $handle, $src) {
                if ($handle === 'theme') {
                    return '<script type="module" defer src="' . esc_url($src) . '" id="theme-js"></script>' . "\n";
                }
                return $tag;
            }, 10, 3);
        }
    }
});
