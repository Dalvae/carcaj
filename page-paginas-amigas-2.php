<?php
/* Template Name: Paginas Amigas */
get_header();
?>
<main class="container mx-auto px-4">
    <section>
        <h1 class="text-5xl text-rojo font-semibold text-center my-8"><?php the_title(); ?></h1>
        <?php
        $image_url = wp_get_attachment_image_url(22955, 'full');
        ?>
        <img src="<?php echo $image_url; ?>" alt="" class="h-auto w-full">
        <?php if (have_posts()): while (have_posts()) : the_post(); ?>
                <article class="max-w-6xl prose mx-auto">
                    <div class="max-w-none">
                        <?php the_content(); ?>
                    </div>
                    <style>
                        .wp-block-gallery.has-nested-images.is-layout-flex {
                            display: grid !important;
                            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                            gap: 2rem !important;
                            margin: 0 !important;
                        }

                        .wp-block-gallery.has-nested-images figure.wp-block-image {
                            width: 100% !important;
                            display: flex !important;
                            justify-content: center !important;
                            align-items: center !important;
                            margin: 0 !important;
                            grid-column: auto !important;
                        }

                        .wp-block-image img {
                            width: 100% !important;
                            height: auto !important;
                            max-width: 300px !important;
                        }

                        .sites-list {
                            font-size: 1.875rem !important;
                            line-height: 2.25rem !important;
                            font-weight: 600 !important;
                            margin-top: 2rem !important;
                            margin-bottom: 2rem !important;
                        }
                    </style>
                </article>
            <?php endwhile; ?>
        <?php else: endif; ?>
    </section>
</main>
<?php get_footer(); ?>