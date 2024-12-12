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

// 2. Configuración del metabox
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

// 3. Renderizado del metabox
function render_coauthor_role_metabox($post)
{
    wp_nonce_field('coauthor_role_nonce', 'coauthor_role_nonce');
    render_coauthor_roles($post->ID);
}

// 4. Función auxiliar para renderizar roles
function render_coauthor_roles($post_id)
{
    $coauthors = get_coauthors($post_id);

    if (empty($coauthors)) {
        echo '<p>No hay coautores asignados</p>';
        return;
    }

    echo '<div class="coauthor-roles-wrapper">';
    foreach ($coauthors as $coauthor) {
        $role = get_post_meta($post_id, '_coauthor_role_' . $coauthor->ID, true);
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

// 5. Manejo de AJAX para reactividad
function refresh_coauthor_metabox_callback()
{
    check_ajax_referer('refresh_coauthor_metabox', 'nonce');
    render_coauthor_roles($_POST['post_id']);
    wp_die();
}
add_action('wp_ajax_refresh_coauthor_metabox', 'refresh_coauthor_metabox_callback');

// 6. Script para reactividad
function add_coauthor_metabox_script()
{
    ?>
    <script>
        jQuery(document).ready(function($) {
            const observer = new MutationObserver(function() {
                $.post(ajaxurl, {
                    action: 'refresh_coauthor_metabox',
                    post_id: '<?php echo get_the_ID(); ?>',
                    nonce: '<?php echo wp_create_nonce("refresh_coauthor_metabox"); ?>'
                }, function(response) {
                    $('.coauthor-roles-wrapper').html(response);
                });
            });

            const coauthorsPanel = document.querySelector('.coauthors');
            if (coauthorsPanel) {
                observer.observe(coauthorsPanel, {
                    childList: true,
                    subtree: true
                });
            }
        });
    </script>
<?php
}
add_action('admin_footer', 'add_coauthor_metabox_script');

// 7. Guardar roles
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

// 8. Función principal para mostrar autores y traductores
function get_coauthors_with_roles($post_id = null)
{
    if (!$post_id) {
        $post_id = get_the_ID();
    }

    $coauthors = get_coauthors($post_id);
    $authors = [];
    $translators = [];

    foreach ($coauthors as $coauthor) {
        $role = get_post_meta($post_id, '_coauthor_role_' . $coauthor->ID, true);
        $link = sprintf(
            '<a href="%s" class="author-link">%s</a>',
            get_author_posts_url($coauthor->ID, $coauthor->user_nicename),
            $coauthor->display_name
        );

        if ($role === 'translator') {
            $translators[] = $link;
        } else {
            $authors[] = $link;
        }
    }

    echo '<div class="text-gris tracking-tighter text-2xl font-semibold text-center">';
    if (!empty($authors)) {
        echo implode(', ', $authors);
    }
    if (!empty($translators)) {
        echo '<div class="translators text-sm mt-1">';
        echo 'Traducido por: ' . implode(', ', $translators);
        echo '</div>';
    }
    echo '</div>';
}

// 9. Función alternativa para mostrar créditos (mantener compatibilidad)
function display_post_credits($post_id = null)
{
    get_coauthors_with_roles($post_id);
}
