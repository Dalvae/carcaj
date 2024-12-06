<?php get_header(); ?>
<div class="container mx-auto my-8 px-4">
    <div class="my-16">
        <?php get_template_part('template-parts/search-form'); ?>
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