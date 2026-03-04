<?php
/**
 * Router.php — Enrutador MVC con soporte para subdirectorio
 * Funciona tanto en dominio raíz como en /clinica/, /app/, etc.
 */
class Router {
    private array $routes = [];

    public function get(string $path, array $handler): void {
        $this->routes['GET'][$path] = $handler;
    }
    public function post(string $path, array $handler): void {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $rawUri): void {
        // 1. Quitar query string
        $uri = strtok($rawUri, '?');

        // 2. Detectar y quitar el prefijo del subdirectorio automáticamente
        //    Ej: /clinica/pacientes → /pacientes
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($scriptDir !== '' && strpos($uri, $scriptDir) === 0) {
            $uri = substr($uri, strlen($scriptDir));
        }

        // 3. Normalizar
        $uri = '/' . ltrim($uri ?: '/', '/');

        // 4. Buscar ruta exacta
        if (isset($this->routes[$method][$uri])) {
            [$controller, $action] = $this->routes[$method][$uri];
            $c = new $controller();
            $c->$action();
            return;
        }

        // 5. Rutas dinámicas con parámetro {id}
        foreach ($this->routes[$method] as $route => $handler) {
            $pattern = preg_replace('/\{[a-zA-Z_]+\}/', '([^/]+)', $route);
            if (preg_match('#^' . $pattern . '$#', $uri, $matches)) {
                array_shift($matches);
                [$controller, $action] = $handler;
                $c = new $controller();
                $c->$action(...$matches);
                return;
            }
        }

        // 6. 404
        http_response_code(404);
        $viewFile = ROOT . '/views/404.php';
        if (file_exists($viewFile)) require $viewFile;
        else echo '<h1>404 — Página no encontrada</h1><a href="' . APP_URL . '/dashboard">Inicio</a>';
    }
}
