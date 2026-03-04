<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/UsuarioModel.php';

class AuthController extends Controller {
    private UsuarioModel $model;
    public function __construct() { $this->model = new UsuarioModel(); }

    public function login(): void {
        if (isset($_SESSION['user'])) $this->redirect(APP_URL . '/dashboard');
        $csrf = $this->generateCsrf();
        $this->view('auth/login', ['csrf' => $csrf, 'error' => '']);
    }

    public function doLogin(): void {
        $this->verifyCsrf();
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        if (empty($username) || empty($password)) {
            $this->view('auth/login', ['csrf' => $this->generateCsrf(), 'error' => 'Todos los campos son requeridos.']);
            return;
        }
        $user = $this->model->getByUsername($username);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->view('auth/login', ['csrf' => $this->generateCsrf(), 'error' => 'Credenciales incorrectas.']);
            return;
        }
        $this->model->updateLogin($user['id']);
        $_SESSION['user'] = ['id' => $user['id'], 'username' => $user['username'], 'email' => $user['email'], 'rol' => $user['rol']];
        $this->redirect(APP_URL . '/dashboard');
    }

    public function logout(): void {
        session_destroy();
        $this->redirect(APP_URL . '/login');
    }
}
