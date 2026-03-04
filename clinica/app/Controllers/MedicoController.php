<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/MedicoModel.php';

class MedicoController extends Controller {
    private MedicoModel $model;
    public function __construct() { $this->model = new MedicoModel(); }

    public function index(): void {
        $this->requireAuth();
        $search = $this->sanitize($_GET['q'] ?? '');
        $this->view('medicos/index', ['medicos' => $this->model->getAll($search), 'search' => $search, 'csrf' => $this->generateCsrf()]);
    }

    public function create(): void {
        $this->requireAuth('admin');
        $this->view('medicos/form', ['medico' => null, 'csrf' => $this->generateCsrf(), 'error' => '']);
    }

    public function store(): void {
        $this->requireAuth('admin');
        $this->verifyCsrf();
        $d = $this->getFormData();
        $errors = $this->validate($d);
        if ($errors) {
            $this->view('medicos/form', ['medico' => $d, 'csrf' => $this->generateCsrf(), 'error' => implode('<br>', $errors)]);
            return;
        }
        $this->model->create($d) ? $this->redirect(APP_URL . '/medicos?success=1') :
            $this->view('medicos/form', ['medico' => $d, 'csrf' => $this->generateCsrf(), 'error' => 'Error al guardar. Verifique que el ID y licencia sean únicos.']);
    }

    public function edit(string $id): void {
        $this->requireAuth('admin');
        $medico = $this->model->getById($id);
        if (!$medico) $this->redirect(APP_URL . '/medicos');
        $this->view('medicos/form', ['medico' => $medico, 'csrf' => $this->generateCsrf(), 'error' => '']);
    }

    public function update(string $id): void {
        $this->requireAuth('admin');
        $this->verifyCsrf();
        $d = $this->getFormData();
        $errors = $this->validate($d, true);
        if ($errors) {
            $this->view('medicos/form', ['medico' => array_merge($d, ['identificacion' => $id]), 'csrf' => $this->generateCsrf(), 'error' => implode('<br>', $errors)]);
            return;
        }
        $this->model->update($id, $d) ? $this->redirect(APP_URL . '/medicos?success=2') :
            $this->view('medicos/form', ['medico' => array_merge($d, ['identificacion' => $id]), 'csrf' => $this->generateCsrf(), 'error' => 'Error al actualizar.']);
    }

    public function delete(string $id): void {
        $this->requireAuth('admin');
        $this->verifyCsrf();
        $this->model->delete($id);
        $this->redirect(APP_URL . '/medicos?success=3');
    }

    public function apiList(): void {
        $this->requireAuth();
        $this->json($this->model->getDisponibles());
    }

    private function getFormData(): array {
        return [
            'identificacion' => $this->sanitize($_POST['identificacion'] ?? ''),
            'nombre'         => $this->sanitize($_POST['nombre'] ?? ''),
            'direccion'      => $this->sanitize($_POST['direccion'] ?? ''),
            'telefono'       => $this->sanitize($_POST['telefono'] ?? ''),
            'cargo'          => $this->sanitize($_POST['cargo'] ?? 'Médico'),
            'fecha_ingreso'  => $this->sanitize($_POST['fecha_ingreso'] ?? ''),
            'salario'        => $this->sanitize($_POST['salario'] ?? '0'),
            'especialidad'   => $this->sanitize($_POST['especialidad'] ?? ''),
            'nro_licencia'   => $this->sanitize($_POST['nro_licencia'] ?? ''),
            'universidad'    => $this->sanitize($_POST['universidad'] ?? ''),
            'disponible'     => isset($_POST['disponible']) ? 1 : 0,
        ];
    }
    private function validate(array $d, bool $update = false): array {
        $e = [];
        if (!$update && empty($d['identificacion'])) $e[] = 'La identificación es requerida.';
        if (empty($d['nombre']))       $e[] = 'El nombre es requerido.';
        if (empty($d['especialidad'])) $e[] = 'La especialidad es requerida.';
        if (empty($d['nro_licencia'])) $e[] = 'El número de licencia es requerido.';
        if (empty($d['fecha_ingreso'])) $e[] = 'La fecha de ingreso es requerida.';
        return $e;
    }
}
