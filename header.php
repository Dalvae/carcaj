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
    <link rel="preload" href="<?php echo get_template_directory_uri(); ?>/dist/assets/fonts/Alegreya-VariableFont_wght.ttf" as="font" type="font/ttf" crossorigin>
    <link rel="preload" href="<?php echo get_template_directory_uri(); ?>/dist/assets/fonts/Alegreya-Italic-VariableFont_wght.ttf" as="font" type="font/ttf" crossorigin>

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
            Alpine.data('header', () => ({
                isOpen: false,
                isSearchOpen: false,
                currentCategory: null,
                hasScrolled: false,
                init() {
                    this.$watch('isOpen', value => {
                        document.body.style.overflow = value ? 'hidden' : '';
                    });
                }
            }))
        })
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
                <div class="flex-shrink-0">
                    <a href="<?php echo get_option('home'); ?>" class="block">
                        <img src="<?php bloginfo('template_url'); ?>/img/logo.svg"
                            alt="<?php bloginfo('name'); ?>"
                            class="h-10 lg:h-20 w-auto">
                    </a>
                </div>
                <!-- Navegación Desktop -->
                <div class="hidden md:space-x-0 lg:flex lg:items-center lg:space-x-8">
                    <nav class="font-bold  lg:text-md xl:text-xl italic">
                        <?php
                        wp_nav_menu(array(
                            'menu' => 'Menú Superior',
                            'container' => false,
                            'menu_class' => 'flex md:space-x-4 lg:space-x-8 ',
                            'theme_location' => 'header-menu',
                            'depth' => 2,
                            'fallback_cb' => 'wp_page_menu',
                            'link_class' => 'relative hover:text-[#EA6060] transition-colors duration-200
                                      after:content-[""] after:absolute after:bottom-0 after:left-0 
                                      after:w-full after:h-0.5 after:bg-[#EA6060] after:scale-x-0 
                                      after:transition-transform after:duration-300
                                      hover:after:scale-x-100'
                        ));
                        ?>
                    </nav>

                    <!-- Iconos de acción -->
                    <div class="flex items-center space-x-6">
                        <!-- Botón de búsqueda -->
                        <button @click="isSearchOpen = !isSearchOpen"
                            class="p-2 hover:text-[#EA6060] transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>

                        <!-- Social Icons con hover effects -->
                        <div class="flex items-center space-x-4">
                            <a href="https://www.facebook.com/revistacarcaj/"
                                target="_blank"
                                class="group">
                                <svg class="h-5 w-5 fill-[#EA6060] transition-transform duration-300 group-hover:scale-110">
                                    <use xlink:href="#icon-facebook" />
                                </svg>
                            </a>
                            <a href="https://www.instagram.com/revista.carcaj/"
                                target="_blank"
                                class="group">
                                <svg class="h-5 w-5 fill-[#EA6060] transition-transform duration-300 group-hover:scale-110">
                                    <use xlink:href="#icon-instagram" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Botón menú móvil con animación -->
                <button @click="isOpen = !isOpen"
                    class="lg:hidden relative w-10 h-10 focus:outline-none z-[60]"
                    aria-label="Menu">
                    <div class="absolute w-6 transform left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2">
                        <span class="absolute h-0.5 w-6 bg-black transform transition duration-300 ease-in-out"
                            :class="{'rotate-45': isOpen, '-translate-y-2': !isOpen}"></span>
                        <span class="absolute h-0.5 w-6 bg-black transform transition duration-300 ease-in-out"
                            :class="{'opacity-0': isOpen, 'translate-y-0': !isOpen}"></span>
                        <span class="absolute h-0.5 w-6 bg-black transform transition duration-300 ease-in-out"
                            :class="{'-rotate-45': isOpen, 'translate-y-2': !isOpen}"></span>
                    </div>
                </button>
            </div>
            <!-- Barra de búsqueda expandible -->
            <div x-show="isSearchOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="absolute left-0 right-0 bg-white shadow-lg z-[70]">
                <div class="container mx-auto px-4 lg:-mt-8">
                    <!-- Contenedor más específico para alineación -->
                    <div class="flex justify-end">
                        <div class="w-full lg:w-[65%] lg:pb-2">
                            <div class="relative">
                                <button @click="isSearchOpen = false"
                                    class="absolute right-2 top-2 p-2 text-gray-500 hover:text-[#EA6060] md:hidden">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <?php get_template_part('template-parts/search-form'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Menú Móvil Mejorado -->
            <div x-show="isOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="lg:hidden fixed inset-0 bg-white z-50 overflow-y-auto">

                <div class="container mx-auto px-4 py-8 font-bold">
                    <!-- Buscador integrado en el menú -->
                    <div class="mt-8 mb-4">
                        <?php get_template_part('template-parts/search-form'); ?>
                    </div>

                    <nav class="space-y-6">
                        <?php
                        wp_nav_menu(array(
                            'menu' => 'Menú Superior',
                            'container' => false,
                            'menu_class' => 'space-y-4',
                            'theme_location' => 'header-menu',
                            'depth' => 2,
                            'fallback_cb' => 'wp_page_menu',
                            'link_class' => 'text-xl font-bold block py-2 hover:text-[#EA6060] transition-colors duration-200'
                        ));
                        ?>
                    </nav>

                    <!-- Mobile Social Icons -->
                    <div class="flex justify-center space-x-6 mt-8 pt-8 border-t">
                        <a href="https://www.facebook.com/revistacarcaj/"
                            target="_blank"
                            class="p-3 rounded-full fill-[#EA6060] text-white hover:bg-[#9A7A14] transition-colors duration-200">
                            <svg class="h-6 w-6">
                                <use xlink:href="#icon-facebook" />
                            </svg>
                        </a>
                        <a href="https://www.instagram.com/revista.carcaj/"
                            target="_blank"
                            class="p-3 rounded-full fill-[#EA6060] text-white hover:bg-[#9A7A14] transition-colors duration-200">
                            <svg class="h-6 w-6">
                                <use xlink:href="#icon-instagram" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div id="content" class="site-content flex-grow">
        <main>