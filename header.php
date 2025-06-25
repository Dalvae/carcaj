<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">

<head>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-LDH31X2HDV"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-LDH31X2HDV');
    </script>
    <!-- End Google Tag Manager -->
    <meta charset="<?php bloginfo('charset'); ?>">
    <title><?php wp_title(''); ?><?php if (wp_title('', false)) {
                                        echo ' : ';
                                    } ?><?php bloginfo('name'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- FAVICONS -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php bloginfo('template_url'); ?>/img/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php bloginfo('template_url'); ?>/img/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php bloginfo('template_url'); ?>/img/icons/favicon-16x16.png">
    <link rel="manifest" href="<?php bloginfo('template_url'); ?>/img/icons/manifest.json">
    <meta name="theme-color" content="#ffffff">

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-142230775-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'UA-142230775-1');
    </script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('header', {
                isOpen: false,
                currentCategory: null,
                hasScrolled: false
            });

            Alpine.data('header', () => ({
                isSearchOpen: false, 
                init() {
                    this.$watch('$store.header.isOpen', value => {
                        document.body.style.overflow = value ? 'hidden' : '';
                        if (value) {
                            window.dispatchEvent(new Event('stopSlider'));
                        } else {
                            window.dispatchEvent(new Event('startSlider'));
                        }
                    });
                }
            }))
        });
    </script>

    <?php include_once('img/sprite.svg'); ?>
    <?php wp_head(); ?>
</head>

<body <?php body_class('bg-white font-alegreya min-h-screen flex flex-col'); ?>>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TQLBVTPB"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <header class="w-full bg-white" x-data="header" @scroll.window="hasScrolled = (window.pageYOffset > 20)">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4 lg:py-6"
                :class="{ 'lg:py-3': hasScrolled }"
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
