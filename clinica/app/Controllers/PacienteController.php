<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/PacienteModel.php';
require_once __DIR__ . '/../Models/HistoriaModel.php';

class PacienteController extends Controller {
    private PacienteModel $model;
    private HistoriaModel $historia;
    public function __construct() {
        $this->model   = new PacienteModel();
        $this->historia = new HistoriaModel();
    }

    public function index(): void {
        $this->requireAuth();
        $search = $this->sanitize($_GET['q'] ?? '');
        $pacientes = $this->model->getAll($search);
        $this->view('pacientes/index', ['pacientes' => $pacientes, 'search' => $search, 'csrf' => $this->generateCsrf()]);
    }

    public function create(): void {
        $this->requireAuth('admin','recepcionista');
        $this->view('pacientes/form', ['paciente' => null, 'csrf' => $this->generateCsrf(), 'error' => '']);
    }

    public function store(): void {
        $this->requireAuth('admin','recepcionista');
        $this->verifyCsrf();
        $d = $this->getFormData();
        $errors = $this->validate($d);
        if ($errors) {
            $this->view('pacientes/form', ['paciente' => $d, 'csrf' => $this->generateCsrf(), 'error' => implode('<br>', $errors)]);
            return;
        }
        if ($this->model->exists($d['identificacion'])) {
            $this->view('pacientes/form', ['paciente' => $d, 'csrf' => $this->generateCsrf(), 'error' => 'Ya existe un paciente con ese ID.']);
            return;
        }
        if ($this->model->create($d)) {
            $this->redirect(APP_URL . '/pacientes?success=1');
        } else {
            $this->view('pacientes/form', ['paciente' => $d, 'csrf' => $this->generateCsrf(), 'error' => 'Error al guardar.']);
        }
    }

    public function edit(string $id): void {
        $this->requireAuth('admin','recepcionista');
        $paciente = $this->model->getById($id);
        if (!$paciente) { $this->redirect(APP_URL . '/pacientes'); }
        $this->view('pacientes/form', ['paciente' => $paciente, 'csrf' => $this->generateCsrf(), 'error' => '']);
    }

    public function update(string $id): void {
        $this->requireAuth('admin','recepcionista');
        $this->verifyCsrf();
        $d = $this->getFormData();
        $errors = $this->validate($d, true);
        if ($errors) {
            $this->view('pacientes/form', ['paciente' => array_merge($d, ['identificacion' => $id]), 'csrf' => $this->generateCsrf(), 'error' => implode('<br>', $errors)]);
            return;
        }
        if ($this->model->update($id, $d)) {
            $this->redirect(APP_URL . '/pacientes?success=2');
        } else {
            $this->view('pacientes/form', ['paciente' => array_merge($d, ['identificacion' => $id]), 'csrf' => $this->generateCsrf(), 'error' => 'Error al actualizar.']);
        }
    }

    public function delete(string $id): void {
        $this->requireAuth('admin');
        $this->verifyCsrf();
        $this->model->delete($id);
        $this->redirect(APP_URL . '/pacientes?success=3');
    }

    public function show(string $id): void {
        $this->requireAuth();
        $paciente = $this->model->getById($id);
        if (!$paciente) { $this->redirect(APP_URL . '/pacientes'); }
        $historia = $this->historia->getByPaciente($id);
        $consultas = $historia ? $this->historia->getConsultas($historia['id_historia']) : [];
        $this->view('pacientes/show', ['paciente' => $paciente, 'historia' => $historia, 'consultas' => $consultas, 'csrf' => $this->generateCsrf()]);
    }

    // --- API JSON ---
    public function apiSearch(): void {
        $this->requireAuth();
        $q = $this->sanitize($_GET['q'] ?? '');
        $this->json($this->model->getAll($q));
    }

    private function getFormData(): array {
        return [
            'identificacion'  => $this->sanitize($_POST['identificacion'] ?? ''),
            'nombre'          => $this->sanitize($_POST['nombre'] ?? ''),
            'direccion'       => $this->sanitize($_POST['direccion'] ?? ''),
            'telefono'        => $this->sanitize($_POST['telefono'] ?? ''),
            'nss'             => $this->sanitize($_POST['nss'] ?? ''),
            'fecha_nacimiento'=> $this->sanitize($_POST['fecha_nacimiento'] ?? ''),
            'labor'           => $this->sanitize($_POST['labor'] ?? ''),
            'eps'             => $this->sanitize($_POST['eps'] ?? ''),
            'fecha_afiliacion'=> $this->sanitize($_POST['fecha_afiliacion'] ?? ''),
        ];
    }

    private function validate(array $d, bool $update = false): array {
        $e = [];
        if (!$update && empty($d['identificacion'])) $e[] = 'La identificación es requerida.';
        if (empty($d['nombre']))          $e[] = 'El nombre es requerido.';
        if (empty($d['nss']))             $e[] = 'El NSS es requerido.';
        if (empty($d['fecha_nacimiento'])) $e[] = 'La fecha de nacimiento es requerida.';
        return $e;
    }
}
