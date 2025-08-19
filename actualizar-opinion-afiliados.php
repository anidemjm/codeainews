<?php
/**
 * Script para actualizar "Opinión" por "Afiliados" en la base de datos
 * Ejecutar una sola vez después de la migración
 */

require_once 'config/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    echo "🔄 Iniciando actualización de 'Opinión' a 'Afiliados'...\n\n";
    
    // 1. Actualizar la tabla categorias
    $stmt = $pdo->prepare("UPDATE categorias SET nombre = 'Afiliados', slug = 'afiliados' WHERE slug = 'opinion'");
    $stmt->execute();
    $categoriasActualizadas = $stmt->rowCount();
    echo "✅ Categorías actualizadas: $categoriasActualizadas\n";
    
    // 2. Actualizar la tabla blog_posts
    $stmt = $pdo->prepare("UPDATE blog_posts SET categoria = 'afiliados' WHERE categoria = 'opinion'");
    $stmt->execute();
    $postsActualizados = $stmt->rowCount();
    echo "✅ Posts del blog actualizados: $postsActualizados\n";
    
    // 3. Actualizar la tabla noticias
    $stmt = $pdo->prepare("UPDATE noticias SET categoria = 'afiliados' WHERE categoria = 'opinion'");
    $stmt->execute();
    $noticiasActualizadas = $stmt->rowCount();
    echo "✅ Noticias actualizadas: $noticiasActualizadas\n";
    
    // 4. Actualizar etiquetas que contengan "opinion"
    $stmt = $pdo->prepare("UPDATE etiquetas SET nombre = 'afiliados', slug = 'afiliados' WHERE slug = 'opinion'");
    $stmt->execute();
    $etiquetasActualizadas = $stmt->rowCount();
    echo "✅ Etiquetas actualizadas: $etiquetasActualizadas\n";
    
    // 5. Actualizar la tabla blog_posts_etiquetas
    $stmt = $pdo->prepare("UPDATE blog_posts_etiquetas SET etiqueta_id = (SELECT id FROM etiquetas WHERE slug = 'afiliados') WHERE etiqueta_id = (SELECT id FROM etiquetas WHERE slug = 'opinion')");
    $stmt->execute();
    $relacionesActualizadas = $stmt->rowCount();
    echo "✅ Relaciones de etiquetas actualizadas: $relacionesActualizadas\n";
    
    echo "\n🎉 Actualización completada exitosamente!\n";
    echo "📊 Resumen:\n";
    echo "   - Categorías: $categoriasActualizadas\n";
    echo "   - Posts del blog: $postsActualizados\n";
    echo "   - Noticias: $noticiasActualizadas\n";
    echo "   - Etiquetas: $etiquetasActualizadas\n";
    echo "   - Relaciones: $relacionesActualizadas\n";
    
} catch (PDOException $e) {
    echo "❌ Error en la base de datos: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Error general: " . $e->getMessage() . "\n";
}
?>




