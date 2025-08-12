-- Base de datos para CodeaiNews
-- SQLite compatible

-- Tabla de usuarios/administradores
CREATE TABLE IF NOT EXISTS usuarios (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    rol ENUM('admin', 'editor', 'autor') DEFAULT 'autor',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    ultimo_login DATETIME,
    activo BOOLEAN DEFAULT 1
);

-- Tabla de categorías
CREATE TABLE IF NOT EXISTS categorias (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    color VARCHAR(7) DEFAULT '#ff9800',
    descripcion TEXT,
    orden INTEGER DEFAULT 0,
    activa BOOLEAN DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de entradas del blog
CREATE TABLE IF NOT EXISTS blog_posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    titulo VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    excerpt TEXT,
    contenido LONGTEXT NOT NULL,
    imagen VARCHAR(500),
    categoria_id INTEGER,
    autor_id INTEGER,
    estado ENUM('borrador', 'publicado', 'programado') DEFAULT 'borrador',
    fecha_publicacion DATETIME,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    vistas INTEGER DEFAULT 0,
    destacado BOOLEAN DEFAULT 0,
    meta_title VARCHAR(255),
    meta_description TEXT,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    FOREIGN KEY (autor_id) REFERENCES usuarios(id)
);

-- Tabla de etiquetas
CREATE TABLE IF NOT EXISTS etiquetas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre VARCHAR(100) UNIQUE NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    descripcion TEXT,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de relación entre posts y etiquetas
CREATE TABLE IF NOT EXISTS blog_posts_etiquetas (
    post_id INTEGER,
    etiqueta_id INTEGER,
    PRIMARY KEY (post_id, etiqueta_id),
    FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
    FOREIGN KEY (etiqueta_id) REFERENCES etiquetas(id) ON DELETE CASCADE
);

-- Tabla de noticias
CREATE TABLE IF NOT EXISTS noticias (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    titulo VARCHAR(255) NOT NULL,
    resumen TEXT,
    imagen VARCHAR(500),
    categoria_id INTEGER,
    autor_id INTEGER,
    estado ENUM('borrador', 'publicado', 'programado') DEFAULT 'borrador',
    fecha_publicacion DATETIME,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    vistas INTEGER DEFAULT 0,
    destacada BOOLEAN DEFAULT 0,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id),
    FOREIGN KEY (autor_id) REFERENCES usuarios(id)
);

-- Tabla de banners
CREATE TABLE IF NOT EXISTS banners (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    texto VARCHAR(255) NOT NULL,
    posicion ENUM('izquierdo', 'derecho', 'intermedio', 'footer') NOT NULL,
    activo BOOLEAN DEFAULT 1,
    orden INTEGER DEFAULT 0,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de elementos del footer
CREATE TABLE IF NOT EXISTS footer_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    titulo VARCHAR(100) NOT NULL,
    contenido TEXT,
    tipo ENUM('enlace', 'texto', 'imagen') DEFAULT 'enlace',
    url VARCHAR(500),
    orden INTEGER DEFAULT 0,
    activo BOOLEAN DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de mensajes de contacto
CREATE TABLE IF NOT EXISTS contactos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    asunto VARCHAR(200) NOT NULL,
    mensaje TEXT NOT NULL,
    estado ENUM('nuevo', 'leido', 'respondido', 'archivado') DEFAULT 'nuevo',
    fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_respuesta DATETIME,
    ip_origen VARCHAR(45),
    user_agent TEXT
);

-- Tabla de información de contacto del sitio
CREATE TABLE IF NOT EXISTS info_contacto (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tipo ENUM('direccion', 'email', 'telefono', 'horario') NOT NULL,
    contenido TEXT NOT NULL,
    orden INTEGER DEFAULT 0,
    activo BOOLEAN DEFAULT 1,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de configuración del sitio
CREATE TABLE IF NOT EXISTS configuracion (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    clave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    descripcion TEXT,
    tipo ENUM('texto', 'numero', 'booleano', 'json') DEFAULT 'texto',
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de logs de actividad
CREATE TABLE IF NOT EXISTS logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    usuario_id INTEGER,
    accion VARCHAR(100) NOT NULL,
    tabla_afectada VARCHAR(100),
    registro_id INTEGER,
    detalles TEXT,
    ip_origen VARCHAR(45),
    fecha_accion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Insertar datos iniciales
INSERT INTO usuarios (username, password_hash, email, nombre_completo, rol) VALUES 
('admin1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@codeainews.com', 'Administrador CodeaiNews', 'admin');

INSERT INTO categorias (nombre, slug, color, descripcion, orden) VALUES 
('Tutoriales', 'tutoriales', '#ff9800', 'Guías paso a paso y tutoriales', 1),
('Análisis', 'analisis', '#2196f3', 'Análisis profundos y estudios técnicos', 2),
('Noticias', 'noticias', '#4caf50', 'Últimas noticias del mundo Linux', 3),
('Afiliados', 'afiliados', '#9c27b0', 'Artículos de afiliados y editoriales', 4),
('Reviews', 'reviews', '#f44336', 'Reseñas de software y hardware', 5),
('Hardware', 'hardware', '#795548', 'Noticias y análisis de hardware', 6),
('Software', 'software', '#607d8b', 'Noticias y análisis de software', 7);

INSERT INTO etiquetas (nombre, slug) VALUES 
('linux', 'linux'),
('ubuntu', 'ubuntu'),
('servidores', 'servidores'),
('cloud', 'cloud'),
('docker', 'docker'),
('devops', 'devops'),
('software-libre', 'software-libre'),
('tutorial', 'tutorial'),
('instalacion', 'instalacion'),
('configuracion', 'configuracion');

INSERT INTO configuracion (clave, valor, descripcion, tipo) VALUES 
('site_name', 'CodeaiNews', 'Nombre del sitio web', 'texto'),
('site_description', 'Portal de noticias sobre Linux, software libre y tecnología', 'Descripción del sitio web', 'texto'),
('site_keywords', 'linux, software libre, tecnología, tutoriales, noticias', 'Palabras clave del sitio', 'texto'),
('posts_per_page', '10', 'Número de entradas por página', 'numero'),
('enable_comments', '1', 'Habilitar comentarios en el blog', 'booleano'),
('maintenance_mode', '0', 'Modo mantenimiento del sitio', 'booleano');

-- Crear índices para mejorar el rendimiento
CREATE INDEX IF NOT EXISTS idx_blog_posts_estado ON blog_posts(estado);
CREATE INDEX IF NOT EXISTS idx_blog_posts_categoria ON blog_posts(categoria_id);
CREATE INDEX IF NOT EXISTS idx_blog_posts_fecha ON blog_posts(fecha_publicacion);
CREATE INDEX IF NOT EXISTS idx_blog_posts_autor ON blog_posts(autor_id);
CREATE INDEX IF NOT EXISTS idx_noticias_estado ON noticias(estado);
CREATE INDEX IF NOT EXISTS idx_contactos_estado ON contactos(estado);
CREATE INDEX IF NOT EXISTS idx_usuarios_username ON usuarios(username);
CREATE INDEX IF NOT EXISTS idx_categorias_slug ON categorias(slug);
CREATE INDEX IF NOT EXISTS idx_etiquetas_slug ON etiquetas(slug);
