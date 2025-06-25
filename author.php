<?php get_header(); ?>

<?php
// Get the author object for the current archive page
$author = get_queried_object();
get_template_part('template-parts/author-box', null, ['coauthor' => $author]);
?>

<div class="container mx-auto px-4 py-8">
    <?php
    global $wp_query;
    display_articles_grid(['query' => $wp_query]);
    ?>
</div>

<?php get_footer(); ?>
