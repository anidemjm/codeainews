<?php
// Versión simplificada para diagnosticar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🚀 CodeaiNews - Diagnóstico Detallado</h1>";
echo "<p>✅ PHP funcionando: " . phpversion() . "</p>";
echo "<p>✅ Fecha: " . date('Y-m-d H:i:s') . "</p>";

// Verificar extensiones PHP disponibles
echo "<h2>🔍 Extensiones PHP Disponibles:</h2>";
$extensions = get_loaded_extensions();
echo "<p><strong>Total de extensiones:</strong> " . count($extensions) . "</p>";

// Buscar extensiones relacionadas con PostgreSQL
$pgsql_extensions = array_filter($extensions, function($ext) {
    return stripos($ext, 'pgsql') !== false || stripos($ext, 'pdo') !== false;
});

echo "<h3>🔍 Extensiones PostgreSQL/PDO:</h3>";
if (empty($pgsql_extensions)) {
    echo "<p>❌ <strong>NO se encontraron extensiones PostgreSQL/PDO</strong></p>";
} else {
    echo "<ul>";
    foreach ($pgsql_extensions as $ext) {
        echo "<li>✅ $ext</li>";
    }
    echo "</ul>";
}

// Verificar extensiones específicas
echo "<h3>🔍 Verificación Específica:</h3>";
echo "<p><strong>PDO:</strong> " . (extension_loaded('pdo') ? '✅ Instalado' : '❌ NO instalado') . "</p>";
echo "<p><strong>PDO PostgreSQL:</strong> " . (extension_loaded('pdo_pgsql') ? '✅ Instalado' : '❌ NO instalado') . "</p>";
echo "<p><strong>PostgreSQL:</strong> " . (extension_loaded('pgsql') ? '✅ Instalado' : '❌ NO instalado') . "</p>";

// Probar configuración de base de datos
echo "<h2>🔍 Probando Base de Datos:</h2>";

try {
    // Verificar si estamos en Heroku
    if (getenv('DATABASE_URL')) {
        echo "<p>✅ Detectado Heroku con DATABASE_URL</p>";
        
        // Incluir configuración de Heroku
        require_once 'config/database-heroku.php';
        
        $db = new Database();
        $connection = $db->getConnection();
        echo "<p>✅ Base de datos PostgreSQL conectada</p>";
        
        // Probar consulta simple
        $stmt = $connection->query("SELECT 1 as test");
        $result = $stmt->fetch();
        echo "<p>✅ Consulta de prueba exitosa</p>";
        
    } else {
        echo "<p>⚠️ No detectado Heroku - usando configuración local</p>";
        require_once 'config/database-local.php';
        $db = new Database();
        echo "<p>✅ Base de datos SQLite conectada</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>📁 Archivo: " . $e->getFile() . "</p>";
    echo "<p>📍 Línea: " . $e->getLine() . "</p>";
}

echo "<h2>🎯 Enlaces de Prueba:</h2>";
echo "<p><a href='login.php'>🔐 Login</a></p>";
echo "<p><a href='dashboard.php'>📊 Dashboard</a></p>";
echo "<p><a href='blog.php'>📝 Blog</a></p>";
echo "<p><a href='contacto.php'>📧 Contacto</a></p>";
?>


