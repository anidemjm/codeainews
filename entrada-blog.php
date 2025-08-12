<?php
session_start();
require_once 'config/database.php';

$post = null;
$error = null;

if (isset($_GET['id'])) {
    try {
        $db = new Database();
        $pdo = $db->getConnection();
        
        $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ? AND estado = 'publicado'");
        $stmt->execute([$_GET['id']]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$post) {
            $error = "Post no encontrado";
        } else {
            // Incrementar vistas
            $stmt = $pdo->prepare("UPDATE blog_posts SET vistas = vistas + 1 WHERE id = ?");
            $stmt->execute([$_GET['id']]);
        }
        
    } catch (PDOException $e) {
        $error = "Error al cargar el post";
    }
} else {
    $error = "ID de post no especificado";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $post ? htmlspecialchars($post['titulo']) : 'Post no encontrado'; ?> - CodeaiNews</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-content">
            <div class="logo">
                <h1>CodeaiNews</h1>
                <p>Tu fuente de noticias de Linux y Software Libre</p>
            </div>
            <nav>
                <a href="index.php">Inicio</a>
                <a href="#">Actualidad</a>
                <a href="#">Afiliados</a>
                <a href="contacto.php">Contacto</a>
                <a href="blog.php">Blog</a>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="dashboard.php">Panel</a>
                    <a href="logout.php">Salir</a>
                <?php else: ?>
                    <a href="login.php">Acceso</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="entrada-blog-main">
        <div class="container">
            <?php if ($post && !$error): ?>
                <article class="entrada-blog">
                    <header class="entrada-header">
                        <span class="categoria-tag"><?php echo htmlspecialchars($post['categoria']); ?></span>
                        <h1><?php echo htmlspecialchars($post['titulo']); ?></h1>
                        <div class="entrada-meta">
                            <span class="fecha"><?php echo date('d/m/Y', strtotime($post['fecha_publicacion'])); ?></span>
                            <span class="autor"><?php echo htmlspecialchars($post['autor']); ?></span>
                            <span class="vistas"><?php echo $post['vistas']; ?> vistas</span>
                        </div>
                    </header>

                    <div class="entrada-imagen">
                        <img src="<?php echo htmlspecialchars($post['imagen']); ?>" alt="<?php echo htmlspecialchars($post['titulo']); ?>">
                    </div>

                    <div class="entrada-content">
                        <div class="entrada-excerpt">
                            <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
                        </div>

                        <div class="entrada-texto">
                            <?php echo nl2br(htmlspecialchars($post['contenido'])); ?>
                        </div>

                        <div class="entrada-tags">
                            <h3>Etiquetas:</h3>
                            <div class="tags-container">
                                <?php 
                                $etiquetas = explode(',', $post['etiquetas']);
                                foreach ($etiquetas as $etiqueta): 
                                    $etiqueta = trim($etiqueta);
                                    if (!empty($etiqueta)):
                                ?>
                                    <span class="tag"><?php echo htmlspecialchars($etiqueta); ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        </div>

                        <div class="entrada-stats">
                            <div class="stat">
                                <span class="stat-label">Popularidad:</span>
                                <span class="stat-value"><?php echo $post['popularidad']; ?>/10</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Estado:</span>
                                <span class="stat-value"><?php echo ucfirst($post['estado']); ?></span>
                            </div>
                        </div>
                    </div>

                    <footer class="entrada-footer">
                        <a href="blog.php" class="btn-secondary">← Volver al Blog</a>
                        <div class="social-share">
                            <span>Compartir:</span>
                            <a href="#" class="share-btn">Facebook</a>
                            <a href="#" class="share-btn">Twitter</a>
                            <a href="#" class="share-btn">LinkedIn</a>
                        </div>
                    </footer>
                </article>
            <?php else: ?>
                <div class="error-message">
                    <h1>Post no encontrado</h1>
                    <p><?php echo $error; ?></p>
                    <a href="blog.php" class="btn-primary">Volver al Blog</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Sobre CodeaiNews</h3>
                    <p>Tu fuente confiable de noticias sobre Linux, software libre y tecnología. Mantente informado sobre las últimas novedades del mundo open source.</p>
                </div>
                <div class="footer-section">
                    <h3>Contacto</h3>
                    <p>¿Tienes alguna pregunta o sugerencia? No dudes en contactarnos.</p>
                    <a href="contacto.php">Contactar</a>
                </div>
                <div class="footer-section">
                    <h3>Síguenos</h3>
                    <p>Mantente al día con nuestras últimas publicaciones y actualizaciones.</p>
                    <a href="index.php">Inicio</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 CodeaiNews. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>


