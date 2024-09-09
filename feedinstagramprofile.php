<?php
/**
 * Plugin Name: Mostrar Nombre y Cargar Imagen Usuario Activo
 * Description: Muestra el campo "Nombre" del usuario activo en sesión de la tabla Usuarios en Airtable y permite cargar una imagen de perfil.
 * Version: 1.7
 * Author: Jesus Jimenez
 */

 // Incluir archivo de funciones de posts
require_once plugin_dir_path(__FILE__) . 'display_user_posts.php';

// Incluir el shortcode para mostrar las publicaciones del usuario
function obtener_nombre_usuario_sesion_airtable() {
    $api_key = 'patv59bjnbEGUFZG8.cd0546b6e89b9368307894b52c97ef81268d5253071ed72b4d94d955b441b576';
    $url_usuarios = 'https://api.airtable.com/v0/appzmB3zBmwWkhnkn/Usuarios';
    $url_posts = 'https://api.airtable.com/v0/appzmB3zBmwWkhnkn/Posts';

    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        $user_email = $user->user_email;
    } else {
        return 'No hay ningún usuario en sesión.';
    }

    // Obtener datos del usuario desde Airtable
    $response_usuarios = wp_remote_get($url_usuarios, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key
        )
    ));

    if (is_wp_error($response_usuarios)) {
        return 'Error obteniendo los datos de Airtable.';
    }

    $body_usuarios = wp_remote_retrieve_body($response_usuarios);
    $data_usuarios = json_decode($body_usuarios, true);

    if (empty($data_usuarios['records'])) {
        return 'No se encontraron registros en Airtable.';
    }

    foreach ($data_usuarios['records'] as $record) {
        if (!empty($record['fields']['email']) && $record['fields']['email'] == $user_email) {
            $nombre_usuario = esc_html($record['fields']['Nombre']);
            $avatar_url = !empty($record['fields']['Avatar']) ? esc_url($record['fields']['Avatar']) : '';

            // Obtener las publicaciones del usuario desde Airtable
            $response_posts = wp_remote_get($url_posts, array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $api_key
                )
            ));

            if (is_wp_error($response_posts)) {
                return 'Error obteniendo las publicaciones de Airtable.';
            }

            $body_posts = wp_remote_retrieve_body($response_posts);
            $data_posts = json_decode($body_posts, true);

            $post_count = 0;

            if (!empty($data_posts['records'])) {
                foreach ($data_posts['records'] as $post_record) {
                    if (!empty($post_record['fields']['Email de Usuario']) && $post_record['fields']['Email de Usuario'] == $user_email) {
                        $post_count++;
                    }
                }
            }

            // Mostrar singular o plural según la cantidad de publicaciones
            $publicaciones_texto = ($post_count === 1) ? '1 publicación' : $post_count . ' publicaciones';

            // Generar HTML de salida
            $nonce = wp_create_nonce('upload_profile_image');
            $upload_url = esc_url(admin_url('admin-post.php'));

            $avatar_html = '';
            $form_html = '';

            if ($avatar_url) {
                $avatar_html = '<img id="user-avatar" src="' . $avatar_url . '" alt="Avatar" class="user-avatar" />';
                $form_html = '
                    <a href="' . esc_url(admin_url('admin-post.php?action=delete_profile_image&_wpnonce=' . $nonce)) . '" class="delete-avatar-button">Eliminar Imagen</a><br>
                    <form id="upload-form" action="' . $upload_url . '" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="upload_profile_image">
                        <input type="hidden" name="_wpnonce" value="' . $nonce . '">
                        <input id="file-input" type="file" name="profile_image" accept="image/*" style="display: none;" />
                    </form>
                ';
            } else {
                $form_html = '
                    <form id="upload-form" action="' . $upload_url . '" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="upload_profile_image">
                        <input type="hidden" name="_wpnonce" value="' . $nonce . '">
                        <input id="file-input" type="file" name="profile_image" accept="image/*" />
                        <input id="submit-button" type="submit" value="Subir Imagen" />
                    </form>
                ';
            }

            // Añadir el script JavaScript para enviar el formulario automáticamente y manejar el modal
            $script = '
                <script type="text/javascript">
                    document.addEventListener("DOMContentLoaded", function() {
                        var fileInput = document.getElementById("file-input");
                        var uploadForm = document.getElementById("upload-form");
                        var userAvatar = document.getElementById("user-avatar");

                        if (fileInput) {
                            fileInput.addEventListener("change", function() {
                                if (fileInput.files.length > 0) {
                                    uploadForm.submit();
                                }
                            });
                        }

                        // Mostrar modal al hacer clic en la imagen options
                        var optionsIcon = document.getElementById("options-icon");
                        var modal = document.getElementById("options-modal");
                        var closeBtn = document.getElementById("close-modal");

                        if (optionsIcon && modal && closeBtn) {
                            optionsIcon.addEventListener("click", function() {
                                modal.style.display = "block";
                            });

                            closeBtn.addEventListener("click", function() {
                                modal.style.display = "none";
                            });

                            window.onclick = function(event) {
                                if (event.target == modal) {
                                    modal.style.display = "none";
                                }
                            };
                        }
                    });
                </script>
            ';

            // Obtener la URL de la imagen desde la raíz del plugin
            $options_image_url = plugins_url('options.png', __FILE__);

            // El modal siempre debe estar disponible en el DOM
            $modal_html = '
                <div id="options-modal" class="modal" style="display: none;">
                    <div class="modal-content">
                        <span id="close-modal" class="close">&times;</span>
                        <p class="text-sesion"><a class="url-close" href="' . wp_logout_url(home_url()) . '">Cerrar Sesión</a></p>
                    </div>
                </div>
            ';

            return '<div class="profile-container">
                        <div class="avatar-container">
                            ' . $avatar_html . '
                            ' . $form_html . '
                        </div>
                        <div class="profile-info">
                            <span class="user-name">' . $nombre_usuario . '</span>
                            <span class="user-post-count">' . $publicaciones_texto . '</span>
                        </div>
                        <img id="options-icon" src="' . $options_image_url . '" alt="Options" class="options-icon custom-class-options" style="cursor: pointer;" />
                        ' . $modal_html . '
                        ' . $script . '
                    </div>';
        }
    }

    return 'No se encontró el usuario en Airtable.';
}

