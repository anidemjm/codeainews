<?php
/**
 * Script de instalación para Heroku
 * Ejecutar UNA SOLA VEZ después del despliegue
 */

// Incluir configuración de Heroku
require_once 'config/heroku.php';
require_once 'config/database-heroku.php';

// Función para mostrar estado
function mostrarEstado($titulo, $estado, $mensaje = '') {
    $icono = $estado ? '✅' : '❌';
    $color = $estado ? 'green' : 'red';
    echo "<div style='margin: 10px 0; padding: 10px; border-left: 4px solid $color; background: #f9f9f9;'>";
    echo "<strong>$icono $titulo:</strong> ";
    echo $estado ? 'OK' : 'ERROR';
    if ($mensaje) echo " - $mensaje";
    echo "</div>";
}

// Verificar configuración de Heroku
function verificarConfiguracionHeroku() {
    $errores = [];
    
    // Verificar variables de entorno
    if (empty($_ENV['DATABASE_URL'])) {
        $errores[] = "DATABASE_URL no está configurada";
    }
    
    // HEROKU_APP_NAME es opcional, no crítico
    if (empty($_ENV['HEROKU_APP_NAME'])) {
        echo "⚠️ HEROKU_APP_NAME no está configurada (opcional)<br>";
    }
    
    return [empty($errores), $errores];
}

// Crear conexión a la base de datos PostgreSQL
function crearConexionPostgreSQL() {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Verificar conexión
        $stmt = $conn->query("SELECT 1 as test");
        $result = $stmt->fetch();
        
        if ($result['test'] == 1) {
            echo "✅ Conexión a PostgreSQL exitosa<br>";
            return $conn;
        } else {
            throw new Exception("No se pudo verificar la conexión");
        }
        
    } catch (Exception $e) {
        die("❌ Error de conexión: " . $e->getMessage());
    }
}

// Crear tablas en PostgreSQL
function crearTablasPostgreSQL($conn) {
    $tables = [
        'usuarios' => "CREATE TABLE IF NOT EXISTS usuarios (
            id SERIAL PRIMARY KEY,
            usuario VARCHAR(50) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
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

    // Crear tablas
    foreach ($tables as $table => $sql) {
        try {
            $conn->exec($sql);
            echo "✅ Tabla '$table' creada/verificada en PostgreSQL<br>";
        } catch (PDOException $e) {
            echo "❌ Error creando tabla '$table': " . $e->getMessage() . "<br>";
        }
    }
}

// Insertar datos iniciales
function insertarDatosIniciales($conn) {
    try {
        // Usuario administrador
        $adminPassword = password_hash('admin1', PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO usuarios (usuario, password_hash, email) VALUES (?, ?, ?) ON CONFLICT (usuario) DO NOTHING");
        $stmt->execute(['admin1', $adminPassword, ADMIN_EMAIL]);
        echo "✅ Usuario administrador creado (admin1/admin1)<br>";
        
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
        echo "✅ Categorías iniciales creadas<br>";
        
        // Carrusel inicial
        $carrusel = [
            ['Bienvenido a CodeaiNews', 'Tu fuente de noticias tecnológicas', 'https://via.placeholder.com/1200x400/007bff/ffffff?text=Bienvenido', '#', 1],
            ['Linux y Software Libre', 'Descubre el mundo del código abierto', 'https://via.placeholder.com/1200x400/28a745/ffffff?text=Linux', '#', 2]
        ];
        
        $stmt = $conn->prepare("INSERT INTO carrusel (titulo, descripcion, imagen, enlace, orden) VALUES (?, ?, ?, ?, ?) ON CONFLICT DO NOTHING");
        foreach ($carrusel as $item) {
            $stmt->execute($item);
        }
        echo "✅ Carrusel inicial creado<br>";
        
        // Banner inicial
        $stmt = $conn->prepare("INSERT INTO banners (titulo, imagen, orden) VALUES (?, ?, ?) ON CONFLICT DO NOTHING");
        $stmt->execute(['Banner Promocional', 'https://via.placeholder.com/728x90/ffc107/000000?text=Publicidad', 1]);
        echo "✅ Banner inicial creado<br>";
        
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
        echo "✅ Información de contacto creada<br>";
        
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
        echo "✅ Footer inicial creado<br>";
        
        echo "<br>🎉 ¡Instalación en Heroku completada exitosamente!<br>";
        echo "Puedes acceder al panel con: admin1 / admin1<br>";
        echo "<strong>IMPORTANTE:</strong> Elimina este archivo (install-heroku.php) por seguridad<br>";
        
    } catch (PDOException $e) {
        echo "❌ Error insertando datos: " . $e->getMessage() . "<br>";
    }
}

// Inicio de la instalación
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalación Heroku - CodeaiNews</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; text-align: center; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .info h3 { margin-top: 0; color: #1976d2; }
        .warning { background: #fff3e0; border: 1px solid #ff9800; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .warning h4 { margin-top: 0; color: #f57c00; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Instalación en Heroku - CodeaiNews</h1>
        
        <div class="info">
            <h3>📋 Información del Entorno</h3>
            <p><strong>App Name:</strong> <?php echo HEROKU_APP_NAME ?? 'No configurado'; ?></p>
            <p><strong>Release Version:</strong> <?php echo HEROKU_RELEASE_VERSION ?? 'No configurado'; ?></p>
            <p><strong>Database:</strong> PostgreSQL</p>
            <p><strong>URL:</strong> <?php echo SITE_URL ?? 'No configurado'; ?></p>
        </div>

        <div class="warning">
            <h4>⚠️ Importante</h4>
            <p>Este script solo debe ejecutarse UNA VEZ después del despliegue en Heroku.</p>
            <p>Después de la instalación, elimina este archivo por seguridad.</p>
        </div>

        <h2>🔧 Proceso de Instalación</h2>
        
        <?php
        // Verificar configuración
        list($configOk, $errores) = verificarConfiguracionHeroku();
        if (!$configOk) {
            echo "<div style='background: #ffebee; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>❌ Errores de Configuración:</h3>";
            echo "<ul>";
            foreach ($errores as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul>";
            echo "</div>";
        } else {
            // Crear conexión
            $conn = crearConexionPostgreSQL();
            
            // Crear tablas
            crearTablasPostgreSQL($conn);
            
            // Insertar datos
            insertarDatosIniciales($conn);
            
            // Cerrar conexión
            $conn = null;
        }
        ?>

        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;">🏠 Ir al Inicio</a>
            <a href="dashboard.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;">⚙️ Panel de Control</a>
        </div>
    </div>
</body>
</html>




