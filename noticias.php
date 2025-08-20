<?php
require_once 'config/database.php';

try {
    $db = new Database();
    $connection = $db->getConnection();
    
    // Obtener categorías para el filtro
    $stmt = $connection->query("SELECT * FROM categorias ORDER BY nombre");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Filtro por categoría
    $categoria_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
    $where_clause = "WHERE n.activo = true";
    if ($categoria_id > 0) {
        $where_clause .= " AND n.categoria_id = " . $categoria_id;
    }
    
    // Obtener noticias
    $stmt = $connection->query("SELECT n.*, c.nombre as categoria_nombre, c.color as categoria_color 
                               FROM noticias n 
                               LEFT JOIN categorias c ON n.categoria_id = c.id 
                               $where_clause 
                               ORDER BY n.fecha_creacion DESC");
    $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error = $e->getMessage();
    $categorias = [];
    $noticias = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias - CodeaiNews</title>
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
        
        .filtros {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .filtros h3 {
            color: #333;
            margin-bottom: 20px;
        }
        
        .filtros select {
            padding: 10px 20px;
            border: 2px solid #667eea;
            border-radius: 25px;
            font-size: 16px;
            margin: 0 10px;
            background: white;
            color: #333;
        }
        
        .filtros button {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .filtros button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .noticias-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .noticia-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .noticia-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }
        
        .noticia-imagen {
            width: 100%;
            height: 200px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        
        .noticia-contenido {
            padding: 25px;
        }
        
        .noticia-categoria {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: bold;
            color: white;
            margin-bottom: 15px;
        }
        
        .noticia-titulo {
            color: #333;
            font-size: 1.4em;
            margin-bottom: 15px;
            line-height: 1.3;
        }
        
        .noticia-resumen {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .noticia-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #999;
            font-size: 0.9em;
        }
        
        .btn-leer {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: bold;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-leer:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        .sin-noticias {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 50px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .sin-noticias h3 {
            color: #333;
            margin-bottom: 20px;
        }
        
        .sin-noticias p {
            color: #666;
            margin-bottom: 30px;
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
        }
        
        .btn-volver:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📰 Noticias</h1>
            <p class="subtitle">Las últimas noticias tecnológicas</p>
        </header>

        <div class="nav-menu">
            <a href="index.php">🏠 Inicio</a>
            <a href="blog.php">📝 Blog</a>
            <a href="dashboard.php">⚙️ Panel Admin</a>
            <a href="contacto.php">📧 Contacto</a>
        </div>

        <div class="filtros">
            <h3>🔍 Filtrar por Categoría</h3>
            <form method="GET" action="noticias.php">
                <select name="categoria" onchange="this.form.submit()">
                    <option value="0">Todas las categorías</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['id']; ?>" 
                                <?php echo $categoria_id == $categoria['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($categoria['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Filtrar</button>
            </form>
        </div>

        <?php if (!empty($noticias)): ?>
            <div class="noticias-grid">
                <?php foreach ($noticias as $noticia): ?>
                    <div class="noticia-card">
                        <div class="noticia-imagen" style="background-image: url('<?php echo htmlspecialchars($noticia['imagen']); ?>')"></div>
                        <div class="noticia-contenido">
                            <div class="noticia-categoria" style="background-color: <?php echo htmlspecialchars($noticia['categoria_color'] ?? '#667eea'); ?>">
                                <?php echo htmlspecialchars($noticia['categoria_nombre'] ?? 'Sin categoría'); ?>
                            </div>
                            <h3 class="noticia-titulo"><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
                            <p class="noticia-resumen">
                                <?php echo htmlspecialchars(substr($noticia['contenido'], 0, 150)) . '...'; ?>
                            </p>
                            <div class="noticia-meta">
                                <span>📅 <?php echo date('d/m/Y', strtotime($noticia['fecha_creacion'])); ?></span>
                                <a href="noticia.php?id=<?php echo $noticia['id']; ?>" class="btn-leer">Leer más</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="sin-noticias">
                <h3>📭 No hay noticias disponibles</h3>
                <p>No se encontraron noticias en esta categoría o no hay noticias publicadas aún.</p>
                <a href="index.php" class="btn-volver">Volver al Inicio</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
