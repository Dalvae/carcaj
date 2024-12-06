<?php get_header(); ?>

<div class="container mx-auto my-8 px-4">
    <?php if (have_posts()) : ?>
        <h1 class="text-4xl font-bold mb-8 text-rojo">
            <?php
            if (is_category()) {
                single_cat_title('Categoría: ');
            } elseif (is_tag()) {
                single_tag_title('Etiqueta: ');
            } elseif (is_author()) {
                echo 'Artículos por ' . get_the_author();
            } elseif (is_date()) {
                echo 'Archivos de ' . get_the_date('F Y');
            } else {
                echo 'Archivos';
            }
            ?>
        </h1>

        <?php
        display_articles_grid([
            'query' => null,
            'show_pagination' => true,
            'show_excerpt' => true
        ]);
        ?>

    <?php else : ?>
        <p>No se encontraron artículos.</p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>