<?php
/* Template Name: Página de Inicio */
get_header();
?>
<div class="container mx-auto px-4 py-8">
  <?php get_template_part('template-parts/slider'); ?>
  <?php
  $paged = (isset($_GET['pg'])) ? absint($_GET['pg']) : 1;

  $posts_query = new WP_Query([
    'post_type' => 'post',
    'posts_per_page' => 12,
    'paged' => $paged,
    'orderby' => 'date',
    'order' => 'DESC'
  ]);

  display_articles_grid([
    'query' => $posts_query,
    'use_query_string' => true  // Usar query string para la paginación
  ]);
  ?>
  <section class="categorias">
    <div class="container mx-auto px-2.5 lg:px-0 my-8">
      <nav>
        <!-- Menú de WordPress con clases personalizadas -->
        <?php
        wp_nav_menu(array(
          'menu' => 'Categorías',
          'menu_class' => 'grid grid-cols-1 lg:grid-cols-6 gap-3 m-0 p-0 list-none',
          'container' => false,
          'li_class' => 'text-3xl font-semibold italic bg-gold block py-5 px-2.5 text-center  text-rojo transition-colors duration-300 hover:bg-rojo hover:text-white [&>a:hover]:text-white',
        ));
        ?>
      </nav>
    </div>
  </section>

  <section class="newsletter block my-8 mx-auto bg-gris">
    <div class="container mx-auto text-white px-5 py-5 lg:px-50 lg:py-[70px]">
      <p class="text-lightgrey  font-bold text-3xl italic my-8 p-0 text-center">
        Suscríbete y recibe actualizaciones en tu correo electrónico
      </p>

      <form class="pt-7.5 flex flex-wrap justify-center items-center">
        <input
          type="email"
          placeholder="Tu email"
          class="w-[600px] text-base py-2.5 px-2.5 border-none mr-2.5 bg-white text-black">
        <button
          class="w-[200px] py-2.5 px-2.5 text-base bg-darkgold border-none text-white uppercase transition-colors duration-300 hover:cursor-pointer hover:bg-gold">
          Enviar
        </button>
      </form>
    </div>
  </section>
</div>
<?php get_footer(); ?>