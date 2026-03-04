<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/AreaModel.php';

class AreaController extends Controller {
    private AreaModel $model;
    public function __construct() { $this->model = new AreaModel(); }

    public function index(): void {
        $this->requireAuth();
        $this->view('areas/index', ['areas' => $this->model->getAll(), 'csrf' => $this->generateCsrf()]);
    }

    public function create(): void {
        $this->requireAuth('admin');
        $this->view('areas/form', ['area' => null, 'csrf' => $this->generateCsrf(), 'error' => '']);
    }

    public function store(): void {
        $this->requireAuth('admin');
        $this->verifyCsrf();
        $d = $this->getFormData();
        $errors = $this->validate($d);
        if ($errors) { $this->view('areas/form', ['area' => $d, 'csrf' => $this->generateCsrf(), 'error' => implode('<br>', $errors)]); return; }
        if ($this->model->exists($d['cod_area'])) { $this->view('areas/form', ['area' => $d, 'csrf' => $this->generateCsrf(), 'error' => 'Ya existe un área con ese código.']); return; }
        $this->model->create($d) ? $this->redirect(APP_URL.'/areas?success=1') : $this->view('areas/form', ['area' => $d, 'csrf' => $this->generateCsrf(), 'error' => 'Error al guardar.']);
    }

    public function edit(string $id): void {
        $this->requireAuth('admin');
        $area = $this->model->getById($id);
        if (!$area) $this->redirect(APP_URL.'/areas');
        $this->view('areas/form', ['area' => $area, 'csrf' => $this->generateCsrf(), 'error' => '']);
    }

    public function update(string $id): void {
        $this->requireAuth('admin');
        $this->verifyCsrf();
        $d = $this->getFormData();
        if (empty($d['nombre_area'])) { $this->view('areas/form', ['area' => array_merge($d, ['cod_area'=>$id]), 'csrf' => $this->generateCsrf(), 'error' => 'El nombre es requerido.']); return; }
        $this->model->update($id, $d) ? $this->redirect(APP_URL.'/areas?success=2') : $this->view('areas/form', ['area' => array_merge($d, ['cod_area'=>$id]), 'csrf' => $this->generateCsrf(), 'error' => 'Error al actualizar.']);
    }

    public function delete(string $id): void {
        $this->requireAuth('admin');
        $this->verifyCsrf();
        $this->model->delete($id);
        $this->redirect(APP_URL.'/areas?success=3');
    }

    private function getFormData(): array {
        return ['cod_area' => strtoupper($this->sanitize($_POST['cod_area'] ?? '')), 'nombre_area' => $this->sanitize($_POST['nombre_area'] ?? '')];
    }
    private function validate(array $d): array {
        $e = [];
        if (empty($d['cod_area']))    $e[] = 'El código es requerido.';
        if (empty($d['nombre_area'])) $e[] = 'El nombre es requerido.';
        if (!preg_match('/^[A-Z0-9_]{1,20}$/', $d['cod_area'])) $e[] = 'El código solo puede contener letras mayúsculas, números y guión bajo (ej: URG, PED_1).';
        return $e;
    }
}
