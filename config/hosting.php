<?php
/**
 * Configuración para hosting
 * Modifica estos valores según tu proveedor de hosting
 */

// Configuración de base de datos para hosting
define('DB_HOST', 'localhost');        // Cambiar si tu hosting usa otro host
define('DB_NAME', 'tu_nombre_db');     // Nombre de tu base de datos en el hosting
define('DB_USER', 'tu_usuario_db');    // Usuario de la base de datos
define('DB_PASS', 'tu_password_db');   // Contraseña de la base de datos

// Configuración de la aplicación
define('SITE_URL', 'https://tu-dominio.000webhostapp.com'); // URL de tu sitio
define('ADMIN_EMAIL', 'tu@email.com'); // Tu email de administrador

// Configuración de seguridad
define('SESSION_TIMEOUT', 3600); // 1 hora en segundos
define('MAX_LOGIN_ATTEMPTS', 3); // Intentos máximos de login

// Configuración de archivos
define('UPLOAD_DIR', 'uploads/'); // Directorio para subir imágenes
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB máximo por archivo

// Configuración de Google Maps (opcional)
define('GOOGLE_MAPS_API_KEY', ''); // Tu API key de Google Maps si la tienes

// Configuración de correo (opcional)
define('SMTP_HOST', '');
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
?>





