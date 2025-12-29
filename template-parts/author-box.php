<?php
$coauthor = $args['coauthor'] ?? null;
if (!$coauthor) return;
?>
<div class="bg-swhite my-7">
    <div class="container lg:w-4/5 mx-auto px-4 py-10 lg:py-10 text-center">
        <h2 class="text-rojo font-semibold font-als text-3xl mb-5">
            <a href="<?php echo esc_url(get_author_posts_url($coauthor->ID)); ?>">
                <?php echo esc_html($coauthor->display_name); ?>
            </a>
        </h2>
        <div class="text-gris font-semibold text-2xl leading-tight container text-justify">
            <?php echo wp_kses_post($coauthor->description); ?>
        </div>
    </div>
</div>
