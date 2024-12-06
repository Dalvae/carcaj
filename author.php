<?php get_header(); ?>

<div class="bg-swhite">
    <div class="container lg:w-4/5 mx-auto px-4 py-10 lg:py-10 text-center">
        <h2 class="text-rojo font-semibold font-als text-3xl mb-5">
            <?php the_author_posts_link(); ?>
        </h2>
        <div class="text-gris text-2xl leading-tight text-justify">
            <?php echo get_the_author_meta('description'); ?>
        </div>
    </div>
</div>
<div class="container mx-auto px-4 py-8">

    <?php get_template_part('template-parts/articles'); ?>
</div>

<?php get_footer(); ?>