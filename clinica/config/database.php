<?php
/**
 * config/database.php — Configuracion BD desde .env
 */
$env = [];
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        [$k, $v] = array_map('trim', explode('=', $line, 2));
        $env[$k] = $v;
    }
}
return [
    'host'    => $env['DB_HOST']    ?? 'localhost',
    'dbname'  => $env['DB_NAME']    ?? 'clinica_db',
    'user'    => $env['DB_USER']    ?? 'root',
    'pass'    => $env['DB_PASS']    ?? '',
    'charset' => $env['DB_CHARSET'] ?? 'utf8mb4',
];
