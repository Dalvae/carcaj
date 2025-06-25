<?php get_header(); ?>
<div class="container mx-auto my-8 px-4">
    <div class="my-16">
        <?php get_template_part('template-parts/search-form'); ?>
    </div>

    <div class="mb-12">
        <?php
        $title_parts = [];
        $search_query = get_search_query();
        
        $author_id = get_query_var('author');
        $cat_id = get_query_var('cat');
        $year_slug = get_query_var('anho');

        if ($search_query) {
            $title_parts[] = sprintf(esc_html__('"%s"', 'carcaj'), $search_query);
        }
        if ($author_id) {
            $author_name = get_the_author_meta('display_name', $author_id);
            $title_parts[] = sprintf(esc_html__('autor: %s', 'carcaj'), $author_name);
        }
        if ($cat_id) {
            $category = get_category($cat_id);
            if ($category) {
                $title_parts[] = sprintf(esc_html__('categoría: %s', 'carcaj'), $category->name);
            }
        }
        if ($year_slug) {
            $term = get_term_by('slug', $year_slug, 'anho');
            if ($term) {
                $title_parts[] = sprintf(esc_html__('año: %s', 'carcaj'), $term->name);
            }
        }

        if (!empty($title_parts)) {
            echo '<h1 class="text-3xl font-semibold text-rojo">Resultados para ' . implode(' y ', $title_parts) . '</h1>';
        } else {
            echo '<h1 class="text-3xl font-semibold text-rojo">Resultados de búsqueda</h1>';
        }
        ?>
    </div>

    <?php
    if (have_posts()) :
        // Usar la función display_articles_grid con el query actual
        display_articles_grid(array(
            'query' => $wp_query, // Pasar el query de búsqueda actual
            'show_pagination' => true,
            'show_excerpt' => true,
            'show_author' => true,
            'use_query_string' => false // Usar la paginación estándar de WordPress
        ));
    else :
    ?>
        <p>No se encontraron resultados para tu búsqueda.</p>
    <?php
    endif;
    ?>
</div>
<?php get_footer(); ?>
