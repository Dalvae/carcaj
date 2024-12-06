<?php
/* Enable comments support
----------------------------------------------------------------------------------------------------*/
// Enable support for comments and trackbacks in post types
add_action('admin_init', function () {
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        add_post_type_support($post_type, 'comments');
        add_post_type_support($post_type, 'trackbacks');
    }
});

// Open comments on the front-end
add_filter('comments_open', function ($open, $post_id) {
    return true;
}, 20, 2);

add_filter('pings_open', function ($open, $post_id) {
    return true;
}, 20, 2);

// Show existing comments
add_filter('comments_array', function ($comments) {
    return $comments; // Return comments as is, don't empty the array
}, 10, 2);

// Add comments page in menu
add_action('admin_menu', function () {
    // No need to do anything - WordPress adds this by default
});

// Style comment form fields
add_filter('comment_form_fields', function ($fields) {
    // Reordenar para que el comentario esté primero
    $comment_field = '<div class="mb-8">
        <textarea id="comment" name="comment" class="w-full p-4 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-0" placeholder="Mensaje" rows="8"></textarea>
    </div>';

    $fields['author'] = '<div class="mb-8">
        <input id="author" name="author" type="text" class="w-full p-4 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-0" placeholder="Nombre" required />
    </div>';

    $fields['email'] = '<div class="mb-8">
        <input id="email" name="email" type="email" class="w-full p-4 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-0" placeholder="Email" required />
    </div>';


    // Remover el campo de comentario original y reordenarlo
    unset($fields['comment']);
    $fields = array('comment' => $comment_field) + $fields;

    // Remover campos que no necesitamos
    unset($fields['cookies']);

    return $fields;
});

// Personalizar el formulario de comentarios
add_filter('comment_form_defaults', function ($defaults) {
    $defaults['title_reply'] = '<h2 class="text-2xl font-bold mb-4">DEJA UNA RESPUESTA</h2>';
    $defaults['title_reply_to'] = '<h2 class="text-2xl font-bold mb-4">Deja una respuesta a %s</h2>';
    $defaults['cancel_reply_link'] = 'Cancelar respuesta';

    // Texto antes del formulario
    $defaults['comment_notes_before'] = '<p class="mb-4">Tu dirección de correo electrónico no será publicada. Los campos obligatorios están marcados con *</p>';

    // Clase para el botón de enviar
    $defaults['submit_button'] = '<button type="submit" class="w-full bg-gold-600 text-white py-4 px-6 rounded-lg hover:bg-gold-700 transition-colors">PUBLICAR EL COMENTARIO</button>';

    // Remover el título de la caja de comentarios
    $defaults['comment_field'] = '';

    // Eliminar etiquetas de campos
    $defaults['format'] = 'html5';

    return $defaults;
});

// Add comments to admin bar
add_action('init', function () {
    if (is_admin_bar_showing()) {
        add_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
});
