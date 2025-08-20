<?php
require_once 'config/database.php';

$noticia = null;
$noticiasRelacionadas = [];

try {
    $db = new Database();
    $connection = $db->getConnection();
    
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        
        // Obtener la noticia
        $stmt = $connection->prepare("SELECT n.*, c.nombre as categoria_nombre, c.color as categoria_color 
                                    FROM noticias n 
                                    LEFT JOIN categorias c ON n.categoria_id = c.id 
                                    WHERE n.id = ? AND n.activo = true");
        $stmt->execute([$id]);
        $noticia = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($noticia) {
            // Obtener noticias relacionadas (misma categoría)
            $stmt = $connection->prepare("SELECT n.*, c.nombre as categoria_nombre 
                                       FROM noticias n 
                                       LEFT JOIN categorias c ON n.categoria_id = c.id 
                                       WHERE n.categoria_id = ? AND n.id != ? AND n.activo = true 
                                       ORDER BY n.fecha_creacion DESC 
                                       LIMIT 3");
            $stmt->execute([$noticia['categoria_id'], $id]);
            $noticiasRelacionadas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

if (!$noticia) {
    header('Location: noticias.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($noticia['titulo']); ?> - CodeaiNews</title>
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
            max-width: 1000px;
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
            font-size: 2.5em;
            margin: 0;
            line-height: 1.2;
        }
        
        .subtitle {
            color: #666;
            font-size: 1.1em;
            margin: 10px 0;
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
        
        .noticia-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .noticia-imagen {
            width: 100%;
            height: 400px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        .noticia-contenido {
            padding: 40px;
        }
        
        .noticia-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .noticia-categoria {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
            color: white;
        }
        
        .noticia-fecha {
            color: #999;
            font-size: 0.9em;
        }
        
        .noticia-titulo {
            color: #333;
            font-size: 2.2em;
            margin-bottom: 25px;
            line-height: 1.3;
        }
        
        .noticia-texto {
            color: #444;
            line-height: 1.8;
            font-size: 1.1em;
            margin-bottom: 30px;
        }
        
        .noticias-relacionadas {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .noticias-relacionadas h3 {
            color: #333;
            margin-bottom: 25px;
            text-align: center;
            font-size: 1.8em;
        }
        
        .relacionadas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        
        .relacionada-card {
            background: rgba(102, 126, 234, 0.1);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(102, 126, 234, 0.2);
            transition: transform 0.3s ease;
        }
        
        .relacionada-card:hover {
            transform: translateY(-3px);
        }
        
        .relacionada-titulo {
            color: #667eea;
            font-size: 1.1em;
            margin-bottom: 10px;
            line-height: 1.3;
        }
        
        .relacionada-resumen {
            color: #666;
            font-size: 0.9em;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        
        .relacionada-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8em;
            color: #999;
        }
        
        .btn-leer {
            background: #667eea;
            color: white;
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8em;
            transition: all 0.3s ease;
        }
        
        .btn-leer:hover {
            background: #5a6fd8;
            transform: translateY(-1px);
        }
        
        .btn-volver {
            background: #667eea;
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
            display: inline-block;
            margin-top: 20px;
        }
        
        .btn-volver:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
        
        .sin-relacionadas {
            text-align: center;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📰 <?php echo htmlspecialchars($noticia['titulo']); ?></h1>
            <p class="subtitle">Noticia tecnológica</p>
        </header>

        <div class="nav-menu">
            <a href="index.php">🏠 Inicio</a>
            <a href="noticias.php">📰 Noticias</a>
            <a href="blog.php">📝 Blog</a>
            <a href="dashboard.php">⚙️ Panel Admin</a>
            <a href="contacto.php">📧 Contacto</a>
        </div>

        <div class="noticia-container">
            <div class="noticia-imagen" style="background-image: url('<?php echo htmlspecialchars($noticia['imagen']); ?>')"></div>
            <div class="noticia-contenido">
                <div class="noticia-meta">
                    <div class="noticia-categoria" style="background-color: <?php echo htmlspecialchars($noticia['categoria_color'] ?? '#667eea'); ?>">
                        <?php echo htmlspecialchars($noticia['categoria_nombre'] ?? 'Sin categoría'); ?>
                    </div>
                    <div class="noticia-fecha">
                        📅 <?php echo date('d/m/Y H:i', strtotime($noticia['fecha_creacion'])); ?>
                    </div>
                </div>
                
                <h2 class="noticia-titulo"><?php echo htmlspecialchars($noticia['titulo']); ?></h2>
                
                <div class="noticia-texto">
                    <?php echo nl2br(htmlspecialchars($noticia['contenido'])); ?>
                </div>
            </div>
        </div>

        <?php if (!empty($noticiasRelacionadas)): ?>
            <div class="noticias-relacionadas">
                <h3>📚 Noticias Relacionadas</h3>
                <div class="relacionadas-grid">
                    <?php foreach ($noticiasRelacionadas as $relacionada): ?>
                        <div class="relacionada-card">
                            <h4 class="relacionada-titulo"><?php echo htmlspecialchars($relacionada['titulo']); ?></h4>
                            <p class="relacionada-resumen">
                                <?php echo htmlspecialchars(substr($relacionada['contenido'], 0, 120)) . '...'; ?>
                            </p>
                            <div class="relacionada-meta">
                                <span>📅 <?php echo date('d/m/Y', strtotime($relacionada['fecha_creacion'])); ?></span>
                                <a href="noticia.php?id=<?php echo $relacionada['id']; ?>" class="btn-leer">Leer más</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="noticias-relacionadas">
                <h3>📚 Noticias Relacionadas</h3>
                <div class="sin-relacionadas">
                    <p>No hay noticias relacionadas en esta categoría.</p>
                </div>
            </div>
        <?php endif; ?>

        <div style="text-align: center;">
            <a href="noticias.php" class="btn-volver">← Volver a Noticias</a>
        </div>
    </div>
</body>
</html>
