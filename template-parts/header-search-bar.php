<!-- Barra de búsqueda -->
<div x-show="isSearchOpen"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 -translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 -translate-y-2"
     @click.away="isSearchOpen = false"
     class="absolute top-full w-full bg-white shadow-sm rounded-b-lg mt-4 z-20"
     style="display: none;">
    <?php get_template_part('template-parts/search-form'); ?>
</div>
