<div class="flex justify-center items-center gap-8 my-24">
    <!-- Botón de imprimir -->
    <div class="inline-flex text-rojo">
        <button type="button" onclick="window.print();" class="inline-flex p-2 cursor-pointer" aria-label="Imprimir artículo">
            <svg class="w-12 h-12 fill-rojo hover:fill-darkgold transition-colors duration-300" aria-hidden="true">
                <use xlink:href="#icon-print" />
            </svg>
        </button>
    </div>

    <!-- Firma -->
    <img class="w-12 h-12" src="<?php echo esc_url(get_template_directory_uri()); ?>/img/diana.svg" 
        width="53" height="52" alt="Carcaj" loading="lazy">

    <!-- Botón de compartir -->
    <div class="relative share-container">
        <button class="share-toggle inline-flex p-2" aria-label="Compartir artículo" aria-expanded="false">
            <svg class="w-12 h-12 fill-rojo hover:fill-darkgold transition-colors duration-300" aria-hidden="true">
                <use xlink:href="#icon-share" />
            </svg>
        </button>

        <!-- Menú desplegable de redes sociales -->
        <div class="share-menu absolute left-full top-0 ml-2 bg-white p-4 rounded-lg shadow-lg flex items-center gap-4 z-10 opacity-0 pointer-events-none transition-opacity duration-200" style="transform: translateX(10px);">
             <div class="relative after:content-[''] after:absolute after:top-1/2 after:right-full after:border-8 after:border-transparent after:border-r-white after:-translate-y-1/2">
                <!-- Facebook -->
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener" class="inline-flex p-2" aria-label="Compartir en Facebook">
                    <svg class="w-10 h-10 fill-rose-400 hover:fill-darkgold transition-colors duration-300" aria-hidden="true">
                        <use xlink:href="#icon-facebook"></use>
                    </svg>
                </a>
                <!-- Twitter/X -->
                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" rel="noopener" class="inline-flex p-2" aria-label="Compartir en Twitter">
                    <svg class="w-10 h-10 fill-rosado hover:fill-darkgold transition-colors duration-300" aria-hidden="true">
                        <use xlink:href="#icon-twitter"></use>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>
