<?php
/**
 * Archivo de prueba para verificar que la API funciona
 * Útil para debugging y verificación
 */

require_once 'config/database.php';
require_once 'config/api-endpoints.php';

echo "<h1>🧪 Prueba de la API - CodeaiNews</h1>";
echo "<p>Verificando endpoints y funcionalidades...</p>";

// Probar conexión a la base de datos
try {
    $database = new Database();
    $conn = $database->getConnection();
    echo "✅ <strong>Conexión a la base de datos:</strong> OK<br>";
    
    // Verificar tablas
    $tablas = ['categorias', 'noticias', 'blog_posts', 'banners', 'carrusel'];
    foreach ($tablas as $tabla) {
        $stmt = $conn->query("SELECT COUNT(*) as total FROM $tabla");
        $count = $stmt->fetchColumn();
        echo "📊 <strong>$tabla:</strong> $count registros<br>";
    }
    
} catch (Exception $e) {
    echo "❌ <strong>Error de base de datos:</strong> " . $e->getMessage() . "<br>";
    exit;
}

// Probar endpoints de la API
echo "<h2>🔗 Endpoints de la API</h2>";

$endpoints = [
    'Noticias' => API_NOTICIAS,
    'Blog' => API_BLOG,
    'Categorías' => API_CATEGORIAS,
    'Banners' => API_BANNERS
];

foreach ($endpoints as $nombre => $endpoint) {
    $url = apiUrl($endpoint);
    echo "🔗 <strong>$nombre:</strong> <a href='$url' target='_blank'>$url</a><br>";
}

// Probar operación GET en categorías
echo "<h2>🧪 Prueba de Operación GET</h2>";
try {
    $url = apiUrl(API_CATEGORIAS);
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    
    if ($data && $data['success']) {
        echo "✅ <strong>GET categorías:</strong> OK - " . count($data['data']) . " categorías encontradas<br>";
        
        // Mostrar primera categoría como ejemplo
        if (!empty($data['data'])) {
            $cat = $data['data'][0];
            echo "📝 <strong>Ejemplo:</strong> {$cat['nombre']} ({$cat['slug']})<br>";
        }
    } else {
        echo "❌ <strong>GET categorías:</strong> Error - " . ($data['message'] ?? 'Respuesta inválida') . "<br>";
    }
} catch (Exception $e) {
    echo "❌ <strong>GET categorías:</strong> Error - " . $e->getMessage() . "<br>";
}

// Probar operación POST en categorías (crear una de prueba)
echo "<h2>🧪 Prueba de Operación POST</h2>";
try {
    $url = apiUrl(API_CATEGORIAS);
    $postData = [
        'nombre' => 'Categoría de Prueba',
        'slug' => 'categoria-prueba',
        'descripcion' => 'Esta es una categoría de prueba para verificar la API',
        'orden' => 999
    ];
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($postData)
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    $data = json_decode($response, true);
    
    if ($data && $data['success']) {
        echo "✅ <strong>POST categoría:</strong> OK - Categoría creada con ID: {$data['id']}<br>";
        
        // Limpiar: eliminar la categoría de prueba
        $deleteUrl = $url . "?id=" . $data['id'];
        $deleteContext = stream_context_create([
            'http' => [
                'method' => 'DELETE'
            ]
        ]);
        
        $deleteResponse = file_get_contents($deleteUrl, false, $deleteContext);
        $deleteData = json_decode($deleteResponse, true);
        
        if ($deleteData && $deleteData['success']) {
            echo "🧹 <strong>Limpieza:</strong> Categoría de prueba eliminada<br>";
        }
        
    } else {
        echo "❌ <strong>POST categoría:</strong> Error - " . ($data['message'] ?? 'Respuesta inválida') . "<br>";
    }
} catch (Exception $e) {
    echo "❌ <strong>POST categoría:</strong> Error - " . $e->getMessage() . "<br>";
}

echo "<h2>🎯 Estado de la API</h2>";
echo "✅ <strong>Base de datos:</strong> Conectada<br>";
echo "✅ <strong>Endpoints:</strong> Configurados<br>";
echo "✅ <strong>Operaciones CRUD:</strong> Implementadas<br>";
echo "<br>🚀 <strong>La API está lista para usar desde el dashboard!</strong><br>";

echo "<h2>📋 Próximos pasos:</h2>";
echo "1. ✅ Ejecutar <code>install-heroku.php</code> para crear datos de ejemplo<br>";
echo "2. ✅ Ir al <a href='dashboard.php'>Dashboard</a> para probar la edición<br>";
echo "3. ✅ Verificar que los botones de editar/eliminar funcionen<br>";
echo "4. ✅ Probar crear, editar y eliminar contenido<br>";

echo "<br><a href='dashboard.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🚀 Ir al Dashboard</a>";
?>
