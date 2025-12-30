<form action="<?php bloginfo('url'); ?>/" method="get" class="flex" role="search">
    <div class="relative w-full">
        <label for="search" class="sr-only">Buscar</label>
        <input type="text"
            name="s"
            id="search"
            placeholder="Buscar"
            value="<?php the_search_query(); ?>"
            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-rosado focus:border-rosado" />
        <button type="submit"
            aria-label="Buscar"
            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-rosado transition-colors duration-200 p-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </button>
    </div>
</form>
