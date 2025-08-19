<?php
// Incluir configuración de base de datos
require_once 'config/database.php';

// Verificar si la base de datos está funcionando
try {
    $db = new Database();
    $connection = $db->getConnection();
    $dbStatus = "✅ Conectado";
} catch (Exception $e) {
    $dbStatus = "❌ Error: " . $e->getMessage();
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
        </header>

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


