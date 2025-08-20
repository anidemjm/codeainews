<?php
/**
 * Configuración automática del entorno
 * Detecta si estamos en local o en hosting y carga la configuración apropiada
 */

// Detectar el entorno
function detectEnvironment() {
    // Si estamos en localhost o 127.0.0.1
    if (in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', '::1'])) {
        return 'local';
    }
    
    // Si existe el archivo de configuración de hosting
    if (file_exists(__DIR__ . '/hosting.php')) {
        return 'hosting';
    }
    
    // Por defecto, usar local
    return 'local';
}

// Cargar configuración según el entorno
$environment = detectEnvironment();

if ($environment === 'hosting') {
    // Cargar configuración de hosting
    require_once __DIR__ . '/hosting.php';
    require_once __DIR__ . '/database-hosting.php';
    
    // Definir constantes por defecto si no están definidas
    if (!defined('UPLOAD_DIR')) define('UPLOAD_DIR', 'uploads/');
    if (!defined('MAX_FILE_SIZE')) define('MAX_FILE_SIZE', 5 * 1024 * 1024);
    if (!defined('SESSION_TIMEOUT')) define('SESSION_TIMEOUT', 3600);
    if (!defined('MAX_LOGIN_ATTEMPTS')) define('MAX_LOGIN_ATTEMPTS', 3);
    
} else {
    // Cargar configuración local (SQLite)
    require_once __DIR__ . '/database.php';
    
    // Definir constantes para entorno local
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'codeainews.db');
    define('DB_USER', '');
    define('DB_PASS', '');
    define('SITE_URL', 'http://localhost');
    define('ADMIN_EMAIL', 'admin@localhost');
    define('UPLOAD_DIR', 'uploads/');
    define('MAX_FILE_SIZE', 5 * 1024 * 1024);
    define('SESSION_TIMEOUT', 3600);
    define('MAX_LOGIN_ATTEMPTS', 3);
    define('GOOGLE_MAPS_API_KEY', '');
}

// Función para obtener la configuración de base de datos
function getDatabaseConfig() {
    global $environment;
    
    if ($environment === 'hosting') {
        return [
            'type' => 'mysql',
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_USER,
            'pass' => DB_PASS
        ];
    } else {
        return [
            'type' => 'sqlite',
            'file' => DB_NAME
        ];
    }
}

// Función para obtener la URL base
function getBaseUrl() {
    if (defined('SITE_URL') && !empty(SITE_URL)) {
        return rtrim(SITE_URL, '/');
    }
    
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    return $protocol . '://' . $host;
}

// Función para verificar si estamos en hosting
function isHostingEnvironment() {
    global $environment;
    return $environment === 'hosting';
}

// Función para verificar si estamos en local
function isLocalEnvironment() {
    global $environment;
    return $environment === 'local';
}

// Función para obtener información del entorno
function getEnvironmentInfo() {
    global $environment;
    
    $info = [
        'environment' => $environment,
        'php_version' => PHP_VERSION,
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido',
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Desconocido',
        'current_directory' => getcwd(),
        'base_url' => getBaseUrl()
    ];
    
    if ($environment === 'hosting') {
        $info['database_type'] = 'MySQL';
        $info['database_host'] = DB_HOST;
        $info['database_name'] = DB_NAME;
    } else {
        $info['database_type'] = 'SQLite';
        $info['database_file'] = DB_NAME;
    }
    
    return $info;
}

// Función para mostrar información del entorno (solo en desarrollo)
function debugEnvironment() {
    if (isLocalEnvironment() || (isset($_GET['debug']) && $_GET['debug'] === 'env')) {
        echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px; border: 1px solid #ccc; font-family: monospace;'>";
        echo "<strong>Entorno:</strong> " . detectEnvironment() . "<br>";
        echo "<strong>Base de datos:</strong> " . (isHostingEnvironment() ? 'MySQL' : 'SQLite') . "<br>";
        echo "<strong>URL base:</strong> " . getBaseUrl() . "<br>";
        echo "<strong>Directorio de uploads:</strong> " . UPLOAD_DIR . "<br>";
        echo "</div>";
    }
}

// Configuración de zona horaria
if (!defined('TIMEZONE')) {
    define('TIMEZONE', 'Europe/Madrid');
}
date_default_timezone_set(TIMEZONE);

// Configuración de errores (solo mostrar en local)
if (isLocalEnvironment()) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Función para manejar errores de base de datos
function handleDatabaseError($e, $context = '') {
    if (isLocalEnvironment()) {
        // En local, mostrar el error completo
        throw $e;
    } else {
        // En hosting, loggear el error y mostrar mensaje genérico
        error_log("Error de base de datos en $context: " . $e->getMessage());
        throw new Exception("Error interno del sistema. Por favor, contacta al administrador.");
    }
}

// Función para verificar si el sistema está listo
function isSystemReady() {
    try {
        if (isHostingEnvironment()) {
            $database = new Database();
            $conn = $database->getConnection();
            $conn = null;
        } else {
            $database = new Database();
            $conn = $database->getConnection();
            $conn = null;
        }
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Mostrar información de debug si es necesario
if (isset($_GET['debug']) && $_GET['debug'] === 'env') {
    debugEnvironment();
}
?>





