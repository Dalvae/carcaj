<?php
/**
 * Search Filters Functions
 * 
 * Functions for crossfiltering in advanced search.
 * Includes caching for performance.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get filtered post IDs based on current search parameters.
 *
 * @param string $search  Search term.
 * @param int    $cat     Category ID.
 * @param string $year    Year taxonomy slug.
 * @param int    $author  Author ID.
 * @return array Array of post IDs.
 */
function carcaj_get_filtered_post_ids($search = '', $cat = 0, $year = '', $author = 0) {
    $cache_key = 'carcaj_filter_' . md5(serialize(compact('search', 'cat', 'year', 'author')));
    $cached = wp_cache_get($cache_key, 'carcaj_search');
    
    if (false !== $cached) {
        return $cached;
    }

    $query_args = [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 500, // Limit for performance
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ];

    if (!empty($search)) {
        $query_args['s'] = sanitize_text_field($search);
    }

    if ($cat > 0) {
        $query_args['cat'] = absint($cat);
    }

    if (!empty($year)) {
        $query_args['tax_query'][] = [
            'taxonomy' => 'anho',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($year),
        ];
    }

    if ($author > 0) {
        $query_args['author'] = absint($author);
    }

    $query = new WP_Query($query_args);
    $result = $query->posts;
    
    wp_cache_set($cache_key, $result, 'carcaj_search', HOUR_IN_SECONDS);
    
    return $result;
}

/**
 * Get available categories from a set of post IDs.
 *
 * @param array $post_ids Array of post IDs.
 * @return array Array of category objects.
 */
function carcaj_get_available_categories($post_ids = []) {
    if (empty($post_ids)) {
        return get_categories(['hide_empty' => true, 'orderby' => 'name']);
    }

    $post_ids = array_map('absint', $post_ids);
    $cache_key = 'carcaj_cats_' . md5(serialize($post_ids));
    $cached = wp_cache_get($cache_key, 'carcaj_search');
    
    if (false !== $cached) {
        return $cached;
    }

    $term_ids = get_terms([
        'taxonomy'   => 'category',
        'object_ids' => $post_ids,
        'fields'     => 'ids',
        'hide_empty' => true,
    ]);

    if (empty($term_ids) || is_wp_error($term_ids)) {
        return [];
    }

    $result = get_categories([
        'include'    => $term_ids,
        'hide_empty' => false,
        'orderby'    => 'name',
    ]);
    
    wp_cache_set($cache_key, $result, 'carcaj_search', HOUR_IN_SECONDS);
    
    return $result;
}

/**
 * Get available years from a set of post IDs.
 *
 * @param array $post_ids Array of post IDs.
 * @return array Array of term objects.
 */
function carcaj_get_available_years($post_ids = []) {
    if (empty($post_ids)) {
        return get_terms([
            'taxonomy'   => 'anho', 
            'hide_empty' => true,
            'orderby'    => 'name',
            'order'      => 'DESC',
        ]);
    }

    $post_ids = array_map('absint', $post_ids);
    $cache_key = 'carcaj_years_' . md5(serialize($post_ids));
    $cached = wp_cache_get($cache_key, 'carcaj_search');
    
    if (false !== $cached) {
        return $cached;
    }

    $term_ids = get_terms([
        'taxonomy'   => 'anho',
        'object_ids' => $post_ids,
        'fields'     => 'ids',
        'hide_empty' => true,
    ]);

    if (empty($term_ids) || is_wp_error($term_ids)) {
        return [];
    }

    $result = get_terms([
        'taxonomy'   => 'anho',
        'include'    => $term_ids,
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'DESC',
    ]);
    
    wp_cache_set($cache_key, $result, 'carcaj_search', HOUR_IN_SECONDS);
    
    return is_wp_error($result) ? [] : $result;
}

/**
 * Get available authors from a set of post IDs.
 *
 * @param array $post_ids Array of post IDs.
 * @return array Array of user objects.
 */
function carcaj_get_available_authors($post_ids = []) {
    if (empty($post_ids)) {
        return get_users([
            'capability' => ['edit_posts'], 
            'orderby'    => 'display_name',
        ]);
    }

    $post_ids = array_map('absint', $post_ids);
    $cache_key = 'carcaj_authors_' . md5(serialize($post_ids));
    $cached = wp_cache_get($cache_key, 'carcaj_search');
    
    if (false !== $cached) {
        return $cached;
    }

    global $wpdb;
    
    // Use prepared statement for author IDs query
    $placeholders = implode(',', array_fill(0, count($post_ids), '%d'));
    $author_ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT DISTINCT post_author FROM {$wpdb->posts} WHERE ID IN ({$placeholders})",
            ...$post_ids
        )
    );

    if (empty($author_ids)) {
        return [];
    }

    $result = get_users([
        'include' => array_map('absint', $author_ids),
        'orderby' => 'display_name',
    ]);
    
    wp_cache_set($cache_key, $result, 'carcaj_search', HOUR_IN_SECONDS);
    
    return $result;
}

/**
 * Clear search filter caches when posts are updated.
 */
function carcaj_clear_search_cache($post_id) {
    if (wp_is_post_revision($post_id)) {
        return;
    }
    wp_cache_delete_group('carcaj_search');
}
add_action('save_post', 'carcaj_clear_search_cache');
add_action('delete_post', 'carcaj_clear_search_cache');
