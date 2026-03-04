<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/UsuarioModel.php';

class PerfilController extends Controller {
    private UsuarioModel $model;
    public function __construct() { $this->model = new UsuarioModel(); }

    public function index(): void {
        $this->requireAuth();
        $user = $this->model->getById($_SESSION['user']['id']);
        $this->view('perfil/index', [
            'user'    => $user,
            'csrf'    => $this->generateCsrf(),
            'success' => $_GET['success'] ?? '',
            'error'   => '',
        ]);
    }

    public function update(): void {
        $this->requireAuth();
        $this->verifyCsrf();
        $id    = $_SESSION['user']['id'];
        $email = $this->sanitize($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $user = $this->model->getById($id);
            $this->view('perfil/index', ['user' => $user, 'csrf' => $this->generateCsrf(), 'success' => '', 'error' => 'Email inválido.']);
            return;
        }

        $this->model->updateEmail($id, $email);
        $_SESSION['user']['email'] = $email;
        $this->redirect(APP_URL . '/perfil?success=1');
    }

    public function cambiarPassword(): void {
        $this->requireAuth();
        $this->verifyCsrf();
        $id          = $_SESSION['user']['id'];
        $actual      = $_POST['password_actual']     ?? '';
        $nueva       = $_POST['password_nueva']      ?? '';
        $confirmacion= $_POST['password_confirmacion']?? '';

        $user = $this->model->getById($id);

        if (!password_verify($actual, $user['password_hash'])) {
            $this->view('perfil/index', ['user' => $user, 'csrf' => $this->generateCsrf(), 'success' => '', 'error' => 'La contraseña actual es incorrecta.']);
            return;
        }
        if (strlen($nueva) < 6) {
            $this->view('perfil/index', ['user' => $user, 'csrf' => $this->generateCsrf(), 'success' => '', 'error' => 'La nueva contraseña debe tener al menos 6 caracteres.']);
            return;
        }
        if ($nueva !== $confirmacion) {
            $this->view('perfil/index', ['user' => $user, 'csrf' => $this->generateCsrf(), 'success' => '', 'error' => 'Las contraseñas no coinciden.']);
            return;
        }

        $hash = password_hash($nueva, PASSWORD_BCRYPT);
        $this->model->updatePassword($id, $hash);
        $this->redirect(APP_URL . '/perfil?success=2');
    }
}
