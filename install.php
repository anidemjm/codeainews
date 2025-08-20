<?php
/**
 * Script de instalación completa para CodeaiNews
 * Ejecutar una sola vez para configurar todo el sistema
 */

echo "🚀 Iniciando instalación de CodeaiNews...\n\n";

// 1. Verificar si existe la configuración de base de datos
if (!file_exists('config/database.php')) {
    echo "❌ Error: No se encontró config/database.php\n";
    echo "Asegúrate de que existe la carpeta config/ con database.php\n";
    exit;
}

require_once 'config/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    echo "✅ Conexión a base de datos establecida\n\n";
    
    // 2. Crear tablas
    echo "📋 Creando tablas de la base de datos...\n";
    $db->createTables();
    echo "✅ Tablas creadas exitosamente\n\n";
    
    // 3. Ejecutar migración de datos
    echo "📊 Poblando base de datos con datos iniciales...\n";
    
    // Verificar si ya hay datos
    $stmt = $pdo->query("SELECT COUNT(*) FROM categorias");
    $categoriasCount = $stmt->fetchColumn();
    
    if ($categoriasCount == 0) {
        // Insertar categorías
        $categorias = [
            ['Tutoriales', 'tutoriales', '#4caf50', 'Guías paso a paso y tutoriales', 1],
            ['Análisis', 'analisis', '#2196f3', 'Análisis profundos y estudios técnicos', 2],
            ['Noticias', 'noticias', '#4caf50', 'Últimas noticias del mundo Linux', 3],
            ['Afiliados', 'afiliados', '#9c27b0', 'Artículos de afiliados y editoriales', 4],
            ['Reviews', 'reviews', '#f44336', 'Reseñas de software y hardware', 5],
            ['Hardware', 'hardware', '#795548', 'Noticias y análisis de hardware', 6],
            ['Software', 'software', '#607d8b', 'Noticias y análisis de software', 7],
            ['Comunidad', 'comunidad', '#ff5722', 'Eventos y noticias de la comunidad', 8]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO categorias (nombre, slug, color, descripcion, orden) VALUES (?, ?, ?, ?, ?)");
        foreach ($categorias as $cat) {
            $stmt->execute($cat);
        }
        echo "✅ Categorías insertadas\n";
        
        // Insertar etiquetas
        $etiquetas = [
            ['linux', 'Linux'],
            ['ubuntu', 'Ubuntu'],
            ['debian', 'Debian'],
            ['fedora', 'Fedora'],
            ['arch', 'Arch Linux'],
            ['software-libre', 'Software Libre'],
            ['open-source', 'Open Source'],
            ['terminal', 'Terminal'],
            ['bash', 'Bash'],
            ['python', 'Python'],
            ['docker', 'Docker'],
            ['virtualizacion', 'Virtualización'],
            ['servidores', 'Servidores'],
            ['desarrollo', 'Desarrollo'],
            ['tutorial', 'Tutorial'],
            ['afiliados', 'Afiliados']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO etiquetas (slug, nombre) VALUES (?, ?)");
        foreach ($etiquetas as $tag) {
            $stmt->execute($tag);
        }
        echo "✅ Etiquetas insertadas\n";
        
        // Insertar posts de ejemplo del blog
        $blogPosts = [
            [
                'Guía Completa: Instalación de Ubuntu 24.04 LTS',
                'Tutorial paso a paso para instalar Ubuntu 24.04 LTS en tu computadora, incluyendo particionado, configuración de red y primeros pasos.',
                'Contenido completo del tutorial de instalación de Ubuntu 24.04 LTS...',
                'https://images.unsplash.com/photo-1618401471353-b98afee0b2eb?auto=format&fit=crop&w=800&q=80',
                'tutoriales',
                'Admin CodeaiNews',
                '2024-01-01',
                'ubuntu,instalacion,linux,tutorial,guia',
                'guia-completa-instalacion-ubuntu-24-04-lts',
                'publicado'
            ],
            [
                'Docker para Principiantes: Conceptos Básicos',
                'Introducción a Docker: contenedores, imágenes, y cómo empezar con esta tecnología de virtualización.',
                'Contenido completo sobre Docker para principiantes...',
                'https://images.unsplash.com/photo-1605745341112-85968b19335b?auto=format&fit=crop&w=800&q=80',
                'tutoriales',
                'Admin CodeaiNews',
                '2024-01-02',
                'docker,contenedores,virtualizacion,tutorial,principiantes',
                'docker-para-principiantes-conceptos-basicos',
                'publicado'
            ],
            [
                'Afiliados: El futuro del software libre en la IA',
                'Reflexiones sobre cómo la inteligencia artificial está transformando el panorama del software libre y qué podemos esperar.',
                'Contenido completo del post sobre IA y software libre...',
                'https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&w=800&q=80',
                'afiliados',
                'Admin CodeaiNews',
                '2024-01-05',
                'inteligencia-artificial,software-libre,futuro,tecnologia,afiliados',
                'futuro-software-libre-ia',
                'borrador'
            ]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO blog_posts (titulo, excerpt, contenido, imagen, categoria, autor, fecha_publicacion, etiquetas, slug, estado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($blogPosts as $post) {
            $stmt->execute($post);
        }
        echo "✅ Posts del blog insertados\n";
        
        // Insertar banners de ejemplo
        $banners = [
            ['¡Nuevo tutorial de Ubuntu 24.04 disponible!', 'izquierdo', 5, 1],
            ['Descubre Docker: La guía completa', 'derecho', 4, 2],
            ['Software libre y código abierto', 'intermedio', 6, 3]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO banners (texto, posicion, duracion, orden) VALUES (?, ?, ?, ?)");
        foreach ($banners as $banner) {
            $stmt->execute($banner);
        }
        echo "✅ Banners insertados\n";
        
        // Insertar información de contacto
        $infoContacto = [
            'CodeaiNews',
            'info@codeainews.com',
            '+34 600 000 000',
            'Calle Linux, 123, Madrid, España',
            'Somos una comunidad dedicada al software libre, Linux y tecnología open source.'
        ];
        
        $stmt = $pdo->prepare("INSERT INTO info_contacto (nombre_empresa, email, telefono, direccion, descripcion) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute($infoContacto);
        echo "✅ Información de contacto insertada\n";
        
        // Insertar elementos del footer
        $footerItems = [
            ['Sobre Nosotros', 'https://codeainews.com/sobre-nosotros', 'Conoce más sobre nuestra misión y valores'],
            ['Política de Privacidad', 'https://codeainews.com/privacidad', 'Cómo protegemos tu información'],
            ['Términos de Uso', 'https://codeainews.com/terminos', 'Condiciones de uso del sitio'],
            ['Contacto', 'https://codeainews.com/contacto', 'Ponte en contacto con nosotros']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO footer_items (titulo, enlace, descripcion, orden) VALUES (?, ?, ?, ?)");
        foreach ($footerItems as $index => $item) {
            $stmt->execute([$item[0], $item[1], $item[2], $index + 1]);
        }
        echo "✅ Elementos del footer insertados\n";
        
    } else {
        echo "ℹ️  La base de datos ya contiene datos, saltando inserción de datos de ejemplo\n";
    }
    
    // 4. Crear usuario administrador
    echo "\n👤 Configurando usuario administrador...\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
    $userCount = $stmt->fetchColumn();
    
    if ($userCount == 0) {
        $adminUser = 'admin1';
        $adminPassword = 'admin1';
        $passwordHash = password_hash($adminPassword, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO usuarios (usuario, password_hash, email, rol, fecha_creacion) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $adminUser,
            $passwordHash,
            'admin@codeainews.com',
            'administrador',
            date('Y-m-d H:i:s')
        ]);
        
        echo "✅ Usuario administrador creado\n";
        echo "📋 Credenciales: $adminUser / $adminPassword\n";
    } else {
        echo "ℹ️  Ya existe al menos un usuario en el sistema\n";
    }
    
    echo "\n🎉 ¡Instalación completada exitosamente!\n\n";
    echo "📋 Resumen de lo que se ha configurado:\n";
    echo "   ✅ Base de datos SQLite creada\n";
    echo "   ✅ Todas las tablas creadas\n";
    echo "   ✅ Categorías y etiquetas insertadas\n";
    echo "   ✅ Posts de ejemplo del blog\n";
    echo "   ✅ Banners de ejemplo\n";
    echo "   ✅ Información de contacto\n";
    echo "   ✅ Elementos del footer\n";
    echo "   ✅ Usuario administrador\n\n";
    
    echo "🌐 Próximos pasos:\n";
    echo "   1. Accede al panel de administración: dashboard.php\n";
    echo "   2. Usa las credenciales mostradas arriba\n";
    echo "   3. Personaliza el contenido según tus necesidades\n";
    echo "   4. Cambia la contraseña del administrador por seguridad\n\n";
    
    echo "🔐 IMPORTANTE: Elimina este archivo (install.php) después de la instalación por seguridad.\n";
    
} catch (PDOException $e) {
    echo "❌ Error de base de datos: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Error general: " . $e->getMessage() . "\n";
}
?>





