<?php get_header(); ?>
<div
    x-data="progressBar"
    x-init="init"
    x-cloak>
    <div
        x-ref="progressBar"
        class="fixed top-0 left-0 h-1.5 bg-rojo transform-gpu transition-all duration-500 ease-out z-50"
        :style="{ width: `${progress}%` }">
    </div>
</div>

<section>
    <?php if (have_posts()): while (have_posts()) : the_post(); ?>
            <article class="container mx-auto px-4">
                <?php get_template_part('template-parts/breadcrumbs'); ?>
                <?php if (has_post_thumbnail()): ?>
                    <div class=" mb-8">
                        <div class="w-full">
                            <?php the_post_thumbnail('large', ['class' => 'w-full h-auto lg:max-h-[650px] object-cover']); ?>
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
                        <?php
                        if (function_exists('coauthors_posts_links')) {
                            coauthors_posts_links();
                        } else {
                            the_author_posts_link();
                        }
                        ?>
                    </div>
                    <div class="mt-12 text-2xl font-al leading-relaxed prose max-w-none content-full text-justify">
                        <?php the_content(); ?>
                    </div>

                    <div class="prose prose-lg mx-auto my-8 [&_a]:hover:bg-rojo [&_a]:hover:text-white [&_a]:p-2">
                        <?php if (function_exists('pf_show_link')) {
                            echo pf_show_link();
                        } ?>
                    </div>

                    <div class="flex justify-center items-center gap-8 mt-12">
                        <img class="firma" src="<?php bloginfo('template_url'); ?>/img/diana.svg" alt="">

                        <div class="relative" x-data="{ isOpen: false }" @click.away="isOpen = false">
                            <button @click="isOpen = !isOpen" class="cursor-pointer">
                                <svg class="w-10 h-10 fill-red-600 hover:fill-gold-600 transition-colors duration-300" viewBox="0 0 24 24">
                                    <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z" />
                                </svg>
                            </button>

                            <div x-show="isOpen"
                                x-transition
                                class="absolute left-full top-0 transform translate-x-2 bg-white p-4 rounded-lg shadow-lg flex items-center gap-4">
                                <div class="relative after:content-[''] after:absolute after:top-1/2 after:right-full after:border-8 after:border-transparent after:border-r-white after:-translate-y-1/2">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo get_permalink(); ?>"
                                        target="_blank"
                                        class="inline-flex p-2">
                                        <svg class="w-6 h-6 fill-rose-400 hover:fill-gold-800 transition-colors duration-300">
                                            <use xlink:href="#icon-facebook"></use>
                                        </svg>
                                    </a>
                                    <a href="instagram://story?url=<?php echo urlencode(get_permalink()); ?>&media=<?php echo urlencode(get_the_post_thumbnail_url(get_the_ID(), 'large')); ?>"
                                        target="_blank"
                                        onclick="window.open('https://www.instagram.com', '_blank'); return false;"
                                        class="inline-flex p-2">
                                        <svg class="w-6 h-6 fill-rosa hover:fill-gold-800 transition-colors duration-300">
                                            <use xlink:href="#icon-instagram"></use>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
            <?php
            if (function_exists('get_coauthors')) {
                $coauthors = get_coauthors();
            } else {
                $coauthors = array(get_userdata(get_the_author_meta('ID')));
            }

            foreach ($coauthors as $coauthor): ?>
                <div class="bg-swhite my-7">
                    <div class="container lg:w-4/5 mx-auto px-4 py-10 lg:py-10 text-center">
                        <h2 class="text-rojo font-semibold font-als text-3xl mb-5">
                            <a href="<?php echo get_author_posts_url($coauthor->ID); ?>">
                                <?php echo $coauthor->display_name; ?>
                            </a>
                        </h2>
                        <div class="text-gris font-semibold text-2xl leading-tight container text-justify">
                            <?php echo $coauthor->description; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

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