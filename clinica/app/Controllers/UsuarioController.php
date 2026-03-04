<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/UsuarioModel.php';

class UsuarioController extends Controller {
    private UsuarioModel $model;
    public function __construct() { $this->model = new UsuarioModel(); }

    public function index(): void {
        $this->requireAuth('admin');
        $this->view('usuarios/index', ['usuarios' => $this->model->getAll(), 'csrf' => $this->generateCsrf()]);
    }

    public function create(): void {
        $this->requireAuth('admin');
        $this->view('usuarios/form', ['usuario' => null, 'csrf' => $this->generateCsrf(), 'error' => '']);
    }

    public function store(): void {
        $this->requireAuth('admin');
        $this->verifyCsrf();
        $d = $this->getFormData();
        if (empty($d['password'])) { $this->view('usuarios/form', ['usuario' => $d, 'csrf' => $this->generateCsrf(), 'error' => 'La contraseña es requerida.']); return; }
        $this->model->create($d) ? $this->redirect(APP_URL . '/usuarios?success=1') :
            $this->view('usuarios/form', ['usuario' => $d, 'csrf' => $this->generateCsrf(), 'error' => 'Error. El usuario o email ya existe.']);
    }

    public function edit(string $id): void {
        $this->requireAuth('admin');
        $usuario = $this->model->getById((int)$id);
        if (!$usuario) $this->redirect(APP_URL . '/usuarios');
        $this->view('usuarios/form', ['usuario' => $usuario, 'csrf' => $this->generateCsrf(), 'error' => '']);
    }

    public function update(string $id): void {
        $this->requireAuth('admin');
        $this->verifyCsrf();
        $d = $this->getFormData();
        $this->model->update((int)$id, $d);
        if (!empty($d['password'])) $this->model->updatePassword((int)$id, $d['password']);
        $this->redirect(APP_URL . '/usuarios?success=2');
    }

    public function delete(string $id): void {
        $this->requireAuth('admin');
        $this->verifyCsrf();
        if ((int)$id === (int)$_SESSION['user']['id']) { $this->redirect(APP_URL . '/usuarios?error=self'); }
        $this->model->delete((int)$id);
        $this->redirect(APP_URL . '/usuarios?success=3');
    }

    private function getFormData(): array {
        return [
            'username' => $this->sanitize($_POST['username'] ?? ''),
            'email'    => $this->sanitize($_POST['email']    ?? ''),
            'rol'      => $this->sanitize($_POST['rol']      ?? 'recepcionista'),
            'activo'   => isset($_POST['activo']) ? 1 : 0,
            'password' => $_POST['password'] ?? '',
        ];
    }
}
