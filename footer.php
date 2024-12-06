</main>



</div>

<!-- Footer -->
<footer class="bg-[#585858] text-white pt-16 bottom-0">
    <div class="container mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-[2fr,3fr] gap-12 lg:gap-16">
            <!-- Columna Logos -->
            <div class="grid grid-cols-2 gap-8 items-start">
                <div class="flex justify-center lg:justify-start">
                    <img src="<?php bloginfo('template_url'); ?>/img/logo_gobierno.png"
                        alt="Logo Gobierno"
                        class="w-auto h-auto object-contain transform transition-all duration-300 hover:opacity-90">
                </div>

                <div class="flex justify-center lg:justify-start">
                    <img src="<?php bloginfo('template_url'); ?>/img/logolom.png"
                        alt="Logo LOM"
                        class="w-auto h-auto object-contain transform transition-all duration-300 hover:opacity-90">
                </div>
            </div>

            <!-- Columna Contenido -->
            <div class="space-y-12 flex flex-col justify-around">
                <!-- Navegación -->
                <nav class="w-full border-b border-white/30 pb-8 relative before:absolute before:bottom-0 before:left-0 before:w-1/4 before:h-[2px] hidden lg:flex  justify-between">
                    <?php
                    wp_nav_menu(array(
                        'menu' => 'Menu Superior',
                        'container' => false,
                        'menu_class' => 'flex flex-col lg:flex-row flex-wrap gap-4 lg:gap-x-5 lg:gap-y-6 font-bold footer-nav justify-between w-full',
                        'theme_location' => 'footer-menu',
                        'depth' => 1,
                    ));
                    ?>
                </nav>

                <!-- Búsqueda y Social -->
                <div class="flex flex-col lg:flex-row gap-8 lg:gap-16 items-baseline w-full justify-between">
                    <!-- Búsqueda -->
                    <div class="text-black">
                        <?php get_template_part('template-parts/search-form'); ?>
                    </div>

                    <!-- Social -->
                    <div class="flex flex-row items-end lg:items-center gap-6 justify-between"> <!-- Cambiado a items-end -->
                        <p class="font-alegreya text-lg font-semibold italic text-white/90">síguenos como @revistacarcaj</p>
                        <div class="flex items-center gap-6">
                            <a href="https://www.facebook.com/revistacarcaj/"
                                target="_blank"
                                class="text-white/90 hover:text-[#EA6060] transform transition-all duration-300 hover:scale-110">
                                <svg class="w-7 h-7 fill-current">
                                    <use xlink:href="#icon-facebook" />
                                </svg>
                            </a>
                            <a href="https://www.instagram.com/revista.carcaj/"
                                target="_blank"
                                class="text-white/90 hover:text-[#EA6060] transform transition-all duration-300 hover:scale-110">
                                <svg class="w-7 h-7 fill-current">
                                    <use xlink:href="#icon-instagram" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Texto Footer -->
                <div class="w-full font-alegreya text-sm leading-relaxed text-white/80 border-t border-white/30 pt-8 relative before:absolute before:top-0 before:right-0 before:w-1/4 before:h-[2px] text-right"> <!-- Añadido text-right -->
                    <?php the_field('texto_footer'); ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>

</html>