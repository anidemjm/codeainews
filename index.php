<?php
// Versión simplificada para diagnosticar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🚀 CodeaiNews - Diagnóstico</h1>";
echo "<p>✅ PHP funcionando: " . phpversion() . "</p>";
echo "<p>✅ Fecha: " . date('Y-m-d H:i:s') . "</p>";

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


