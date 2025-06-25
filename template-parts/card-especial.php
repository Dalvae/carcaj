<?php
/**
 * Displays a single "especial" card.
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('bg-white overflow-hidden group relative'); ?>>
    <a href="<?php the_permalink(); ?>" class="absolute inset-0 z-10"></a>

    <?php
    $image = get_field('imagen_superior');
    if (!empty($image)): ?>
        <div class="h-60 overflow-hidden">
            <img
                src="<?php echo esc_url($image['sizes']['large']); ?>"
                alt="<?php echo esc_attr($image['alt']); ?>"
                class="w-full h-full object-cover transition-all duration-300 group-hover:brightness-75" />
        </div>
    <?php endif; ?>

    <div class="p-6">
        <?php if (get_field('fecha_especial')): ?>
            <div class="text-gray-400 text-center mb-2">
                <?php echo esc_html(get_field('fecha_especial')); ?>
            </div>
        <?php endif; ?>

        <h2 class="text-3xl font-semibold text-center mb-4 text-rojo group-hover:text-darkgold transition-colors duration-300">
            <?php echo esc_html(get_field('nombre_especial')); ?>
        </h2>

        <?php if (get_field('bajada_especial')): ?>
            <div class="text-gray-800 text-lg text-center">
                <?php echo esc_html(get_field('bajada_especial')); ?>
            </div>
        <?php endif; ?>
    </div>
</article>
