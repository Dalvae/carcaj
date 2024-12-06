<?php get_header(); ?>

<div class=" mx-auto my-8 px-4 prose container">

    <?php if (have_posts()) : ?>

        <?php
        while (have_posts()) :
            the_post();
        ?>

            <div class="flex flex-col">
                <h1 class="mx-auto container text-center"><?php the_title(); ?></h1>

                <div class="container">
                    <?php the_content(); ?>

                </div>
            </div>

        <?php endwhile; ?>

    <?php endif; ?>

</div>

<?php
get_footer();
