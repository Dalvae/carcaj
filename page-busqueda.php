<?php get_header(); ?>
<main class="container mx-auto px-4 py-8 mb-8">
  <section class="w-full max-w-xl mx-auto" style="max-width: 900px;">
    <?php if (have_posts()): while (have_posts()) : the_post(); ?>

        <article class="bg-white ">
          <!-- Barra de título -->
          <div class="flex items-center justify-center  p-6">
            <h1 class="text-5xl font-semibold text-rojo">Búsqueda</h1>
            <img src="<?php bloginfo('template_url'); ?>/img/search.png" alt="Search" style="height: 62px;">
          </div>

          <!-- Filtros de búsqueda -->
          <div class="flex flex-col gap-4 p-4"> <!-- Cambiado a flex-col y reducido gap y padding -->

            <!-- Búsqueda por término -->
            <div>
              <form class="relative" method="get" action="<?php echo home_url(); ?>" role="search">
                <input
                  class="w-full px-4 py-2 border border-gray-300   focus:ring-2 focus:ring-rojo focus:border-transparent"
                  type="search"
                  name="s"
                  placeholder="Buscar por palabra...">
                <button
                  class="mt-2 w-full bg-rojo hover:bg-darkgold text-white text-bold italic font-medium py-2 px-4   transition duration-300"
                  type="submit">
                  Buscar
                </button>
              </form>
            </div>

            <!-- Selector de categoría -->
            <div class="flex-1">
              <form id="category-select" action="<?php echo esc_url(home_url('/')); ?>" method="get">
                <?php
                $args = array(
                  'show_option_none' => __('Categoría'),
                  'show_count'       => 0,
                  'orderby'          => 'name',
                  'name'             => 'cat',
                  'echo'             => 0,
                  'taxonomy'         => 'category',
                  'value_field'      => 'id',
                  'class'            => 'w-full px-4 py-2 border border-gray-300   focus:ring-2 focus:ring-rojo focus:border-transparent'
                );
                $select = wp_dropdown_categories($args);
                $replace = "<select$1 onchange='return this.form.submit()' class='w-full px-4 py-2 border border-gray-300   focus:ring-2 focus:ring-rojo focus:border-transparent'>";
                $select = preg_replace('#<select([^>]*)>#', $replace, $select);
                echo $select;
                ?>
              </form>
            </div>

            <!-- Selector de año -->
            <div class="flex-1">
              <form id="year-select" action="<?php echo esc_url(home_url('/')); ?>" method="get">
                <?php
                $args = array(
                  'show_option_none' => __('Año'),
                  'show_count'       => 0,
                  'orderby'          => 'name',
                  'name'             => 'anho',
                  'echo'             => 0,
                  'taxonomy'         => 'anho',
                  'value_field'      => 'slug',
                  'class'            => 'w-full px-4 py-2 border border-gray-300 focus:ring-2 focus:ring-rojo focus:border-transparent'
                );

                // Debug output
                echo '<!-- Debug: Checking for anho terms -->';
                $terms = get_terms(array(
                  'taxonomy' => 'anho',
                  'hide_empty' => false,
                ));
                if (is_wp_error($terms)) {
                  echo '<!-- Debug: Error getting terms: ' . esc_html($terms->get_error_message()) . ' -->';
                } else {
                  echo '<!-- Debug: Found ' . count($terms) . ' terms -->';
                }

                $select = wp_dropdown_categories($args);
                $replace = "<select$1 onchange='return this.form.submit()' class='w-full px-4 py-2 border border-gray-300 focus:ring-2 focus:ring-rojo focus:border-transparent'>";
                $select = preg_replace('#<select([^>]*)>#', $replace, $select);
                echo $select;
                ?>
              </form>
            </div>

            <!-- Selector de autor -->
            <div class="flex-1">
              <select
                name="author-dropdown"
                id="author-dropdown--1"
                onchange="document.location.href=this.options[this.selectedIndex].value;"
                class="w-full px-4 py-2 border border-gray-300   focus:ring-2 focus:ring-rojo focus:border-transparent">
                <option value=""><?php echo esc_attr(__('Autor')); ?></option>
                <?php
                $users = get_users('role=author');
                foreach ($users as $user) {
                  if (count_user_posts($user->id) > 0) {
                    echo '<option value="' . get_author_posts_url($user->id) . '">';
                    echo $user->display_name;
                    echo '</option>';
                  }
                }
                ?>
              </select>
            </div>
          </div>
        </article>

      <?php endwhile; ?>
    <?php else: endif; ?>

  </section>
</main>
<?php get_footer(); ?>