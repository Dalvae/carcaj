<?php

function add_opengraph_tags()
{
    $site_name = esc_attr(get_bloginfo('name'));
    $site_description = esc_attr(get_bloginfo('description'));
    
    // Fallback site description
    if (empty($site_description)) {
        $site_description = 'Revista de literatura, arte y pensamiento crítico';
    }
    
    $default_image = get_template_directory_uri() . '/img/thumb.png';
    
    if (is_front_page() || is_home()) {
        // Homepage
        echo '<meta name="description" content="' . $site_description . '" />';
        
        echo '<meta property="og:type" content="website" />';
        echo '<meta property="og:title" content="' . $site_name . '" />';
        echo '<meta property="og:description" content="' . $site_description . '" />';
        echo '<meta property="og:url" content="' . esc_url(home_url('/')) . '" />';
        echo '<meta property="og:image" content="' . $default_image . '" />';
        echo '<meta property="og:site_name" content="' . $site_name . '" />';
        
        echo '<meta name="twitter:card" content="summary_large_image" />';
        echo '<meta name="twitter:title" content="' . $site_name . '" />';
        echo '<meta name="twitter:description" content="' . $site_description . '" />';
        echo '<meta name="twitter:image" content="' . $default_image . '" />';
        
    } elseif (is_single() || is_page()) {
        global $post;

        $title = esc_attr(get_the_title());
        $description = esc_attr(wp_strip_all_tags(get_the_excerpt()));
        
        // Fallback if no excerpt
        if (empty($description)) {
            $description = $site_description;
        }
        
        $url = esc_url(get_permalink());
        $image = $default_image;

        if (has_post_thumbnail($post->ID)) {
            $image = esc_url(get_the_post_thumbnail_url($post->ID, 'large'));
        }

        echo '<meta name="description" content="' . $description . '" />';

        echo '<meta property="og:type" content="article" />';
        echo '<meta property="og:title" content="' . $title . '" />';
        echo '<meta property="og:description" content="' . $description . '" />';
        echo '<meta property="og:url" content="' . $url . '" />';
        echo '<meta property="og:image" content="' . $image . '" />';
        echo '<meta property="og:site_name" content="' . $site_name . '" />';

        echo '<meta name="twitter:card" content="summary_large_image" />';
        echo '<meta name="twitter:title" content="' . $title . '" />';
        echo '<meta name="twitter:description" content="' . $description . '" />';
        echo '<meta name="twitter:image" content="' . $image . '" />';
        
    } elseif (is_category() || is_tag() || is_tax()) {
        // Archives
        $term = get_queried_object();
        $title = esc_attr($term->name);
        $description = !empty($term->description) 
            ? esc_attr(wp_strip_all_tags($term->description))
            : $site_description;
        
        echo '<meta name="description" content="' . $description . '" />';
        
        echo '<meta property="og:type" content="website" />';
        echo '<meta property="og:title" content="' . $title . ' - ' . $site_name . '" />';
        echo '<meta property="og:description" content="' . $description . '" />';
        echo '<meta property="og:url" content="' . esc_url(get_term_link($term)) . '" />';
        echo '<meta property="og:image" content="' . $default_image . '" />';
        echo '<meta property="og:site_name" content="' . $site_name . '" />';
        
    } elseif (is_author()) {
        // Author archives
        $author = get_queried_object();
        $title = esc_attr($author->display_name);
        $description = !empty($author->description) 
            ? esc_attr(wp_strip_all_tags($author->description))
            : 'Artículos de ' . $title . ' en ' . $site_name;
        
        echo '<meta name="description" content="' . $description . '" />';
        
        echo '<meta property="og:type" content="profile" />';
        echo '<meta property="og:title" content="' . $title . ' - ' . $site_name . '" />';
        echo '<meta property="og:description" content="' . $description . '" />';
        echo '<meta property="og:url" content="' . esc_url(get_author_posts_url($author->ID)) . '" />';
        echo '<meta property="og:site_name" content="' . $site_name . '" />';
        
    } else {
        // Fallback for other pages
        echo '<meta name="description" content="' . $site_description . '" />';
    }
}
add_action('wp_head', 'add_opengraph_tags');
