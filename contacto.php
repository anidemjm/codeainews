<?php
session_start();
require_once 'config/database.php';

$contactInfo = null;
$mensaje = '';
$tipoMensaje = '';

try {
    $db = new Database();
    $pdo = $db->getConnection();
    
    // Cargar información de contacto
    $stmt = $pdo->query("SELECT * FROM contact_info LIMIT 1");
    $contactInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // En caso de error, usar información por defecto
    $contactInfo = null;
}

// Procesar formulario de contacto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $asunto = trim($_POST['asunto'] ?? '');
    $mensaje_texto = trim($_POST['mensaje'] ?? '');
    $captcha = trim($_POST['captcha'] ?? '');
    $captcha_esperado = $_POST['captcha_esperado'] ?? '';
    
    // Validaciones
    if (empty($nombre) || empty($email) || empty($asunto) || empty($mensaje_texto)) {
        $mensaje = 'Todos los campos son obligatorios.';
        $tipoMensaje = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'El formato del email no es válido.';
        $tipoMensaje = 'error';
    } elseif ($captcha !== $captcha_esperado) {
        $mensaje = 'El código CAPTCHA es incorrecto.';
        $tipoMensaje = 'error';
    } else {
        try {
            // Guardar mensaje en la base de datos
            $stmt = $pdo->prepare("INSERT INTO contact_messages (nombre, email, asunto, mensaje, fecha_envio, estado) VALUES (?, ?, ?, ?, NOW(), 'nuevo')");
            $stmt->execute([$nombre, $email, $asunto, $mensaje_texto]);
            
            $mensaje = '¡Mensaje enviado correctamente! Te responderemos pronto.';
            $tipoMensaje = 'exito';
            
            // Limpiar campos del formulario
            $nombre = $email = $asunto = $mensaje_texto = '';
            
        } catch (PDOException $e) {
            $mensaje = 'Error al enviar el mensaje. Inténtalo de nuevo.';
            $tipoMensaje = 'error';
        }
    }
}

// Generar CAPTCHA
$num1 = rand(1, 10);
$num2 = rand(1, 10);
$captcha_resultado = $num1 + $num2;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - CodeaiNews</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-content">
            <div class="logo">
                <h1>CodeaiNews</h1>
                <p>Tu fuente de noticias de Linux y Software Libre</p>
            </div>
            <nav>
                <a href="index.php">Inicio</a>
                <a href="#">Actualidad</a>
                <a href="#">Afiliados</a>
                <a href="contacto.php" class="active">Contacto</a>
                <a href="blog.php">Blog</a>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="dashboard.php">Panel</a>
                    <a href="logout.php">Salir</a>
                <?php else: ?>
                    <a href="login.php">Acceso</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Contenido Principal -->
    <main class="contacto-main">
        <div class="container">
            <div class="contacto-header">
                <h1>Contacto</h1>
                <p>¿Tienes alguna pregunta, sugerencia o quieres colaborar? ¡Contáctanos!</p>
            </div>

            <div class="contacto-content">
                <!-- Información de Contacto -->
                <div class="contacto-info">
                    <h2>Información de Contacto</h2>
                    
                    <?php if ($contactInfo): ?>
                        <div class="info-item">
                            <h3>Ubicación</h3>
                            <p><?php echo htmlspecialchars($contactInfo['direccion']); ?></p>
                            <div class="mapa">
                                <iframe 
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3037.5!2d-3.7038!3d40.4168!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDI1JzAwLjQiTiAzw7A0MicxMy43Ilc!5e0!3m2!1ses!2ses!4v1234567890"
                                    width="100%" 
                                    height="300" 
                                    style="border:0;" 
                                    allowfullscreen="" 
                                    loading="lazy" 
                                    referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <h3>Email</h3>
                            <p><a href="mailto:<?php echo htmlspecialchars($contactInfo['email']); ?>"><?php echo htmlspecialchars($contactInfo['email']); ?></a></p>
                        </div>
                        
                        <div class="info-item">
                            <h3>Teléfono</h3>
                            <p><a href="tel:<?php echo htmlspecialchars($contactInfo['telefono']); ?>"><?php echo htmlspecialchars($contactInfo['telefono']); ?></a></p>
                        </div>
                        
                        <div class="info-item">
                            <h3>Horarios</h3>
                            <p><?php echo htmlspecialchars($contactInfo['horarios']); ?></p>
                        </div>
                    <?php else: ?>
                        <!-- Información por defecto -->
                        <div class="info-item">
                            <h3>Ubicación</h3>
                            <p>Madrid, España</p>
                            <div class="mapa">
                                <iframe 
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3037.5!2d-3.7038!3d40.4168!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDI1JzAwLjQiTiAzw7A0MicxMy43Ilc!5e0!3m2!1ses!2ses!4v1234567890"
                                    width="100%" 
                                    height="300" 
                                    style="border:0;" 
                                    allowfullscreen="" 
                                    loading="lazy" 
                                    referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <h3>Email</h3>
                            <p><a href="mailto:info@codeainews.com">info@codeainews.com</a></p>
                        </div>
                        
                        <div class="info-item">
                            <h3>Teléfono</h3>
                            <p><a href="tel:+34912345678">+34 912 345 678</a></p>
                        </div>
                        
                        <div class="info-item">
                            <h3>Horarios</h3>
                            <p>Lunes a Viernes: 9:00 - 18:00</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Formulario de Contacto -->
                <div class="contacto-form">
                    <h2>Envíanos un Mensaje</h2>
                    
                    <?php if ($mensaje): ?>
                        <div class="mensaje <?php echo $tipoMensaje; ?>">
                            <?php echo htmlspecialchars($mensaje); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="nombre">Nombre completo *</label>
                            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="asunto">Asunto *</label>
                            <input type="text" id="asunto" name="asunto" value="<?php echo htmlspecialchars($asunto ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="mensaje">Mensaje *</label>
                            <textarea id="mensaje" name="mensaje" rows="5" required><?php echo htmlspecialchars($mensaje_texto ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group captcha-group">
                            <label for="captcha">Verificación: ¿Cuánto es <?php echo $num1; ?> + <?php echo $num2; ?>? *</label>
                            <input type="number" id="captcha" name="captcha" required>
                            <input type="hidden" name="captcha_esperado" value="<?php echo $captcha_resultado; ?>">
                        </div>
                        
                        <button type="submit" class="btn-primary">Enviar Mensaje</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Sobre CodeaiNews</h3>
                    <p>Tu fuente confiable de noticias sobre Linux, software libre y tecnología. Mantente informado sobre las últimas novedades del mundo open source.</p>
                </div>
                <div class="footer-section">
                    <h3>Contacto</h3>
                    <p>¿Tienes alguna pregunta o sugerencia? No dudes en contactarnos.</p>
                    <a href="contacto.php">Contactar</a>
                </div>
                <div class="footer-section">
                    <h3>Síguenos</h3>
                    <p>Mantente al día con nuestras últimas publicaciones y actualizaciones.</p>
                    <a href="index.php">Inicio</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 CodeaiNews. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>





