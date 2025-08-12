<?php
session_start();
require_once 'config/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Cargar carrusel
    $stmt = $pdo->query("SELECT * FROM carrusel ORDER BY orden ASC");
    $carrusel = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Cargar noticias
    $stmt = $pdo->query("SELECT * FROM noticias ORDER BY fecha_publicacion DESC");
    $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Cargar categorías
    $stmt = $pdo->query("SELECT * FROM categorias ORDER BY orden ASC");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Cargar banners
    $stmt = $pdo->query("SELECT * FROM banners ORDER BY orden ASC");
    $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Cargar información del footer
    $stmt = $pdo->query("SELECT * FROM footer_items ORDER BY orden ASC");
    $footerItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // En caso de error, usar arrays vacíos
    $carrusel = [];
    $noticias = [];
    $categorias = [];
    $banners = [];
    $footerItems = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeaiNews - Noticias de Linux y Software Libre</title>
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
                <a href="#">Inicio</a>
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

    <!-- Carrusel Principal -->
    <section class="carrusel">
        <div class="carrusel-container">
            <?php if (!empty($carrusel)): ?>
                <?php foreach ($carrusel as $index => $item): ?>
                    <div class="carrusel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <img src="<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['titulo']); ?>">
                        <div class="carrusel-content">
                            <h2><?php echo htmlspecialchars($item['titulo']); ?></h2>
                            <p><?php echo htmlspecialchars($item['descripcion']); ?></p>
                            <a href="<?php echo htmlspecialchars($item['enlace']); ?>" class="btn-primary">Leer más</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Contenido por defecto si no hay carrusel -->
                <div class="carrusel-item active">
                    <img src="https://images.unsplash.com/photo-1518709268805-4e9042af2176?auto=format&fit=crop&w=1200&q=80" alt="Linux y Software Libre">
                    <div class="carrusel-content">
                        <h2>Bienvenido a CodeaiNews</h2>
                        <p>Tu fuente confiable de noticias sobre Linux, software libre y tecnología</p>
                        <a href="#" class="btn-primary">Explorar</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <button class="carrusel-btn prev">‹</button>
        <button class="carrusel-btn next">›</button>
        <div class="carrusel-dots">
            <?php if (!empty($carrusel)): ?>
                <?php for ($i = 0; $i < count($carrusel); $i++): ?>
                    <span class="dot <?php echo $i === 0 ? 'active' : ''; ?>" data-index="<?php echo $i; ?>"></span>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Espacios de Publicidad -->
    <section class="adsense-section">
        <div class="adsense-container">
            <div class="adsense-ready">
                <div class="placeholder-banner">Publicidad</div>
            </div>
            <div class="adsense-ready">
                <div class="placeholder-banner">Publicidad</div>
            </div>
        </div>
    </section>

    <!-- Sección de Noticias -->
    <section class="noticias-section">
        <div class="container">
            <h2>Últimas Noticias</h2>
            
            <!-- Filtros de Categoría -->
            <div class="categoria-filtros">
                <button class="categoria active" data-categoria="todas">Todas</button>
                <?php foreach ($categorias as $categoria): ?>
                    <button class="categoria" data-categoria="<?php echo htmlspecialchars($categoria['slug']); ?>">
                        <?php echo htmlspecialchars($categoria['nombre']); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Grid de Noticias -->
            <div class="noticias-grid">
                <?php if (!empty($noticias)): ?>
                    <?php foreach ($noticias as $noticia): ?>
                        <article class="noticia-card" data-categoria="<?php echo htmlspecialchars($noticia['categoria']); ?>">
                            <div class="noticia-imagen">
                                <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" alt="<?php echo htmlspecialchars($noticia['titulo']); ?>">
                            </div>
                            <div class="noticia-content">
                                <span class="categoria-tag" style="background-color: <?php echo htmlspecialchars($noticia['color_categoria'] ?? '#2196f3'); ?>">
                                    <?php echo htmlspecialchars($noticia['categoria']); ?>
                                </span>
                                <h3><?php echo htmlspecialchars($noticia['titulo']); ?></h3>
                                <p><?php echo htmlspecialchars($noticia['resumen']); ?></p>
                                <div class="noticia-meta">
                                    <span class="fecha"><?php echo date('d/m/Y', strtotime($noticia['fecha_publicacion'])); ?></span>
                                    <span class="autor"><?php echo htmlspecialchars($noticia['autor']); ?></span>
                                </div>
                                <a href="<?php echo htmlspecialchars($noticia['enlace']); ?>" class="btn-secondary">Leer más</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Noticias por defecto si no hay en la BD -->
                    <article class="noticia-card" data-categoria="actualidad">
                        <div class="noticia-imagen">
                            <img src="https://images.unsplash.com/photo-1518709268805-4e9042af2176?auto=format&fit=crop&w=400&q=80" alt="Linux Kernel 6.0">
                        </div>
                        <div class="noticia-content">
                            <span class="categoria-tag" style="background-color: #ff9800">Actualidad</span>
                            <h3>Linux Kernel 6.0: Nuevas características y mejoras</h3>
                            <p>Descubre las principales novedades del nuevo kernel de Linux y cómo afectan al rendimiento del sistema.</p>
                            <div class="noticia-meta">
                                <span class="fecha">15/01/2024</span>
                                <span class="autor">Admin CodeaiNews</span>
                            </div>
                            <a href="#" class="btn-secondary">Leer más</a>
                        </div>
                    </article>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Banners Rotativos -->
    <?php if (!empty($banners)): ?>
        <section class="banners-section">
            <div class="container">
                <div class="banners-grid">
                    <?php foreach ($banners as $banner): ?>
                        <div class="banner-item">
                            <img src="<?php echo htmlspecialchars($banner['imagen']); ?>" alt="<?php echo htmlspecialchars($banner['titulo']); ?>">
                            <div class="banner-content">
                                <h3><?php echo htmlspecialchars($banner['titulo']); ?></h3>
                                <p><?php echo htmlspecialchars($banner['descripcion']); ?></p>
                                <a href="<?php echo htmlspecialchars($banner['enlace']); ?>" class="btn-primary">Ver más</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <?php if (!empty($footerItems)): ?>
                    <?php foreach ($footerItems as $item): ?>
                        <div class="footer-section">
                            <h3><?php echo htmlspecialchars($item['titulo']); ?></h3>
                            <p><?php echo htmlspecialchars($item['contenido']); ?></p>
                            <?php if ($item['enlace']): ?>
                                <a href="<?php echo htmlspecialchars($item['enlace']); ?>"><?php echo htmlspecialchars($item['texto_enlace'] ?? 'Leer más'); ?></a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Footer por defecto -->
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
                        <a href="blog.php">Blog</a>
                    </div>
                <?php endif; ?>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 CodeaiNews. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        // Carrusel functionality
        let currentSlide = 0;
        const slides = document.querySelectorAll('.carrusel-item');
        const dots = document.querySelectorAll('.dot');
        const totalSlides = slides.length;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.toggle('active', i === index);
            });
            
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            showSlide(currentSlide);
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            showSlide(currentSlide);
        }

        // Event listeners
        document.querySelector('.next').addEventListener('click', nextSlide);
        document.querySelector('.prev').addEventListener('click', prevSlide);

        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentSlide = index;
                showSlide(currentSlide);
            });
        });

        // Auto-advance carrusel
        setInterval(nextSlide, 5000);

        // Filtros de categoría
        const categoriaBtns = document.querySelectorAll('.categoria');
        const noticiaCards = document.querySelectorAll('.noticia-card');

        categoriaBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const categoria = btn.dataset.categoria;
                
                // Actualizar botones activos
                categoriaBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                // Filtrar noticias
                noticiaCards.forEach(card => {
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


