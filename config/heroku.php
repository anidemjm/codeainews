<?php
/**
 * Configuración para Heroku
 * Usa variables de entorno para mayor seguridad
 */

// Configuración de base de datos PostgreSQL para Heroku
define('DB_HOST', $_ENV['DATABASE_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DATABASE_NAME'] ?? 'codeainews');
define('DB_USER', $_ENV['DATABASE_USER'] ?? 'postgres');
define('DB_PASS', $_ENV['DATABASE_PASSWORD'] ?? '');
define('DB_PORT', $_ENV['DATABASE_PORT'] ?? '5432');

// Configuración de la aplicación
define('SITE_URL', $_ENV['SITE_URL'] ?? 'https://tu-app.herokuapp.com');
define('ADMIN_EMAIL', $_ENV['ADMIN_EMAIL'] ?? 'admin@codeainews.com');

// Configuración de seguridad
define('SESSION_TIMEOUT', 3600); // 1 hora en segundos
define('MAX_LOGIN_ATTEMPTS', 3); // Intentos máximos de login

// Configuración de archivos
define('UPLOAD_DIR', 'uploads/'); // Directorio para subir imágenes
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB máximo por archivo

// Configuración de Google Maps (opcional)
define('GOOGLE_MAPS_API_KEY', $_ENV['GOOGLE_MAPS_API_KEY'] ?? '');

// Configuración de correo (opcional)
define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? '');
define('SMTP_USER', $_ENV['SMTP_USER'] ?? '');
define('SMTP_PASS', $_ENV['SMTP_PASS'] ?? '');
define('SMTP_PORT', $_ENV['SMTP_PORT'] ?? 587);
define('SMTP_SECURE', $_ENV['SMTP_SECURE'] ?? 'tls');

// Configuración específica de Heroku
define('HEROKU_APP_NAME', $_ENV['HEROKU_APP_NAME'] ?? 'codeainews');
define('HEROKU_RELEASE_VERSION', $_ENV['HEROKU_RELEASE_VERSION'] ?? '1.0.0');
define('HEROKU_SLUG_COMMIT', $_ENV['HEROKU_SLUG_COMMIT'] ?? '');

// Configuración de logs
define('LOG_LEVEL', $_ENV['LOG_LEVEL'] ?? 'INFO');
define('ENABLE_DEBUG', $_ENV['ENABLE_DEBUG'] ?? 'false');

// Configuración de caché (Redis en Heroku)
define('REDIS_URL', $_ENV['REDIS_URL'] ?? '');
define('CACHE_ENABLED', !empty($_ENV['REDIS_URL']));

// Configuración de monitoreo
define('ENABLE_METRICS', $_ENV['ENABLE_METRICS'] ?? 'true');
define('METRICS_INTERVAL', $_ENV['METRICS_INTERVAL'] ?? 300); // 5 minutos
?>





