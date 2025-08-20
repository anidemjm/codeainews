<?php
/**
 * Configuración de endpoints de la API
 * Centraliza todas las URLs de la API para facilitar mantenimiento
 */

// Base URL de la API
define('API_BASE_URL', '/api');

// Endpoints específicos
define('API_NOTICIAS', API_BASE_URL . '/noticias-crud.php');
define('API_BLOG', API_BASE_URL . '/blog-crud.php');
define('API_CATEGORIAS', API_BASE_URL . '/categorias-crud.php');
define('API_BANNERS', API_BASE_URL . '/banners-crud.php');
define('API_CARRUSEL', API_BASE_URL . '/carrusel-crud.php');
define('API_CONTACTO', API_BASE_URL . '/contacto-crud.php');
define('API_FOOTER', API_BASE_URL . '/footer-crud.php');

// Función helper para construir URLs completas
function apiUrl($endpoint) {
    $base = rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']), '/');
    return $base . $endpoint;
}

// Función helper para obtener la URL base del sitio
function getSiteUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['REQUEST_URI']);
    return $protocol . '://' . $host . $path;
}
?>
