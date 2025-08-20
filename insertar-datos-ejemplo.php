<?php
/**
 * Script para insertar datos de ejemplo en tablas existentes
 * Útil cuando las tablas ya están creadas pero están vacías
 */

// Incluir configuración de Heroku
require_once 'config/heroku.php';
require_once 'config/database-heroku.php';

// Función para mostrar estado
function mostrarEstado($titulo, $estado, $mensaje = '') {
    $icono = $estado ? '✅' : '❌';
    $color = $estado ? 'green' : 'red';
    echo "<div style='margin: 10px 0; padding: 10px; border-left: 4px solid $color; background: #f9f9f9;'>";
    echo "<strong>$icono $titulo:</strong> ";
    echo $estado ? 'OK' : 'ERROR';
    if ($mensaje) echo " - $mensaje";
    echo "</div>";
}

// Crear conexión a la base de datos PostgreSQL
function crearConexionPostgreSQL() {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Verificar conexión
        $stmt = $conn->query("SELECT 1 as test");
        $result = $stmt->fetch();
        
        if ($result['test'] == 1) {
            echo "✅ Conexión a PostgreSQL exitosa<br>";
            return $conn;
        } else {
            throw new Exception("No se pudo verificar la conexión");
        }
        
    } catch (Exception $e) {
        die("❌ Error de conexión: " . $e->getMessage());
    }
}

// Verificar si las tablas tienen datos
function verificarTablasVacias($conn) {
    $tablas = ['categorias', 'noticias', 'blog_posts', 'carrusel', 'banners'];
    $vacias = [];
    
    foreach ($tablas as $tabla) {
        try {
            $stmt = $conn->query("SELECT COUNT(*) as total FROM $tabla");
            $result = $stmt->fetch();
            $count = $result['total'];
            
            if ($count == 0) {
                $vacias[] = $tabla;
                echo "⚠️ Tabla '$tabla' está vacía<br>";
            } else {
                echo "✅ Tabla '$tabla' tiene $count registros<br>";
            }
        } catch (PDOException $e) {
            echo "❌ Error verificando tabla '$tabla': " . $e->getMessage() . "<br>";
        }
    }
    
    return $vacias;
}

