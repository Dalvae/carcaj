<section class="relatedPosts">
  <div class="container mx-auto">
    <h2 class="text-rojo font-semibold text-center uppercase underline my-7 text-xl">Artículos Recomendados</h2>
    <?php
    $postid = get_the_ID();
    $current_categories = wp_get_post_categories($postid);
    $posts_per_page = 3;

    // Posts del mismo autor - priorizando los más recientes
    $author_query = new WP_Query([
      'post_type' => 'post',
      'author' => get_the_author_meta('ID'),
      'posts_per_page' => $posts_per_page,
      'post__not_in' => array($postid),
      'orderby' => 'date',
      'order' => 'DESC'
    ]);

    if ($author_query->post_count < $posts_per_page) {
      $remaining_posts = $posts_per_page - $author_query->post_count;

      // Query para posts recientes con categorías similares
      $category_query = new WP_Query([
        'post_type' => 'post',
        'posts_per_page' => 30, // Aumentamos para tener más posts recientes para elegir
        'category__in' => $current_categories,
        'orderby' => 'date', // Ordenar por fecha
        'order' => 'DESC', // Los más recientes primero
        'post__not_in' => array_merge(
          [$postid],
          wp_list_pluck($author_query->posts, 'ID')
        )
      ]);

      $category_posts = [];

      if ($category_query->have_posts()) {
        while ($category_query->have_posts()) {
          $category_query->the_post();
          $post_categories = wp_get_post_categories(get_the_ID());
          $matches = count(array_intersect($current_categories, $post_categories));
          $category_posts[] = [
            'post' => get_post(),
            'matches' => $matches,
            'date' => get_the_date('U') // Timestamp para comparar fechas
          ];
        }
        wp_reset_postdata();

        // Ordenar primero por coincidencias y luego por fecha
        usort($category_posts, function ($a, $b) {
          if ($b['matches'] == $a['matches']) {
            return $b['date'] - $a['date']; // Si tienen las mismas coincidencias, priorizar el más reciente
          }
          return $b['matches'] - $a['matches'];
        });

        $category_posts = array_slice(array_column($category_posts, 'post'), 0, $remaining_posts);
      }

      // Si aún no tenemos suficientes posts, buscar los más recientes de las categorías
      if (count($category_posts) < $remaining_posts) {
        $needed = $remaining_posts - count($category_posts);
        $fallback_query = new WP_Query([
          'post_type' => 'post',
          'posts_per_page' => $needed,
          'category__in' => $current_categories,
          'orderby' => 'date',
          'order' => 'DESC',
          'post__not_in' => array_merge(
            [$postid],
            wp_list_pluck($author_query->posts, 'ID'),
            wp_list_pluck($category_posts, 'ID')
          )
        ]);

        if ($fallback_query->have_posts()) {
          while ($fallback_query->have_posts()) {
            $fallback_query->the_post();
            $category_posts[] = get_post();
          }
          wp_reset_postdata();
        }
      }

      // Combinar posts del autor y categorías
      $all_posts = array_merge(
        $author_query->posts,
        $category_posts
      );

      $final_query = new WP_Query([
        'post_type' => 'post',
        'post__in' => wp_list_pluck($all_posts, 'ID'),
        'posts_per_page' => $posts_per_page,
        'orderby' => 'post__in'
      ]);
    } else {
      $final_query = $author_query;
    }

    // Mostrar los posts
    display_articles_grid([
      'query' => $final_query,
      'show_pagination' => false,
    ]);
    ?>
  </div>
</section>