<?php
/**
 * Critical CSS - Reads auto-generated critical CSS from dist/critical.css
 *
 * The file is generated at build time by scripts/generate-critical-css.js
 * which extracts above-the-fold styles from the compiled Tailwind output,
 * preserving @layer structure for correct cascade behavior.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Get critical CSS as a string.
 *
 * Reads dist/critical.css and rewrites relative font URLs to absolute
 * URLs so they resolve correctly when inlined in <style>.
 */
function carcaj_get_critical_css() {
    $critical_path = DIST_PATH . '/critical.css';

    if (!file_exists($critical_path)) {
        return '';
    }

    $css = file_get_contents($critical_path);

    // Rewrite relative font URLs for inline context:
    // url(./fonts/...) → url(DIST_URI/assets/fonts/...)
    $dist_uri = DIST_URI;
    $css = str_replace('url(./fonts/', "url({$dist_uri}/assets/fonts/", $css);

    return $css;
}

/**
 * Output critical CSS in head
 */
function carcaj_output_critical_css() {
    $css = carcaj_get_critical_css();
    if ($css) {
        echo '<style id="critical-css">' . $css . '</style>' . "\n";
    }
}
