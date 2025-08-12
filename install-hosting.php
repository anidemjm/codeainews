<?php
/**
 * Script de instalación para hosting
 * Ejecutar UNA SOLA VEZ después de subir los archivos
 */

// Incluir configuración de hosting
require_once 'config/hosting.php';

// Crear conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexión a la base de datos exitosa<br>";
} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->message);
}

// Crear tablas si no existen
$tables = [
    'usuarios' => "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100),
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    'categorias' => "CREATE TABLE IF NOT EXISTS categorias (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        slug VARCHAR(100) UNIQUE NOT NULL,
        descripcion TEXT,
        orden INT DEFAULT 0
    )",
    
    'noticias' => "CREATE TABLE IF NOT EXISTS noticias (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(200) NOT NULL,
        contenido TEXT NOT NULL,
        imagen VARCHAR(255),
        categoria_id INT,
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        activo BOOLEAN DEFAULT TRUE,
        FOREIGN KEY (categoria_id) REFERENCES categorias(id)
    )",
    
    'blog_posts' => "CREATE TABLE IF NOT EXISTS blog_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(200) NOT NULL,
        contenido LONGTEXT NOT NULL,
        excerpt TEXT,
        imagen VARCHAR(255),
        slug VARCHAR(200) UNIQUE NOT NULL,
        categoria VARCHAR(100),
        tags TEXT,
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        vistas INT DEFAULT 0,
        activo BOOLEAN DEFAULT TRUE
    )",
    
    'carrusel' => "CREATE TABLE IF NOT EXISTS carrusel (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(200) NOT NULL,
        descripcion TEXT,
        imagen VARCHAR(255) NOT NULL,
        enlace VARCHAR(255),
        orden INT DEFAULT 0,
        activo BOOLEAN DEFAULT TRUE
    )",
    
    'banners' => "CREATE TABLE IF NOT EXISTS banners (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(200) NOT NULL,
        imagen VARCHAR(255) NOT NULL,
        enlace VARCHAR(255),
        orden INT DEFAULT 0,
        activo BOOLEAN DEFAULT TRUE
    )",
    
    'mensajes_contacto' => "CREATE TABLE IF NOT EXISTS mensajes_contacto (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        asunto VARCHAR(200),
        mensaje TEXT NOT NULL,
        fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        leido BOOLEAN DEFAULT FALSE
    )",
    
    'info_contacto' => "CREATE TABLE IF NOT EXISTS info_contacto (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tipo VARCHAR(50) NOT NULL,
        valor TEXT NOT NULL,
        orden INT DEFAULT 0
    )",
    
    'footer_items' => "CREATE TABLE IF NOT EXISTS footer_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titulo VARCHAR(100) NOT NULL,
        contenido TEXT,
        tipo VARCHAR(50),
        orden INT DEFAULT 0
    )"
];

// Crear tablas
foreach ($tables as $table => $sql) {
    try {
        $pdo->exec($sql);
        echo "✅ Tabla '$table' creada/verificada<br>";
    } catch (PDOException $e) {
        echo "❌ Error creando tabla '$table': " . $e->getMessage() . "<br>";
    }
}

// Insertar datos iniciales
try {
    // Usuario administrador
    $adminPassword = password_hash('admin1', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO usuarios (usuario, password, email) VALUES (?, ?, ?)");
    $stmt->execute(['admin1', $adminPassword, ADMIN_EMAIL]);
    echo "✅ Usuario administrador creado (admin1/admin1)<br>";
    
    // Categorías iniciales
    $categorias = [
        ['Actualidad', 'actualidad', 'Noticias y novedades del mundo tecnológico'],
        ['Afiliados', 'afiliados', 'Contenido de nuestros afiliados'],
        ['Tutoriales', 'tutoriales', 'Guías paso a paso'],
        ['Reviews', 'reviews', 'Análisis de productos y software']
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO categorias (nombre, slug, descripcion) VALUES (?, ?, ?)");
    foreach ($categorias as $cat) {
        $stmt->execute($cat);
    }
    echo "✅ Categorías iniciales creadas<br>";
    
    // Carrusel inicial
    $carrusel = [
        ['Bienvenido a CodeaiNews', 'Tu fuente de noticias tecnológicas', 'https://via.placeholder.com/1200x400/007bff/ffffff?text=Bienvenido', '#', 1],
        ['Linux y Software Libre', 'Descubre el mundo del código abierto', 'https://via.placeholder.com/1200x400/28a745/ffffff?text=Linux', '#', 2]
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO carrusel (titulo, descripcion, imagen, enlace, orden) VALUES (?, ?, ?, ?, ?)");
    foreach ($carrusel as $item) {
        $stmt->execute($item);
    }
    echo "✅ Carrusel inicial creado<br>";
    
    // Banner inicial
    $stmt = $pdo->prepare("INSERT IGNORE INTO banners (titulo, imagen, orden) VALUES (?, ?, ?)");
    $stmt->execute(['Banner Promocional', 'https://via.placeholder.com/728x90/ffc107/000000?text=Publicidad', 1]);
    echo "✅ Banner inicial creado<br>";
    
    // Información de contacto
    $contactInfo = [
        ['direccion', 'Calle Tecnología 123, Madrid, España', 1],
        ['telefono', '+34 91 123 45 67', 2],
        ['email', 'info@codeainews.com', 3],
        ['horario', 'Lunes a Viernes: 9:00 - 18:00', 4]
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO info_contacto (tipo, valor, orden) VALUES (?, ?, ?)");
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
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO footer_items (titulo, contenido, tipo, orden) VALUES (?, ?, ?, ?)");
    foreach ($footerItems as $item) {
        $stmt->execute($item);
    }
    echo "✅ Footer inicial creado<br>";
    
    echo "<br>🎉 ¡Instalación completada exitosamente!<br>";
    echo "Puedes acceder al panel con: admin1 / admin1<br>";
    echo "<strong>IMPORTANTE:</strong> Elimina este archivo (install-hosting.php) por seguridad<br>";
    
} catch (PDOException $e) {
    echo "❌ Error insertando datos: " . $e->getMessage() . "<br>";
}
?>


