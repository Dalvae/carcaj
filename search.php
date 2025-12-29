<?php
/**
 * Search Results Template
 * 
 * Displays search results with advanced filtering capabilities.
 */

get_header();

// Get current filter values
$search_query = get_search_query();
$author_id    = get_query_var('author');
$cat_id       = get_query_var('cat');
$year_slug    = get_query_var('anho');

// Build results title
$title_parts = [];

if ($search_query) {
    $title_parts[] = sprintf('"%s"', esc_html($search_query));
}

if ($author_id) {
    $author_name = get_the_author_meta('display_name', $author_id);
    if ($author_name) {
        $title_parts[] = sprintf(__('autor: %s', 'carcaj'), esc_html($author_name));
    }
}

if ($cat_id) {
    $category = get_category($cat_id);
    if ($category && !is_wp_error($category)) {
        $title_parts[] = sprintf(__('categoría: %s', 'carcaj'), esc_html($category->name));
    }
}

if ($year_slug) {
    $term = get_term_by('slug', $year_slug, 'anho');
    if ($term && !is_wp_error($term)) {
        $title_parts[] = sprintf(__('año: %s', 'carcaj'), esc_html($term->name));
    }
}

$results_title = !empty($title_parts) 
    ? sprintf(__('Resultados para %s', 'carcaj'), implode(' · ', $title_parts))
    : __('Resultados de búsqueda', 'carcaj');
?>

<div class="container mx-auto my-8 px-4">
    <!-- Advanced Search Form -->
    <div class="mb-12">
        <?php 
        get_template_part('template-parts/advanced-search-form', null, [
            'show_title' => true,
            'title'      => __('Archivo', 'carcaj'),
            'show_icon'  => true,
            'layout'     => 'vertical',
        ]); 
        ?>
    </div>

    <!-- Results Section -->
    <div class="mb-8">
        <h2 class="text-2xl lg:text-3xl font-semibold text-rojo mb-6">
            <?php echo esc_html($results_title); ?>
        </h2>
        
        <?php if ($wp_query->found_posts > 0) : ?>
            <p class="text-gray-600 mb-6">
                <?php 
                printf(
                    _n('%d resultado encontrado', '%d resultados encontrados', $wp_query->found_posts, 'carcaj'),
                    $wp_query->found_posts
                );
                ?>
            </p>
        <?php endif; ?>
    </div>

    <?php if (have_posts()) : ?>
        <?php
        display_articles_grid([
            'query'            => $wp_query,
            'show_pagination'  => true,
            'show_excerpt'     => true,
            'show_author'      => true,
            'use_query_string' => false,
        ]);
        ?>
    <?php else : ?>
        <div class="text-center py-12">
            <p class="text-xl text-gray-600 mb-4">
                <?php esc_html_e('No se encontraron resultados para tu búsqueda.', 'carcaj'); ?>
            </p>
            <p class="text-gray-500">
                <?php esc_html_e('Intenta con otros términos o utiliza los filtros para refinar tu búsqueda.', 'carcaj'); ?>
            </p>
        </div>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
