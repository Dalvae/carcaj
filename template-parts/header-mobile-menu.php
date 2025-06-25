<template x-if="$store.header.isOpen">
    <div class="fixed inset-0 bg-black bg-opacity-50 z-[968]"
        @click="$store.header.isOpen = false"></div>
</template>
<!-- Menú Móvil Mejorado -->

<div x-show="$store.header.isOpen"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="lg:hidden fixed inset-0 bg-white z-[979] overflow-y-auto"
    @click.away="$store.header.isOpen = false"
    style="position: fixed;">

    <div class="container mx-auto px-4 py-8 font-bold italic text-sm">
        <!-- Buscador integrado -->
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
                'walker' => new Walker_Nav_Menu_Tailwind(),
                'fallback_cb' => 'wp_page_menu',
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