// Insertar datos de ejemplo
function insertarDatosEjemplo($conn) {
    try {
        echo "<h3>📝 Insertando datos de ejemplo...</h3>";
        
        // 1. Categorías (si no existen)
        $categorias = [
            ['Actualidad', 'actualidad', 'Noticias y novedades del mundo tecnológico'],
            ['Afiliados', 'afiliados', 'Contenido de nuestros afiliados'],
            ['Tutoriales', 'tutoriales', 'Guías paso a paso'],
            ['Reviews', 'reviews', 'Análisis de productos y software']
        ];
        
        $stmt = $conn->prepare("INSERT INTO categorias (nombre, slug, descripcion) VALUES (?, ?, ?) ON CONFLICT (slug) DO NOTHING");
        foreach ($categorias as $cat) {
            $stmt->execute($cat);
        }
        echo "✅ Categorías verificadas/creadas<br>";
        
        // 2. Noticias de ejemplo
        $noticias = [
            [
                'Nuevo lanzamiento de Ubuntu 24.04 LTS',
                'Canonical ha anunciado el lanzamiento oficial de Ubuntu 24.04 LTS "Noble Numbat", la versión de soporte extendido más reciente del sistema operativo Linux más popular del mundo. Esta nueva versión incluye mejoras significativas en rendimiento, seguridad y compatibilidad con hardware moderno.',
                'https://via.placeholder.com/800x400/007bff/ffffff?text=Ubuntu+24.04',
                1, // categoria_id = Actualidad
                true
            ],
            [
                'Linux conquista el 3% del mercado de escritorio',
                'Según las últimas estadísticas de NetMarketShare, Linux ha alcanzado el 3% del mercado de escritorio, superando a macOS en algunas regiones. Este crecimiento se atribuye a la mayor adopción en entornos empresariales y educativos.',
                'https://via.placeholder.com/800x400/28a745/ffffff?text=Linux+3%25',
                1, // categoria_id = Actualidad
                true
            ],
            [
                'Guía completa: Instalar Docker en Ubuntu',
                'Docker se ha convertido en una herramienta esencial para desarrolladores y administradores de sistemas. Te enseñamos paso a paso cómo instalar y configurar Docker en Ubuntu para comenzar a trabajar con contenedores.',
                'https://via.placeholder.com/800x400/ffc107/000000?text=Docker+Ubuntu',
                3, // categoria_id = Tutoriales
                true
            ],
            [
                'Review: Visual Studio Code para desarrollo Linux',
                'Microsoft Visual Studio Code se ha posicionado como uno de los editores de código más populares en Linux. Analizamos sus características, extensiones y rendimiento en sistemas basados en Debian y Red Hat.',
                'https://via.placeholder.com/800x400/6f42c1/ffffff?text=VS+Code+Linux',
                4, // categoria_id = Reviews
                true
            ]
        ];
        
        $stmt = $conn->prepare("INSERT INTO noticias (titulo, contenido, imagen, categoria_id, activo) VALUES (?, ?, ?, ?, ?) ON CONFLICT DO NOTHING");
        foreach ($noticias as $noticia) {
            $stmt->execute($noticia);
        }
        echo "✅ Noticias de ejemplo insertadas (4 artículos)<br>";
        
        // 3. Blog posts de ejemplo
        $blogPosts = [
            [
                'Cómo migrar de Windows a Linux sin perder datos',
                'Migrar de Windows a Linux puede parecer intimidante, pero con la preparación adecuada es un proceso sencillo. Te guiamos a través de todos los pasos necesarios para hacer la transición de manera segura.',
                'Migrar de Windows a Linux puede parecer intimidante, pero con la preparación adecuada es un proceso sencillo. Te guiamos a través de todos los pasos necesarios para hacer la transición de manera segura.',
                'https://via.placeholder.com/800x400/007bff/ffffff?text=Windows+to+Linux',
                'migrar-windows-linux',
                'Tutoriales',
                'linux,windows,migracion,guia',
                0,
                true
            ],
            [
                'Top 10 distribuciones Linux para principiantes en 2025',
                'Si eres nuevo en Linux, elegir la distribución correcta puede marcar la diferencia. Hemos seleccionado las 10 mejores distribuciones para principiantes, considerando facilidad de uso, soporte y comunidad.',
                'Si eres nuevo en Linux, elegir la distribución correcta puede marcar la diferencia. Hemos seleccionado las 10 mejores distribuciones para principiantes, considerando facilidad de uso, soporte y comunidad.',
                'https://via.placeholder.com/800x400/28a745/ffffff?text=Top+10+Linux',
                'top-10-distribuciones-linux-principiantes-2025',
                'Tutoriales',
                'linux,distribuciones,principiantes,guia',
                0,
                true
            ],
            [
                'Análisis completo: Ubuntu vs Fedora vs Arch Linux',
                'Comparación detallada de tres de las distribuciones Linux más populares. Analizamos rendimiento, facilidad de uso, estabilidad y comunidad para ayudarte a elegir la mejor opción para tus necesidades.',
                'Comparación detallada de tres de las distribuciones Linux más populares. Analizamos rendimiento, facilidad de uso, estabilidad y comunidad para ayudarte a elegir la mejor opción para tus necesidades.',
                'https://via.placeholder.com/800x400/ffc107/000000?text=Ubuntu+vs+Fedora+vs+Arch',
                'ubuntu-vs-fedora-vs-arch-linux',
                'Reviews',
                'ubuntu,fedora,arch,comparacion,linux',
                0,
                true
            ],
            [
                'Guía de seguridad: Proteger tu servidor Linux',
                'La seguridad es fundamental en cualquier servidor Linux. Te proporcionamos una guía completa con las mejores prácticas para proteger tu servidor contra amenazas comunes y mantenerlo actualizado.',
                'La seguridad es fundamental en cualquier servidor Linux. Te proporcionamos una guía completa con las mejores prácticas para proteger tu servidor contra amenazas comunes y mantenerlo actualizado.',
                'https://via.placeholder.com/800x400/dc3545/ffffff?text=Seguridad+Linux',
                'guia-seguridad-servidor-linux',
                'Tutoriales',
                'linux,seguridad,servidor,guia',
                0,
                true
            ],
            [
                'Software libre alternativo a aplicaciones populares',
                'Descubre excelentes alternativas de software libre para aplicaciones comerciales populares. Desde suites de oficina hasta editores de imagen, hay opciones gratuitas y de código abierto para casi todo.',
                'Descubre excelentes alternativas de software libre para aplicaciones comerciales populares. Desde suites de oficina hasta editores de imagen, hay opciones gratuitas y de código abierto para casi todo.',
                'https://via.placeholder.com/800x400/6f42c1/ffffff?text=Software+Libre',
                'software-libre-alternativas-aplicaciones-populares',
                'Tutoriales',
                'software-libre,alternativas,open-source',
                0,
                true
            ]
        ];
        
        $stmt = $conn->prepare("INSERT INTO blog_posts (titulo, contenido, excerpt, imagen, slug, categoria, tags, vistas, activo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) ON CONFLICT (slug) DO NOTHING");
        foreach ($blogPosts as $post) {
            $stmt->execute($post);
        }
        echo "✅ Blog posts de ejemplo insertados (5 entradas)<br>";
        
        // 4. Carrusel (si no existe)
        $carrusel = [
            ['Bienvenido a CodeaiNews', 'Tu fuente de noticias tecnológicas', 'https://via.placeholder.com/1200x400/007bff/ffffff?text=Bienvenido', '#', 1],
            ['Linux y Software Libre', 'Descubre el mundo del código abierto', 'https://via.placeholder.com/1200x400/28a745/ffffff?text=Linux', '#', 2]
        ];
        
        $stmt = $conn->prepare("INSERT INTO carrusel (titulo, descripcion, imagen, enlace, orden) VALUES (?, ?, ?, ?, ?) ON CONFLICT DO NOTHING");
        foreach ($carrusel as $item) {
            $stmt->execute($item);
        }
        echo "✅ Carrusel verificado/creado<br>";
        
        // 5. Banner (si no existe)
        $stmt = $conn->prepare("INSERT INTO banners (titulo, imagen, orden) VALUES (?, ?, ?) ON CONFLICT DO NOTHING");
        $stmt->execute(['Banner Promocional', 'https://via.placeholder.com/728x90/ffc107/000000?text=Publicidad', 1]);
        echo "✅ Banner verificado/creado<br>";
        
        echo "<br>🎉 ¡Datos de ejemplo insertados exitosamente!<br>";
        echo "Ahora las secciones deberían mostrar contenido:<br>";
        echo "• Blog: 5 entradas<br>";
        echo "• Noticias: 4 artículos<br>";
        echo "• Categorías: 4 categorías<br>";
        
    } catch (PDOException $e) {
        echo "❌ Error insertando datos: " . $e->getMessage() . "<br>";
    }
}

