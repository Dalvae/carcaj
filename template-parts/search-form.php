<form action="<?php bloginfo('url'); ?>/" method="get" class="flex">
    <div class="relative w-full">
        <input type="text"
            name="s"
            id="search"
            placeholder="Buscar"
            value="<?php the_search_query(); ?>"
            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-rosado focus:border-rosado" />
        <button type="submit"
            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-rosado transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </button>
    </div>
</form>
