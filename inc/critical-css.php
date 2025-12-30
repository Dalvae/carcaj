<?php
/**
 * Critical CSS - Inline styles for above-the-fold content
 * This reduces render-blocking CSS and improves LCP/FCP
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Get critical CSS as a string
 * This includes only what's needed for initial render:
 * - Font faces (required for text)
 * - Base layout (body, header, container)
 * - Above-the-fold components
 */
function carcaj_get_critical_css() {
    $dist_uri = DIST_URI;
    
    return <<<CSS
/* Critical CSS - Inline for fast first paint */

/* Font faces - critical for text rendering (Latin Extended subset) */
@font-face {
  font-family: "Alegreya";
  src: url("{$dist_uri}/assets/fonts/subset-Alegreya-Regular.woff2") format("woff2");
  font-weight: 400;
  font-style: normal;
  font-display: swap;
  unicode-range: U+0000-00FF, U+0100-017F, U+0180-024F, U+2000-206F;
  ascent-override: 96%;
  descent-override: 25%;
  line-gap-override: 0%;
}

@font-face {
  font-family: "Alegreya";
  src: url("{$dist_uri}/assets/fonts/subset-Alegreya-Medium.woff2") format("woff2");
  font-weight: 500;
  font-style: normal;
  font-display: swap;
  unicode-range: U+0000-00FF, U+0100-017F, U+0180-024F, U+2000-206F;
  ascent-override: 96%;
  descent-override: 25%;
  line-gap-override: 0%;
}

@font-face {
  font-family: "Alegreya";
  src: url("{$dist_uri}/assets/fonts/subset-Alegreya-Bold.woff2") format("woff2");
  font-weight: 700;
  font-style: normal;
  font-display: swap;
  unicode-range: U+0000-00FF, U+0100-017F, U+0180-024F, U+2000-206F;
  ascent-override: 96%;
  descent-override: 25%;
  line-gap-override: 0%;
}

@font-face {
  font-family: "Alegreya";
  src: url("{$dist_uri}/assets/fonts/subset-Alegreya-Italic.woff2") format("woff2");
  font-weight: 400;
  font-style: italic;
  font-display: swap;
  unicode-range: U+0000-00FF, U+0100-017F, U+0180-024F, U+2000-206F;
  ascent-override: 96%;
  descent-override: 25%;
  line-gap-override: 0%;
}

/* CSS Variables */
:root {
  --color-rojo: #D22800;
  --color-gris: #47586A;
  --color-swhite: #F5F5ED;
  --color-gold: #D8D4AE;
  --color-darkgold: #9A7A14;
  --color-rosado: #EA6060;
  --font-alegreya: "Alegreya", Georgia, "Times New Roman", serif;
}

/* Base resets */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { -webkit-text-size-adjust: 100%; line-height: 1.5; }
body { font-family: var(--font-alegreya); min-height: 100vh; display: flex; flex-direction: column; background: #fff; }
img, video { max-width: 100%; height: auto; display: block; }
a { color: inherit; text-decoration: inherit; }

/* Alpine.js cloak */
[x-cloak] { display: none !important; }

/* Container */
.container { width: 100%; margin-left: auto; margin-right: auto; padding-left: 1rem; padding-right: 1rem; }
@media (min-width: 640px) { .container { max-width: 640px; padding-left: 2rem; padding-right: 2rem; } }
@media (min-width: 768px) { .container { max-width: 768px; } }
@media (min-width: 1024px) { .container { max-width: 1024px; padding-left: 4rem; padding-right: 4rem; } }
@media (min-width: 1280px) { .container { max-width: 1280px; padding-left: 5rem; padding-right: 5rem; } }

/* Flexbox utilities */
.flex { display: flex; }
.flex-col { flex-direction: column; }
.flex-grow { flex-grow: 1; }
.flex-shrink-0 { flex-shrink: 0; }
.items-center { align-items: center; }
.justify-between { justify-content: space-between; }
.space-x-8 > :not([hidden]) ~ :not([hidden]) { margin-left: 2rem; }

/* Display utilities */
.block { display: block; }
.hidden { display: none; }
@media (min-width: 1024px) { .lg\\:block { display: block; } }

/* Sizing */
.w-full { width: 100%; }
.h-10 { height: 2.5rem; }
.h-auto { height: auto; }
@media (min-width: 1024px) { .lg\\:h-20 { height: 5rem; } }

/* Header */
header { width: 100%; background: #fff; }
.py-4 { padding-top: 1rem; padding-bottom: 1rem; }
.py-6 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
@media (min-width: 1024px) { .lg\\:py-6 { padding-top: 1.5rem; padding-bottom: 1.5rem; } .lg\\:py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; } }
.px-4 { padding-left: 1rem; padding-right: 1rem; }
.mx-auto { margin-left: auto; margin-right: auto; }

/* Site content */
.site-content { flex-grow: 1; }
main { display: block; }

/* Background colors */
.bg-white { background-color: #fff; }
.bg-rojo { background-color: var(--color-rojo); }
.bg-swhite { background-color: var(--color-swhite); }

/* Text colors */
.text-rojo { color: var(--color-rojo); }
.text-gris { color: var(--color-gris); }
.text-white { color: #fff; }
.text-gray-500 { color: #6b7280; }
.text-gray-600 { color: #4b5563; }

/* Typography */
.text-base { font-size: 1rem; line-height: 1.5; }
.text-lg { font-size: 1.125rem; }
.text-xl { font-size: 1.25rem; }
.text-2xl { font-size: 1.5rem; line-height: 2rem; }
.text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
.text-4xl { font-size: 2.25rem; line-height: 2.5rem; }
.text-5xl { font-size: 3rem; line-height: 1; }
@media (min-width: 1024px) { .lg\\:text-5xl { font-size: 3rem; line-height: 1; } }
.font-bold { font-weight: 700; }
.font-semibold { font-weight: 600; }
.italic { font-style: italic; }
.leading-tight { line-height: 1.25; }
.leading-relaxed { line-height: 1.625; }
.tracking-tighter { letter-spacing: -0.05em; }
.text-center { text-align: center; }
.text-right { text-align: right; }
.underline { text-decoration: underline; }
.no-underline { text-decoration: none; }

/* Spacing */
.mb-8 { margin-bottom: 2rem; }
.mt-8 { margin-top: 2rem; }
.my-8 { margin-top: 2rem; margin-bottom: 2rem; }
.my-12 { margin-top: 3rem; margin-bottom: 3rem; }
.pt-2 { padding-top: 0.5rem; }
.space-x-1 > :not([hidden]) ~ :not([hidden]) { margin-left: 0.25rem; }

/* Images */
.object-cover { object-fit: cover; }
@media (min-width: 1024px) { 
  .lg\\:aspect-\\[2\\/3\\] { aspect-ratio: 2/3; }
  .lg\\:max-h-\\[650px\\] { max-height: 650px; }
  .lg\\:w-4\\/5 { width: 80%; }
  .lg\\:w-\\[258px\\] { width: 258px; }
}
.w-\\[129px\\] { width: 129px; }

/* Breadcrumbs */
.flex-wrap { flex-wrap: wrap; }
.gap-x-2 { column-gap: 0.5rem; }
.gap-y-1 { row-gap: 0.25rem; }

/* Progress bar (fixed at top) */
.fixed { position: fixed; }
.top-0 { top: 0; }
.left-0 { left: 0; }
.h-1\\.5 { height: 0.375rem; }
.z-50 { z-index: 50; }
.transform-gpu { transform: translateZ(0); }

/* Slider (homepage) - critical for LCP */
.relative { position: relative; }
.absolute { position: absolute; }
.inset-0 { top: 0; right: 0; bottom: 0; left: 0; }
.overflow-hidden { overflow: hidden; }
.aspect-\\[5\\/4\\] { aspect-ratio: 5/4; }
@media (min-width: 1024px) { .lg\\:aspect-\\[21\\/9\\] { aspect-ratio: 21/9; } }
CSS;
}

/**
 * Output critical CSS in head
 */
function carcaj_output_critical_css() {
    echo '<style id="critical-css">' . carcaj_get_critical_css() . '</style>' . "\n";
}
