<?php
// comments-template.php
?>
<div class="comments-form container mx-auto py-8">
    <h2 class="text-2xl font-bold my-4 text-custom-grey">DEJA UNA RESPUESTA</h2>

    <form action="<?php echo site_url('/wp-comments-post.php'); ?>" method="post" class="space-y-8">
        <div>
            <textarea name="comment" id="comment" rows="8"
                class="w-full p-4 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-0"
                placeholder="Mensaje" required></textarea>
        </div>

        <div>
            <input type="text" name="author" id="author"
                class="w-full p-4 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-0"
                placeholder="Nombre" required>
        </div>

        <div>
            <input type="email" name="email" id="email"
                class="w-full p-4 bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-0"
                placeholder="Email" required>
        </div>

        <button type="submit" class="w-full bg-gold-600 text-white py-4 px-6 rounded-lg bg-darkgold hover:bg-gold hover:text-darkgold transition-colors">
            PUBLICAR EL COMENTARIO
        </button>

        <?php comment_id_fields(); ?>
        <?php do_action('comment_form', $post->ID); ?>
    </form>
</div>