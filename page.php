<?php get_header(); ?>

<div class=" mx-auto px-4 prose container mb-16">

    <?php if (have_posts()) : ?>

        <?php
        while (have_posts()) :
            the_post();
        ?>

            <div class="flex flex-col container">
                <h1 class="mx-auto container text-center text-5xl font-bold my-8"><?php the_title(); ?></h1>

                <div class="mt-12 text-2xl font-al leading-relaxed prose max-w-none content-full text-justify ">
                    <?php the_content(); ?>
                </div>
            </div>

        <?php endwhile; ?>

    <?php endif; ?>

</div>

<?php
get_footer();
