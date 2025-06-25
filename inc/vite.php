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
const JS_LOAD_IN_FOOTER = true; // load scripts in footer?

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
                // enqueue CSS files
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

            // Preload fonts
            add_action('wp_head', function () use ($manifest) {
                $entry_point_manifest = $manifest[VITE_ENTRY_POINT] ?? null;
                if (!$entry_point_manifest) {
                    return;
                }

                $fonts_to_preload = [];

                // Collect CSS files from the entry point
                $css_files = $entry_point_manifest['css'] ?? [];

                // Find assets (like fonts) from those CSS files by traversing the manifest
                foreach ($manifest as $chunk) {
                    // If this chunk is one of the CSS files for our entry point
                    if (isset($chunk['file']) && in_array($chunk['file'], $css_files, true)) {
                        // And if it has assets
                        if (!empty($chunk['assets'])) {
                            foreach ($chunk['assets'] as $asset_key) {
                                // Find the asset's final file path from its own manifest entry
                                if (isset($manifest[$asset_key]['file'])) {
                                    $asset_file = $manifest[$asset_key]['file'];
                                    if (pathinfo($asset_file, PATHINFO_EXTENSION) === 'woff2') {
                                        $fonts_to_preload[$asset_file] = true;
                                    }
                                }
                            }
                        }
                    }
                }

                // Output preload links for the unique fonts found
                foreach (array_keys($fonts_to_preload) as $font_file) {
                    echo '<link rel="preload" href="' . esc_url(DIST_URI . '/' . $font_file) . '" as="font" type="font/woff2" crossorigin>' . "\n";
                }
            });
        }
    }
});
