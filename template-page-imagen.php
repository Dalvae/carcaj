<?php /* Template Name: Pagina con Imagen */ get_header(); ?>

<main class="container">
	<section class="">

		<h1 class="text-5xl text-rojo font-semibold text-center my-8"><?php the_title(); ?></h1>

		<?php if (have_posts()): while (have_posts()) : the_post(); ?>

				<!-- article -->
				<article class="flex prose flex-col md:flex-row">


					<div class="mt-12 text-2xl font-al leading-relaxed prose max-w-none content-full text-justify">
						<?php the_content(); ?>
					</div>

					<div class="h-full w-full">
						<img src="<?php bloginfo('template_url'); ?>/img/carcaj-about.png" alt="">
					</div>

				</article>
				<!-- /article -->

			<?php endwhile; ?>

		<?php else: endif; ?>

	</section>
	<!-- /section -->
</main>

<?php get_footer(); ?>