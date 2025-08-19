<?php
// Verificar que PHP funcione
echo "<h1>¡CodeaiNews funciona en Heroku!</h1>";
echo "<p>PHP está funcionando correctamente.</p>";
echo "<p>Versión de PHP: " . phpversion() . "</p>";
echo "<p>Fecha y hora: " . date('Y-m-d H:i:s') . "</p>";

// Verificar archivos disponibles
echo "<h2>Archivos disponibles:</h2>";
$files = scandir('.');
echo "<ul>";
foreach($files as $file) {
    if ($file != '.' && $file != '..' && $file != '.git') {
        echo "<li>$file</li>";
    }
}
echo "</ul>";

// Enlaces a otras páginas
echo "<h2>Enlaces:</h2>";
echo "<p><a href='login.php'>Login</a></p>";
echo "<p><a href='dashboard.php'>Dashboard</a></p>";
echo "<p><a href='install-heroku.php'>Instalar</a></p>";
?>


