<?php
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area container mx-auto py-8">
    <?php if (have_comments()) : ?>
        <h2 class="text-4xl font-bold text-center my-8 text-custom-grey">
            <?php
            $comments_number = get_comments_number();
            if ($comments_number === '1') {
                printf(_x('Un comentario en "%s"', 'comments title'), get_the_title());
            } else {
                printf(
                    _nx(
                        '%1$s comentario en "%2$s"',
                        '%1$s comentarios en "%2$s"',
                        $comments_number,
                        'comments title'
                    ),
                    number_format_i18n($comments_number),
                    get_the_title()
                );
            }
            ?>
        </h2>

        <ul class="comment-list space-y-6 mb-8">
            <?php
            wp_list_comments(array(
                'style'      => 'ul',
                'short_ping' => true,
                'callback'   => function ($comment, $args, $depth) {
            ?>
                <li id="comment-<?php comment_ID(); ?>" class="comment bg-white p-6 shadow-sm">
                    <div class="comment-meta mb-2">
                        <span class="comment-author font-bold text-lg text-gris">
                            <?php echo get_comment_author(); ?>
                        </span>
                        <span class="comment-date text-sm text-gray-500 ml-2">
                            <?php echo get_comment_date(); ?>
                        </span>
                    </div>
                    <div class="comment-content text-lg">
                        <?php comment_text(); ?>
                    </div>
                    <div class="reply mt-2">
                        <?php
                        comment_reply_link(array_merge($args, array(
                            'depth'     => $depth,
                            'max_depth' => $args['max_depth'],
                            'class'     => 'text-sm text-gold hover:text-darkgold'
                        )));
                        ?>
                    </div>
                </li>
            <?php
                }
            ));
            ?>
        </ul>

        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
            <nav class="comment-navigation flex justify-between">
                <div class="nav-previous"><?php previous_comments_link(__('Comentarios anteriores')); ?></div>
                <div class="nav-next"><?php next_comments_link(__('Comentarios más recientes')); ?></div>
            </nav>
        <?php endif; ?>
    <?php endif; ?>

    <?php
    $comments_args = array(
        'title_reply'          => '<span class="text-2xl font-bold text-custom-grey">DEJA UNA RESPUESTA</span>',
        'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title mb-4">',
        'title_reply_after'    => '</h3>',
        'comment_notes_before' => '<p class="comment-notes text-sm text-gray-600 mb-4">Tu dirección de correo electrónico no será publicada. Los campos obligatorios están marcados con <span class="required text-red-600">*</span></p>',
        'comment_field'        => '<div class="comment-form-comment mb-4"><label for="comment" class="sr-only">Mensaje</label><textarea id="comment" name="comment" class="w-full p-4 bg-white border border-gray-200  focus:outline-none focus:ring-0" rows="8" placeholder="Mensaje" required></textarea></div>',
        'show_cookies_consent' => false,
        'fields'               => array(
            'author' => '<div class="comment-form-author mb-4"><label for="author" class="sr-only">Nombre</label><input id="author" name="author" type="text" class="w-full p-4 bg-white border border-gray-200  focus:outline-none focus:ring-0" placeholder="Nombre" required /></div>',
            'email'  => '<div class="comment-form-email mb-4"><label for="email" class="sr-only">Email</label><input id="email" name="email" type="email" class="w-full p-4 bg-white border border-gray-200  focus:outline-none focus:ring-0" placeholder="Email" required /></div>',
            'cookies' => '<p class="comment-form-cookies-consent mb-4"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" class="mr-2"> <label for="wp-comment-cookies-consent" class="text-sm text-gray-600">Guarda mi nombre, correo electrónico y web en este navegador para la próxima vez que comente.</label></p>',
        ),
        'submit_button'        => '<button type="submit" class="w-full text-white font-bold py-4 px-6 bg-darkgold hover:bg-gold hover:text-darkgold transition-colors">PUBLICAR EL COMENTARIO</button>',
        'class_form'           => 'space-y-4',
        'cancel_reply_link'    => '<span class="text-sm text-gold hover:text-darkgold ml-2">Cancelar respuesta</span>',
        'form_attributes'      => array('data-turbo' => 'false'),
    );

    comment_form($comments_args);
    ?>
</div>
