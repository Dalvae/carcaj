<?php
function display_articles_grid($args = array())
{
    $defaults = array(
        'query' => null,
        'posts_per_page' => 12,
        'show_pagination' => true,
        'show_excerpt' => true,
        'show_author' => true,
        'use_query_string' => false
    );

    $settings = wp_parse_args($args, $defaults);

    // Obtener página según el contexto
    if ($settings['use_query_string']) {
        $paged = (isset($_GET['pg'])) ? absint($_GET['pg']) : 1;
    } else {
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    }

    if (!$settings['query']) {
        global $wp_query;
        $query = $wp_query;
    } else {
        $query = $settings['query'];
    }
?>

    <section id="articles-section" class="articles-section">

        <div class="articles grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post(); ?>
                    <article class="article-card bg-white  overflow-hidden group relative">
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
                            <div class="date text-gris"><?php the_time('d'); ?> de <?php the_time('F Y'); ?></div>
                            <h3 class="text-3xl font-semibold transition-colors duration-300 text-rojo group-hover:text-darkgold">
                                <?php the_title(); ?>
                            </h3>
                            <?php if ($settings['show_author']) : ?>
                                <div class="autor italic text-end font-semibold">
                                    <span class="text-gris">por</span>
                                    <span class="relative z-20">
                                        <?php
                                        if (function_exists('get_coauthors')) {
                                            $coauthors = get_coauthors();
                                        } else {
                                            $coauthors = array(get_userdata(get_the_author_meta('ID')));
                                        }

                                        $author_links = array();
                                        foreach ($coauthors as $author) {
                                            $author_links[] = '<a href="' . get_author_posts_url($author->ID) . '" class="text-rojo ml-1 hover:text-darkgold">' .
                                                $author->display_name . '</a>';
                                        }
                                        echo implode(', ', $author_links);
                                        ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php if ($settings['show_excerpt']) : ?>
                                <div class="text-gris text-justify text-xl my-4">
                                    <?php the_excerpt(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endwhile;
            else : ?>
                <p>No hay artículos para mostrar.</p>
            <?php endif; ?>
        </div>

        <?php if ($settings['show_pagination'] && $query->max_num_pages > 1) : ?>
            <div class="pagination flex justify-center items-center gap-2 mt-8">
                <?php
                if ($settings['use_query_string']) {
                    // Paginación con query string para página de inicio
                    if ($paged > 1) {
                        printf(
                            '<a href="%s#articles-section" class="px-3 py-2 bg-rojo text-white rounded hover:bg-selection">&laquo;</a>',
                            esc_url(add_query_arg('pg', $paged - 1))
                        );
                    }

                    // Números de página con `...`
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
                                    '<a href="%s#articles-section" class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-selection hover:text-white">%s</a>',
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
                            '<a href="%s#articles-section" class="px-3 py-2 bg-rojo text-white rounded hover:bg-selection">&raquo;</a>',
                            esc_url(add_query_arg('pg', $paged + 1))
                        );
                    }
                } else {
                    // Paginación estándar de WordPress para archives con estilos personalizados
                    $args = array(
                        'base' => str_replace(999999999, '%#%', get_pagenum_link(999999999)),
                        'format' => 'page/%#%',
                        'current' => $paged,
                        'total' => $query->max_num_pages,
                        'prev_text' => '&laquo;',
                        'next_text' => '&raquo;',
                        'type' => 'plain',
                        'end_size' => 1,
                        'mid_size' => 2,
                        'before_page_number' => '<span class="px-3 py-2  text-gray-700 rounded hover:bg-selection hover:text-white">',
                        'after_page_number' => '</span>'
                    );

                    // Personalizar los enlaces de paginación
                    $links = paginate_links($args);
                    $links = str_replace('current', 'px-3 py-2 bg-rojo text-white rounded', $links);
                    $links = str_replace('page-numbers', 'px-3 py-2 bg-gray-200  rounded hover:bg-selection hover:text-white', $links);

                    echo $links;
                }
                ?>
            </div>
        <?php endif; ?>
    </section>
    <script>
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', async function(e) {
                const href = this.getAttribute('href');
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>
<?php
    wp_reset_postdata();
}
?>