// Inicio del script
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar Datos de Ejemplo - CodeaiNews</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; text-align: center; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .warning { background: #fff3e0; border: 1px solid #ff9800; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .success { background: #e8f5e8; border: 1px solid #4caf50; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 Insertar Datos de Ejemplo - CodeaiNews</h1>
        
        <div class="info">
            <h3>📋 Propósito</h3>
            <p>Este script inserta datos de ejemplo en las tablas existentes para que las secciones del sitio muestren contenido.</p>
            <p><strong>Útil cuando:</strong> Las tablas están creadas pero vacías, y las secciones no muestran contenido.</p>
        </div>

        <div class="warning">
            <h4>⚠️ Importante</h4>
            <p>Este script es seguro de ejecutar múltiples veces (usa ON CONFLICT DO NOTHING).</p>
            <p>Después de usarlo, puedes eliminarlo por seguridad.</p>
        </div>

        <h2>🔍 Verificando estado de las tablas...</h2>
        
        <?php
        try {
            // Crear conexión
            $conn = crearConexionPostgreSQL();
            
            // Verificar tablas vacías
            $tablasVacias = verificarTablasVacias($conn);
            
            if (!empty($tablasVacias)) {
                echo "<div class='warning'>";
                echo "<h3>⚠️ Se encontraron tablas vacías:</h3>";
                echo "<ul>";
                foreach ($tablasVacias as $tabla) {
                    echo "<li>$tabla</li>";
                }
                echo "</ul>";
                echo "<p>Procediendo a insertar datos de ejemplo...</p>";
                echo "</div>";
                
                // Insertar datos
                insertarDatosEjemplo($conn);
                
            } else {
                echo "<div class='success'>";
                echo "<h3>✅ Todas las tablas tienen contenido</h3>";
                echo "<p>No es necesario insertar datos adicionales.</p>";
                echo "</div>";
            }
            
            // Cerrar conexión
            $conn = null;
            
        } catch (Exception $e) {
            echo "<div style='background: #ffebee; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>❌ Error:</h3>";
            echo "<p>" . $e->getMessage() . "</p>";
            echo "</div>";
        }
        ?>

        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;">🏠 Ir al Inicio</a>
            <a href="blog.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;">📝 Ver Blog</a>
            <a href="dashboard.php" style="background: #ffc107; color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;">⚙️ Panel de Control</a>
        </div>
    </div>
</body>
</html>
