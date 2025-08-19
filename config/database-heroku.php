<?php
/**
 * Configuración de base de datos para Heroku
 * Esta versión usa PostgreSQL en lugar de MySQL
 */

class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Obtener DATABASE_URL de Heroku
            $database_url = getenv('DATABASE_URL');
            
            if (!$database_url) {
                throw new Exception("DATABASE_URL no está configurada en Heroku");
            }
            
            // Crear conexión PDO directamente desde DATABASE_URL
            $this->conn = new PDO($database_url);
            
            // Configurar atributos después de la conexión
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->conn->setAttribute(PDO::ATTR_PERSISTENT, false);
            
            // Configurar timezone
            $this->conn->exec("SET timezone = 'UTC'");
            
        } catch(PDOException $exception) {
            error_log("Error de conexión PostgreSQL: " . $exception->getMessage());
            throw new Exception("No se pudo conectar a la base de datos PostgreSQL: " . $exception->getMessage());
        } catch(Exception $exception) {
            error_log("Error general: " . $exception->getMessage());
            throw $exception;
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
            $stmt = $conn->query("SELECT 1 as test");
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
            $stmt = $conn->query("SELECT version() as version");
            $version = $stmt->fetch();
            
            $stmt = $conn->query("SELECT current_database() as name");
            $name = $stmt->fetch();
            
            return [
                'version' => $version['version'],
                'name' => $name['name']
            ];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Crear tablas si no existen (específico para PostgreSQL)
     */
    public function createTables() {
        $tables = [
            'usuarios' => "CREATE TABLE IF NOT EXISTS usuarios (
                id SERIAL PRIMARY KEY,
                usuario VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(100),
                fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
            'categorias' => "CREATE TABLE IF NOT EXISTS categorias (
                id SERIAL PRIMARY KEY,
                nombre VARCHAR(100) NOT NULL,
                slug VARCHAR(100) UNIQUE NOT NULL,
                descripcion TEXT,
                orden INTEGER DEFAULT 0
            )",
            
            'noticias' => "CREATE TABLE IF NOT EXISTS noticias (
                id SERIAL PRIMARY KEY,
                titulo VARCHAR(200) NOT NULL,
                contenido TEXT NOT NULL,
                imagen VARCHAR(255),
                categoria_id INTEGER REFERENCES categorias(id),
                fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                activo BOOLEAN DEFAULT TRUE
            )",
            
            'blog_posts' => "CREATE TABLE IF NOT EXISTS blog_posts (
                id SERIAL PRIMARY KEY,
                titulo VARCHAR(200) NOT NULL,
                contenido TEXT NOT NULL,
                excerpt TEXT,
                imagen VARCHAR(255),
                slug VARCHAR(200) UNIQUE NOT NULL,
                categoria VARCHAR(100),
                tags TEXT,
                fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                vistas INTEGER DEFAULT 0,
                activo BOOLEAN DEFAULT TRUE
            )",
            
            'carrusel' => "CREATE TABLE IF NOT EXISTS carrusel (
                id SERIAL PRIMARY KEY,
                titulo VARCHAR(200) NOT NULL,
                descripcion TEXT,
                imagen VARCHAR(255) NOT NULL,
                enlace VARCHAR(255),
                orden INTEGER DEFAULT 0,
                activo BOOLEAN DEFAULT TRUE
            )",
            
            'banners' => "CREATE TABLE IF NOT EXISTS banners (
                id SERIAL PRIMARY KEY,
                titulo VARCHAR(200) NOT NULL,
                imagen VARCHAR(255) NOT NULL,
                enlace VARCHAR(255),
                orden INTEGER DEFAULT 0,
                activo BOOLEAN DEFAULT TRUE
            )",
            
            'mensajes_contacto' => "CREATE TABLE IF NOT EXISTS mensajes_contacto (
                id SERIAL PRIMARY KEY,
                nombre VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                asunto VARCHAR(200),
                mensaje TEXT NOT NULL,
                fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                leido BOOLEAN DEFAULT FALSE
            )",
            
            'info_contacto' => "CREATE TABLE IF NOT EXISTS info_contacto (
                id SERIAL PRIMARY KEY,
                tipo VARCHAR(50) NOT NULL,
                valor TEXT NOT NULL,
                orden INTEGER DEFAULT 0
            )",
            
            'footer_items' => "CREATE TABLE IF NOT EXISTS footer_items (
                id SERIAL PRIMARY KEY,
                titulo VARCHAR(100) NOT NULL,
                contenido TEXT,
                tipo VARCHAR(50),
                orden INTEGER DEFAULT 0
            )"
        ];

        $conn = $this->getConnection();
        
        foreach ($tables as $table => $sql) {
            try {
                $conn->exec($sql);
                error_log("Tabla '$table' creada/verificada en PostgreSQL");
            } catch (PDOException $e) {
                error_log("Error creando tabla '$table': " . $e->getMessage());
            }
        }
    }

    /**
     * Insertar datos iniciales
     */
    public function insertInitialData() {
        try {
            $conn = $this->getConnection();
            
            // Usuario administrador
            $adminPassword = password_hash('admin1', PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO usuarios (usuario, password, email) VALUES (?, ?, ?) ON CONFLICT (usuario) DO NOTHING");
            $stmt->execute(['admin1', $adminPassword, ADMIN_EMAIL]);
            
            // Categorías iniciales
            $categorias = [
                ['Actualidad', 'actualidad', 'Noticias y novedades del mundo tecnológico'],
                ['Afiliados', 'afiliados', 'Contenido de nuestros afiliados'],
                ['Tutoriales', 'tutoriales', 'Guías paso a paso'],
                ['Reviews', 'reviews', 'Análisis de productos y software']
            ];
            
            $stmt = $conn->prepare("INSERT INTO categorias (nombre, slug, descripcion) VALUES (?, ?, ?) ON CONFLICT (slug) DO NOTHING");
            foreach ($categorias as $cat) {
                $stmt->execute($cat);
            }
            
            // Carrusel inicial
            $carrusel = [
                ['Bienvenido a CodeaiNews', 'Tu fuente de noticias tecnológicas', 'https://via.placeholder.com/1200x400/007bff/ffffff?text=Bienvenido', '#', 1],
                ['Linux y Software Libre', 'Descubre el mundo del código abierto', 'https://via.placeholder.com/1200x400/28a745/ffffff?text=Linux', '#', 2]
            ];
            
            $stmt = $conn->prepare("INSERT INTO carrusel (titulo, descripcion, imagen, enlace, orden) VALUES (?, ?, ?, ?, ?) ON CONFLICT DO NOTHING");
            foreach ($carrusel as $item) {
                $stmt->execute($item);
            }
            
            // Banner inicial
            $stmt = $conn->prepare("INSERT INTO banners (titulo, imagen, orden) VALUES (?, ?, ?) ON CONFLICT DO NOTHING");
            $stmt->execute(['Banner Promocional', 'https://via.placeholder.com/728x90/ffc107/000000?text=Publicidad', 1]);
            
            // Información de contacto
            $contactInfo = [
                ['direccion', 'Calle Tecnología 123, Madrid, España', 1],
                ['telefono', '+34 91 123 45 67', 2],
                ['email', 'info@codeainews.com', 3],
                ['horario', 'Lunes a Viernes: 9:00 - 18:00', 4]
            ];
            
            $stmt = $conn->prepare("INSERT INTO info_contacto (tipo, valor, orden) VALUES (?, ?, ?) ON CONFLICT DO NOTHING");
            foreach ($contactInfo as $info) {
                $stmt->execute($info);
            }
            
            // Footer inicial
            $footerItems = [
                ['Sobre Nosotros', 'Somos una plataforma dedicada a las noticias tecnológicas y el software libre.', 'sobre', 1],
                ['Política de Privacidad', 'Nuestra política de privacidad y términos de uso.', 'legal', 2],
                ['Contacto', 'Información de contacto y formulario.', 'contacto', 3]
            ];
            
            $stmt = $conn->prepare("INSERT INTO footer_items (titulo, contenido, tipo, orden) VALUES (?, ?, ?, ?) ON CONFLICT DO NOTHING");
            foreach ($footerItems as $item) {
                $stmt->execute($item);
            }
            
            error_log("Datos iniciales insertados en PostgreSQL");
            return true;
            
        } catch (PDOException $e) {
            error_log("Error insertando datos: " . $e->getMessage());
            return false;
        }
    }
}

// Función de utilidad para crear conexión directa
function getDatabaseConnection() {
    $database = new Database();
    return $database->getConnection();
}

// Función para verificar si estamos en Heroku
function isHerokuEnvironment() {
    return defined('HEROKU_APP_NAME') && !empty(HEROKU_APP_NAME);
}

// Función para obtener la URL base del sitio
function getBaseUrl() {
    if (defined('SITE_URL') && !empty(SITE_URL)) {
        return SITE_URL;
    }
    
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    return $protocol . '://' . $host;
}
?>




