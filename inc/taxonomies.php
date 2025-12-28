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

function carcaj_handle_search_and_archive_filters($query)
{
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    // We are only interested in search and archive pages
    if (!$query->is_search() && !$query->is_archive()) {
        return;
    }

    $tax_query_updates = [];

    // Handle 'anho' filter from search or archive pages
    $year_slug = get_query_var('anho');
    if (!empty($year_slug) && $year_slug != '-1') {
        $tax_query_updates[] = [
            'taxonomy' => 'anho',
            'field'    => 'slug',
            'terms'    => $year_slug,
        ];
    }

    // Handle search-specific filters
    if ($query->is_search()) {
        // Handle category filter
        $cat_id = get_query_var('cat');
        if (!empty($cat_id) && $cat_id > 0) {
            $tax_query_updates[] = [
                'taxonomy' => 'category',
                'field'    => 'term_id',
                'terms'    => $cat_id,
            ];
        }

        // Handle author filter
        $author_id = get_query_var('author');
        if (!empty($author_id) && $author_id > 0) {
            $query->set('author', $author_id);
        } elseif (isset($author_id) && $author_id < 1) {
            unset($query->query_vars['author']);
        }

        // If search term is empty but we have filters, get all posts matching filters
        $search_term = get_query_var('s');
        if (empty($search_term)) {
            $has_filters = !empty($cat_id) && $cat_id > 0 
                || !empty($author_id) && $author_id > 0 
                || !empty($year_slug) && $year_slug != '-1';
            
            if ($has_filters) {
                $query->set('post_type', 'post');
            }
        }
    }

    if (!empty($tax_query_updates)) {
        $existing_tax_query = $query->get('tax_query') ?: [];
        if (!is_array($existing_tax_query)) {
            $existing_tax_query = [];
        }
        
        $final_tax_query = array_merge($existing_tax_query, $tax_query_updates);

        if (count($final_tax_query) > 1 && !isset($final_tax_query['relation'])) {
            $final_tax_query['relation'] = 'AND';
        }
        $query->set('tax_query', $final_tax_query);
    }
}
add_action('pre_get_posts', 'carcaj_handle_search_and_archive_filters');
