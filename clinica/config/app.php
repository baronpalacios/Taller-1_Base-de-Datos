<?php
/**
 * config/app.php — Configuración de la aplicación
 */
$env = [];
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        [$k, $v] = array_map('trim', explode('=', $line, 2));
        $env[$k] = $v;
    }
}

define('APP_NAME',     $env['APP_NAME']          ?? 'Sistema Clinica');
define('APP_URL',      rtrim($env['APP_URL']      ?? 'http://localhost/clinica', '/'));
define('APP_DEBUG',    ($env['APP_DEBUG']          ?? 'false') === 'true');
define('SESSION_NAME', $env['SESSION_NAME']        ?? 'clinica_session');
define('SESSION_LIFE', (int)($env['SESSION_LIFETIME'] ?? 3600));

// Mostrar errores solo en desarrollo
if (APP_DEBUG) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}
