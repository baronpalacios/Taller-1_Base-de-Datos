<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/HistoriaModel.php';
require_once __DIR__ . '/../Models/PacienteModel.php';
require_once __DIR__ . '/../Models/MedicoModel.php';

class HistoriaController extends Controller {
    private HistoriaModel $model;
    public function __construct() { $this->model = new HistoriaModel(); }

    public function show(string $idPaciente): void {
        $this->requireAuth();
        $paciente = (new PacienteModel())->getById($idPaciente);
        if (!$paciente) $this->redirect(APP_URL . '/pacientes');
        $historia = $this->model->getByPaciente($idPaciente);
        if (!$historia) {
            $idHist = $this->model->crearHistoria($idPaciente);
            $historia = ['id_historia' => $idHist, 'id_paciente' => $idPaciente];
        }
        $consultas = $this->model->getConsultas($historia['id_historia']);
        $medicos   = (new MedicoModel())->getDisponibles();
        $this->view('historia/show', [
            'paciente'  => $paciente,
            'historia'  => $historia,
            'consultas' => $consultas,
            'medicos'   => $medicos,
            'csrf'      => $this->generateCsrf(),
        ]);
    }

    public function addConsulta(string $idPaciente): void {
        $this->requireAuth('admin','medico');
        $this->verifyCsrf();
        $historia = $this->model->getByPaciente($idPaciente);
        if (!$historia) { $id = $this->model->crearHistoria($idPaciente); $historia = ['id_historia' => $id]; }
        $d = [
            'fecha_consulta' => $this->sanitize($_POST['fecha_consulta'] ?? date('Y-m-d')),
            'precio'         => $this->sanitize($_POST['precio'] ?? '0'),
            'resumen'        => $this->sanitize($_POST['resumen'] ?? ''),
            'id_medico'      => $this->sanitize($_POST['id_medico'] ?? ''),
            'diagnostico'    => $this->sanitize($_POST['diagnostico'] ?? ''),
            'tratamiento'    => $this->sanitize($_POST['tratamiento'] ?? ''),
        ];
        $this->model->addConsulta((int)$historia['id_historia'], $d);
        $this->redirect(APP_URL . '/historia/' . $idPaciente . '?success=1');
    }
}
