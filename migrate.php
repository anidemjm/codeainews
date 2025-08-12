<?php
/**
 * Script de migración para CodeaiNews
 * Inserta datos de ejemplo en la base de datos
 */

require_once 'config/database.php';

echo "🚀 Iniciando migración de CodeaiNews...\n\n";

try {
    // Insertar entradas de ejemplo del blog
    $blogPosts = [
        [
            'titulo' => 'Guía Completa: Instalación de Ubuntu 24.04 LTS',
            'slug' => 'guia-instalacion-ubuntu-24-04-lts',
            'excerpt' => 'Aprende paso a paso cómo instalar Ubuntu 24.04 LTS en tu computadora. Incluye configuración de particiones, drivers y primeros pasos.',
            'contenido' => 'Esta es una guía completa para instalar Ubuntu 24.04 LTS en tu computadora. Te guiaremos a través de todo el proceso, desde la preparación hasta la configuración post-instalación.

## Requisitos Previos
- USB de al menos 4GB
- Computadora compatible con UEFI
- Conexión a internet

## Paso 1: Descargar Ubuntu
Visita ubuntu.com y descarga la versión 24.04 LTS. Esta es una versión de soporte extendido que recibirá actualizaciones hasta 2029.

## Paso 2: Crear USB Booteable
Usa herramientas como Rufus (Windows) o dd (Linux/Mac) para crear un USB booteable.

## Paso 3: Instalación
1. Reinicia tu computadora
2. Entra en la BIOS/UEFI
3. Selecciona el USB como dispositivo de arranque
4. Sigue el asistente de instalación

## Configuración Post-Instalación
- Actualiza el sistema
- Instala drivers adicionales si es necesario
- Configura el firewall
- Instala software esencial

¡Disfruta de tu nueva instalación de Ubuntu!',
            'imagen' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=800&q=80',
            'categoria_id' => 1, // Tutoriales
            'autor_id' => 1,
            'estado' => 'publicado',
            'fecha_publicacion' => '2024-01-15 10:00:00',
            'vistas' => 1250,
            'destacado' => 1,
            'meta_title' => 'Guía Completa: Instalación de Ubuntu 24.04 LTS - CodeaiNews',
            'meta_description' => 'Aprende paso a paso cómo instalar Ubuntu 24.04 LTS. Guía completa con requisitos, proceso de instalación y configuración post-instalación.'
        ],
        [
            'titulo' => 'Análisis: ¿Por qué Linux domina los servidores?',
            'slug' => 'por-que-linux-domina-servidores',
            'excerpt' => 'Descubre las razones técnicas y económicas por las que Linux es la opción preferida para servidores empresariales y cloud computing.',
            'contenido' => 'Linux se ha convertido en el sistema operativo dominante en el mundo de los servidores. ¿Por qué las empresas más grandes del mundo confían en Linux para sus infraestructuras críticas?

## Ventajas Técnicas
### Estabilidad y Confiabilidad
Linux es conocido por su estabilidad excepcional. Los servidores Linux pueden funcionar durante años sin reiniciarse, algo crucial para servicios que requieren disponibilidad 24/7.

### Rendimiento
Linux es más eficiente en el uso de recursos que otros sistemas operativos. Esto significa que puedes ejecutar más servicios en el mismo hardware.

### Seguridad
El modelo de seguridad de Linux es superior, con un sistema de permisos granular y actualizaciones de seguridad regulares.

## Ventajas Económicas
### Costo Cero
No hay costos de licenciamiento, lo que representa un ahorro significativo para empresas grandes.

### Flexibilidad
Puedes personalizar Linux exactamente para tus necesidades sin depender de un proveedor.

## Casos de Uso
- **Google**: Todos sus servidores ejecutan Linux
- **Amazon**: AWS se basa en Linux
- **Facebook**: Su infraestructura está construida sobre Linux
- **Netflix**: Sirve contenido a millones de usuarios desde servidores Linux

## Conclusión
Linux domina los servidores por una combinación de ventajas técnicas, económicas y de flexibilidad que otros sistemas operativos no pueden igualar.',
            'imagen' => 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?auto=format&fit=crop&w=800&q=80',
            'categoria_id' => 2, // Análisis
            'autor_id' => 1,
            'estado' => 'publicado',
            'fecha_publicacion' => '2024-01-12 14:30:00',
            'vistas' => 890,
            'destacado' => 0,
            'meta_title' => '¿Por qué Linux domina los servidores? Análisis completo - CodeaiNews',
            'meta_description' => 'Descubre las razones técnicas y económicas por las que Linux es la opción preferida para servidores empresariales y cloud computing.'
        ],
        [
            'titulo' => 'Review: Las mejores distribuciones Linux para 2024',
            'slug' => 'mejores-distribuciones-linux-2024',
            'excerpt' => 'Evaluamos las distribuciones Linux más populares del momento, analizando pros, contras y casos de uso ideales para cada una.',
            'contenido' => '2024 es un año emocionante para Linux, con muchas distribuciones excelentes disponibles. Te presentamos las mejores opciones según diferentes necesidades.

## Para Principiantes

### Ubuntu 24.04 LTS
**Pros:**
- Fácil de usar
- Gran comunidad
- Actualizaciones regulares
- Software preinstalado

**Contras:**
- Puede ser lento en hardware antiguo
- Algunas decisiones de diseño controvertidas

**Ideal para:** Usuarios nuevos en Linux

### Linux Mint
**Pros:**
- Interfaz familiar para usuarios de Windows
- Muy estable
- Excelente rendimiento

**Contras:**
- Menos actualizaciones que Ubuntu
- Comunidad más pequeña

**Ideal para:** Usuarios que vienen de Windows

## Para Usuarios Avanzados

### Arch Linux
**Pros:**
- Totalmente personalizable
- Rolling release (siempre actualizado)
- Excelente documentación

**Contras:**
- Curva de aprendizaje empinada
- Requiere más mantenimiento

**Ideal para:** Usuarios experimentados que quieren control total

### Fedora
**Pros:**
- Tecnología de vanguardia
- Patrocinado por Red Hat
- Muy estable para ser cutting-edge

**Contras:**
- Ciclo de vida más corto
- Algunos paquetes pueden tener bugs

**Ideal para:** Desarrolladores y entusiastas

## Para Servidores

### Ubuntu Server
**Pros:**
- Excelente soporte empresarial
- Actualizaciones de seguridad regulares
- Gran ecosistema de herramientas

**Contras:**
- Puede ser pesado para servidores pequeños

**Ideal para:** Servidores de producción

### CentOS Stream
**Pros:**
- Basado en Red Hat Enterprise Linux
- Muy estable
- Excelente para entornos empresariales

**Contras:**
- Menos paquetes disponibles
- Ciclo de vida más corto

**Ideal para:** Servidores empresariales

## Conclusión
La mejor distribución depende de tus necesidades específicas. Para principiantes, Ubuntu o Linux Mint son excelentes opciones. Para usuarios avanzados, Arch Linux ofrece control total. Para servidores, Ubuntu Server o CentOS Stream son las mejores opciones.',
            'imagen' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=800&q=80',
            'categoria_id' => 5, // Reviews
            'autor_id' => 1,
            'estado' => 'publicado',
            'fecha_publicacion' => '2024-01-10 09:15:00',
            'vistas' => 2100,
            'destacado' => 1,
            'meta_title' => 'Mejores distribuciones Linux 2024 - Review completo - CodeaiNews',
            'meta_description' => 'Evaluamos las distribuciones Linux más populares del momento. Comparación detallada con pros, contras y casos de uso ideales.'
        ]
    ];

    echo "📝 Insertando entradas del blog...\n";
    
    foreach ($blogPosts as $post) {
        $postId = db_insert('blog_posts', $post);
        if ($postId) {
            echo "✅ Entrada insertada: {$post['titulo']}\n";
            
            // Insertar etiquetas para cada post
            $etiquetas = [
                'ubuntu', 'instalacion', 'linux', 'tutorial', 'beginners'
            ];
            
            foreach ($etiquetas as $etiqueta) {
                // Buscar o crear etiqueta
                $etiquetaId = db_fetch("SELECT id FROM etiquetas WHERE slug = ?", [$etiqueta]);
                if (!$etiquetaId) {
                    $etiquetaId = db_insert('etiquetas', [
                        'nombre' => ucfirst($etiqueta),
                        'slug' => $etiqueta
                    ]);
                } else {
                    $etiquetaId = $etiquetaId['id'];
                }
                
                // Relacionar post con etiqueta
                db_insert('blog_posts_etiquetas', [
                    'post_id' => $postId,
                    'etiqueta_id' => $etiquetaId
                ]);
            }
        } else {
            echo "❌ Error insertando: {$post['titulo']}\n";
        }
    }

    // Insertar banners de ejemplo
    echo "\n🎯 Insertando banners...\n";
    
    $banners = [
        ['texto' => '¡Nuevo tutorial de Ubuntu 24.04!', 'posicion' => 'izquierdo', 'orden' => 1],
        ['texto' => 'Linux domina los servidores en 2024', 'posicion' => 'derecho', 'orden' => 1],
        ['texto' => 'Descubre las mejores distros Linux', 'posicion' => 'intermedio', 'orden' => 1],
        ['texto' => 'Síguenos en redes sociales', 'posicion' => 'footer', 'orden' => 1]
    ];
    
    foreach ($banners as $banner) {
        $bannerId = db_insert('banners', $banner);
        if ($bannerId) {
            echo "✅ Banner insertado: {$banner['texto']}\n";
        }
    }

    // Insertar información de contacto
    echo "\n📞 Insertando información de contacto...\n";
    
    $infoContacto = [
        ['tipo' => 'direccion', 'contenido' => "Calle de la Innovación 123\n28001 Madrid, España", 'orden' => 1],
        ['tipo' => 'email', 'contenido' => "info@codeainews.com\nredaccion@codeainews.com", 'orden' => 2],
        ['tipo' => 'telefono', 'contenido' => "+34 91 123 45 67\n+34 91 123 45 68", 'orden' => 3],
        ['tipo' => 'horario', 'contenido' => "Lunes a Viernes: 9:00 - 18:00\nSábados: 10:00 - 14:00", 'orden' => 4]
    ];
    
    foreach ($infoContacto as $info) {
        $infoId = db_insert('info_contacto', $info);
        if ($infoId) {
            echo "✅ Info de contacto insertada: {$info['tipo']}\n";
        }
    }

    // Insertar elementos del footer
    echo "\n🔗 Insertando elementos del footer...\n";
    
    $footerItems = [
        ['titulo' => 'Sobre CodeaiNews', 'tipo' => 'enlace', 'url' => '#', 'orden' => 1],
        ['titulo' => 'Publicidad', 'tipo' => 'enlace', 'url' => '#', 'orden' => 2],
        ['titulo' => 'Política de cookies', 'tipo' => 'enlace', 'url' => '#', 'orden' => 3],
        ['titulo' => 'Información legal', 'tipo' => 'enlace', 'url' => '#', 'orden' => 4]
    ];
    
    foreach ($footerItems as $item) {
        $itemId = db_insert('footer_items', $item);
        if ($itemId) {
            echo "✅ Footer item insertado: {$item['titulo']}\n";
        }
    }

    echo "\n🎉 ¡Migración completada exitosamente!\n";
    echo "📊 Resumen:\n";
    echo "   - Entradas del blog: " . count($blogPosts) . "\n";
    echo "   - Banners: " . count($banners) . "\n";
    echo "   - Info de contacto: " . count($infoContacto) . "\n";
    echo "   - Footer items: " . count($footerItems) . "\n\n";
    
    echo "🔑 Credenciales de acceso:\n";
    echo "   Usuario: admin1\n";
    echo "   Contraseña: admin1\n\n";
    
    echo "🌐 Tu sitio web está listo para usar con base de datos real!\n";

} catch (Exception $e) {
    echo "❌ Error durante la migración: " . $e->getMessage() . "\n";
}
?>

