<?php
/**
 * Configuración de base de datos para hosting
 * Esta versión usa MySQL en lugar de SQLite
 */

require_once 'hosting.php';

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            );
        } catch(PDOException $exception) {
            error_log("Error de conexión: " . $exception->getMessage());
            throw new Exception("No se pudo conectar a la base de datos");
        }

        return $this->conn;
    }

    public function closeConnection() {
        $this->conn = null;
    }

    /**
     * Verificar si la base de datos está funcionando
     */
    public function testConnection() {
        try {
            $conn = $this->getConnection();
            $stmt = $conn->query("SELECT 1");
            $result = $stmt->fetch();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obtener información de la base de datos
     */
    public function getDatabaseInfo() {
        try {
            $conn = $this->getConnection();
            $stmt = $conn->query("SELECT VERSION() as version");
            $version = $stmt->fetch();
            
            $stmt = $conn->query("SELECT DATABASE() as name");
            $name = $stmt->fetch();
            
            return [
                'version' => $version['version'],
                'name' => $name['name'],
                'host' => $this->host
            ];
        } catch (Exception $e) {
            return null;
        }
    }
}

// Función de utilidad para crear conexión directa
function getDatabaseConnection() {
    $database = new Database();
    return $database->getConnection();
}

// Función para verificar si estamos en un entorno de hosting
function isHostingEnvironment() {
    return defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASS');
}

// Función para obtener la URL base del sitio
function getBaseUrl() {
    if (defined('SITE_URL')) {
        return SITE_URL;
    }
    
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['SCRIPT_NAME']);
    
    return $protocol . '://' . $host . $path;
}
?>





