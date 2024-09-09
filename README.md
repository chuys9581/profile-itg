# Mostrar Nombre y Cargar Imagen Usuario Activo

## Descripción

Este plugin de WordPress permite mostrar el nombre del usuario activo en sesión, cargar y actualizar su imagen de perfil, y mostrar la cantidad de publicaciones realizadas por el usuario. También muestra las publicaciones del usuario con imágenes o videos en su perfil.

## Funcionalidades

1. **Mostrar Nombre y Cargar Imagen de Perfil:**
   - Muestra el nombre del usuario activo en sesión.
   - Permite al usuario subir una imagen de perfil desde su ordenador.
   - Muestra la imagen de perfil actual y proporciona un botón para eliminar la imagen.
   - Muestra la cantidad de publicaciones realizadas por el usuario.

2. **Mostrar Publicaciones del Usuario:**
   - Muestra una galería de publicaciones realizadas por el usuario activo.
   - Permite ver imágenes o videos asociados a las publicaciones.
   - Incluye un popup para ver detalles adicionales de las publicaciones al hacer clic en ellas.

## Instalación

1. **Subir el Plugin:**
   - Descarga el archivo del plugin.
   - Descomprime el archivo si es necesario.
   - Sube la carpeta del plugin al directorio `/wp-content/plugins/` en tu instalación de WordPress.

2. **Activar el Plugin:**
   - Inicia sesión en el área de administración de WordPress.
   - Ve a la sección "Plugins".
   - Busca "Mostrar Nombre y Cargar Imagen Usuario Activo" y haz clic en "Activar".

## Uso

1. **Mostrar Nombre y Cargar Imagen de Perfil:**
   - Usa el shortcode `[mostrar_nombre_usuario]` en una página o entrada para mostrar el nombre del usuario, la imagen de perfil y la cantidad de publicaciones.

2. **Mostrar Publicaciones del Usuario:**
   - Usa el shortcode `[mostrar_publicaciones_usuario]` en una página o entrada para mostrar las publicaciones del usuario con imágenes o videos.

## Configuración

- **Airtable API Key y URLs:**
  - Asegúrate de reemplazar la API Key y las URLs en los archivos del plugin con tus credenciales y URLs de Airtable.

## Archivos del Plugin

1. `mostrar-nombre-cargar-imagen.php`: 
   - Controla la carga y visualización de la imagen de perfil del usuario, así como la visualización del nombre del usuario y la cantidad de publicaciones.
   
2. `display_user_posts.php`: 
   - Muestra las publicaciones del usuario, incluyendo imágenes o videos, y gestiona el popup para ver los detalles de las publicaciones.

## Estilos y Scripts

- **CSS:**
  - Los estilos para la página de perfil y el popup están en `css/custom-plugin-style.css`.

- **JavaScript:**
  - Los scripts para el manejo del popup y otras funcionalidades están en `js/custom-plugin-script.js`.

## Contribuciones

Las contribuciones al plugin son bienvenidas. Por favor, envía un pull request o informa de cualquier problema a través del sistema de issues.

## Licencia

Este plugin es de código abierto y está disponible bajo la [Licencia GPL v2](https://www.gnu.org/licenses/gpl-2.0.html).

## Contacto

Para cualquier pregunta o soporte adicional, por favor contacta al autor:

- **Autor:** Jesus Jimenez
- **Correo Electrónico:** [chuy.dev.f@gmail.com]
