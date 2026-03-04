<?php
require_once __DIR__ . '/../Core/Controller.php';
require_once __DIR__ . '/../Models/CitaModel.php';
require_once __DIR__ . '/../Models/PacienteModel.php';

class ReporteController extends Controller {
    public function index(): void {
        $this->requireAuth('admin','medico');
        $this->view('reportes/index', ['csrf' => $this->generateCsrf()]);
    }

    public function citas(): void {
        $this->requireAuth('admin','medico');
        $inicio = $this->sanitize($_GET['inicio'] ?? date('Y-m-01'));
        $fin    = $this->sanitize($_GET['fin']    ?? date('Y-m-t'));
        $model  = new CitaModel();
        $data   = $model->getAll(['fecha' => '']);
        // Get all for range
        require_once __DIR__ . '/../Core/Database.php';
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT ci.id_cita, ci.fecha_cita, ci.hora_cita, ci.estado, ci.motivo,
                    p.nombre AS nombre_paciente, mp.nombre AS nombre_medico, m.especialidad
             FROM cita ci
             JOIN paciente pa ON pa.identificacion=ci.id_paciente
             JOIN persona  p  ON  p.identificacion=pa.identificacion
             JOIN medico   m  ON  m.identificacion=ci.id_medico
             JOIN persona  mp ON mp.identificacion=m.identificacion
             WHERE ci.fecha_cita BETWEEN ? AND ?
             ORDER BY ci.fecha_cita, ci.hora_cita"
        );
        $stmt->execute([$inicio, $fin]);
        $citas = $stmt->fetchAll();

        if (isset($_GET['export']) && $_GET['export'] === 'csv') {
            $this->exportCsv($citas, 'reporte_citas_' . $inicio . '_' . $fin, [
                'id_cita', 'fecha_cita', 'hora_cita', 'estado', 'motivo', 'nombre_paciente', 'nombre_medico', 'especialidad'
            ]);
            return;
        }
        $this->view('reportes/citas', ['citas' => $citas, 'inicio' => $inicio, 'fin' => $fin]);
    }

    public function pacientes(): void {
        $this->requireAuth('admin','medico');
        $inicio = $this->sanitize($_GET['inicio'] ?? date('Y-m-01'));
        $fin    = $this->sanitize($_GET['fin']    ?? date('Y-m-t'));
        require_once __DIR__ . '/../Core/Database.php';
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT p.identificacion, p.nombre, p.telefono, pa.eps, pa.labor,
                    pa.fecha_afiliacion,
                    TIMESTAMPDIFF(YEAR, pa.fecha_nacimiento, CURDATE()) AS edad
             FROM persona p JOIN paciente pa ON pa.identificacion=p.identificacion
             WHERE pa.fecha_afiliacion BETWEEN ? AND ?
             ORDER BY pa.fecha_afiliacion DESC"
        );
        $stmt->execute([$inicio, $fin]);
        $pacientes = $stmt->fetchAll();

        if (isset($_GET['export']) && $_GET['export'] === 'csv') {
            $this->exportCsv($pacientes, 'reporte_pacientes_' . $inicio, [
                'identificacion','nombre','telefono','eps','labor','fecha_afiliacion','edad'
            ]);
            return;
        }
        $this->view('reportes/pacientes', ['pacientes' => $pacientes, 'inicio' => $inicio, 'fin' => $fin]);
    }

    private function exportCsv(array $rows, string $filename, array $columns): void {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
        fputcsv($out, $columns);
        foreach ($rows as $row) {
            fputcsv($out, array_intersect_key($row, array_flip($columns)));
        }
        fclose($out);
        exit;
    }
}
