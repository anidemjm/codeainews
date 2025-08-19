<?php
/**
 * Configuración de Base de Datos para CodeaiNews
 * Detecta automáticamente si está en Heroku o local
 * Updated: Heroku deployment v7
 */

// Detectar si estamos en Heroku
if (getenv('DATABASE_URL')) {
    // Estamos en Heroku - usar PostgreSQL
    require_once __DIR__ . '/database-heroku.php';
} else {
    // Estamos en local - usar SQLite
    require_once __DIR__ . '/database-local.php';
}

