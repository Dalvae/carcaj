<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <title><?php echo wp_get_document_title(); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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

    <!-- Google Tag Manager - deferred to not block render -->
    <script>
        window.addEventListener('load', function() {
            (function(w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start': new Date().getTime(),
                    event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s),
                    dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', 'GTM-TQLBVTPB');
        });
    </script>
    <!-- End Google Tag Manager -->

    <?php include_once('img/sprite.svg'); ?>
    <?php wp_head(); ?>
</head>

<body <?php body_class('bg-white font-alegreya min-h-screen flex flex-col'); ?>>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TQLBVTPB"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
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
