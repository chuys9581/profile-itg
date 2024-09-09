<?php
function obtener_publicaciones_usuario_airtable() {
    $api_key = 'aqui va tu key si usas esta repo by Jesus Jimenez';
    $url = 'https://api.airtable.com/v0/tu clave de posts/Posts';

    if (!is_user_logged_in()) {
        return 'No hay ningún usuario en sesión.';
    }

    $user = wp_get_current_user();
    $user_email = $user->user_email;

    $response = wp_remote_get($url, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key
        )
    ));

    if (is_wp_error($response)) {
        return 'Error obteniendo los datos de Airtable.';
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (empty($data['records'])) {
        return 'No se encontraron publicaciones en Airtable.';
    }

    $publicaciones_html = '<h3 class="text-posts">PUBLICACIONES</h3><div class="user-posts">';

    $filtered_posts = array();

    foreach ($data['records'] as $record) {
        if (!empty($record['fields']['Email de Usuario']) && $record['fields']['Email de Usuario'] == $user_email) {
            $imagen_video_url = !empty($record['fields']['Imagen/Video']) ? esc_url($record['fields']['Imagen/Video']) : '';
            $titulo = !empty($record['fields']['Titulo']) ? esc_html($record['fields']['Titulo']) : '';
            $contenido = !empty($record['fields']['Contenido']) ? esc_html($record['fields']['Contenido']) : '';
            $post_id = esc_attr($record['id']);

            if ($imagen_video_url) {
                $publicaciones_html .= '<div class="user-post">';
                $publicaciones_html .= '<img src="' . $imagen_video_url . '" alt="Publicación" class="user-post-image" data-post-id="' . $post_id . '" />';
                $publicaciones_html .= '</div>';

                $filtered_posts[] = array(
                    'id' => $post_id,
                    'image' => $imagen_video_url,
                    'title' => $titulo,
                    'content' => $contenido
                );
            }
        }
    }

    $publicaciones_html .= '</div>';

    // Popup HTML
    $publicaciones_html .= '
    <div id="postlike-popup" class="postlike-popup">
        <div class="postlike-popup-content">
            <span class="postlike-close-popup">&times;</span>
            <div class="postlike-popup-body">
                <img id="postlike-popup-image" src="" alt="Imagen del Post" />
                <div id="postlike-popup-text"></div>
            </div>
        </div>
    </div>';

    wp_enqueue_script('custom-plugin-script', plugins_url('js/custom-plugin-script.js', __FILE__), array('jquery'), '1.0', true);
        wp_localize_script('custom-plugin-script', 'postlikePosts', array('posts' => $filtered_posts));

    return $publicaciones_html;
}

add_shortcode('mostrar_publicaciones_usuario', 'obtener_publicaciones_usuario_airtable');

function cargar_estilos_modal_plugin() {
    wp_enqueue_style('custom-plugin-style', plugins_url('css/custom-plugin-style.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'cargar_estilos_modal_plugin');
