<?php
// 1. Registro de meta
function add_coauthor_role_meta()
{
    register_meta('post', '_coauthor_role', [
        'type' => 'string',
        'single' => false,
        'show_in_rest' => true,
    ]);
}
add_action('init', 'add_coauthor_role_meta');

// 2. Hook para actualizar roles cuando se actualizan coautores
function update_coauthor_roles($post_id, $coauthors)
{
    // Obtener roles actuales
    $current_roles = [];
    foreach ($coauthors as $coauthor) {
        $role = get_post_meta($post_id, '_coauthor_role_' . $coauthor->ID, true);
        if (!$role) {
            // Si no tiene rol asignado, establecer por defecto como autor
            update_post_meta($post_id, '_coauthor_role_' . $coauthor->ID, 'author');
        }
    }
}
add_action('coauthors_updated_post', 'update_coauthor_roles', 10, 2);

// 3. Metabox para roles
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
        $role = get_post_meta($post->ID, '_coauthor_role_' . $coauthor->ID, true) ?: 'author';
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

// 4. Guardar roles
function save_coauthor_role($post_id)
{
    if (
        !isset($_POST['coauthor_role_nonce']) ||
        !wp_verify_nonce($_POST['coauthor_role_nonce'], 'coauthor_role_nonce') ||
        defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ||
        !current_user_can('edit_post', $post_id)
    ) {
        return;
    }

    if (isset($_POST['coauthor_role'])) {
        foreach ($_POST['coauthor_role'] as $author_id => $role) {
            update_post_meta($post_id, '_coauthor_role_' . $author_id, sanitize_text_field($role));
        }
    }
}
add_action('save_post', 'save_coauthor_role');

// 5. Función para mostrar autores con sus roles
function get_coauthors_with_roles($post_id = null)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $coauthors = get_coauthors($post_id);
    $authors = [];
    $translators = [];

    foreach ($coauthors as $coauthor) {
        $role = get_post_meta($post_id, '_coauthor_role_' . $coauthor->ID, true) ?: 'author';
        $link = sprintf(
            '<a href="%s" class="text-gris">%s</a>',
            get_author_posts_url($coauthor->ID, $coauthor->user_nicename),
            $coauthor->display_name
        );

        if ($role === 'translator') {
            $translators[] = $link;
        } else {
            $authors[] = $link;
        }
    }

    if (!empty($authors)) {
        echo implode(', ', $authors);
    }

    if (!empty($translators)) {
        echo '<div class="mt-1">';
        echo 'Traducido por: ' . implode(', ', $translators);
        echo '</div>';
    }
}
