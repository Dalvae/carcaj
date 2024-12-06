<!-- ESTO NO DEBERIA ESTAR EN USO, LO GUARDO PARA SACAR LOS ESTILOS, LUEGO BORRAR  -->

<section
    id="articles-section"
    class="articles-section"
    x-data="{ 
        async handlePageClick(e) {
            e.preventDefault();
            const url = e.target.href;
            window.location = url;
        }
    }">
    <?php
    $posts_per_page = isset($args['posts_per_page']) ? $args['posts_per_page'] : 12;
    $paged = (isset($_GET['pg'])) ? absint($_GET['pg']) : 1;

    // Si estamos en la página de inicio y queremos una query personalizada
    if (isset($args['custom_query']) && $args['custom_query']) {
        $query_args = array(
            'post_type' => 'post',
            'posts_per_page' => $posts_per_page,
            'paged' => $paged
        );

        $query = new WP_Query($query_args);
    }
    // Si ya hay una query principal (como en archive o search), usamos esa
    elseif (have_posts()) {
        global $wp_query;
        $query = $wp_query;
    }
    // Por defecto, creamos una nueva query
    else {
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => $posts_per_page,
            'paged' => $paged
        );

        $query = new WP_Query($args);
    }
    ?>
    <!-- Contenedor de artículos -->
    <div class="articles grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        if ($query->have_posts()) :
            while ($query->have_posts()) : $query->the_post();
        ?>
                <article class="article-card bg-white overflow-hidden group relative">
                    <!-- Link principal que cubre toda la card -->
                    <a href="<?php the_permalink(); ?>" class="absolute inset-0 z-10"></a>

                    <div class="thumb relative">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="article-image h-60 overflow-hidden">
                                <?php the_post_thumbnail('medium', array('class' => 'w-full h-full object-cover transition-brightness duration-300 group-hover:brightness-50')); ?>
                            </div>
                        <?php endif; ?>
                        <div class="categories absolute bottom-0 right-0 font-medium text-4xl flex flex-col items-end opacity-0 transform translate-y-2 transition-all duration-300 group-hover:opacity-100 group-hover:translate-y-0 z-20">
                            <div class="relative z-20">
                                <?php the_category(' '); ?>
                            </div>
                        </div>
                    </div>

                    <div class="p-4">
                        <div class="date text-gris"><?php the_time('d') ?> de <?php the_time('F Y') ?></div>
                        <h3 class="text-3xl font-semibold transition-colors duration-300 text-rojo group-hover:text-darkgold">
                            <?php the_title(); ?>
                        </h3>
                        <div class="autor italic text-end">
                            <span class="text-gris">por</span>
                            <span class="relative z-20">
                                <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" class="text-rojo ml-1">
                                    <?php the_author(); ?>
                                </a>
                            </span>
                        </div>
                        <div class="text-gris text-justify text-xl my-4">
                            <?php the_excerpt(); ?>
                        </div>
                    </div>
                </article>
            <?php
            endwhile;
        else :
            ?>
            <p>No hay artículos para mostrar.</p>
        <?php
        endif;
        ?>
    </div>

    <?php if ($query->max_num_pages > 1) : ?>
        <div class="pagination flex justify-center items-center gap-2 mt-8">
            <?php
            // Botón anterior
            if ($paged > 1) {
                printf(
                    '<a href="%s#articles-section" @click="handlePageClick" class="px-3 py-2 bg-rojo text-white rounded hover:bg-selection">&laquo;</a>',
                    esc_url(add_query_arg('pg', $paged - 1))
                );
            }
            // Mostrar números de página con `...`
            $range = 2;
            $show_dots = false;

            for ($i = 1; $i <= $query->max_num_pages; $i++) {
                if ($i == 1 || $i == $query->max_num_pages || ($i >= $paged - $range && $i <= $paged + $range)) {
                    if ($i == $paged) {
                        printf(
                            '<span class="px-3 py-2 bg-rojo text-white rounded">%s</span>',
                            $i
                        );
                    } else {
                        printf(
                            '<a href="%s#articles-section" @click="scrollToArticles" class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-selection hover:text-white">%s</a>',
                            esc_url(add_query_arg('pg', $i)),
                            $i
                        );
                    }
                    $show_dots = true;
                } elseif ($show_dots) {
                    echo '<span class="px-3 py-2 text-gray-500">...</span>';
                    $show_dots = false;
                }
            }

            // Botón siguiente
            if ($paged < $query->max_num_pages) {
                printf(
                    '<a href="%s#articles-section" @click="handlePageClick" class="px-3 py-2 bg-rojo text-white rounded hover:bg-selection">&raquo;</a>',
                    esc_url(add_query_arg('pg', $paged + 1))
                );
            }
            ?>
        </div>
    <?php endif; ?>
</section>

<script>
    document.addEventListener("turbo:load", () => {
        if (window.location.hash === '#articles-section') {
            const section = document.getElementById('articles-section');
            if (section) {
                requestAnimationFrame(() => {
                    window.scrollTo({
                        top: section.offsetTop - 200,
                        behavior: "smooth",
                    });
                });
            }
        }
    });
</script>