<?php get_header(); ?>

<div class="container mx-auto my-10 px-4 text-center ">
    <section class="prose">

        <h1>Error 404</h1>

        <img src="<?php bloginfo('template_url'); ?>/img/404.png" alt="">

        <div>
            <p class="error">Página no encontrada.</p>
            <p class="error">Puedes volver a <a href="<?php echo get_option('home'); ?>">nuestra página de inicio</a> o <a href="<?php echo get_option('home'); ?>/busqueda">buscar en nuestro archivo</a>.</p>
        </div>

    </section>

</div>

<?php
get_footer();
