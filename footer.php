</main>



</div>

<!-- Footer -->
<footer class="bg-custom-grey text-white pt-12 lg:pt-16 pb-8">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
            <!-- Columna Logos (1/3) -->
            <div class="flex gap-4 items-start justify-center lg:justify-start">
                <img src="<?php bloginfo('template_url'); ?>/img/logo_gobierno.png"
                    alt="Logo Gobierno"
                    class="h-24 lg:h-auto lg:max-w-[45%] w-auto object-contain">
                <img src="<?php bloginfo('template_url'); ?>/img/logolom.png"
                    alt="Logo LOM"
                    class="h-32 lg:h-auto lg:max-w-[45%] w-auto object-contain">
            </div>

            <!-- Columna Contenido (2/3) -->
            <div class="lg:col-span-2 flex flex-col justify-between gap-6">
                <!-- Navegación -->
                <nav class="w-full border-b border-white/30 pb-6 hidden lg:block">
                    <?php
                    wp_nav_menu(array(
                        'menu' => 'Menu Superior',
                        'container' => false,
                        'menu_class' => 'flex flex-row flex-wrap gap-x-6 gap-y-3 font-bold footer-nav justify-between w-full',
                        'theme_location' => 'footer-menu',
                        'depth' => 1,
                    ));
                    ?>
                </nav>

                <!-- Búsqueda y Social -->
                <div class="flex flex-col lg:flex-row gap-6 w-full justify-between items-center">
                    <div class="text-black w-full max-w-xs lg:w-auto lg:max-w-none">
                        <?php get_template_part('template-parts/search-form'); ?>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4">
                        <p class="font-alegreya text-base lg:text-lg font-semibold italic text-white/90">síguenos como @revistacarcaj</p>
                        <div class="flex items-center gap-4">
                            <a href="https://www.facebook.com/revistacarcaj/" target="_blank"
                                class="text-white/90 hover:text-rosado transition-all duration-300 hover:scale-110">
                                <svg class="w-7 h-7 fill-current">
                                    <use xlink:href="#icon-facebook" />
                                </svg>
                            </a>
                            <a href="https://www.instagram.com/revista.carcaj/" target="_blank"
                                class="text-white/90 hover:text-rosado transition-all duration-300 hover:scale-110">
                                <svg class="w-7 h-7 fill-current">
                                    <use xlink:href="#icon-instagram" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Texto Footer -->
                <div class="w-full font-alegreya text-sm leading-relaxed text-white/80 border-t border-white/30 pt-6 text-center lg:text-right">
                    <?php the_field('texto_footer'); ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>

</html>
