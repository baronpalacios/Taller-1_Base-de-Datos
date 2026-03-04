<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/CampaniaModel.php';
require_once __DIR__ . '/../Models/MedicoModel.php';
require_once __DIR__ . '/../Models/PacienteModel.php';

class CampaniaController extends Controller {
    private CampaniaModel $model;
    private MedicoModel   $medicoModel;
    private PacienteModel $pacienteModel;

    public function __construct() {
        $this->model         = new CampaniaModel();
        $this->medicoModel   = new MedicoModel();
        $this->pacienteModel = new PacienteModel();
    }

    public function index(): void {
        $this->requireAuth();
        $search = $this->sanitize($_GET['q'] ?? '');
        $this->view('campanias/index', [
            'campanias' => $this->model->getAll($search),
            'search'    => $search,
            'csrf'      => $this->generateCsrf(),
        ]);
    }

    public function show(string $id): void {
        $this->requireAuth();
        $campania = $this->model->getById($id);
        if (!$campania) $this->redirect(APP_URL . '/campanias');
        $this->view('campanias/show', [
            'campania'       => $campania,
            'participantes'  => $this->model->getParticipantes($id),
            'pacientes'      => $this->pacienteModel->getAll(),
            'csrf'           => $this->generateCsrf(),
        ]);
    }

    public function create(): void {
        $this->requireAuth('admin');
        $this->view('campanias/form', [
            'campania' => null,
            'medicos'  => $this->medicoModel->getDisponibles(),
            'csrf'     => $this->generateCsrf(),
            'error'    => '',
        ]);
    }

    public function store(): void {
        $this->requireAuth('admin');
        $this->verifyCsrf();
        $d = $this->getFormData();
        $errors = $this->validate($d);
        if ($errors) {
            $this->view('campanias/form', ['campania' => $d, 'medicos' => $this->medicoModel->getDisponibles(), 'csrf' => $this->generateCsrf(), 'error' => implode('<br>', $errors)]);
            return;
        }
        $this->model->create($d)
            ? $this->redirect(APP_URL . '/campanias?success=1')
            : $this->view('campanias/form', ['campania' => $d, 'medicos' => $this->medicoModel->getDisponibles(), 'csrf' => $this->generateCsrf(), 'error' => 'Error al guardar. Verifique que el código sea único.']);
    }

    public function edit(string $id): void {
        $this->requireAuth('admin');
        $campania = $this->model->getById($id);
        if (!$campania) $this->redirect(APP_URL . '/campanias');
        $this->view('campanias/form', [
            'campania' => $campania,
            'medicos'  => $this->medicoModel->getDisponibles(),
            'csrf'     => $this->generateCsrf(),
            'error'    => '',
        ]);
    }

    public function update(string $id): void {
        $this->requireAuth('admin');
        $this->verifyCsrf();
        $d = $this->getFormData();
        $errors = $this->validate($d, true);
        if ($errors) {
            $this->view('campanias/form', ['campania' => array_merge($d, ['cod_campania' => $id]), 'medicos' => $this->medicoModel->getDisponibles(), 'csrf' => $this->generateCsrf(), 'error' => implode('<br>', $errors)]);
            return;
        }
        $this->model->update($id, $d)
            ? $this->redirect(APP_URL . '/campanias?success=2')
            : $this->view('campanias/form', ['campania' => array_merge($d, ['cod_campania' => $id]), 'medicos' => $this->medicoModel->getDisponibles(), 'csrf' => $this->generateCsrf(), 'error' => 'Error al actualizar.']);
    }

    public function delete(string $id): void {
        $this->requireAuth('admin');
        $this->verifyCsrf();
        $this->model->delete($id);
        $this->redirect(APP_URL . '/campanias?success=3');
    }

    public function addParticipante(string $id): void {
        $this->requireAuth('admin', 'recepcionista');
        $this->verifyCsrf();
        $pacienteId = $this->sanitize($_POST['id_paciente'] ?? '');
        if ($pacienteId) $this->model->addParticipante($id, $pacienteId);
        $this->redirect(APP_URL . '/campanias/' . $id);
    }

    public function removeParticipante(string $id): void {
        $this->requireAuth('admin', 'recepcionista');
        $this->verifyCsrf();
        $pacienteId = $this->sanitize($_POST['id_paciente'] ?? '');
        if ($pacienteId) $this->model->removeParticipante($id, $pacienteId);
        $this->redirect(APP_URL . '/campanias/' . $id);
    }

    private function getFormData(): array {
        return [
            'cod_campania'     => strtoupper($this->sanitize($_POST['cod_campania']     ?? '')),
            'nombre'           => $this->sanitize($_POST['nombre']           ?? ''),
            'objetivo'         => $this->sanitize($_POST['objetivo']         ?? ''),
            'fecha_realizacion'=> $this->sanitize($_POST['fecha_realizacion']?? ''),
            'id_medico_resp'   => $this->sanitize($_POST['id_medico_resp']   ?? ''),
        ];
    }

    private function validate(array $d, bool $update = false): array {
        $e = [];
        if (!$update && empty($d['cod_campania']))  $e[] = 'El código de campaña es requerido.';
        if (empty($d['nombre']))           $e[] = 'El nombre es requerido.';
        if (empty($d['fecha_realizacion']))$e[] = 'La fecha es requerida.';
        if (empty($d['id_medico_resp']))   $e[] = 'El médico responsable es requerido.';
        return $e;
    }
}
