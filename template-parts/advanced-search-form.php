<?php
/**
 * Advanced Search Form Template with Crossfiltering
 * 
 * Reusable search form with filters that update based on current selection.
 * Uses functions from inc/search-filters.php for secure, cached queries.
 * 
 * @param array $args {
 *     Optional. Arguments to customize the form.
 *     @type bool   $show_title    Whether to show the title. Default true.
 *     @type string $title         Form title. Default 'Archivo'.
 *     @type bool   $show_icon     Whether to show the search icon. Default true.
 *     @type string $layout        Form layout: 'vertical' or 'horizontal'. Default 'vertical'.
 * }
 */

$defaults = [
    'show_title' => true,
    'title'      => 'Archivo',
    'show_icon'  => true,
    'layout'     => 'vertical',
];

$args = wp_parse_args($args ?? [], $defaults);

// Get current filter values
$current_search = get_search_query();
$current_cat    = absint(get_query_var('cat'));
$current_year   = sanitize_text_field(get_query_var('anho'));
$current_author = absint(get_query_var('author'));

// Check if we have any active filters
$has_filters = !empty($current_search) || $current_cat > 0 || !empty($current_year) || $current_author > 0;

// Get filtered post IDs if we have active filters
$filtered_post_ids = $has_filters 
    ? carcaj_get_filtered_post_ids($current_search, $current_cat, $current_year, $current_author) 
    : [];

// For each dropdown, get available options excluding that filter from the query
// This allows selecting a new value for that filter

// Categories: get available based on other filters (excluding category)
$cat_post_ids = $has_filters 
    ? carcaj_get_filtered_post_ids($current_search, 0, $current_year, $current_author)
    : [];
$available_categories = carcaj_get_available_categories($cat_post_ids);

// Years: get available based on other filters (excluding year)
$year_post_ids = $has_filters 
    ? carcaj_get_filtered_post_ids($current_search, $current_cat, '', $current_author)
    : [];
$available_years = carcaj_get_available_years($year_post_ids);

// Authors: get available based on other filters (excluding author)
$author_post_ids = $has_filters 
    ? carcaj_get_filtered_post_ids($current_search, $current_cat, $current_year, 0)
    : [];
$available_authors = carcaj_get_available_authors($author_post_ids);

// Layout classes
$form_class = $args['layout'] === 'horizontal' 
    ? 'flex flex-wrap gap-4 items-end' 
    : 'flex flex-col gap-4';

$field_class = $args['layout'] === 'horizontal' 
    ? 'flex-1 min-w-[200px]' 
    : '';

$input_class = 'w-full px-4 py-2 bg-white border border-gray-300 focus:ring-2 focus:ring-rojo focus:border-transparent focus:outline-none';
?>

<div class="advanced-search-wrapper max-w-3xl mx-auto">
    <?php if ($args['show_title'] || $args['show_icon']) : ?>
        <div class="flex items-center justify-center flex-col mb-6">
            <?php if ($args['show_title']) : ?>
                <h1 class="text-4xl lg:text-5xl font-semibold my-4 text-rojo"><?php echo esc_html($args['title']); ?></h1>
            <?php endif; ?>
            <?php if ($args['show_icon']) : ?>
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/search.png" 
                     alt="Buscar" 
                     class="h-16 w-auto">
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form class="<?php echo esc_attr($form_class); ?> p-4" 
          method="get" 
          action="<?php echo esc_url(home_url('/')); ?>" 
          role="search" 
          id="advanced-search-form">
        
        <!-- Search Term -->
        <div class="<?php echo esc_attr($field_class); ?>">
            <label for="search-term" class="sr-only">Buscar por palabra</label>
            <input type="search"
                   id="search-term"
                   name="s"
                   value="<?php echo esc_attr($current_search); ?>"
                   placeholder="Buscar por palabra..."
                   class="<?php echo esc_attr($input_class); ?>">
        </div>

        <!-- Category Filter -->
        <div class="<?php echo esc_attr($field_class); ?>">
            <label for="cat-select" class="sr-only">Categoria</label>
            <select name="cat" id="cat-select" class="<?php echo esc_attr($input_class); ?>">
                <option value="">Todas las categorias</option>
                <?php foreach ($available_categories as $category) : ?>
                    <option value="<?php echo esc_attr($category->term_id); ?>" <?php selected($current_cat, $category->term_id); ?>>
                        <?php echo esc_html($category->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Year Filter -->
        <div class="<?php echo esc_attr($field_class); ?>">
            <label for="anho-select" class="sr-only">Ano</label>
            <select name="anho" id="anho-select" class="<?php echo esc_attr($input_class); ?>">
                <option value="">Todos los anos</option>
                <?php foreach ($available_years as $year) : ?>
                    <option value="<?php echo esc_attr($year->slug); ?>" <?php selected($current_year, $year->slug); ?>>
                        <?php echo esc_html($year->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Author Filter -->
        <div class="<?php echo esc_attr($field_class); ?>">
            <label for="author-select" class="sr-only">Autor</label>
            <select name="author" id="author-select" class="<?php echo esc_attr($input_class); ?>">
                <option value="">Todos los autores</option>
                <?php foreach ($available_authors as $author) : ?>
                    <option value="<?php echo esc_attr($author->ID); ?>" <?php selected($current_author, $author->ID); ?>>
                        <?php echo esc_html($author->display_name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Submit Button -->
        <div class="<?php echo $args['layout'] === 'horizontal' ? '' : 'mt-2'; ?>">
            <button type="submit" 
                    class="w-full bg-rojo hover:bg-darkgold text-white font-bold italic py-2 px-6 transition duration-300">
                Buscar
            </button>
        </div>

        <?php if ($has_filters) : ?>
        <!-- Clear Filters -->
        <div class="text-center">
            <a href="<?php echo esc_url(home_url('/busqueda/')); ?>" 
               class="text-sm text-gray-500 hover:text-rojo underline">
                Limpiar filtros
            </a>
        </div>
        <?php endif; ?>
    </form>
</div>

<script>
(function() {
    const form = document.getElementById('advanced-search-form');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const params = new URLSearchParams();
        const searchInput = form.querySelector('input[name="s"]');
        
        // Always include search param for WordPress
        params.set('s', searchInput ? searchInput.value : '');

        // Add filters only if they have valid values
        form.querySelectorAll('select').forEach(function(select) {
            if (select.value && select.value !== '-1' && select.value !== '0' && select.value !== '') {
                params.set(select.name, select.value);
            }
        });

        window.location.href = form.action + '?' + params.toString();
    });
})();
</script>
