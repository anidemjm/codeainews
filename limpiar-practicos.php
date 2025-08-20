<?php
/**
 * Script para limpiar la categoría "Prácticos" de la base de datos
 * Ejecutar solo una vez después de la migración inicial
 */

require_once 'config/database.php';

echo "🧹 Limpiando categoría 'Prácticos' de la base de datos...\n\n";

try {
    // Buscar la categoría "Prácticos"
    $categoriaPracticos = db_fetch("SELECT id FROM categorias WHERE nombre = 'Prácticos'");
    
    if ($categoriaPracticos) {
        $categoriaId = $categoriaPracticos['id'];
        echo "📝 Categoría 'Prácticos' encontrada con ID: {$categoriaId}\n";
        
        // Actualizar entradas que usen esta categoría (cambiar a Tutoriales)
        $tutorialesId = db_fetch("SELECT id FROM categorias WHERE nombre = 'Tutoriales'");
        
        if ($tutorialesId) {
            $tutorialesId = $tutorialesId['id'];
            
            // Actualizar blog posts
            $postsActualizados = db_update('blog_posts', 
                ['categoria_id' => $tutorialesId], 
                'categoria_id = ?', 
                [$categoriaId]
            );
            
            // Actualizar noticias
            $noticiasActualizadas = db_update('noticias', 
                ['categoria_id' => $tutorialesId], 
                'categoria_id = ?', 
                [$categoriaId]
            );
            
            echo "✅ {$postsActualizados} entradas del blog actualizadas\n";
            echo "✅ {$noticiasActualizadas} noticias actualizadas\n";
            
            // Eliminar la categoría "Prácticos"
            $eliminada = db_delete('categorias', 'id = ?', [$categoriaId]);
            
            if ($eliminada) {
                echo "✅ Categoría 'Prácticos' eliminada exitosamente\n";
            } else {
                echo "❌ Error al eliminar la categoría\n";
            }
        } else {
            echo "❌ No se encontró la categoría 'Tutoriales'\n";
        }
    } else {
        echo "ℹ️ La categoría 'Prácticos' no existe en la base de datos\n";
    }
    
    echo "\n🎉 Limpieza completada!\n";
    echo "📊 Categorías disponibles:\n";
    
    $categorias = db_fetch_all("SELECT nombre, slug FROM categorias ORDER BY orden");
    foreach ($categorias as $cat) {
        echo "   - {$cat['nombre']} ({$cat['slug']})\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error durante la limpieza: " . $e->getMessage() . "\n";
}
?>





