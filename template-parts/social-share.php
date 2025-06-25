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
            <svg class="w-12 h-12 fill-rojo hover:fill-darkgold transition-colors duration-300">
                <use xlink:href="#icon-share" />
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
