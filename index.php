<?php
// Incluir configuración de base de datos
require_once 'config/database.php';

// Verificar si la base de datos está funcionando
try {
    $db = new Database();
    $connection = $db->getConnection();
    $dbStatus = "✅ Conectado";
    
    // Obtener datos para mostrar en la página de inicio
    $stmt = $connection->query("SELECT COUNT(*) as total FROM noticias WHERE activo = true");
    $totalNoticias = $stmt->fetchColumn();
    
    $stmt = $connection->query("SELECT COUNT(*) as total FROM blog_posts WHERE activo = true");
    $totalBlogPosts = $stmt->fetchColumn();
    
    $stmt = $connection->query("SELECT COUNT(*) as total FROM categorias");
    $totalCategorias = $stmt->fetchColumn();
    
    // Obtener últimas noticias
    $stmt = $connection->query("SELECT n.*, c.nombre as categoria_nombre 
                               FROM noticias n 
                               LEFT JOIN categorias c ON n.categoria_id = c.id 
                               WHERE n.activo = true 
                               ORDER BY n.fecha_creacion DESC 
                               LIMIT 3");
    $ultimasNoticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener últimas entradas del blog
    $stmt = $connection->query("SELECT * FROM blog_posts 
                               WHERE activo = true 
                               ORDER BY fecha_creacion DESC 
                               LIMIT 3");
    $ultimasEntradasBlog = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $dbStatus = "❌ Error: " . $e->getMessage();
    $totalNoticias = 0;
    $totalBlogPosts = 0;
    $totalCategorias = 0;
    $ultimasNoticias = [];
    $ultimasEntradasBlog = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeaiNews - Noticias Tecnológicas</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        h1 {
            color: #333;
            font-size: 3em;
            margin: 0;
            background: linear-gradient(45deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .subtitle {
            color: #666;
            font-size: 1.2em;
            margin: 10px 0;
        }
        
        .status-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9em;
            margin: 10px 5px;
        }
        
        .nav-menu {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .nav-menu a {
            display: inline-block;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
            margin: 8px 5px;
        }
        
        .nav-menu a:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .main-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }
        
        .card h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.5em;
        }
        
        .btn {
            display: inline-block;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
            margin: 10px 5px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .content-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .content-section h3 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.8em;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .content-item {
            background: rgba(102, 126, 234, 0.1);
            padding: 20px;
            border-radius: 15px;
            border: 1px solid rgba(102, 126, 234, 0.2);
            transition: transform 0.3s ease;
        }
        
        .content-item:hover {
            transform: translateY(-3px);
        }
        
        .content-item h4 {
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .content-item p {
            color: #666;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .content-item .meta {
            font-size: 0.9em;
            color: #999;
            margin-bottom: 15px;
        }
        
        .content-item .btn-small {
            background: #667eea;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9em;
            transition: all 0.3s ease;
        }
        
        .content-item .btn-small:hover {
            background: #5a6fd8;
            transform: translateY(-1px);
        }
        
        .tech-info {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .tech-info h3 {
            color: #333;
            margin-bottom: 20px;
        }
        
        .tech-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .tech-item {
            background: rgba(102, 126, 234, 0.1);
            padding: 15px;
            border-radius: 15px;
            border: 1px solid rgba(102, 126, 234, 0.2);
        }
        
        .tech-item strong {
            color: #667eea;
        }
        
        footer {
            text-align: center;
            color: white;
            margin-top: 40px;
            padding: 20px;
        }
        
        .hero-icon {
            font-size: 4em;
            margin-bottom: 20px;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .stat-item {
            background: rgba(102, 126, 234, 0.1);
            padding: 15px;
            border-radius: 15px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="hero-icon">🚀</div>
            <h1>CodeaiNews</h1>
            <p class="subtitle">Tu fuente de noticias tecnológicas</p>
            <div class="status-badge">✅ Heroku</div>
            <div class="status-badge">✅ PostgreSQL</div>
            <div class="status-badge">✅ PHP 8.4.11</div>
            
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-number"><?php echo $totalNoticias; ?></div>
                    <div class="stat-label">Noticias</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo $totalBlogPosts; ?></div>
                    <div class="stat-label">Entradas Blog</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo $totalCategorias; ?></div>
                    <div class="stat-label">Categorías</div>
                </div>
            </div>
        </header>

        <div class="nav-menu">
            <a href="index.php">🏠 Inicio</a>
            <a href="blog.php">📝 Blog</a>
            <a href="dashboard.php">⚙️ Panel Admin</a>
            <a href="contacto.php">📧 Contacto</a>
            <a href="test-api.php">🧪 Probar API</a>
            <a href="diagnostico.php">🔍 Diagnóstico</a>
        </div>

        <div class="main-content">
            <div class="card">
                <h3>🔐 Administración</h3>
                <p>Accede al panel de control para gestionar tu sitio</p>
                <a href="login.php" class="btn">Iniciar Sesión</a>
                <a href="dashboard.php" class="btn">Dashboard</a>
            </div>

            <div class="card">
                <h3>📝 Contenido</h3>
                <p>Gestiona noticias, blog y contenido de tu sitio</p>
                <a href="blog.php" class="btn">Ver Blog</a>
                <a href="entrada-blog.php" class="btn">Nueva Entrada</a>
            </div>

            <div class="card">
                <h3>📧 Comunicación</h3>
                <p>Mantén contacto con tus usuarios y lectores</p>
                <a href="contacto.php" class="btn">Contacto</a>
                <a href="api/contacto.php" class="btn">API Contacto</a>
            </div>
        </div>

        <?php if (!empty($ultimasNoticias)): ?>
        <div class="content-section">
            <h3>📰 Últimas Noticias</h3>
            <div class="content-grid">
                <?php foreach ($ultimasNoticias as $noticia): ?>
                <div class="content-item">
                    <h4><?php echo htmlspecialchars($noticia['titulo']); ?></h4>
                    <div class="meta">
                        📅 <?php echo date('d/m/Y', strtotime($noticia['fecha_creacion'])); ?>
                        🏷️ <?php echo htmlspecialchars($noticia['categoria_nombre'] ?? 'Sin categoría'); ?>
                    </div>
                    <p><?php echo htmlspecialchars(substr($noticia['contenido'], 0, 150)) . '...'; ?></p>
                    <a href="noticia.php?id=<?php echo $noticia['id']; ?>" class="btn-small">Leer más</a>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="noticias.php" class="btn">Ver Todas las Noticias</a>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($ultimasEntradasBlog)): ?>
        <div class="content-section">
            <h3>📝 Últimas Entradas del Blog</h3>
            <div class="content-grid">
                <?php foreach ($ultimasEntradasBlog as $post): ?>
                <div class="content-item">
                    <h4><?php echo htmlspecialchars($post['titulo']); ?></h4>
                    <div class="meta">
                        📅 <?php echo date('d/m/Y', strtotime($post['fecha_creacion'])); ?>
                        🏷️ <?php echo htmlspecialchars($post['categoria'] ?? 'Sin categoría'); ?>
                    </div>
                    <p><?php echo htmlspecialchars(substr($post['excerpt'] ?: $post['contenido'], 0, 150)) . '...'; ?></p>
                    <a href="entrada-blog.php?slug=<?php echo $post['slug']; ?>" class="btn-small">Leer más</a>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="blog.php" class="btn">Ver Todo el Blog</a>
            </div>
        </div>
        <?php endif; ?>

        <div class="tech-info">
            <h3>🛠️ Información Técnica</h3>
            <div class="tech-grid">
                <div class="tech-item">
                    <strong>Plataforma:</strong><br>
                    Heroku
                </div>
                <div class="tech-item">
                    <strong>Base de Datos:</strong><br>
                    PostgreSQL
                </div>
                <div class="tech-item">
                    <strong>Estado BD:</strong><br>
                    <?php echo $dbStatus; ?>
                </div>
                <div class="tech-item">
                    <strong>PHP:</strong><br>
                    <?php echo phpversion(); ?>
                </div>
                <div class="tech-item">
                    <strong>Fecha:</strong><br>
                    <?php echo date('Y-m-d H:i:s'); ?>
                </div>
                <div class="tech-item">
                    <strong>URL:</strong><br>
                    <?php echo $_SERVER['HTTP_HOST']; ?>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 CodeaiNews - Desplegado exitosamente en Heroku</p>
        <p>🚀 Tu sitio está funcionando perfectamente en la nube</p>
    </footer>
</body>
</html>


