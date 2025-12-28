<?php get_header(); ?>

<div class="container mx-auto mb-16">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>

            <h1 class="text-center text-4xl lg:text-5xl font-bold mt-8 text-rojo"><?php the_title(); ?></h1>

            <div class="mt-8 text-xl lg:text-2xl leading-relaxed prose max-w-none text-justify">
                <?php the_content(); ?>
            </div>

        <?php endwhile; ?>
    <?php endif; ?>
</div>

<?php get_footer();
