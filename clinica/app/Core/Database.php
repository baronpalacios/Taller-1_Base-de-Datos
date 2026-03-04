<?php
class Database {
    private static ?PDO $instance = null;
    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
            try {
                self::$instance = new PDO($dsn, $config['user'], $config['pass'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]);
            } catch (PDOException $e) {
                http_response_code(500);
                $debug = defined('APP_DEBUG') && APP_DEBUG;
                if ($debug) {
                    die("<h2>Error de BD</h2><p>" . htmlspecialchars($e->getMessage()) . "</p><p>Host: {$config['host']} | DB: {$config['dbname']} | User: {$config['user']}</p>");
                } else {
                    error_log("DB Error: " . $e->getMessage());
                    die("Error de conexión a la base de datos. Contacte al administrador.");
                }
            }
        }
        return self::$instance;
    }
}
