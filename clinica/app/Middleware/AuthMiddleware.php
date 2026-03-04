<?php
/**
 * app/Middleware/AuthMiddleware.php
 * Controla acceso y roles
 */

class AuthMiddleware {

    /** Redirige al login si no hay sesión válida */
    public static function require(): void {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        // Verificar que el usuario siga activo en BD
        $db   = Database::getInstance();
        $stmt = $db->prepare('SELECT activo FROM usuarios WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        if (!$user || !$user['activo']) {
            session_destroy();
            header('Location: ' . APP_URL . '/login?msg=inactivo');
            exit;
        }
    }

    /** Verifica que el usuario tenga alguno de los roles permitidos */
    public static function requireRole(array $roles): void {
        self::require();
        if (!in_array($_SESSION['user_rol'], $roles, true)) {
            http_response_code(403);
            require_once VIEWS_PATH . '/layouts/403.php';
            exit;
        }
    }

    /** Helper: ¿el usuario actual es admin? */
    public static function isAdmin(): bool {
        return ($_SESSION['user_rol'] ?? '') === 'admin';
    }

    /** Helper: ¿es médico? */
    public static function isMedico(): bool {
        return ($_SESSION['user_rol'] ?? '') === 'medico';
    }

    /** Genera y verifica tokens CSRF */
    public static function generateCsrf(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrf(): void {
        $token  = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(419);
            die(json_encode(['error' => 'Token CSRF inválido']));
        }
    }
}