function manejar_subida_imagen() {
    // Verificar el nonce
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'upload_profile_image')) {
        wp_die('No tienes permiso para realizar esta acción.');
    }

    if (isset($_FILES['profile_image']) && !empty($_FILES['profile_image']['name'])) {
        $user = wp_get_current_user();
        $user_email = $user->user_email;

        // Subir imagen a la biblioteca de medios de WordPress
        $file = $_FILES['profile_image'];
        $upload = wp_handle_upload($file, array('test_form' => false));

        if (isset($upload['url'])) {
            $image_url = $upload['url'];

            // Actualizar URL del avatar en Airtable
            $api_key = 'patv59bjnbEGUFZG8.cd0546b6e89b9368307894b52c97ef81268d5253071ed72b4d94d955b441b576';
            $url = 'https://api.airtable.com/v0/appzmB3zBmwWkhnkn/Usuarios';

            // Obtener los registros de Airtable
            $response = wp_remote_get($url, array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $api_key
                )
            ));
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);

            // Buscar el registro del usuario y actualizar el avatar
            foreach ($data['records'] as $record) {
                if (!empty($record['fields']['email']) && $record['fields']['email'] == $user_email) {
                    $record_id = $record['id'];

                    // Actualizar el registro en Airtable usando wp_remote_request
                    $update_response = wp_remote_request('https://api.airtable.com/v0/appzmB3zBmwWkhnkn/Usuarios/' . $record_id, array(
                        'method'    => 'PATCH',
                        'headers'   => array(
                            'Authorization' => 'Bearer ' . $api_key,
                            'Content-Type'  => 'application/json'
                        ),
                        'body'      => json_encode(array(
                            'fields' => array(
                                'Avatar' => $image_url
                            )
                        ))
                    ));

                    // Verificar la respuesta de la solicitud PATCH
                    if (is_wp_error($update_response)) {
                        wp_die('Error al actualizar el avatar en Airtable.');
                    }

                    $update_body = wp_remote_retrieve_body($update_response);
                    $update_data = json_decode($update_body, true);

                    // Verifica si se actualizó correctamente
                    if (isset($update_data['id'])) {
                        wp_redirect($_SERVER['HTTP_REFERER']);
                        exit;
                    } else {
                        wp_die('No se pudo actualizar el avatar en Airtable.');
                    }
                    break;
                }
            }
        }
    }

    wp_redirect($_SERVER['HTTP_REFERER']);
    exit;
}

function manejar_eliminacion_imagen() {
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'upload_profile_image')) {
        wp_die('No tienes permiso para realizar esta acción.');
    }

    $user = wp_get_current_user();
    $user_email = $user->user_email;

    // Actualizar URL del avatar en Airtable para eliminar la imagen
    $api_key = 'patv59bjnbEGUFZG8.cd0546b6e89b9368307894b52c97ef81268d5253071ed72b4d94d955b441b576';
    $url = 'https://api.airtable.com/v0/appzmB3zBmwWkhnkn/Usuarios';

    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key
        )
    ));
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    foreach ($data['records'] as $record) {
        if (!empty($record['fields']['email']) && $record['fields']['email'] == $user_email) {
            $record_id = $record['id'];

            $update_response = wp_remote_request('https://api.airtable.com/v0/appzmB3zBmwWkhnkn/Usuarios/' . $record_id, array(
                'method'    => 'PATCH',
                'headers'   => array(
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type'  => 'application/json'
                ),
                'body'      => json_encode(array(
                    'fields' => array(
                        'Avatar' => ''
                    )
                ))
            ));

            if (is_wp_error($update_response)) {
                wp_die('Error al actualizar el avatar en Airtable.');
            }

            $update_body = wp_remote_retrieve_body($update_response);
            $update_data = json_decode($update_body, true);

            if (isset($update_data['id'])) {
                wp_redirect($_SERVER['HTTP_REFERER']);
                exit;
            } else {
                wp_die('No se pudo actualizar el avatar en Airtable.');
            }
            break;
        }
    }

    wp_redirect($_SERVER['HTTP_REFERER']);
    exit;
}

add_action('admin_post_upload_profile_image', 'manejar_subida_imagen');
add_action('admin_post_delete_profile_image', 'manejar_eliminacion_imagen');

function registrar_shortcode_mostrar_nombre_usuario() {
    add_shortcode('mostrar_nombre_usuario', 'obtener_nombre_usuario_sesion_airtable');
}
add_action('init', 'registrar_shortcode_mostrar_nombre_usuario');

add_action('wp_enqueue_scripts', 'ocultar_barra_administracion');
function ocultar_barra_administracion() {
    if (!current_user_can('administrator')) {
        add_filter('show_admin_bar', '__return_false');
    }
}

function encolar_style_profile() {
    wp_enqueue_style('style-profile', plugins_url('style-profile.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'encolar_style_profile');
?>