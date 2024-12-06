<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

// Main switch to get frontend assets from a Vite dev server OR from production built folder
// If you specify a DEVELOPMENT_IP constant, the frontend assets will be loaded from the Vite dev server only for the IP specified
// it is recommended to move it into wp-config.php

if (!defined('IS_VITE_DEVELOPMENT')) {
    define('IS_VITE_DEVELOPMENT', true);
}
//const DEVELOPMENT_IP = '123.123.123.123';

require 'vendor/autoload.php';

require 'inc/vite.php';

require 'inc/acf.php';
require 'inc/blog.php';
require 'inc/cleanup.php';
// require 'inc/comments.php';
require 'inc/general.php';
require 'inc/gutenberg.php';
require 'inc/login.php';
require 'inc/nav_walker.php';
require 'inc/post-types.php';
require 'inc/shortcodes.php';
require 'inc/svg.php';
require 'inc/updates.php';
require 'inc/useful.php';
require 'inc/widgets.php';
/* Comments Support
----------------------------------------------------------------------------------------------------*/
class ThemeComments
{
    public function __construct()
    {
        $this->init_hooks();
    }

    private function init_hooks()
    {
        add_action('admin_init', [$this, 'enable_comments_support']);
        add_filter('comments_open', [$this, 'enable_front_end_comments'], 20, 2);
        add_filter('comments_template', [$this, 'custom_comments_template']);
    }

    public function enable_comments_support()
    {
        foreach (get_post_types() as $post_type) {
            add_post_type_support($post_type, 'comments');
            add_post_type_support($post_type, 'trackbacks');
        }
    }

    public function enable_front_end_comments($open, $post_id)
    {
        return true;
    }

    public function custom_comments_template($template)
    {
        return get_template_directory() . '/comments-template.php';
    }
}

new ThemeComments();

require_once get_template_directory() . '/template-parts/articles-grid.php';
// Register Custom Taxonomy
function especiales()
{

    $labels = array(
        'name'                       => _x('Especiales', 'Taxonomy General Name', 'especiales'),
        'singular_name'              => _x('Especial', 'Taxonomy Singular Name', 'especiales'),
        'menu_name'                  => __('Especiales', 'especiales'),
        'all_items'                  => __('All Items', 'especiales'),
        'parent_item'                => __('Parent Item', 'especiales'),
        'parent_item_colon'          => __('Parent Item:', 'especiales'),
        'new_item_name'              => __('Nuevo especial', 'especiales'),
        'add_new_item'               => __('Agregar', 'especiales'),
        'edit_item'                  => __('Edit Item', 'especiales'),
        'update_item'                => __('Update Item', 'especiales'),
        'view_item'                  => __('View Item', 'especiales'),
        'separate_items_with_commas' => __('Separate items with commas', 'especiales'),
        'add_or_remove_items'        => __('Add or remove items', 'especiales'),
        'choose_from_most_used'      => __('Choose from the most used', 'especiales'),
        'popular_items'              => __('Popular Items', 'especiales'),
        'search_items'               => __('Search Items', 'especiales'),
        'not_found'                  => __('Not Found', 'especiales'),
        'no_terms'                   => __('No items', 'especiales'),
        'items_list'                 => __('Items list', 'especiales'),
        'items_list_navigation'      => __('Items list navigation', 'especiales'),
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
    register_taxonomy('especiales', array('post'), $args);
}
add_action('init', 'especiales', 0);

function year_post()
{
    $labels = array(
        'name'                       => _x('Año', 'Taxonomy General Name', 'year_post'),
        'singular_name'              => _x('Año', 'Taxonomy Singular Name', 'year_post'),
        'menu_name'                  => __('Año', 'year_post'),
        'all_items'                  => __('Todos los Años', 'year_post'),
        'parent_item'                => __('Parent Item', 'year_post'),
        'parent_item_colon'          => __('Parent Item:', 'year_post'),
        'new_item_name'              => __('Nuevo Año', 'year_post'),
        'add_new_item'               => __('Agregar año', 'year_post'),
        'edit_item'                  => __('Edit Item', 'year_post'),
        'update_item'                => __('Update Item', 'year_post'),
        'view_item'                  => __('View Item', 'year_post'),
        'separate_items_with_commas' => __('Separate items with commas', 'year_post'),
        'add_or_remove_items'        => __('Add or remove items', 'year_post'),
        'choose_from_most_used'      => __('Choose from the most used', 'year_post'),
        'popular_items'              => __('Popular Items', 'year_post'),
        'search_items'               => __('Search Items', 'year_post'),
        'not_found'                  => __('Not Found', 'year_post'),
        'no_terms'                   => __('No items', 'year_post'),
        'items_list'                 => __('Items list', 'year_post'),
        'items_list_navigation'      => __('Items list navigation', 'year_post'),
    );
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
        'show_in_rest'               => true
    );
    register_taxonomy('anho', array('post'), $args);
}
add_action('init', 'year_post', 0);

// Add this new function to handle the year archive query
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
function html5blank_nav()
{
    wp_nav_menu(array(
        'theme_location' => 'header-menu',
        'menu' => 'Menú Superior',
        'container' => 'nav',
        'container_class' => 'hidden lg:block',
        'menu_class' => 'flex space-x-8',
        'items_wrap' => '<ul class="flex flex-col lg:flex-row lg:items-center lg:space-x-8">%3$s</ul>',
        'depth' => 2,
    ));
}

add_action('init', 'year_post', 0);
add_action('after_setup_theme', function () {

    add_theme_support('title-tag');

    add_theme_support(
        'html5',
        array(
            'search-form',
            'gallery',
            'caption',
        )
    );

    /* `Add Support to change the logo */
    add_theme_support('custom-logo');

    add_theme_support('wp-block-styles');

    /* `Add Support for Menus */
    add_theme_support('menus');
    register_nav_menu('main-menu', 'Navigation');
    register_nav_menu('mobile-menu', 'Navigation (Mobile)');

    /* `Add Support for Post thumbnail */
    //    add_theme_support('post-thumbnails');
    //    set_post_thumbnail_size( 800, 800 );

    /* `Custom image sizes */

    //    add_image_size( 'category-thumb', 300 ); // 300 pixels wide (and unlimited height)
    //    add_image_size( 'custom-size', 220, 180 ); // 220 pixels wide by 180 pixels tall, soft proportional crop mode
    //    add_image_size( 'homepage-thumb', 220, 180, true ); // (cropped)
    function enqueue_theme_assets()
    {
        wp_enqueue_style('theme-fonts', get_template_directory_uri() . '/assets/fonts/fonts.css');
    }
    add_action('wp_enqueue_scripts', 'enqueue_theme_assets');
});
