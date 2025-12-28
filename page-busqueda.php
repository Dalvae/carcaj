<?php get_header(); ?>
<main class="container mx-auto px-4 mb-16 ">
  <section class="w-full max-w-xl mx-auto" style="max-width: 900px;">
    <?php if (have_posts()): while (have_posts()) : the_post(); ?>
      <article class="bg-white ">
        <div class="flex items-center justify-center flex-col">
          <h1 class="text-5xl font-semibold my-8 text-rojo">Búsqueda</h1>
          <img src="<?php bloginfo('template_url'); ?>/img/search.png" alt="Search" style="height: 62px;">
        </div>

        <form class="flex flex-col gap-4 p-4" method="get" action="<?php echo esc_url(home_url('/')); ?>" role="search" id="advanced-search-form">
          <!-- Búsqueda por término -->
          <div class="relative">
            <input
              class="w-full px-4 py-2 bg-white border border-gray-300 focus:ring-2 focus:ring-rojo focus:border-transparent"
              type="search"
              name="s"
              value="<?php echo get_search_query(); ?>"
              placeholder="Buscar por palabra...">
          </div>

          <!-- Selector de categoría -->
          <div>
            <?php
            wp_dropdown_categories([
              'show_option_none' => __('Categoría'),
              'name'             => 'cat',
              'id'               => 'cat-select',
              'class'            => 'w-full px-4 py-2 border border-gray-300 focus:ring-2 focus:ring-rojo focus:border-transparent',
              'value_field'      => 'id',
              'selected'         => get_query_var('cat')
            ]);
            ?>
          </div>

          <!-- Selector de año -->
          <div>
            <?php
            wp_dropdown_categories([
                'taxonomy'         => 'anho',
                'show_option_none' => __('Año'),
                'name'             => 'anho',
                'id'               => 'anho-select',
                'class'            => 'w-full px-4 py-2 border border-gray-300 focus:ring-2 focus:ring-rojo focus:border-transparent',
                'value_field'      => 'slug',
                'selected'         => get_query_var('anho')
            ]);
            ?>
          </div>

          <!-- Selector de autor -->
          <div>
            <?php
            wp_dropdown_users([
                'name' => 'author',
                'show_option_none' => __('Autor'),
                'class' => 'w-full px-4 py-2 border border-gray-300 focus:ring-2 focus:ring-rojo focus:border-transparent',
                'who' => 'authors',
                'selected' => get_query_var('author')
            ]);
            ?>
          </div>

          <button class="mt-2 w-full bg-rojo hover:bg-darkgold text-white text-bold italic font-medium py-2 px-4 transition duration-300" type="submit">
            Buscar
          </button>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('advanced-search-form');
                if (form) {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();
                        
                        const searchParams = new URLSearchParams();
                        const searchInput = form.querySelector('input[name="s"]');
                        
                        // Always include the search term, even if empty, as WordPress expects it.
                        if (searchInput) {
                            searchParams.set('s', searchInput.value);
                        }

                        // Include other filters only if they have a valid selection.
                        form.querySelectorAll('select').forEach(select => {
                            // A value of -1 or 0 typically means no selection.
                            if (select.value && select.value !== '-1' && select.value !== '0') {
                                searchParams.set(select.name, select.value);
                            }
                        });

                        window.location.href = form.action + '?' + searchParams.toString();
                    });
                }
            });
        </script>

      </article>
    <?php endwhile; endif; ?>
  </section>
</main>
<?php get_footer(); ?>
