<?php
require_once 'config/database.php';
require_once 'config/api-endpoints.php';

try {
    $db = new Database();
    $connection = $db->getConnection();
    $dbStatus = "✅ Conectado";
    
    // Verificar tablas
    $stmt = $connection->query("SELECT COUNT(*) as total FROM noticias WHERE activo = true");
    $totalNoticias = $stmt->fetchColumn();
    
    $stmt = $connection->query("SELECT COUNT(*) as total FROM blog_posts WHERE activo = true");
    $totalBlogPosts = $stmt->fetchColumn();
    
    $stmt = $connection->query("SELECT COUNT(*) as total FROM categorias");
    $totalCategorias = $stmt->fetchColumn();
    
    $stmt = $connection->query("SELECT COUNT(*) as total FROM banners");
    $totalBanners = $stmt->fetchColumn();
    
} catch (Exception $e) {
    $dbStatus = "❌ Error: " . $e->getMessage();
    $totalNoticias = 0;
    $totalBlogPosts = 0;
    $totalCategorias = 0;
    $totalBanners = 0;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Dashboard - CodeaiNews</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-section {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .test-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        .test-button:hover {
            background: #0056b3;
        }
        .result {
            background: #f8f9fa;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        .success {
            border-left-color: #28a745;
            background: #d4edda;
        }
    </style>
</head>
<body>
    <h1>🧪 Test Dashboard - CodeaiNews</h1>
    
    <div class="test-section">
        <h2>📊 Estado de la Base de Datos</h2>
        <p><strong>Estado:</strong> <?php echo $dbStatus; ?></p>
        <p><strong>Noticias:</strong> <?php echo $totalNoticias; ?></p>
        <p><strong>Blog Posts:</strong> <?php echo $totalBlogPosts; ?></p>
        <p><strong>Categorías:</strong> <?php echo $totalCategorias; ?></p>
        <p><strong>Banners:</strong> <?php echo $totalBanners; ?></p>
    </div>

    <div class="test-section">
        <h2>🔗 Test de APIs CRUD</h2>
        
        <h3>📰 Test API Noticias</h3>
        <button class="test-button" onclick="testNoticiasAPI()">Probar API Noticias</button>
        <div id="result-noticias" class="result"></div>
        
        <h3>📝 Test API Blog</h3>
        <button class="test-button" onclick="testBlogAPI()">Probar API Blog</button>
        <div id="result-blog" class="result"></div>
        
        <h3>🏷️ Test API Categorías</h3>
        <button class="test-button" onclick="testCategoriasAPI()">Probar API Categorías</button>
        <div id="result-categorias" class="result"></div>
        
        <h3>📢 Test API Banners</h3>
        <button class="test-button" onclick="testBannersAPI()">Probar API Banners</button>
        <div id="result-banners" class="result"></div>
    </div>

    <div class="test-section">
        <h2>🔍 Test de Funciones del Dashboard</h2>
        <button class="test-button" onclick="testDashboardFunctions()">Probar Funciones Dashboard</button>
        <div id="result-dashboard" class="result"></div>
    </div>

    <div class="test-section">
        <h2>🧭 Navegación</h2>
        <a href="index.php" class="test-button" style="text-decoration: none; display: inline-block;">🏠 Ir al Inicio</a>
        <a href="dashboard.php" class="test-button" style="text-decoration: none; display: inline-block;">⚙️ Ir al Dashboard</a>
        <a href="noticias.php" class="test-button" style="text-decoration: none; display: inline-block;">📰 Ir a Noticias</a>
        <a href="blog.php" class="test-button" style="text-decoration: none; display: inline-block;">📝 Ir al Blog</a>
    </div>

    <script>
        async function testNoticiasAPI() {
            const resultDiv = document.getElementById('result-noticias');
            resultDiv.innerHTML = '🔄 Probando...';
            
            try {
                const response = await fetch('api/noticias-crud.php');
                const result = await response.json();
                
                if (result.success) {
                    resultDiv.innerHTML = `✅ API Noticias funcionando - ${result.data ? result.data.length : 0} noticias encontradas`;
                    resultDiv.className = 'result success';
                } else {
                    resultDiv.innerHTML = `❌ API Noticias error: ${result.message}`;
                    resultDiv.className = 'result error';
                }
            } catch (error) {
                resultDiv.innerHTML = `❌ Error de conexión: ${error.message}`;
                resultDiv.className = 'result error';
            }
        }

        async function testBlogAPI() {
            const resultDiv = document.getElementById('result-blog');
            resultDiv.innerHTML = '🔄 Probando...';
            
            try {
                const response = await fetch('api/blog-crud.php');
                const result = await response.json();
                
                if (result.success) {
                    resultDiv.innerHTML = `✅ API Blog funcionando - ${result.data ? result.data.length : 0} posts encontrados`;
                    resultDiv.className = 'result success';
                } else {
                    resultDiv.innerHTML = `❌ API Blog error: ${result.message}`;
                    resultDiv.className = 'result error';
                }
            } catch (error) {
                resultDiv.innerHTML = `❌ Error de conexión: ${error.message}`;
                resultDiv.className = 'result error';
            }
        }

        async function testCategoriasAPI() {
            const resultDiv = document.getElementById('result-categorias');
            resultDiv.innerHTML = '🔄 Probando...';
            
            try {
                const response = await fetch('api/categorias-crud.php');
                const result = await response.json();
                
                if (result.success) {
                    resultDiv.innerHTML = `✅ API Categorías funcionando - ${result.data ? result.data.length : 0} categorías encontradas`;
                    resultDiv.className = 'result success';
                } else {
                    resultDiv.innerHTML = `❌ API Categorías error: ${result.message}`;
                    resultDiv.className = 'result error';
                }
            } catch (error) {
                resultDiv.innerHTML = `❌ Error de conexión: ${error.message}`;
                resultDiv.className = 'result error';
            }
        }

        async function testBannersAPI() {
            const resultDiv = document.getElementById('result-banners');
            resultDiv.innerHTML = '🔄 Probando...';
            
            try {
                const response = await fetch('api/banners-crud.php');
                const result = await response.json();
                
                if (result.success) {
                    resultDiv.innerHTML = `✅ API Banners funcionando - ${result.data ? result.data.length : 0} banners encontrados`;
                    resultDiv.className = 'result success';
                } else {
                    resultDiv.innerHTML = `❌ API Banners error: ${result.message}`;
                    resultDiv.className = 'result error';
                }
            } catch (error) {
                resultDiv.innerHTML = `❌ Error de conexión: ${error.message}`;
                resultDiv.className = 'result error';
            }
        }

        function testDashboardFunctions() {
            const resultDiv = document.getElementById('result-dashboard');
            resultDiv.innerHTML = '🔄 Probando funciones del dashboard...';
            
            // Verificar si las funciones están definidas
            if (typeof editarNoticia === 'function') {
                resultDiv.innerHTML = '✅ Función editarNoticia disponible';
                resultDiv.className = 'result success';
            } else {
                resultDiv.innerHTML = '❌ Función editarNoticia NO disponible';
                resultDiv.className = 'result error';
            }
        }
    </script>
</body>
</html>
