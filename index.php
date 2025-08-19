<?php
// Incluir configuración de base de datos
require_once 'config/database.php';

// Incluir funciones de la aplicación
require_once 'config/environment.php';

// Verificar si la base de datos está funcionando
try {
    $db = new Database();
    $connection = $db->getConnection();
    $dbStatus = "✅ Base de datos conectada correctamente";
} catch (Exception $e) {
    $dbStatus = "❌ Error de base de datos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeaiNews - Noticias Tecnológicas</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>🚀 CodeaiNews</h1>
        <p>Tu fuente de noticias tecnológicas</p>
    </header>

    <main>
        <section class="hero">
            <h2>¡Bienvenido a CodeaiNews!</h2>
            <p>Tu sitio está funcionando perfectamente en Heroku</p>
            
            <div class="status">
                <h3>Estado del Sistema:</h3>
                <p>✅ PHP funcionando: <?php echo phpversion(); ?></p>
                <p><?php echo $dbStatus; ?></p>
                <p>✅ Heroku: Desplegado correctamente</p>
            </div>
        </section>

        <section class="actions">
            <h3>Acciones Disponibles:</h3>
            <div class="buttons">
                <a href="login.php" class="btn">🔐 Login</a>
                <a href="dashboard.php" class="btn">📊 Dashboard</a>
                <a href="blog.php" class="btn">📝 Blog</a>
                <a href="contacto.php" class="btn">📧 Contacto</a>
            </div>
        </section>

        <section class="info">
            <h3>Información Técnica:</h3>
            <p><strong>URL:</strong> <?php echo $_SERVER['HTTP_HOST']; ?></p>
            <p><strong>Fecha:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><strong>Base de datos:</strong> PostgreSQL (Heroku)</p>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 CodeaiNews - Desplegado en Heroku</p>
    </footer>
</body>
</html>


