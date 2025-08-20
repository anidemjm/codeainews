<?php
/**
 * Script para configurar el usuario administrador inicial
 * Ejecutar una sola vez para crear el usuario admin
 */

require_once 'config/database.php';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    echo "🔧 Configurando usuario administrador...\n\n";
    
    // Verificar si ya existe un usuario admin
    $stmt = $pdo->query("SELECT COUNT(*) FROM usuarios");
    $userCount = $stmt->fetchColumn();
    
    if ($userCount > 0) {
        echo "⚠️  Ya existe al menos un usuario en la base de datos.\n";
        echo "Si quieres crear un nuevo usuario admin, elimina primero los existentes.\n";
        exit;
    }
    
    // Crear usuario administrador
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
    
    echo "✅ Usuario administrador creado exitosamente!\n\n";
    echo "📋 Credenciales de acceso:\n";
    echo "   Usuario: $adminUser\n";
    echo "   Contraseña: $adminPassword\n\n";
    echo "🔐 IMPORTANTE: Cambia la contraseña después del primer login por seguridad.\n";
    echo "🌐 Puedes acceder al panel en: dashboard.php\n";
    
} catch (PDOException $e) {
    echo "❌ Error de base de datos: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Error general: " . $e->getMessage() . "\n";
}
?>





