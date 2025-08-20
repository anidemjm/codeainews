<?php
/**
 * Script de verificación para hosting
 * Verifica que todo esté funcionando correctamente
 */

// Incluir configuración
require_once 'config/hosting.php';
require_once 'config/database-hosting.php';

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

// Función para verificar PHP
function verificarPHP() {
    $errores = [];
    
    // Verificar versión
    if (version_compare(PHP_VERSION, '7.4.0', '<')) {
        $errores[] = "Versión de PHP muy antigua: " . PHP_VERSION . " (mínimo 7.4.0)";
    }
    
    // Verificar extensiones
    $extensiones_requeridas = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
    foreach ($extensiones_requeridas as $ext) {
        if (!extension_loaded($ext)) {
            $errores[] = "Extensión $ext no está disponible";
        }
    }
    
    return [empty($errores), $errores];
}

// Función para verificar base de datos
function verificarBaseDatos() {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        // Verificar conexión
        $stmt = $conn->query("SELECT 1");
        $result = $stmt->fetch();
        
        // Verificar tablas
        $tablas_requeridas = [
            'usuarios', 'categorias', 'noticias', 'blog_posts', 
            'carrusel', 'banners', 'mensajes_contacto', 
            'info_contacto', 'footer_items'
        ];
        
        $tablas_faltantes = [];
        foreach ($tablas_requeridas as $tabla) {
            $stmt = $conn->query("SHOW TABLES LIKE '$tabla'");
            if ($stmt->rowCount() == 0) {
                $tablas_faltantes[] = $tabla;
            }
        }
        
        $conn = null;
        
        if (!empty($tablas_faltantes)) {
            return [false, "Tablas faltantes: " . implode(', ', $tablas_faltantes)];
        }
        
        return [true, "Base de datos funcionando correctamente"];
        
    } catch (Exception $e) {
        return [false, $e->getMessage()];
    }
}

// Función para verificar archivos
function verificarArchivos() {
    $archivos_requeridos = [
        'index.php', 'dashboard.php', 'login.php', 'styles.css',
        'config/hosting.php', 'config/database-hosting.php'
    ];
    
    $archivos_faltantes = [];
    foreach ($archivos_requeridos as $archivo) {
        if (!file_exists($archivo)) {
            $archivos_faltantes[] = $archivo;
        }
    }
    
    if (!empty($archivos_faltantes)) {
        return [false, "Archivos faltantes: " . implode(', ', $archivos_faltantes)];
    }
    
    return [true, "Todos los archivos están presentes"];
}

// Función para verificar permisos
function verificarPermisos() {
    $errores = [];
    
    // Verificar directorio de uploads
    if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true);
    }
    
    if (!is_writable('uploads')) {
        $errores[] = "Directorio 'uploads' no tiene permisos de escritura";
    }
    
    // Verificar archivo de configuración
    if (!is_readable('config/hosting.php')) {
        $errores[] = "Archivo 'config/hosting.php' no es legible";
    }
    
    return [empty($errores), $errores];
}

// Función para verificar configuración
function verificarConfiguracion() {
    $errores = [];
    
    // Verificar constantes definidas
    $constantes_requeridas = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'SITE_URL'];
    foreach ($constantes_requeridas as $constante) {
        if (!defined($constante)) {
            $errores[] = "Constante $constante no está definida";
        }
    }
    
    // Verificar valores no vacíos
    if (defined('DB_NAME') && empty(DB_NAME)) {
        $errores[] = "DB_NAME está vacío";
    }
    
    if (defined('SITE_URL') && empty(SITE_URL)) {
        $errores[] = "SITE_URL está vacío";
    }
    
    return [empty($errores), $errores];
}

// Función para obtener información del sistema
function obtenerInfoSistema() {
    return [
        'PHP Version' => PHP_VERSION,
        'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido',
        'Document Root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Desconocido',
        'Current Directory' => getcwd(),
        'Memory Limit' => ini_get('memory_limit'),
        'Max Upload Size' => ini_get('upload_max_filesize'),
        'Max Post Size' => ini_get('post_max_size'),
        'Timezone' => date_default_timezone_get()
    ];
}

// Inicio de la verificación
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Hosting - CodeaiNews</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; text-align: center; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .seccion { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .seccion h3 { margin-top: 0; color: #007bff; }
        .info-sistema { background: #e9ecef; padding: 15px; border-radius: 5px; }
        .info-sistema table { width: 100%; border-collapse: collapse; }
        .info-sistema td { padding: 8px; border-bottom: 1px solid #ddd; }
        .info-sistema td:first-child { font-weight: bold; width: 200px; }
        .recomendaciones { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin-top: 20px; }
        .recomendaciones h4 { margin-top: 0; color: #856404; }
        .recomendaciones ul { margin: 10px 0; }
        .recomendaciones li { margin: 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verificación de Hosting - CodeaiNews</h1>
        
        <div class="seccion">
            <h3>📋 Información del Sistema</h3>
            <div class="info-sistema">
                <table>
                    <?php foreach (obtenerInfoSistema() as $clave => $valor): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($clave); ?></td>
                            <td><?php echo htmlspecialchars($valor); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

        <div class="seccion">
            <h3>🐘 Verificación de PHP</h3>
            <?php 
            list($estado, $errores) = verificarPHP();
            mostrarEstado('PHP y Extensiones', $estado, $estado ? '' : implode(', ', $errores));
            ?>
        </div>

        <div class="seccion">
            <h3>⚙️ Verificación de Configuración</h3>
            <?php 
            list($estado, $errores) = verificarConfiguracion();
            mostrarEstado('Archivo de Configuración', $estado, $estado ? '' : implode(', ', $errores));
            ?>
        </div>

        <div class="seccion">
            <h3>📁 Verificación de Archivos</h3>
            <?php 
            list($estado, $errores) = verificarArchivos();
            mostrarEstado('Archivos del Sistema', $estado, $estado ? '' : $errores);
            ?>
        </div>

        <div class="seccion">
            <h3>🔐 Verificación de Permisos</h3>
            <?php 
            list($estado, $errores) = verificarPermisos();
            mostrarEstado('Permisos del Sistema', $estado, $estado ? '' : implode(', ', $errores));
            ?>
        </div>

        <div class="seccion">
            <h3>🗄️ Verificación de Base de Datos</h3>
            <?php 
            list($estado, $mensaje) = verificarBaseDatos();
            mostrarEstado('Conexión y Tablas', $estado, $mensaje);
            ?>
        </div>

        <div class="recomendaciones">
            <h4>💡 Recomendaciones</h4>
            <ul>
                <li><strong>Eliminar archivos de instalación:</strong> Después de verificar que todo funciona, elimina <code>install-hosting.php</code> y <code>verificar-hosting.php</code></li>
                <li><strong>Cambiar credenciales:</strong> Modifica la contraseña del administrador por defecto</li>
                <li><strong>Configurar SSL:</strong> Asegúrate de que tu hosting tenga SSL habilitado</li>
                <li><strong>Backup regular:</strong> Haz copias de seguridad de tu base de datos regularmente</li>
                <li><strong>Monitoreo:</strong> Revisa los logs de error de tu hosting periódicamente</li>
            </ul>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="index.php" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;">🏠 Ir al Inicio</a>
            <a href="dashboard.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;">⚙️ Panel de Control</a>
        </div>
    </div>
</body>
</html>





