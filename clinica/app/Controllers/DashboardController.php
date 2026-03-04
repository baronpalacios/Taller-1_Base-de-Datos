<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/PacienteModel.php';
require_once __DIR__ . '/../Models/MedicoModel.php';
require_once __DIR__ . '/../Models/CitaModel.php';

class DashboardController extends Controller {
    public function index(): void {
        $this->requireAuth();
        $pac = new PacienteModel();
        $med = new MedicoModel();
        $cit = new CitaModel();

        $stats = [
            'total_pacientes'  => $pac->countTotal(),
            'citas_hoy'        => $cit->countHoy(),
            'medicos_activos'  => $med->countActivos(),
            'pacientes_mes'    => $pac->countNewThisMonth(),
        ];
        $citasHoy = $cit->getAll(['fecha' => date('Y-m-d')]);
        $this->view('dashboard/index', ['stats' => $stats, 'citasHoy' => $citasHoy]);
    }
}
