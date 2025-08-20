<?php
/**
 * Script de diagnóstico para verificar qué está funcionando
 * y qué necesita arreglo después del deploy
 */

echo "<h1>🔍 Diagnóstico del Sistema - CodeaiNews</h1>";
echo "<p>Verificando componentes después del deploy manual...</p>";

// 1. Verificar configuración básica
echo "<h2>⚙️ Configuración Básica</h2>";

try {
    echo "✅ <strong>PHP Version:</strong> " . phpversion() . "<br>";
    echo "✅ <strong>Server:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "<br>";
    echo "✅ <strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'No definido') . "<br>";
} catch (Exception $e) {
    echo "❌ <strong>Error configuración básica:</strong> " . $e->getMessage() . "<br>";
}

// 2. Verificar archivos críticos
echo "<h2>📁 Archivos Críticos</h2>";

$archivos_criticos = [
    'config/database.php' => 'Configuración de base de datos',
    'config/heroku.php' => 'Configuración de Heroku',
    'config/api-endpoints.php' => 'Endpoints de API',
    'api/noticias-crud.php' => 'API CRUD Noticias',
    'api/blog-crud.php' => 'API CRUD Blog',
    'api/categorias-crud.php' => 'API CRUD Categorías',
    'api/banners-crud.php' => 'API CRUD Banners',
    'test-api.php' => 'Script de prueba API',
    'insertar-datos-ejemplo.php' => 'Script de datos ejemplo'
];

foreach ($archivos_criticos as $archivo => $descripcion) {
    if (file_exists($archivo)) {
        echo "✅ <strong>$descripcion:</strong> $archivo<br>";
    } else {
        echo "❌ <strong>$descripcion:</strong> $archivo - NO ENCONTRADO<br>";
    }
}

// 3. Verificar base de datos
echo "<h2>🗄️ Conexión a Base de Datos</h2>";

try {
    if (file_exists('config/database.php')) {
        require_once 'config/database.php';
        $database = new Database();
        $conn = $database->getConnection();
        echo "✅ <strong>Conexión a BD:</strong> OK<br>";
        
        // Verificar tablas
        $tablas = ['usuarios', 'categorias', 'noticias', 'blog_posts', 'banners', 'carrusel'];
        foreach ($tablas as $tabla) {
            try {
                $stmt = $conn->query("SELECT COUNT(*) as total FROM $tabla");
                $count = $stmt->fetchColumn();
                echo "📊 <strong>Tabla $tabla:</strong> $count registros<br>";
            } catch (PDOException $e) {
                echo "❌ <strong>Tabla $tabla:</strong> Error - " . $e->getMessage() . "<br>";
            }
        }
        
    } else {
        echo "❌ <strong>Archivo config/database.php no encontrado</strong><br>";
    }
} catch (Exception $e) {
    echo "❌ <strong>Error de base de datos:</strong> " . $e->getMessage() . "<br>";
}

// 4. Verificar variables de entorno
echo "<h2>🌍 Variables de Entorno</h2>";

$env_vars = ['DATABASE_URL', 'HEROKU_APP_NAME', 'HEROKU_RELEASE_VERSION'];
foreach ($env_vars as $var) {
    $value = $_ENV[$var] ?? 'No definida';
    if ($value !== 'No definida') {
        // Ocultar información sensible
        if ($var === 'DATABASE_URL') {
            $value = substr($value, 0, 20) . '...' . substr($value, -10);
        }
        echo "✅ <strong>$var:</strong> $value<br>";
    } else {
        echo "⚠️ <strong>$var:</strong> No definida<br>";
    }
}

// 5. Probar endpoints de API
echo "<h2>🔗 Prueba de Endpoints API</h2>";

if (file_exists('config/api-endpoints.php')) {
    require_once 'config/api-endpoints.php';
    
    $endpoints = [
        'Categorías' => '/api/categorias-crud.php',
        'Noticias' => '/api/noticias-crud.php',
        'Blog' => '/api/blog-crud.php',
        'Banners' => '/api/banners-crud.php'
    ];
    
    foreach ($endpoints as $nombre => $endpoint) {
        if (file_exists('.' . $endpoint)) {
            echo "✅ <strong>Endpoint $nombre:</strong> Archivo existe - <a href='$endpoint' target='_blank'>Probar</a><br>";
        } else {
            echo "❌ <strong>Endpoint $nombre:</strong> Archivo no encontrado ($endpoint)<br>";
        }
    }
} else {
    echo "❌ <strong>config/api-endpoints.php no encontrado</strong><br>";
}

// 6. Verificar permisos
echo "<h2>🔐 Permisos y Acceso</h2>";

$directorios = ['api/', 'config/', '.'];
foreach ($directorios as $dir) {
    if (is_readable($dir)) {
        echo "✅ <strong>Directorio $dir:</strong> Lectura OK<br>";
    } else {
        echo "❌ <strong>Directorio $dir:</strong> Sin permisos de lectura<br>";
    }
}

// 7. Verificar sesiones
echo "<h2>🔑 Sistema de Sesiones</h2>";

try {
    session_start();
    echo "✅ <strong>Sesiones:</strong> Funcionando<br>";
    
    if (isset($_SESSION['usuario_id'])) {
        echo "✅ <strong>Usuario logueado:</strong> ID " . $_SESSION['usuario_id'] . "<br>";
    } else {
        echo "ℹ️ <strong>Usuario:</strong> No logueado<br>";
    }
} catch (Exception $e) {
    echo "❌ <strong>Error de sesiones:</strong> " . $e->getMessage() . "<br>";
}

echo "<h2>🎯 Resumen del Diagnóstico</h2>";
echo "<p>Si todo aparece en verde (✅), el sistema debería funcionar correctamente.</p>";
echo "<p>Si hay errores rojos (❌), esos son los problemas que necesitan solucionarse.</p>";

echo "<h2>🔧 Acciones Recomendadas</h2>";
echo "<ol>";
echo "<li><strong>Si las tablas están vacías:</strong> Ejecutar <a href='install-heroku.php'>install-heroku.php</a></li>";
echo "<li><strong>Si hay errores de API:</strong> Ejecutar <a href='test-api.php'>test-api.php</a></li>";
echo "<li><strong>Si hay problemas de datos:</strong> Ejecutar <a href='insertar-datos-ejemplo.php'>insertar-datos-ejemplo.php</a></li>";
echo "<li><strong>Panel de control:</strong> <a href='dashboard.php'>dashboard.php</a></li>";
echo "</ol>";

echo "<br><div style='text-align: center;'>";
echo "<a href='index.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🏠 Inicio</a> ";
echo "<a href='dashboard.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>⚙️ Dashboard</a> ";
echo "<a href='test-api.php' style='background: #ffc107; color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🧪 Probar API</a>";
echo "</div>";
?>
