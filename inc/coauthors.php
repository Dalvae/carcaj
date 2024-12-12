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

// 2. Metabox para roles
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

// 3. Renderizar metabox con soporte para actualización dinámica
function render_coauthor_role_metabox($post)
{
    wp_nonce_field('coauthor_role_nonce', 'coauthor_role_nonce');
?>
    <div id="coauthor-roles-wrapper" class="coauthor-roles-wrapper">
        <?php render_coauthor_roles($post->ID); ?>
    </div>

    <script>
        jQuery(document).ready(function($) {
            // Observar cambios en la lista de coautores
            var coauthorsList = document.getElementById('coauthors-list');
            if (coauthorsList) {
                var observer = new MutationObserver(function(mutations) {
                    updateRolesMetabox();
                });

                observer.observe(coauthorsList, {
                    childList: true,
                    subtree: true
                });
            }

            // Función para actualizar el metabox de roles
            function updateRolesMetabox() {
                var post_id = $('#post_ID').val();
                $.post(ajaxurl, {
                    action: 'refresh_coauthor_roles',
                    post_id: post_id,
                    nonce: '<?php echo wp_create_nonce("refresh_coauthor_roles"); ?>'
                }, function(response) {
                    $('#coauthor-roles-wrapper').html(response);
                });
            }

            // También actualizar cuando se complete un drag & drop
            $('#coauthors-list').on('sortupdate', function() {
                updateRolesMetabox();
            });
        });
    </script>
    <?php
}

// 4. Función auxiliar para renderizar roles
function render_coauthor_roles($post_id)
{
    $coauthors = get_coauthors($post_id);

    if (empty($coauthors)) {
        echo '<p>No hay coautores asignados</p>';
        return;
    }

    foreach ($coauthors as $coauthor) {
        $role = get_post_meta($post_id, '_coauthor_role_' . $coauthor->ID, true) ?: 'author';
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
}

// 5. Endpoint AJAX para actualizar roles
function refresh_coauthor_roles_callback()
{
    check_ajax_referer('refresh_coauthor_roles', 'nonce');

    if (!isset($_POST['post_id'])) {
        wp_die();
    }

    render_coauthor_roles($_POST['post_id']);
    wp_die();
}
add_action('wp_ajax_refresh_coauthor_roles', 'refresh_coauthor_roles_callback');

// 6. Guardar roles
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

// 7. Función para mostrar autores con roles en el frontend
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
        echo '<div class="text-md mt-1">';
        echo 'Traducción de: ' . implode(', ', $translators);
        echo '</div>';
    }
}
