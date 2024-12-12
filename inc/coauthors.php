<?php
// 1. Agregar campo personalizado para el rol de coautor
function add_coauthor_role_meta()
{
    register_meta('post', '_coauthor_role', [
        'type' => 'string',
        'single' => false,
        'show_in_rest' => true,
    ]);
}
add_action('init', 'add_coauthor_role_meta');

// 2. Agregar el metabox para seleccionar el rol
function add_coauthor_role_metabox()
{
    add_meta_box(
        'coauthor-role-meta',
        __('Roles de Coautores', 'textdomain'),
        'render_coauthor_role_metabox',
        'post',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'add_coauthor_role_metabox');

// 3. Renderizar el metabox
function render_coauthor_role_metabox($post)
{
    wp_nonce_field('coauthor_role_nonce', 'coauthor_role_nonce');

    $coauthors = get_coauthors($post->ID);

    if (empty($coauthors)) {
        echo '<p>No hay coautores asignados</p>';
        return;
    }

    echo '<div class="coauthor-roles-wrapper">';
    foreach ($coauthors as $coauthor) {
        $role = get_post_meta($post->ID, '_coauthor_role_' . $coauthor->ID, true);
?>
        <div class="coauthor-role-select" style="margin-bottom: 10px;">
            <label style="display: block; margin-bottom: 5px;">
                <?php echo esc_html($coauthor->display_name); ?>:
            </label>
            <select name="coauthor_role[<?php echo esc_attr($coauthor->ID); ?>]" style="width: 100%;">
                <option value="author" <?php selected($role, 'author'); ?>>
                    <?php _e('Autor', 'textdomain'); ?>
                </option>
                <option value="translator" <?php selected($role, 'translator'); ?>>
                    <?php _e('Traductor', 'textdomain'); ?>
                </option>
            </select>
        </div>
    <?php
    }
    echo '</div>';
}

// 4. Guardar los roles seleccionados
function save_coauthor_role($post_id)
{
    if (
        !isset($_POST['coauthor_role_nonce']) ||
        !wp_verify_nonce($_POST['coauthor_role_nonce'], 'coauthor_role_nonce')
    ) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['coauthor_role'])) {
        foreach ($_POST['coauthor_role'] as $author_id => $role) {
            update_post_meta($post_id, '_coauthor_role_' . $author_id, sanitize_text_field($role));
        }
    }
}
add_action('save_post', 'save_coauthor_role');

// 5. Función para mostrar los créditos en el frontend
function display_post_credits($post_id = null)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $coauthors = get_coauthors($post_id);
    $authors = [];
    $translators = [];

    foreach ($coauthors as $coauthor) {
        $role = get_post_meta($post_id, '_coauthor_role_' . $coauthor->ID, true);

        if ($role === 'translator') {
            $translators[] = sprintf(
                '<a href="%s">%s</a>',
                get_author_posts_url($coauthor->ID, $coauthor->user_nicename),
                $coauthor->display_name
            );
        } else {
            $authors[] = sprintf(
                '<a href="%s">%s</a>',
                get_author_posts_url($coauthor->ID, $coauthor->user_nicename),
                $coauthor->display_name
            );
        }
    }

    $output = '<div class="post-credits">';

    if (!empty($authors)) {
        $output .= '<div class="post-authors">';
        $output .= '<span class="label">' . __('Por', 'textdomain') . '</span> ';
        $output .= implode(', ', $authors);
        $output .= '</div>';
    }

    if (!empty($translators)) {
        $output .= '<div class="post-translators">';
        $output .= '<span class="label">' . __('Traducido por', 'textdomain') . '</span> ';
        $output .= implode(', ', $translators);
        $output .= '</div>';
    }

    $output .= '</div>';

    return $output;
}

// 6. Agregar estilos CSS
function add_coauthor_styles()
{
    ?>
    <style>
        .post-credits {
            margin: 1.5em 0;
            font-size: 0.9em;
        }

        .post-authors,
        .post-translators {
            margin-bottom: 0.5em;
        }

        .post-credits .label {
            font-weight: bold;
        }

        .post-credits a {
            text-decoration: none;
        }

        .post-credits a:hover {
            text-decoration: underline;
        }
    </style>
<?php
}
add_action('wp_head', 'add_coauthor_styles');
