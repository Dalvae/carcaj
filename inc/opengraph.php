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
    }
}
add_action('wp_head', 'add_opengraph_tags');
