<?php
session_start();
require_once 'config/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Cargar posts del blog
    $stmt = $pdo->query("SELECT * FROM blog_posts WHERE estado = 'publicado' ORDER BY fecha_publicacion DESC");
    $blogPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Cargar categorías
    $stmt = $pdo->query("SELECT * FROM categorias ORDER BY orden ASC");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // En caso de error, usar arrays vacíos
    $blogPosts = [];
    $categorias = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - CodeaiNews</title>
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
                <a href="blog.php" class="active">Blog</a>
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
    <main class="blog-main">
        <div class="container">
            <div class="blog-header">
                <h1>Blog</h1>
                <p>Artículos, tutoriales y análisis sobre Linux y software libre</p>
            </div>

            <!-- Filtros de Categoría -->
            <div class="categoria-filtros">
                <button class="categoria active" data-categoria="todas">Todas</button>
                <?php foreach ($categorias as $categoria): ?>
                    <button class="categoria" data-categoria="<?php echo htmlspecialchars($categoria['slug']); ?>">
                        <?php echo htmlspecialchars($categoria['nombre']); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Grid de Posts -->
            <div class="blog-grid">
                <?php if (!empty($blogPosts)): ?>
                    <?php foreach ($blogPosts as $post): ?>
                        <article class="blog-card" data-categoria="<?php echo htmlspecialchars($post['categoria']); ?>">
                            <div class="blog-imagen">
                                <img src="<?php echo htmlspecialchars($post['imagen']); ?>" alt="<?php echo htmlspecialchars($post['titulo']); ?>">
                            </div>
                            <div class="blog-content">
                                <span class="categoria-tag">
                                    <?php echo htmlspecialchars($post['categoria']); ?>
                                </span>
                                <h2>
                                    <a href="entrada-blog.php?id=<?php echo $post['id']; ?>">
                                        <?php echo htmlspecialchars($post['titulo']); ?>
                                    </a>
                                </h2>
                                <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
                                <div class="blog-meta">
                                    <span class="fecha"><?php echo date('d/m/Y', strtotime($post['fecha_publicacion'])); ?></span>
                                    <span class="autor"><?php echo htmlspecialchars($post['autor']); ?></span>
                                    <span class="vistas"><?php echo $post['vistas']; ?> vistas</span>
                                </div>
                                <div class="blog-tags">
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
                                <a href="entrada-blog.php?id=<?php echo $post['id']; ?>" class="btn-secondary">Leer más</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Posts por defecto si no hay en la BD -->
                    <article class="blog-card" data-categoria="tutoriales">
                        <div class="blog-imagen">
                            <img src="https://images.unsplash.com/photo-1518709268805-4e9042af2176?auto=format&fit=crop&w=400&q=80" alt="Instalación Ubuntu">
                        </div>
                        <div class="blog-content">
                            <span class="categoria-tag">Tutoriales</span>
                            <h2>
                                <a href="entrada-blog.php?id=1">
                                    Guía Completa: Instalación de Ubuntu 24.04 LTS
                                </a>
                            </h2>
                            <p>Una guía paso a paso para instalar Ubuntu 24.04 LTS en tu computadora, con consejos y trucos para una instalación exitosa.</p>
                            <div class="blog-meta">
                                <span class="fecha">15/01/2024</span>
                                <span class="autor">Admin CodeaiNews</span>
                                <span class="vistas">1,250 vistas</span>
                            </div>
                            <div class="blog-tags">
                                <span class="tag">ubuntu</span>
                                <span class="tag">instalacion</span>
                                <span class="tag">linux</span>
                                <span class="tag">tutorial</span>
                            </div>
                            <a href="entrada-blog.php?id=1" class="btn-secondary">Leer más</a>
                        </div>
                    </article>
                <?php endif; ?>
            </div>
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

    <script>
        // Filtros de categoría
        const categoriaBtns = document.querySelectorAll('.categoria');
        const blogCards = document.querySelectorAll('.blog-card');

        categoriaBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const categoria = btn.dataset.categoria;
                
                // Actualizar botones activos
                categoriaBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                // Filtrar posts del blog
                blogCards.forEach(card => {
                    if (categoria === 'todas' || card.dataset.categoria === categoria) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>





