<?php
/**
 * Template Name: Página de Búsqueda
 * 
 * Landing page for the archive/search section.
 * Shows only the advanced search form without results.
 */

get_header();
?>

<main class="container mx-auto px-4 mb-16">
    <section class="py-8">
        <?php 
        get_template_part('template-parts/advanced-search-form', null, [
            'show_title' => true,
            'title'      => __('Archivo', 'carcaj'),
            'show_icon'  => true,
            'layout'     => 'vertical',
        ]); 
        ?>
    </section>
</main>

<?php get_footer(); ?>
