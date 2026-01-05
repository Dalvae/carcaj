<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">

<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-LDH31X2HDV"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'G-LDH31X2HDV');
    </script>

    <meta charset="<?php bloginfo('charset'); ?>">
    <title><?php echo wp_get_document_title(); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- PRELOAD CRITICAL ASSETS - discover early, before wp_head -->
    <?php
    $manifest_path = get_template_directory() . '/dist/.vite/manifest.json';
    if (file_exists($manifest_path)) {
        $manifest = json_decode(file_get_contents($manifest_path), true);
        $entry = $manifest['src/theme.js'] ?? null;
        if ($entry && !empty($entry['css'][0])) {
            $css_file = $entry['css'][0];
            echo '<link rel="preload" href="' . esc_url(get_template_directory_uri() . '/dist/' . $css_file) . '" as="style">' . "\n";
        }
    }
    
    // Preload LCP image for homepage slider
    if (is_front_page() || is_page_template('page-inicio.php')) {
        $slides = get_field('slider');
        if (!empty($slides[0]['imagen'])) {
            $image = $slides[0]['imagen'];
            $sizes = $image['sizes'] ?? [];
            $lcp_src = $sizes['medium_large'] ?? $sizes['large'] ?? $image['url'];
            echo '<link rel="preload" as="image" href="' . esc_url($lcp_src) . '" fetchpriority="high">' . "\n";
        }
    }
    ?>
    <link rel="preload" href="<?php echo esc_url(get_template_directory_uri()); ?>/dist/assets/fonts/subset-Alegreya-Regular.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?php echo esc_url(get_template_directory_uri()); ?>/dist/assets/fonts/subset-Alegreya-Bold.woff2" as="font" type="font/woff2" crossorigin>

    <!-- META DESCRIPTION -->
    <?php
    $meta_description = '';
    if (is_front_page() || is_home()) {
        $meta_description = 'Carcaj, flechas de sentido. Revista cultural chilena de arte, literatura y política. Textos críticos, ensayos y crónicas.';
    } elseif (is_singular()) {
        $meta_description = get_the_excerpt();
        if (empty($meta_description)) {
            $meta_description = wp_trim_words(get_the_content(), 25, '...');
        }
    } elseif (is_category() || is_tag() || is_tax()) {
        $meta_description = term_description();
    } elseif (is_author()) {
        $meta_description = get_the_author_meta('description');
    }
    
    if (empty($meta_description)) {
        $meta_description = get_bloginfo('description');
    }
    
    $meta_description = wp_strip_all_tags($meta_description);
    $meta_description = esc_attr(wp_trim_words($meta_description, 25, '...'));
    
    if (!empty($meta_description)) :
    ?>
    <meta name="description" content="<?php echo $meta_description; ?>">
    <?php endif; ?>

    <!-- FAVICONS -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url(get_template_directory_uri()); ?>/img/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url(get_template_directory_uri()); ?>/img/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url(get_template_directory_uri()); ?>/img/icons/favicon-16x16.png">
    <link rel="manifest" href="<?php echo esc_url(get_template_directory_uri()); ?>/img/icons/site.webmanifest">
    <meta name="theme-color" content="#ffffff">

    <?php include_once('img/sprite.svg'); ?>
    <?php wp_head(); ?>
</head>

<body <?php body_class('bg-white font-alegreya min-h-screen flex flex-col'); ?>>
    <header class="w-full bg-white" x-data="header" @scroll.window="$store.header.hasScrolled = (window.pageYOffset > 20)">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4 lg:py-6"
                :class="{ 'lg:py-3': $store.header.hasScrolled }"
                x-transition>

                <!-- Logo -->
                <?php get_template_part('template-parts/header-logo'); ?>
                <!-- Right side content: Nav and Search -->
                <div class="hidden lg:block relative">
                    <!-- Navegación Desktop -->
                    <div class="flex items-center space-x-8">
                        <?php get_template_part('template-parts/header-nav-desktop'); ?>
                        <?php get_template_part('template-parts/header-actions'); ?>
                    </div>

                    <!-- Barra de búsqueda -->
                    <?php get_template_part('template-parts/header-search-bar'); ?>
                </div>
                <?php get_template_part('template-parts/header-mobile-menu-button'); ?>
            </div>
            <?php get_template_part('template-parts/header-mobile-menu'); ?>
        </div>
    </header>
    <div id="content" class="site-content flex-grow">
        <main>
