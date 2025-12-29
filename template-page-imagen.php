<?php /* Template Name: Pagina con Imagen */ get_header(); ?>

<main class="container">
	<section class="">

		<h1 class="text-5xl text-rojo font-semibold text-center my-8"><?php the_title(); ?></h1>

		<?php if (have_posts()): while (have_posts()) : the_post(); ?>

				<!-- article -->
				<article class="flex prose flex-col md:flex-row md:gap-8">

					<div class="mt-12 text-2xl font-al leading-relaxed prose max-w-none content-full text-justify md:w-1/2">
						<?php the_content(); ?>
					</div>

					<div class="w-full md:w-1/2 flex items-start justify-center mt-8 md:mt-12">
						<img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/carcaj-about.webp" alt="" class="max-w-[45%] md:max-w-full h-auto">
					</div>

				</article>
				<!-- /article -->

			<?php endwhile; ?>

		<?php else: endif; ?>

	</section>
	<!-- /section -->
</main>

<?php get_footer(); ?>