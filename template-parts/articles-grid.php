<?php
function display_articles_grid($args = array())
{
    $defaults = array(
        'query' => null,
        'posts_per_page' => 12,
        'show_pagination' => true,
        'show_excerpt' => true,
        'show_author' => true,
        'use_query_string' => false,
        'card_template' => 'template-parts/card-article',
        'card_args' => [],
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

    <section id="articles-section" class="articles-section scroll-mt-[200px]">

        <div class="articles grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
                    get_template_part($settings['card_template'], null, array_merge([
                        'show_excerpt' => $settings['show_excerpt'],
                        'show_author'  => $settings['show_author'],
                    ], $settings['card_args']));
                endwhile;
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
                            '<a href="%s#articles-section" class="px-3 py-2 bg-rojo text-white rounded hover:bg-selection">«</a>',
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
                            '<a href="%s#articles-section" class="px-3 py-2 bg-rojo text-white rounded hover:bg-selection">»</a>',
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
                        'prev_text' => '«',
                        'next_text' => '»',
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
<?php
    wp_reset_postdata();
}
?>
