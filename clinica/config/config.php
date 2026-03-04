<?php
/**
 * config/config.php
 * Carga variables de entorno y define constantes globales
 */

// Cargar .env
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        if (str_contains($line, '=')) {
            [$key, $value] = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// Función helper
function env(string $key, $default = null) {
    return $_ENV[$key] ?? getenv($key) ?: $default;
}

// Zona horaria
date_default_timezone_set(env('APP_TIMEZONE', 'America/Bogota'));

// Constantes
define('APP_NAME',    env('APP_NAME', 'Sistema Clínica'));
define('APP_URL',     env('APP_URL',  'http://localhost'));
define('APP_ENV',     env('APP_ENV',  'production'));
define('APP_DEBUG',   env('APP_DEBUG', 'false') === 'true');
define('BASE_PATH',   dirname(__DIR__));
define('PUBLIC_PATH', BASE_PATH . '/public');
define('VIEWS_PATH',  BASE_PATH . '/views');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

// Manejo de errores
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/error.log');
}

// Sesión segura
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}
session_name(env('SESSION_NAME', 'clinica_session'));
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
