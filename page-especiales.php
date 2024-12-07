<?php get_header(); ?>
<div class="container mx-auto px-4">
    <?php get_template_part('template-parts/breadcrumbs'); ?>

    <section>
        <h1 class="text-5xl font-bold text-center text-rojo text-bold my-8">Archivo de especiales</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-16">
            <?php
            $args = array(
                'post_type' => 'page',
                'post_parent' => 7729,
                'posts_per_page' => 72,
            );
            $query = new WP_Query($args);

            if ($query->have_posts()) :
                while ($query->have_posts()): $query->the_post(); ?>
                    <article class="bg-white  overflow-hidden group relative">
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

                            <h2 class="text-3xl font-semibold  text-center mb-4 text-rojo group-hover:text-darkgold transition-colors duration-300">
                                <?php echo esc_html(get_field('nombre_especial')); ?>
                            </h2>

                            <?php if (get_field('bajada_especial')): ?>
                                <div class="text-gray-800 text-lg text-center">
                                    <?php echo esc_html(get_field('bajada_especial')); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endwhile;
                wp_reset_postdata();
            else: ?>
                <p class="col-span-full text-center text-gray-600">No hay especiales disponibles.</p>
            <?php endif; ?>
        </div>
    </section>
</div>
<?php get_footer(); ?>