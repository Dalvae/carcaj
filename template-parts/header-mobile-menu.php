<!-- Overlay -->
<div id="mobile-menu-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-[968] hidden"></div>

<!-- Menú Móvil -->
<div id="mobile-menu"
    class="lg:hidden fixed inset-0 bg-white z-[979] overflow-y-auto transform transition-[opacity,transform] duration-300 ease-out opacity-0 scale-95 pointer-events-none"
    aria-hidden="true"
    inert>

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
                class="p-3 rounded-full fill-rosado text-white hover:bg-darkgold transition-colors duration-200">
                <svg class="h-6 w-6">
                    <use xlink:href="#icon-facebook" />
                </svg>
            </a>
            <a href="https://www.instagram.com/revista.carcaj/"
                target="_blank"
                class="p-3 rounded-full fill-rosado text-white hover:bg-darkgold transition-colors duration-200">
                <svg class="h-6 w-6">
                    <use xlink:href="#icon-instagram" />
                </svg>
            </a>
        </div>
    </div>
</div>
