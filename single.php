<?php get_header(); ?>

<section>
    <?php if (have_posts()): while (have_posts()) : the_post(); ?>
            <article class="container mx-auto px-4">
                <?php get_template_part('template-parts/breadcrumbs'); ?>
                <?php if (has_post_thumbnail()): ?>
                    <div class=" mb-8">
                        <div class="w-full">
                            <?php 
                            // Build the featured image manually to bypass WordPress lazy loading
                            $thumb_id = get_post_thumbnail_id();
                            $thumb_src = wp_get_attachment_image_src($thumb_id, 'large');
                            $thumb_srcset = wp_get_attachment_image_srcset($thumb_id, 'large');
                            // Custom sizes: full width on mobile, max 768px on desktop
                            $thumb_sizes = '(max-width: 768px) 100vw, 768px';
                            $thumb_alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
                            
                            // Fallback: use post title if no alt text defined (SEO improvement)
                            if (empty($thumb_alt)) {
                                $thumb_alt = get_the_title();
                            }
                            
                            if ($thumb_src): ?>
                            <img
                                src="<?php echo esc_url($thumb_src[0]); ?>"
                                width="<?php echo esc_attr($thumb_src[1]); ?>"
                                height="<?php echo esc_attr($thumb_src[2]); ?>"
                                srcset="<?php echo esc_attr($thumb_srcset); ?>"
                                sizes="<?php echo esc_attr($thumb_sizes); ?>"
                                alt="<?php echo esc_attr($thumb_alt); ?>"
                                style="aspect-ratio: <?php echo esc_attr($thumb_src[1]); ?> / <?php echo esc_attr($thumb_src[2]); ?>"
                                class="w-full h-auto lg:aspect-[2/3] lg:max-h-[650px] object-cover"
                                loading="eager"
                                fetchpriority="high"
                            />
                            <?php endif; ?>
                        </div>
                        <?php if (get_field('creditos_imagen')): ?>
                            <p class="text-gray-500 text-right text-base pt-2"><?php the_field('creditos_imagen'); ?></p>
                        <?php endif ?>
                    </div>
                <?php endif ?>

                <div class="lg:w-4/5 mx-auto mt-8">
                    <div class="flex flex-wrap justify-between items-center text-gris text-xl">
                        <div class="no-underline">
                            <?php the_time('d'); ?> de <?php the_time('F Y'); ?>
                        </div>
                        <div class="space-x-1 underline">
                            <?php the_category(','); ?>
                        </div>
                    </div>

                    <h1 class="text-rojo  text-4xl lg:text-5xl font-bold leading-tight text-center my-8">
                        <?php the_title(); ?>
                    </h1>

                    <div class="text-gris tracking-tighter text-2xl font-semibold text-center">
                        <span class="italic">por</span>
                        <?php
                        if (function_exists('coauthors_posts_links')) {
                            get_coauthors_with_roles();
                        } else {
                            the_author_posts_link();
                        }
                        ?>
                    </div>
                    <div class="my-12 text-2xl font-al leading-relaxed prose max-w-none content-full">
                        <?php the_content(); ?>
                    </div>

                

                    <?php get_template_part('template-parts/social-share'); ?>

            </article>
            <?php
            if (function_exists('get_coauthors')) {
                $coauthors = get_coauthors();
            } else {
                $coauthors = array(get_userdata(get_the_author_meta('ID')));
            }

            foreach ($coauthors as $coauthor):
                get_template_part('template-parts/author-box', null, ['coauthor' => $coauthor]);
            endforeach;
            ?>

            <?php get_template_part('template-parts/include-related-posts'); ?>
            <div class="bg-swhite">
                <?php comments_template(); ?>
            </div>

        <?php endwhile; ?>
    <?php else: ?>
        <article class="container mx-auto px-4">
            <h1>Nada que mostrar</h1>
        </article>
    <?php endif; ?>
</section>

<?php get_footer(); ?>
