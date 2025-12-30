<?php

function add_opengraph_tags()
{
    if (is_single() || is_page()) {
        global $post;

        $title = esc_attr(get_the_title());
        $description = esc_attr(wp_strip_all_tags(get_the_excerpt()));
        $url = esc_url(get_permalink());
        $site_name = esc_attr(get_bloginfo('name'));
        $image = get_template_directory_uri() . '/img/thumb.png'; // Default image

        if (has_post_thumbnail($post->ID)) {
            $image = esc_url(get_the_post_thumbnail_url($post->ID, 'large'));
        }

        // Meta description for SEO
        echo '<meta name="description" content="' . $description . '" />';

        // OpenGraph
        echo '<meta property="og:type" content="article" />';
        echo '<meta property="og:title" content="' . $title . '" />';
        echo '<meta property="og:description" content="' . $description . '" />';
        echo '<meta property="og:url" content="' . $url . '" />';
        echo '<meta property="og:image" content="' . $image . '" />';
        echo '<meta property="og:site_name" content="' . $site_name . '" />';

        // Twitter Cards
        echo '<meta name="twitter:card" content="summary_large_image" />';
        echo '<meta name="twitter:title" content="' . $title . '" />';
        echo '<meta name="twitter:description" content="' . $description . '" />';
        echo '<meta name="twitter:image" content="' . $image . '" />';
    } else {
        // Homepage and archives
        $site_name = esc_attr(get_bloginfo('name'));
        $site_description = esc_attr(get_bloginfo('description'));
        
        if (empty($site_description)) {
            $site_description = 'Revista de literatura, arte y pensamiento crítico';
        }
        
        echo '<meta name="description" content="' . $site_description . '" />';
        
        // OpenGraph for homepage
        echo '<meta property="og:type" content="website" />';
        echo '<meta property="og:title" content="' . $site_name . '" />';
        echo '<meta property="og:description" content="' . $site_description . '" />';
        echo '<meta property="og:url" content="' . esc_url(home_url('/')) . '" />';
        echo '<meta property="og:image" content="' . get_template_directory_uri() . '/img/thumb.png" />';
        echo '<meta property="og:site_name" content="' . $site_name . '" />';
    }
}
add_action('wp_head', 'add_opengraph_tags');
