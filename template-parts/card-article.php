<?php
/**
 * Displays a single article card.
 *
 * @param array $args {
 *     @type bool $show_excerpt
 *     @type bool $show_author
 * }
 */
$settings = wp_parse_args($args, [
    'show_excerpt' => true,
    'show_author'  => true,
]);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('article-card bg-white overflow-hidden group relative'); ?>>
    <a href="<?php the_permalink(); ?>" class="absolute inset-0 z-10" aria-label="<?php echo esc_attr(get_the_title()); ?>">
        <span class="sr-only"><?php the_title(); ?></span>
    </a>

    <div class="thumb relative">
        <div class="article-image h-60 overflow-hidden">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('medium', array('class' => 'w-full h-full object-cover transition-brightness duration-300 group-hover:brightness-50')); ?>
            <?php else : ?>
                <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/thumb.webp" 
                    width="800" height="600" loading="lazy"
                    class="w-full h-full object-cover transition-brightness duration-300 group-hover:brightness-50" 
                    alt="<?php echo esc_attr(get_the_title()); ?>">
            <?php endif; ?>
        </div>
        <div class="categories absolute bottom-0 right-0 font-medium text-4xl flex flex-col items-end opacity-0 transform translate-y-2 transition-[opacity,transform] duration-300 group-hover:opacity-100 group-hover:translate-y-0 z-20">
            <div class="relative z-20">
                <?php the_category(' '); ?>
            </div>
        </div>
    </div>

    <div class="p-4">
        <div class="date text-gris"><?php the_time('d'); ?> de <?php the_time('F Y'); ?></div>
        <h2 class="text-3xl font-semibold transition-colors duration-300 text-rojo group-hover:text-darkgold">
            <?php the_title(); ?>
        </h2>
        <?php if ($settings['show_author']) : ?>
            <div class="autor italic text-end font-semibold">
                <span class="text-gris">por</span>
                <span class="relative z-20">
                    <?php
                    if (function_exists('get_coauthors')) {
                        $coauthors = get_coauthors();
                        $author_links = [];
                        foreach ($coauthors as $author) {
                            $author_links[] = sprintf(
                                '<a href="%s" class="text-rojo ml-1 hover:text-darkgold">%s</a>',
                                esc_url(get_author_posts_url($author->ID, $author->user_nicename)),
                                esc_html($author->display_name)
                            );
                        }
                        echo implode(', ', $author_links);
                    } else {
                        the_author_posts_link();
                    }
                    ?>
                </span>
            </div>
        <?php endif; ?>
        <?php if ($settings['show_excerpt']) : ?>
            <div class="text-gris text-justify text-xl my-4">
                <?php the_excerpt(); ?>
            </div>
        <?php endif; ?>
    </div>
</article>
