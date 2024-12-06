<?php /* Template Name: Especiales */
get_header(); ?>

<div class="container mx-auto px-4">
  <?php get_template_part('template-parts/breadcrumbs'); ?>

  <section>
    <?php if (have_posts()): while (have_posts()) : the_post(); ?>
        <!-- Cabecera del Especial -->
        <div class="mb-12">
          <?php
          $image = get_field('imagen_superior');
          if (!empty($image)): ?>
            <div class="relative">
              <img
                src="<?php echo esc_url($image['sizes']['large']); ?>"
                alt="<?php echo esc_attr($image['alt']); ?>"
                class="w-full h-[65vh] object-cover" />
              <?php if (get_field('creditos_imagen_superior')): ?>
                <p class="text-sm text-gray-600 mt-2 text-right italic">
                  <?php echo esc_html(get_field('creditos_imagen_superior')); ?>
                </p>
              <?php endif ?>
            </div>
          <?php endif ?>

          <div class="my-16 max-w-5xl mx-auto text-center">
            <h1 class="text-5xl font-bold text-rojo">
              <?php echo esc_html(get_field('nombre_especial')); ?>
            </h1>
            <?php if (get_field('bajada_especial')): ?>
              <h2 class="text-2xl text-gray-700 mb-8">
                <?php echo esc_html(get_field('bajada_especial')); ?>
              </h2>
            <?php endif ?>
            <?php if (get_field('descripcion')): ?>
              <div class="text-2xl text-gray-600">
                <?php echo wp_kses_post(get_field('descripcion')); ?>
              </div>
            <?php endif ?>
          </div>
        </div>

        <!-- Grid de Artículos -->
        <?php
        $tax = get_field('posts_especial');


        $args = array(
          'post_type' => 'post',
          'posts_per_page' => 33,
          'tax_query' => array(
            array(
              'taxonomy' => 'especiales',
              'field'    => 'term_id',
              'terms'    => $tax,
            ),
          ),
        );

        $special_query = new WP_Query($args);


        display_articles_grid([
          'query' => $special_query,
          'show_pagination' => true,
          'show_excerpt' => true,
          'show_author' => true
        ]);
        ?>

    <?php endwhile;
    endif; ?>
  </section>
</div>

<?php get_footer(); ?>