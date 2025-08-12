<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

// Obtener datos de la base de datos
try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Obtener categorías
    $stmt = $pdo->query("SELECT * FROM categorias ORDER BY orden");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener noticias
    $stmt = $pdo->query("SELECT n.*, c.nombre as categoria_nombre FROM noticias n LEFT JOIN categorias c ON n.categoria = c.slug ORDER BY n.fecha_creacion DESC");
    $noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener banners
    $stmt = $pdo->query("SELECT * FROM banners ORDER BY orden");
    $banners = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener posts del blog
    $stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY fecha_publicacion DESC");
    $blogPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener mensajes de contacto
    $stmt = $pdo->query("SELECT * FROM contactos ORDER BY fecha_creacion DESC");
    $contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Obtener información de contacto
    $stmt = $pdo->query("SELECT * FROM info_contacto LIMIT 1");
    $infoContacto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Obtener elementos del footer
    $stmt = $pdo->query("SELECT * FROM footer_items ORDER BY orden");
    $footerItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Error de base de datos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CodeaiNews</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: #1a237e;
            color: white;
            padding: 1rem;
        }
        
        .sidebar h2 {
            margin: 0 0 2rem 0;
            text-align: center;
            color: #ff9800;
        }
        
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-nav li {
            margin-bottom: 0.5rem;
        }
        
        .sidebar-nav a {
            display: block;
            padding: 0.8rem 1rem;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            transition: background 0.3s;
        }
        
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: #ff9800;
            color: #1a237e;
        }
        
        .main-content {
            flex: 1;
            padding: 2rem;
            background: #f5f5f5;
        }
        
        .dashboard-section {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .section-actions {
            margin-bottom: 1.5rem;
        }
        
        .section-actions button {
            background: #1a237e;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 0.5rem;
        }
        
        .section-actions button:hover {
            background: #0d47a1;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        th, td {
            padding: 0.8rem;
            border: 1px solid #ddd;
            text-align: left;
        }
        
        th {
            background: #f4f6fa;
            font-weight: bold;
        }
        
        .btn-edit, .btn-delete {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 0.5rem;
        }
        
        .btn-edit {
            background: #2196f3;
            color: white;
        }
        
        .btn-delete {
            background: #f44336;
            color: white;
        }
        
        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .status-publicado {
            background: #e8f5e8;
            color: #2e7d32;
        }
        
        .status-borrador {
            background: #fff3e0;
            color: #f57c00;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>🏠 CodeaiNews</h2>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#inicio" class="nav-link active" data-section="inicio">🏠 Inicio</a></li>
                    <li><a href="#noticias" class="nav-link" data-section="noticias">📰 Noticias</a></li>
                    <li><a href="#banners" class="nav-link" data-section="banners">🖼️ Banners</a></li>
                    <li><a href="#categorias" class="nav-link" data-section="categorias">🏷️ Categorías</a></li>
                    <li><a href="#blog" class="nav-link" data-section="blog">✍️ Blog</a></li>
                    <li><a href="#contacto" class="nav-link" data-section="contacto">📞 Contacto</a></li>
                    <li><a href="#footer" class="nav-link" data-section="footer">🔗 Footer</a></li>
                    <li><a href="logout.php" style="color: #ff9800;">🚪 Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Sección Inicio -->
            <section id="inicio" class="dashboard-section">
                <h1>🎯 Panel de Control - CodeaiNews</h1>
                <p>Bienvenido al panel de administración. Selecciona una sección del menú lateral para gestionar el contenido.</p>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-top: 2rem;">
                    <div style="background: #e3f2fd; padding: 1.5rem; border-radius: 8px; text-align: center;">
                        <h3>📰 Noticias</h3>
                        <p style="font-size: 2rem; margin: 0; color: #1976d2;"><?php echo count($noticias); ?></p>
                        <small>Total de noticias</small>
                    </div>
                    
                    <div style="background: #e8f5e8; padding: 1.5rem; border-radius: 8px; text-align: center;">
                        <h3>✍️ Blog</h3>
                        <p style="font-size: 2rem; margin: 0; color: #2e7d32;"><?php echo count($blogPosts); ?></p>
                        <small>Total de entradas</small>
                    </div>
                    
                    <div style="background: #fff3e0; padding: 1.5rem; border-radius: 8px; text-align: center;">
                        <h3>📞 Contacto</h3>
                        <p style="font-size: 2rem; margin: 0; color: #f57c00;"><?php echo count($contactos); ?></p>
                        <small>Mensajes recibidos</small>
                    </div>
                    
                    <div style="background: #fce4ec; padding: 1.5rem; border-radius: 8px; text-align: center;">
                        <h3>🏷️ Categorías</h3>
                        <p style="font-size: 2rem; margin: 0; color: #c2185b;"><?php echo count($categorias); ?></p>
                        <small>Total de categorías</small>
                    </div>
                </div>
            </section>

            <!-- Sección Noticias -->
            <section id="noticias" class="dashboard-section" style="display: none;">
                <h3>📰 Administrar Noticias</h3>
                <div class="section-actions">
                    <button id="add-noticia-btn">➕ Añadir Noticia</button>
                </div>
                
                <div id="noticia-form-container" style="display: none; margin: 1rem 0; padding: 1.5rem; background: #f9f9f9; border-radius: 6px;">
                    <form id="noticia-form" method="POST" action="api/noticias.php" enctype="multipart/form-data">
                        <input type="hidden" id="noticia-id" name="id">
                        <div class="form-group">
                            <label>Título *</label>
                            <input type="text" id="noticia-titulo" name="titulo" required>
                        </div>
                        <div class="form-group">
                            <label>Descripción *</label>
                            <textarea id="noticia-descripcion" name="descripcion" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Categoría *</label>
                            <select id="noticia-categoria" name="categoria" required>
                                <option value="">Seleccionar categoría</option>
                                <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['slug']); ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Imagen</label>
                            <input type="file" id="noticia-imagen" name="imagen" accept="image/*">
                        </div>
                        <button type="submit">💾 Guardar</button>
                        <button type="button" id="cancelar-noticia">❌ Cancelar</button>
                    </form>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Título</th>
                            <th>Categoría</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($noticias)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem; color: #666;">
                                <div style="margin-bottom: 1rem;">📰</div>
                                <strong>No hay noticias configuradas</strong><br>
                                <span style="font-size: 0.9rem;">Añade noticias para la página principal</span>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($noticias as $noticia): ?>
                        <tr>
                            <td>
                                <?php if ($noticia['imagen']): ?>
                                <img src="<?php echo htmlspecialchars($noticia['imagen']); ?>" alt="img" style="max-width: 60px; max-height: 40px; border-radius: 4px;">
                                <?php else: ?>
                                <span style="color: #999;">Sin imagen</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($noticia['titulo']); ?></strong></td>
                            <td><span style="background: #e3f2fd; padding: 0.2rem 0.5rem; border-radius: 3px; font-size: 0.8rem;"><?php echo htmlspecialchars($noticia['categoria_nombre']); ?></span></td>
                            <td><?php echo date('d/m/Y', strtotime($noticia['fecha_creacion'])); ?></td>
                            <td>
                                <button class="btn-edit" onclick="editarNoticia(<?php echo $noticia['id']; ?>)">✏️ Editar</button>
                                <button class="btn-delete" onclick="eliminarNoticia(<?php echo $noticia['id']; ?>)">🗑️ Eliminar</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

            <!-- Sección Banners -->
            <section id="banners" class="dashboard-section" style="display: none;">
                <h3>🖼️ Administrar Banners</h3>
                <div class="section-actions">
                    <button id="add-banner-btn">➕ Añadir Banner</button>
                </div>
                
                <div id="banner-form-container" style="display: none; margin: 1rem 0; padding: 1.5rem; background: #f9f9f9; border-radius: 6px;">
                    <form id="banner-form" method="POST" action="api/banners.php">
                        <input type="hidden" id="banner-id" name="id">
                        <div class="form-group">
                            <label>Texto del Banner *</label>
                            <input type="text" id="banner-texto" name="texto" required>
                        </div>
                        <div class="form-group">
                            <label>Posición *</label>
                            <select id="banner-posicion" name="posicion" required>
                                <option value="">Seleccionar posición</option>
                                <option value="izquierdo">Banner Izquierdo</option>
                                <option value="derecho">Banner Derecho</option>
                                <option value="intermedio">Banner Intermedio</option>
                                <option value="footer">Banner Footer</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Duración (segundos)</label>
                            <input type="number" id="banner-duracion" name="duracion" value="3" min="1" max="10">
                        </div>
                        <button type="submit">💾 Guardar</button>
                        <button type="button" id="cancelar-banner">❌ Cancelar</button>
                    </form>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Texto</th>
                            <th>Posición</th>
                            <th>Duración</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($banners)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 2rem; color: #666;">
                                <div style="margin-bottom: 1rem;">🖼️</div>
                                <strong>No hay banners configurados</strong><br>
                                <span style="font-size: 0.9rem;">Añade banners para la página principal</span>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($banners as $banner): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($banner['texto']); ?></strong></td>
                            <td><?php echo htmlspecialchars($banner['posicion']); ?></td>
                            <td><?php echo $banner['duracion']; ?>s</td>
                            <td>
                                <button class="btn-edit" onclick="editarBanner(<?php echo $banner['id']; ?>)">✏️ Editar</button>
                                <button class="btn-delete" onclick="eliminarBanner(<?php echo $banner['id']; ?>)">🗑️ Eliminar</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

            <!-- Sección Categorías -->
            <section id="categorias" class="dashboard-section" style="display: none;">
                <h3>🏷️ Administrar Categorías</h3>
                <div class="section-actions">
                    <button id="add-categoria-btn">➕ Añadir Categoría</button>
                </div>
                
                <div id="categoria-form-container" style="display: none; margin: 1rem 0; padding: 1.5rem; background: #f9f9f9; border-radius: 6px;">
                    <form id="categoria-form" method="POST" action="api/categorias.php">
                        <input type="hidden" id="categoria-id" name="id">
                        <div class="form-group">
                            <label>Nombre *</label>
                            <input type="text" id="categoria-nombre" name="nombre" required>
                        </div>
                        <div class="form-group">
                            <label>Color</label>
                            <input type="color" id="categoria-color" name="color" value="#ff9800">
                        </div>
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea id="categoria-descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                        <button type="submit">💾 Guardar</button>
                        <button type="button" id="cancelar-categoria">❌ Cancelar</button>
                    </form>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Color</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($categoria['nombre']); ?></strong></td>
                            <td>
                                <div style="width: 30px; height: 20px; background: <?php echo htmlspecialchars($categoria['color']); ?>; border-radius: 3px; border: 1px solid #ddd;"></div>
                                <span style="margin-left: 0.5rem; font-size: 0.8rem; color: #666;"><?php echo htmlspecialchars($categoria['color']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($categoria['descripcion']); ?></td>
                            <td>
                                <button class="btn-edit" onclick="editarCategoria(<?php echo $categoria['id']; ?>)">✏️ Editar</button>
                                <button class="btn-delete" onclick="eliminarCategoria(<?php echo $categoria['id']; ?>)">🗑️ Eliminar</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <!-- Sección Blog -->
            <section id="blog" class="dashboard-section" style="display: none;">
                <h3>✍️ Administrar Blog</h3>
                <div class="section-actions">
                    <button id="add-blog-post-btn">✍️ Crear Nueva Entrada</button>
                </div>
                
                <div id="blog-form-container" style="display: none; margin: 1rem 0; padding: 1.5rem; background: #f9f9f9; border-radius: 6px;">
                    <form id="blog-form" method="POST" action="api/blog.php" enctype="multipart/form-data">
                        <input type="hidden" id="blog-id" name="id">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Título *</label>
                                <input type="text" id="blog-titulo" name="titulo" required>
                            </div>
                            <div class="form-group">
                                <label>Categoría *</label>
                                <select id="blog-categoria" name="categoria" required>
                                    <option value="">Seleccionar categoría</option>
                                    <?php foreach ($categorias as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat['slug']); ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Autor</label>
                                <input type="text" id="blog-autor" name="autor" value="Admin CodeaiNews">
                            </div>
                            <div class="form-group">
                                <label>Fecha de Publicación</label>
                                <input type="date" id="blog-fecha" name="fecha_publicacion">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Resumen/Excerpt *</label>
                            <textarea id="blog-excerpt" name="excerpt" rows="3" required placeholder="Resumen corto del post (importante para SEO)"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Contenido Completo *</label>
                            <textarea id="blog-contenido" name="contenido" rows="8" required placeholder="Contenido completo del post. Puedes usar HTML básico."></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Imagen Destacada</label>
                            <input type="file" id="blog-imagen" name="imagen" accept="image/*">
                        </div>
                        
                        <div class="form-group">
                            <label>Etiquetas (SEO) *</label>
                            <input type="text" id="blog-etiquetas" name="etiquetas" required placeholder="etiqueta1, etiqueta2, etiqueta3 (separadas por comas)">
                            <small style="color: #666;">Las etiquetas son cruciales para SEO. Usa palabras clave relevantes.</small>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>URL Amigable (Slug)</label>
                                <input type="text" id="blog-slug" name="slug" placeholder="url-amigable-del-post">
                                <small style="color: #666;">Se generará automáticamente si lo dejas vacío.</small>
                            </div>
                            <div class="form-group">
                                <label>Estado</label>
                                <select id="blog-estado" name="estado">
                                    <option value="borrador">Borrador</option>
                                    <option value="publicado">Publicado</option>
                                    <option value="programado">Programado</option>
                                </select>
                            </div>
                        </div>
                        
                        <div style="margin-top: 1.5rem;">
                            <button type="submit" style="background: #1a237e; color: #fff; border: none; padding: 0.8rem 1.5rem; border-radius: 6px; cursor: pointer;">💾 Guardar Entrada</button>
                            <button type="button" id="cancelar-blog-post" style="margin-left: 0.5rem; background: #f44336; color: #fff; border: none; padding: 0.8rem 1.5rem; border-radius: 6px; cursor: pointer;">❌ Cancelar</button>
                        </div>
                    </form>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Imagen</th>
                            <th>Título</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th>Etiquetas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($blogPosts)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: #666;">
                                <div style="margin-bottom: 1rem;">📝</div>
                                <strong>No hay entradas en el blog</strong><br>
                                <span style="font-size: 0.9rem;">Crea la primera entrada para comenzar</span>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($blogPosts as $post): ?>
                        <tr>
                            <td>
                                <?php if ($post['imagen']): ?>
                                <img src="<?php echo htmlspecialchars($post['imagen']); ?>" alt="img" style="max-width: 60px; max-height: 40px; border-radius: 4px;">
                                <?php else: ?>
                                <span style="color: #999;">Sin imagen</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($post['titulo']); ?></strong><br>
                                <small style="color: #666;"><?php echo htmlspecialchars(substr($post['excerpt'], 0, 50)); ?>...</small>
                            </td>
                            <td>
                                <span style="background: #e3f2fd; padding: 0.2rem 0.5rem; border-radius: 3px; font-size: 0.8rem;"><?php echo htmlspecialchars($post['categoria']); ?></span>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $post['estado']; ?>"><?php echo ucfirst($post['estado']); ?></span>
                            </td>
                            <td>
                                <small><?php echo htmlspecialchars($post['etiquetas']); ?></small>
                            </td>
                            <td>
                                <button class="btn-edit" onclick="editarBlogPost(<?php echo $post['id']; ?>)">✏️ Editar</button>
                                <button class="btn-delete" onclick="eliminarBlogPost(<?php echo $post['id']; ?>)">🗑️ Eliminar</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

            <!-- Sección Contacto -->
            <section id="contacto" class="dashboard-section" style="display: none;">
                <h3>📞 Administrar Contacto</h3>
                
                <!-- Información de Contacto -->
                <div style="background: #e8f5e8; border: 1px solid #c8e6c9; padding: 1rem; border-radius: 6px; margin: 1rem 0;">
                    <h4 style="margin: 0 0 1rem 0; color: #2e7d32;">📞 Información de Contacto</h4>
                    <div class="section-actions">
                        <button id="edit-info-contacto-btn">✏️ Editar Información</button>
                    </div>
                </div>
                
                <!-- Mensajes Recibidos -->
                <h4>📨 Mensajes Recibidos</h4>
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Asunto</th>
                            <th>Mensaje</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($contactos)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: #666;">
                                <div style="margin-bottom: 1rem;">📨</div>
                                <strong>No hay mensajes de contacto</strong><br>
                                <span style="font-size: 0.9rem;">Los mensajes del formulario aparecerán aquí</span>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($contactos as $contacto): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($contacto['fecha_creacion'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($contacto['nombre']); ?></strong></td>
                            <td><?php echo htmlspecialchars($contacto['email']); ?></td>
                            <td><?php echo htmlspecialchars($contacto['asunto']); ?></td>
                            <td><?php echo htmlspecialchars(substr($contacto['mensaje'], 0, 100)); ?>...</td>
                            <td>
                                <button class="btn-edit" onclick="verMensaje(<?php echo $contacto['id']; ?>)">👁️ Ver</button>
                                <button class="btn-delete" onclick="eliminarMensaje(<?php echo $contacto['id']; ?>)">🗑️ Eliminar</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

            <!-- Sección Footer -->
            <section id="footer" class="dashboard-section" style="display: none;">
                <h3>🔗 Administrar Footer</h3>
                <div class="section-actions">
                    <button id="add-footer-item-btn">➕ Añadir Elemento</button>
                </div>
                
                <div id="footer-form-container" style="display: none; margin: 1rem 0; padding: 1.5rem; background: #f9f9f9; border-radius: 6px;">
                    <form id="footer-form" method="POST" action="api/footer.php">
                        <input type="hidden" id="footer-id" name="id">
                        <div class="form-group">
                            <label>Título *</label>
                            <input type="text" id="footer-titulo" name="titulo" required>
                        </div>
                        <div class="form-group">
                            <label>Enlace</label>
                            <input type="url" id="footer-enlace" name="enlace" placeholder="https://ejemplo.com">
                        </div>
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea id="footer-descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                        <button type="submit">💾 Guardar</button>
                        <button type="button" id="cancelar-footer">❌ Cancelar</button>
                    </form>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Enlace</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($footerItems)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 2rem; color: #666;">
                                <div style="margin-bottom: 1rem;">🔗</div>
                                <strong>No hay elementos del footer configurados</strong><br>
                                <span style="font-size: 0.9rem;">Añade elementos para el pie de página</span>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($footerItems as $item): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($item['titulo']); ?></strong></td>
                            <td><?php echo $item['enlace'] ? htmlspecialchars($item['enlace']) : '<span style="color: #999;">Sin enlace</span>'; ?></td>
                            <td><?php echo htmlspecialchars($item['descripcion']); ?></td>
                            <td>
                                <button class="btn-edit" onclick="editarFooterItem(<?php echo $item['id']; ?>)">✏️ Editar</button>
                                <button class="btn-delete" onclick="eliminarFooterItem(<?php echo $item['id']; ?>)">🗑️ Eliminar</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </div>

    <script>
        // Navegación del sidebar
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remover clase active de todos los enlaces
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                
                // Agregar clase active al enlace clickeado
                this.classList.add('active');
                
                // Ocultar todas las secciones
                document.querySelectorAll('.dashboard-section').forEach(section => {
                    section.style.display = 'none';
                });
                
                // Mostrar la sección correspondiente
                const sectionId = this.getAttribute('data-section');
                document.getElementById(sectionId).style.display = 'block';
            });
        });

        // Funciones para formularios
        function mostrarFormulario(containerId) {
            document.getElementById(containerId).style.display = 'block';
        }

        function ocultarFormulario(containerId) {
            document.getElementById(containerId).style.display = 'none';
        }

        // Event listeners para botones
        document.getElementById('add-noticia-btn')?.addEventListener('click', () => {
            mostrarFormulario('noticia-form-container');
        });

        document.getElementById('cancelar-noticia')?.addEventListener('click', () => {
            ocultarFormulario('noticia-form-container');
        });

        document.getElementById('add-banner-btn')?.addEventListener('click', () => {
            mostrarFormulario('banner-form-container');
        });

        document.getElementById('cancelar-banner')?.addEventListener('click', () => {
            ocultarFormulario('banner-form-container');
        });

        document.getElementById('add-categoria-btn')?.addEventListener('click', () => {
            mostrarFormulario('categoria-form-container');
        });

        document.getElementById('cancelar-categoria')?.addEventListener('click', () => {
            ocultarFormulario('categoria-form-container');
        });

        document.getElementById('add-blog-post-btn')?.addEventListener('click', () => {
            mostrarFormulario('blog-form-container');
        });

        document.getElementById('cancelar-blog-post')?.addEventListener('click', () => {
            ocultarFormulario('blog-form-container');
        });

        document.getElementById('add-footer-item-btn')?.addEventListener('click', () => {
            mostrarFormulario('footer-form-container');
        });

        document.getElementById('cancelar-footer')?.addEventListener('click', () => {
            ocultarFormulario('footer-form-container');
        });

        // Funciones CRUD para Noticias
        async function editarNoticia(id) {
            try {
                const response = await fetch(`api/noticias.php?id=${id}`);
                const noticia = await response.json();
                
                if (response.ok) {
                    // Llenar el formulario con los datos de la noticia
                    document.getElementById('noticia-titulo').value = noticia.titulo;
                    document.getElementById('noticia-resumen').value = noticia.resumen;
                    document.getElementById('noticia-contenido').value = noticia.contenido;
                    document.getElementById('noticia-imagen').value = noticia.imagen;
                    document.getElementById('noticia-categoria').value = noticia.categoria;
                    document.getElementById('noticia-autor').value = noticia.autor;
                    document.getElementById('noticia-enlace').value = noticia.enlace;
                    
                    // Cambiar el botón a modo edición
                    const btnCrear = document.querySelector('#formulario-noticia button[type="submit"]');
                    btnCrear.textContent = 'Actualizar Noticia';
                    btnCrear.onclick = () => actualizarNoticia(id);
                    
                    mostrarFormulario('noticia-form-container');
                } else {
                    alert('Error al cargar la noticia: ' + noticia.error);
                }
            } catch (error) {
                alert('Error al cargar la noticia: ' + error.message);
            }
        }

        async function actualizarNoticia(id) {
            const formData = new FormData(document.getElementById('formulario-noticia'));
            const noticia = {
                titulo: formData.get('titulo'),
                resumen: formData.get('resumen'),
                contenido: formData.get('contenido'),
                imagen: formData.get('imagen'),
                categoria: formData.get('categoria'),
                autor: formData.get('autor'),
                enlace: formData.get('enlace') || '#',
                estado: 'borrador',
                color_categoria: '#2196f3'
            };
            
            try {
                const response = await fetch(`api/noticias.php?id=${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(noticia)
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    alert('Noticia actualizada exitosamente');
                    location.reload(); // Recargar para mostrar cambios
                } else {
                    alert('Error: ' + result.error);
                }
            } catch (error) {
                alert('Error al actualizar la noticia: ' + error.message);
            }
        }

        async function eliminarNoticia(id) {
            if (confirm('¿Estás seguro de que quieres eliminar esta noticia?')) {
                try {
                    const response = await fetch(`api/noticias.php?id=${id}`, {
                        method: 'DELETE'
                    });
                    
                    const result = await response.json();
                    
                    if (response.ok) {
                        alert('Noticia eliminada exitosamente');
                        location.reload(); // Recargar para mostrar cambios
                    } else {
                        alert('Error: ' + result.error);
                    }
                } catch (error) {
                    alert('Error al eliminar la noticia: ' + error.message);
                }
            }
        }

        // Funciones CRUD para Banners
        async function editarBanner(id) {
            try {
                const response = await fetch(`api/banners.php?id=${id}`);
                const banner = await response.json();
                
                if (response.ok) {
                    // Llenar el formulario con los datos del banner
                    document.getElementById('banner-titulo').value = banner.titulo;
                    document.getElementById('banner-descripcion').value = banner.descripcion;
                    document.getElementById('banner-imagen').value = banner.imagen;
                    document.getElementById('banner-enlace').value = banner.enlace;
                    document.getElementById('banner-orden').value = banner.orden;
                    
                    // Cambiar el botón a modo edición
                    const btnCrear = document.querySelector('#formulario-banner button[type="submit"]');
                    btnCrear.textContent = 'Actualizar Banner';
                    btnCrear.onclick = () => actualizarBanner(id);
                    
                    mostrarFormulario('banner-form-container');
                } else {
                    alert('Error al cargar el banner: ' + banner.error);
                }
            } catch (error) {
                alert('Error al cargar el banner: ' + error.message);
            }
        }

        async function actualizarBanner(id) {
            const formData = new FormData(document.getElementById('formulario-banner'));
            const banner = {
                titulo: formData.get('titulo'),
                descripcion: formData.get('descripcion'),
                imagen: formData.get('imagen'),
                enlace: formData.get('enlace') || '#',
                orden: formData.get('orden') || 1,
                estado: 'activo'
            };
            
            try {
                const response = await fetch(`api/banners.php?id=${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(banner)
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    alert('Banner actualizado exitosamente');
                    location.reload();
                } else {
                    alert('Error: ' + result.error);
                }
            } catch (error) {
                alert('Error al actualizar el banner: ' + error.message);
            }
        }

        async function eliminarBanner(id) {
            if (confirm('¿Estás seguro de que quieres eliminar este banner?')) {
                try {
                    const response = await fetch(`api/banners.php?id=${id}`, {
                        method: 'DELETE'
                    });
                    
                    const result = await response.json();
                    
                    if (response.ok) {
                        alert('Banner eliminado exitosamente');
                        location.reload();
                    } else {
                        alert('Error: ' + result.error);
                    }
                } catch (error) {
                    alert('Error al eliminar el banner: ' + error.message);
                }
            }
        }

        // Funciones CRUD para Categorías
        async function editarCategoria(id) {
            try {
                const response = await fetch(`api/categorias.php?id=${id}`);
                const categoria = await response.json();
                
                if (response.ok) {
                    // Llenar el formulario con los datos de la categoría
                    document.getElementById('categoria-nombre').value = categoria.nombre;
                    document.getElementById('categoria-color').value = categoria.color;
                    document.getElementById('categoria-descripcion').value = categoria.descripcion;
                    document.getElementById('categoria-orden').value = categoria.orden;
                    
                    // Cambiar el botón a modo edición
                    const btnCrear = document.querySelector('#formulario-categoria button[type="submit"]');
                    btnCrear.textContent = 'Actualizar Categoría';
                    btnCrear.onclick = () => actualizarCategoria(id);
                    
                    mostrarFormulario('categoria-form-container');
                } else {
                    alert('Error al cargar la categoría: ' + categoria.error);
                }
            } catch (error) {
                alert('Error al cargar la categoría: ' + error.message);
            }
        }

        async function actualizarCategoria(id) {
            const formData = new FormData(document.getElementById('formulario-categoria'));
            const categoria = {
                nombre: formData.get('nombre'),
                color: formData.get('color'),
                descripcion: formData.get('descripcion'),
                orden: formData.get('orden') || 1
            };
            
            try {
                const response = await fetch(`api/categorias.php?id=${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(categoria)
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    alert('Categoría actualizada exitosamente');
                    location.reload();
                } else {
                    alert('Error: ' + result.error);
                }
            } catch (error) {
                alert('Error al actualizar la categoría: ' + error.message);
            }
        }

        async function eliminarCategoria(id) {
            if (confirm('¿Estás seguro de que quieres eliminar esta categoría?')) {
                try {
                    const response = await fetch(`api/categorias.php?id=${id}`, {
                        method: 'DELETE'
                    });
                    
                    const result = await response.json();
                    
                    if (response.ok) {
                        alert('Categoría eliminada exitosamente');
                        location.reload();
                    } else {
                        alert('Error: ' + result.error);
                    }
                } catch (error) {
                    alert('Error al eliminar la categoría: ' + error.message);
                }
            }
        }

        // Funciones CRUD para Blog Posts
        async function editarBlogPost(id) {
            try {
                const response = await fetch(`api/blog.php?id=${id}`);
                const post = await response.json();
                
                if (response.ok) {
                    // Llenar el formulario con los datos del post
                    document.getElementById('blog-titulo').value = post.titulo;
                    document.getElementById('blog-excerpt').value = post.excerpt;
                    document.getElementById('blog-contenido').value = post.contenido;
                    document.getElementById('blog-imagen').value = post.imagen;
                    document.getElementById('blog-categoria').value = post.categoria;
                    document.getElementById('blog-autor').value = post.autor;
                    document.getElementById('blog-etiquetas').value = post.etiquetas;
                    document.getElementById('blog-estado').value = post.estado;
                    document.getElementById('blog-popularidad').value = post.popularidad;
                    
                    // Cambiar el botón a modo edición
                    const btnCrear = document.querySelector('#formulario-blog button[type="submit"]');
                    btnCrear.textContent = 'Actualizar Post';
                    btnCrear.onclick = () => actualizarBlogPost(id);
                    
                    mostrarFormulario('blog-form-container');
                } else {
                    alert('Error al cargar el post: ' + post.error);
                }
            } catch (error) {
                alert('Error al cargar el post: ' + error.message);
            }
        }

        async function actualizarBlogPost(id) {
            const formData = new FormData(document.getElementById('formulario-blog'));
            const post = {
                titulo: formData.get('titulo'),
                excerpt: formData.get('excerpt'),
                contenido: formData.get('contenido'),
                imagen: formData.get('imagen'),
                categoria: formData.get('categoria'),
                autor: formData.get('autor'),
                etiquetas: formData.get('etiquetas'),
                estado: formData.get('estado'),
                popularidad: formData.get('popularidad') || 5.0
            };
            
            try {
                const response = await fetch(`api/blog.php?id=${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(post)
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    alert('Post actualizado exitosamente');
                    location.reload();
                } else {
                    alert('Error: ' + result.error);
                }
            } catch (error) {
                alert('Error al actualizar el post: ' + error.message);
            }
        }

        async function eliminarBlogPost(id) {
            if (confirm('¿Estás seguro de que quieres eliminar esta entrada del blog?')) {
                try {
                    const response = await fetch(`api/blog.php?id=${id}`, {
                        method: 'DELETE'
                    });
                    
                    const result = await response.json();
                    
                    if (response.ok) {
                        alert('Post eliminado exitosamente');
                        location.reload();
                    } else {
                        alert('Error: ' + result.error);
                    }
                } catch (error) {
                    alert('Error al eliminar el post: ' + error.message);
                }
            }
        }

        // Funciones CRUD para Mensajes de Contacto
        async function verMensaje(id) {
            try {
                const response = await fetch(`api/contacto.php?id=${id}`);
                const mensaje = await response.json();
                
                if (response.ok) {
                    alert(`Mensaje de: ${mensaje.nombre}\nEmail: ${mensaje.email}\nAsunto: ${mensaje.asunto}\n\nMensaje:\n${mensaje.mensaje}`);
                } else {
                    alert('Error al cargar el mensaje: ' + mensaje.error);
                }
            } catch (error) {
                alert('Error al cargar el mensaje: ' + error.message);
            }
        }

        async function eliminarMensaje(id) {
            if (confirm('¿Estás seguro de que quieres eliminar este mensaje?')) {
                try {
                    const response = await fetch(`api/contacto.php?id=${id}`, {
                        method: 'DELETE'
                    });
                    
                    const result = await response.json();
                    
                    if (response.ok) {
                        alert('Mensaje eliminado exitosamente');
                        location.reload();
                    } else {
                        alert('Error: ' + result.error);
                    }
                } catch (error) {
                    alert('Error al eliminar el mensaje: ' + error.message);
                }
            }
        }

        // Funciones CRUD para Footer Items
        async function editarFooterItem(id) {
            try {
                const response = await fetch(`api/footer.php?id=${id}`);
                const item = await response.json();
                
                if (response.ok) {
                    // Llenar el formulario con los datos del item
                    document.getElementById('footer-titulo').value = item.titulo;
                    document.getElementById('footer-contenido').value = item.contenido;
                    document.getElementById('footer-enlace').value = item.enlace;
                    document.getElementById('footer-texto-enlace').value = item.texto_enlace;
                    document.getElementById('footer-orden').value = item.orden;
                    
                    // Cambiar el botón a modo edición
                    const btnCrear = document.querySelector('#formulario-footer button[type="submit"]');
                    btnCrear.textContent = 'Actualizar Elemento';
                    btnCrear.onclick = () => actualizarFooterItem(id);
                    
                    mostrarFormulario('footer-form-container');
                } else {
                    alert('Error al cargar el elemento: ' + item.error);
                }
            } catch (error) {
                alert('Error al cargar el elemento: ' + error.message);
            }
        }

        async function actualizarFooterItem(id) {
            const formData = new FormData(document.getElementById('formulario-footer'));
            const item = {
                titulo: formData.get('titulo'),
                contenido: formData.get('contenido'),
                enlace: formData.get('enlace') || '#',
                texto_enlace: formData.get('texto_enlace') || 'Leer más',
                orden: formData.get('orden') || 1
            };
            
            try {
                const response = await fetch(`api/footer.php?id=${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(item)
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    alert('Elemento actualizado exitosamente');
                    location.reload();
                } else {
                    alert('Error: ' + result.error);
                }
            } catch (error) {
                alert('Error al actualizar el elemento: ' + error.message);
                }
            }

        async function eliminarFooterItem(id) {
            if (confirm('¿Estás seguro de que quieres eliminar este elemento del footer?')) {
                try {
                    const response = await fetch(`api/footer.php?id=${id}`, {
                        method: 'DELETE'
                    });
                    
                    const result = await response.json();
                    
                    if (response.ok) {
                        alert('Elemento eliminado exitosamente');
                        location.reload();
                    } else {
                        alert('Error: ' + result.error);
                    }
                } catch (error) {
                    alert('Error al eliminar el elemento: ' + error.message);
                }
            }
        }
    </script>
</body>
</html>
