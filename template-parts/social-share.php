<div class="flex justify-center items-center gap-8 my-24">
    <!-- Botón de imprimir -->
    <div class="inline-flex text-rojo">
        <?php
        if (function_exists('pf_show_link')) {
            echo pf_show_link();
        }
        ?>
    </div>

    <!-- Firma -->
    <img class="w-12 h-12" src="<?php bloginfo('template_url'); ?>/img/diana.svg" alt="">

    <!-- Botón de compartir -->
    <div class="relative" x-data="{ isOpen: false }" @click.away="isOpen = false">
        <button @click="isOpen = !isOpen" class="inline-flex p-2">
            <svg class="w-12 h-12 fill-rojo hover:fill-darkgold transition-colors duration-300" viewBox="0 0 24 24">
                <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z"/>
            </svg>
        </button>

        <!-- Menú desplegable de redes sociales -->
        <div x-show="isOpen" x-transition class="absolute left-full top-0 ml-2 bg-white p-4 rounded-lg shadow-lg flex items-center gap-4 z-10" style="transform: translateX(10px);">
             <div class="relative after:content-[''] after:absolute after:top-1/2 after:right-full after:border-8 after:border-transparent after:border-r-white after:-translate-y-1/2">
                <!-- Facebook -->
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" class="inline-flex p-2">
                    <svg class="w-10 h-10 fill-rose-400 hover:fill-darkgold transition-colors duration-300">
                        <use xlink:href="#icon-facebook"></use>
                    </svg>
                </a>
                <!-- Instagram -->
                <a href="https://www.instagram.com/share?url=<?php echo urlencode(get_permalink()); ?>" target="_blank" class="inline-flex p-2">
                    <svg class="w-10 h-10 fill-rosado hover:fill-darkgold transition-colors duration-300">
                        <use xlink:href="#icon-instagram"></use>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
