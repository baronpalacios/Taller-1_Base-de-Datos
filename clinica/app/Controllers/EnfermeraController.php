<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/EnfermeraModel.php';
require_once __DIR__ . '/../Models/AreaModel.php';

class EnfermeraController extends Controller {
    private EnfermeraModel $model;
    private AreaModel $areaModel;

    public function __construct() {
        $this->model     = new EnfermeraModel();
        $this->areaModel = new AreaModel();
    }

    public function index(): void {
        $this->requireAuth();
        $search = $this->sanitize($_GET['q']    ?? '');
        $area   = $this->sanitize($_GET['area'] ?? '');
        $this->view('enfermeras/index', [
            'enfermeras' => $this->model->getAll($search, $area),
            'areas'      => $this->areaModel->getAllSimple(),
            'search'     => $search,
            'areaFiltro' => $area,
            'csrf'       => $this->generateCsrf(),
        ]);
    }

    public function show(string $id): void {
        $this->requireAuth();
        $enfermera = $this->model->getById($id);
        if (!$enfermera) $this->redirect(APP_URL . '/enfermeras');
        $this->view('enfermeras/show', [
            'enfermera'   => $enfermera,
            'habilidades' => $this->model->getHabilidades($id),
            'csrf'        => $this->generateCsrf(),
        ]);
    }

    public function create(): void {
        $this->requireAuth('admin');
        $this->view('enfermeras/form', [
            'enfermera' => null,
            'areas'     => $this->areaModel->getAllSimple(),
            'csrf'      => $this->generateCsrf(),
            'error'     => '',
        ]);
    }

    public function store(): void {
        $this->requireAuth('admin');
        $this->verifyCsrf();
        $d = $this->getFormData();
        $errors = $this->validate($d);
        if ($errors) {
            $this->view('enfermeras/form', ['enfermera' => $d, 'areas' => $this->areaModel->getAllSimple(), 'csrf' => $this->generateCsrf(), 'error' => implode('<br>', $errors)]);
            return;
        }
        if ($this->model->create($d)) {
            $this->model->saveHabilidades($d['identificacion'], explode(',', $_POST['habilidades'] ?? ''));
            $this->redirect(APP_URL . '/enfermeras?success=1');
        } else {
            $this->view('enfermeras/form', ['enfermera' => $d, 'areas' => $this->areaModel->getAllSimple(), 'csrf' => $this->generateCsrf(), 'error' => 'Error al guardar. Verifique que la identificación sea única.']);
        }
    }

    public function edit(string $id): void {
        $this->requireAuth('admin');
        $enfermera = $this->model->getById($id);
        if (!$enfermera) $this->redirect(APP_URL . '/enfermeras');
        $enfermera['habilidades'] = implode(', ', $this->model->getHabilidades($id));
        $this->view('enfermeras/form', [
            'enfermera' => $enfermera,
            'areas'     => $this->areaModel->getAllSimple(),
            'csrf'      => $this->generateCsrf(),
            'error'     => '',
        ]);
    }

    public function update(string $id): void {
        $this->requireAuth('admin');
        $this->verifyCsrf();
        $d = $this->getFormData();
        $errors = $this->validate($d, true);
        if ($errors) {
            $this->view('enfermeras/form', ['enfermera' => array_merge($d, ['identificacion' => $id]), 'areas' => $this->areaModel->getAllSimple(), 'csrf' => $this->generateCsrf(), 'error' => implode('<br>', $errors)]);
            return;
        }
        if ($this->model->update($id, $d)) {
            $this->model->saveHabilidades($id, explode(',', $_POST['habilidades'] ?? ''));
            $this->redirect(APP_URL . '/enfermeras?success=2');
        } else {
            $this->view('enfermeras/form', ['enfermera' => array_merge($d, ['identificacion' => $id]), 'areas' => $this->areaModel->getAllSimple(), 'csrf' => $this->generateCsrf(), 'error' => 'Error al actualizar.']);
        }
    }

    public function delete(string $id): void {
        $this->requireAuth('admin');
        $this->verifyCsrf();
        $this->model->delete($id);
        $this->redirect(APP_URL . '/enfermeras?success=3');
    }

    private function getFormData(): array {
        return [
            'identificacion'   => $this->sanitize($_POST['identificacion']   ?? ''),
            'nombre'           => $this->sanitize($_POST['nombre']           ?? ''),
            'direccion'        => $this->sanitize($_POST['direccion']        ?? ''),
            'telefono'         => $this->sanitize($_POST['telefono']         ?? ''),
            'cargo'            => $this->sanitize($_POST['cargo']            ?? 'Enfermera'),
            'fecha_ingreso'    => $this->sanitize($_POST['fecha_ingreso']    ?? ''),
            'salario'          => $this->sanitize($_POST['salario']          ?? '0'),
            'tipo'             => $this->sanitize($_POST['tipo']             ?? 'auxiliar'),
            'anios_experiencia'=> $this->sanitize($_POST['anios_experiencia']?? '0'),
            'cod_area'         => $this->sanitize($_POST['cod_area']        ?? ''),
        ];
    }

    private function validate(array $d, bool $update = false): array {
        $e = [];
        if (!$update && empty($d['identificacion'])) $e[] = 'La identificación es requerida.';
        if (empty($d['nombre']))      $e[] = 'El nombre es requerido.';
        if (empty($d['fecha_ingreso'])) $e[] = 'La fecha de ingreso es requerida.';
        if (empty($d['cod_area']))    $e[] = 'El área es requerida.';
        if (!in_array($d['tipo'], ['auxiliar','asistente','jefe'])) $e[] = 'El tipo es inválido.';
        return $e;
    }
}
