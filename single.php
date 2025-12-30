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

<section x-data="footnotes()" x-init="initialize()">
    <template x-teleport="body">
        <div
            x-ref="tooltip"
            x-show="tooltipVisible"
            @mouseover="clearTimeout(hideTimer)"
            @mouseleave="hideTooltip()"
            x-html="tooltipContent"
            :style="tooltipStyle"
            class="footnote-tooltip bg-white border border-gray-200 p-4 rounded-lg shadow-lg fixed text-lg leading-relaxed z-50 max-w-md"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95">
        </div>
    </template>
    <?php if (have_posts()): while (have_posts()) : the_post(); ?>
            <article class="container mx-auto px-4">
                <?php get_template_part('template-parts/breadcrumbs'); ?>
                <?php if (has_post_thumbnail()): ?>
                    <div class=" mb-8">
                        <div class="w-full">
                            <?php the_post_thumbnail('large', [
                                'class' => 'w-full h-auto lg:aspect-[2/3] lg:max-h-[650px] object-cover',
                                'loading' => 'eager',
                                'fetchpriority' => 'high'
                            ]); ?>
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
