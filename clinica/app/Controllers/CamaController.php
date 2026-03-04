<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/CamaModel.php';
require_once __DIR__ . '/../Models/AreaModel.php';
require_once __DIR__ . '/../Models/PacienteModel.php';

class CamaController extends Controller {
    private CamaModel    $model;
    private AreaModel    $areaModel;
    private PacienteModel $pacienteModel;

    public function __construct() {
        $this->model         = new CamaModel();
        $this->areaModel     = new AreaModel();
        $this->pacienteModel = new PacienteModel();
    }

    public function index(): void {
        $this->requireAuth();
        $area   = $this->sanitize($_GET['area']   ?? '');
        $estado = $this->sanitize($_GET['estado'] ?? '');
        $this->view('camas/index', [
            'camas'   => $this->model->getAll($area, $estado),
            'resumen' => $this->model->getResumenPorArea(),
            'areas'   => $this->areaModel->getAllSimple(),
            'areaFiltro'   => $area,
            'estadoFiltro' => $estado,
            'csrf'    => $this->generateCsrf(),
        ]);
    }

    public function create(): void {
        $this->requireAuth('admin');
        $this->view('camas/form', [
            'cama'  => null,
            'areas' => $this->areaModel->getAllSimple(),
            'csrf'  => $this->generateCsrf(),
            'error' => '',
        ]);
    }

    public function store(): void {
        $this->requireAuth('admin');
        $this->verifyCsrf();
        $d = [
            'nro_cama' => (int)($_POST['nro_cama'] ?? 0),
            'cod_area' => $this->sanitize($_POST['cod_area'] ?? ''),
            'estado'   => 'libre',
        ];
        if (!$d['nro_cama'] || !$d['cod_area']) {
            $this->view('camas/form', ['cama' => $d, 'areas' => $this->areaModel->getAllSimple(), 'csrf' => $this->generateCsrf(), 'error' => 'Número de cama y área son requeridos.']);
            return;
        }
        $this->model->create($d)
            ? $this->redirect(APP_URL . '/camas?success=1')
            : $this->view('camas/form', ['cama' => $d, 'areas' => $this->areaModel->getAllSimple(), 'csrf' => $this->generateCsrf(), 'error' => 'Error al crear la cama. Puede que ya exista ese número en esa área.']);
    }

    public function asignar(int $nro, string $area): void {
        $this->requireAuth('admin', 'recepcionista');
        $this->view('camas/asignar', [
            'nro_cama'  => $nro,
            'cod_area'  => $area,
            'pacientes' => $this->pacienteModel->getAll(),
            'csrf'      => $this->generateCsrf(),
            'error'     => '',
        ]);
    }

    public function doAsignar(int $nro, string $area): void {
        $this->requireAuth('admin', 'recepcionista');
        $this->verifyCsrf();
        $d = [
            'id_paciente'      => $this->sanitize($_POST['id_paciente'] ?? ''),
            'nro_cama'         => $nro,
            'cod_area'         => $area,
            'fecha_asignacion' => $this->sanitize($_POST['fecha_asignacion'] ?? date('Y-m-d')),
            'duracion_dias'    => (int)($_POST['duracion_dias'] ?? 1),
        ];
        $this->model->asignar($d)
            ? $this->redirect(APP_URL . '/camas?success=2')
            : $this->view('camas/asignar', ['nro_cama' => $nro, 'cod_area' => $area, 'pacientes' => $this->pacienteModel->getAll(), 'csrf' => $this->generateCsrf(), 'error' => 'Error al asignar la cama.']);
    }

    public function liberar(int $nro, string $area): void {
        $this->requireAuth('admin', 'recepcionista');
        $this->verifyCsrf();
        $this->model->liberar($nro, $area);
        $this->redirect(APP_URL . '/camas?success=3');
    }

    public function delete(int $nro, string $area): void {
        $this->requireAuth('admin');
        $this->verifyCsrf();
        $this->model->delete($nro, $area);
        $this->redirect(APP_URL . '/camas?success=4');
    }
}
