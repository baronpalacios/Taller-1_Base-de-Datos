<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/CitaModel.php';
require_once __DIR__ . '/../Models/PacienteModel.php';
require_once __DIR__ . '/../Models/MedicoModel.php';

class CitaController extends Controller {
    private CitaModel $model;
    public function __construct() { $this->model = new CitaModel(); }

    public function index(): void {
        $this->requireAuth();
        $filters = [
            'fecha'  => $this->sanitize($_GET['fecha'] ?? ''),
            'estado' => $this->sanitize($_GET['estado'] ?? ''),
            'medico' => $this->sanitize($_GET['medico'] ?? ''),
        ];
        $citas = $this->model->getAll($filters);
        $medicos = (new MedicoModel())->getDisponibles();
        $this->view('citas/index', ['citas' => $citas, 'filters' => $filters, 'medicos' => $medicos, 'csrf' => $this->generateCsrf()]);
    }

    public function create(): void {
        $this->requireAuth('admin','recepcionista');
        $pacientes = (new PacienteModel())->getAll();
        $medicos   = (new MedicoModel())->getDisponibles();
        $this->view('citas/form', ['cita' => null, 'pacientes' => $pacientes, 'medicos' => $medicos, 'csrf' => $this->generateCsrf(), 'error' => '']);
    }

    public function store(): void {
        $this->requireAuth('admin','recepcionista');
        $this->verifyCsrf();
        $d = $this->getFormData();
        if (!$this->model->checkDisponibilidad($d['id_medico'], $d['fecha_cita'], $d['hora_cita'])) {
            $pacientes = (new PacienteModel())->getAll();
            $medicos   = (new MedicoModel())->getDisponibles();
            $this->view('citas/form', ['cita' => $d, 'pacientes' => $pacientes, 'medicos' => $medicos, 'csrf' => $this->generateCsrf(), 'error' => 'El médico ya tiene una cita en ese horario.']);
            return;
        }
        $id = $this->model->create($d);
        $id ? $this->redirect(APP_URL . '/citas?success=1') : $this->redirect(APP_URL . '/citas?error=1');
    }

    public function edit(string $id): void {
        $this->requireAuth('admin','recepcionista');
        $cita = $this->model->getById((int)$id);
        if (!$cita) $this->redirect(APP_URL . '/citas');
        $pacientes = (new PacienteModel())->getAll();
        $medicos   = (new MedicoModel())->getDisponibles();
        $this->view('citas/form', ['cita' => $cita, 'pacientes' => $pacientes, 'medicos' => $medicos, 'csrf' => $this->generateCsrf(), 'error' => '']);
    }

    public function update(string $id): void {
        $this->requireAuth('admin','recepcionista');
        $this->verifyCsrf();
        $d = $this->getFormData();
        $this->model->update((int)$id, $d) ? $this->redirect(APP_URL . '/citas?success=2') : $this->redirect(APP_URL . '/citas?error=1');
    }

    public function cancel(string $id): void {
        $this->requireAuth();
        $this->verifyCsrf();
        $this->model->updateEstado((int)$id, 'cancelada');
        $this->redirect(APP_URL . '/citas?success=3');
    }

    public function complete(string $id): void {
        $this->requireAuth();
        $this->verifyCsrf();
        $this->model->updateEstado((int)$id, 'completada');
        $this->redirect(APP_URL . '/citas?success=4');
    }

    public function delete(string $id): void {
        $this->requireAuth('admin');
        $this->verifyCsrf();
        $this->model->delete((int)$id);
        $this->redirect(APP_URL . '/citas?success=5');
    }

    private function getFormData(): array {
        return [
            'id_paciente' => $this->sanitize($_POST['id_paciente'] ?? ''),
            'id_medico'   => $this->sanitize($_POST['id_medico']   ?? ''),
            'fecha_cita'  => $this->sanitize($_POST['fecha_cita']  ?? ''),
            'hora_cita'   => $this->sanitize($_POST['hora_cita']   ?? ''),
            'motivo'      => $this->sanitize($_POST['motivo']      ?? ''),
            'estado'      => $this->sanitize($_POST['estado']      ?? 'pendiente'),
            'notas'       => $this->sanitize($_POST['notas']       ?? ''),
        ];
    }
}
