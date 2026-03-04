<?php
/**
 * Controller.php — Controlador base
 */
class Controller {

    protected function view(string $view, array $data = []): void {
        extract($data);
        $viewFile = __DIR__ . '/../../views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            die("Vista no encontrada: $view");
        }
        require $viewFile;
    }

    protected function json(mixed $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    protected function redirect(string $url): void {
        header("Location: $url");
        exit;
    }

    protected function requireAuth(string ...$roles): void {
        if (!isset($_SESSION['user'])) {
            $this->redirect(APP_URL . '/login');
        }
        if (!empty($roles) && !in_array($_SESSION['user']['rol'], $roles)) {
            http_response_code(403);
            die('Acceso denegado.');
        }
    }

    protected function verifyCsrf(): void {
        $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(419);
            die(json_encode(['error' => 'Token CSRF inválido.']));
        }
    }

    protected function generateCsrf(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function sanitize(string $input): string {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
}
