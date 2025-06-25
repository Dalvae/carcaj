<?php get_header(); ?>
<div class="container mx-auto px-4 mb-16">

    <section>
        <h1 class="text-5xl font-bold text-center text-rojo text-bold my-8">Archivo de especiales</h1>

        <div class="pt-16">
            <?php
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $query = new WP_Query([
                'post_type'      => 'page',
                'post_parent'    => 7729,
                'posts_per_page' => 12,
                'paged'          => $paged,
            ]);

            if ($query->have_posts()) {
                display_articles_grid([
                    'query'           => $query,
                    'card_template'   => 'template-parts/card-especial',
                    'show_pagination' => true,
                    'use_query_string' => false // Standard WP pagination
                ]);
            } else {
                echo '<p class="col-span-full text-center text-gray-600">No hay especiales disponibles.</p>';
            }
            ?>
        </div>
    </section>
</div>
<?php get_footer(); ?>
