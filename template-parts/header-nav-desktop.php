<nav class="font-bold lg:text-md xl:text-xl italic">
    <?php
    wp_nav_menu(array(
        'menu' => 'Menú Superior',
        'container' => false,
        'menu_class' => 'flex space-x-8',
        'theme_location' => 'header-menu',
        'depth' => 2,
        'fallback_cb' => 'wp_page_menu',
        'link_class' => 'relative hover:text-[#EA6060] transition-colors duration-200
                                  after:content-[""] after:absolute after:bottom-0 after:left-0 
                                  after:w-full after:h-0.5 after:bg-[#EA6060] after:scale-x-0 
                                  after:transition-transform after:duration-300
                                  hover:after:scale-x-100'
    ));
    ?>
</nav>
