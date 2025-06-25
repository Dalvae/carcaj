<?php
function register_custom_taxonomy($singular, $plural, $post_type, $slug)
{
    $labels = array(
        'name'                       => _x($plural, 'Taxonomy General Name', 'carcaj'),
        'singular_name'              => _x($singular, 'Taxonomy Singular Name', 'carcaj'),
        'menu_name'                  => __($plural, 'carcaj'),
        'all_items'                  => __('Todos los ' . $plural, 'carcaj'),
        'parent_item'                => __('Parent ' . $singular, 'carcaj'),
        'parent_item_colon'          => __('Parent ' . $singular . ':', 'carcaj'),
        'new_item_name'              => __('Nuevo ' . $singular, 'carcaj'),
        'add_new_item'               => __('Agregar ' . $singular, 'carcaj'),
        'edit_item'                  => __('Editar ' . $singular, 'carcaj'),
        'update_item'                => __('Actualizar ' . $singular, 'carcaj'),
        'view_item'                  => __('Ver ' . $singular, 'carcaj'),
        'separate_items_with_commas' => __('Separar con comas', 'carcaj'),
        'add_or_remove_items'        => __('Agregar o remover', 'carcaj'),
        'choose_from_most_used'      => __('Elegir de los más usados', 'carcaj'),
        'popular_items'              => __('Populares', 'carcaj'),
        'search_items'               => __('Buscar', 'carcaj'),
        'not_found'                  => __('No encontrado', 'carcaj'),
        'no_terms'                   => __('Sin ' . $plural, 'carcaj'),
        'items_list'                 => __('Lista de ' . $plural, 'carcaj'),
        'items_list_navigation'      => __('Navegación de lista', 'carcaj'),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true,
    );
    register_taxonomy($slug, $post_type, $args);
}

function register_all_taxonomies()
{
    register_custom_taxonomy('Especial', 'Especiales', 'post', 'especiales');
    register_custom_taxonomy('Año', 'Años', 'post', 'anho');
}
add_action('init', 'register_all_taxonomies', 0);

function handle_year_archive($query)
{
    if (!is_admin() && $query->is_main_query()) {
        $year = get_query_var('anho');
        if ($year) {
            $query->set('tax_query', array(
                array(
                    'taxonomy' => 'anho',
                    'field'    => 'slug',
                    'terms'    => $year
                )
            ));
        }
    }
}
add_action('pre_get_posts', 'handle_year_archive');
