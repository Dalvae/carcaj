<?php get_header(); ?>

<div class="container mx-auto my-10 px-4 text-center ">
    <section class="prose">

        <h1>Error 404</h1>

        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/img/404.png" 
            width="2831" height="355" alt="Error 404">

        <div>
            <p class="error">Pagina no encontrada.</p>
            <p class="error">Puedes volver a <a href="<?php echo esc_url(home_url('/')); ?>">nuestra pagina de inicio</a> o <a href="<?php echo esc_url(home_url('/busqueda')); ?>">buscar en nuestro archivo</a>.</p>
        </div>

    </section>

</div>

<?php
get_footer();
