<?php
class CitaModel {
    private PDO $db;
    public function __construct() {
        require_once __DIR__ . '/../Core/Database.php';
        $this->db = Database::getInstance();
    }
    public function getAll(array $filters = []): array {
        $where = ['1=1'];
        $params = [];
        if (!empty($filters['fecha'])) { $where[] = 'ci.fecha_cita = ?'; $params[] = $filters['fecha']; }
        if (!empty($filters['estado'])) { $where[] = 'ci.estado = ?'; $params[] = $filters['estado']; }
        if (!empty($filters['medico'])) { $where[] = 'ci.id_medico = ?'; $params[] = $filters['medico']; }
        $sql = "SELECT ci.id_cita, ci.fecha_cita, ci.hora_cita, ci.motivo, ci.estado,
                       p.nombre AS nombre_paciente, pa.eps,
                       mp.nombre AS nombre_medico, m.especialidad, ci.id_paciente, ci.id_medico
                FROM cita ci
                JOIN paciente pa ON pa.identificacion=ci.id_paciente
                JOIN persona p   ON  p.identificacion=pa.identificacion
                JOIN medico m    ON  m.identificacion=ci.id_medico
                JOIN persona mp  ON mp.identificacion=m.identificacion
                WHERE " . implode(' AND ', $where) . "
                ORDER BY ci.fecha_cita, ci.hora_cita";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    public function getById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT ci.*, p.nombre AS nombre_paciente, mp.nombre AS nombre_medico
             FROM cita ci
             JOIN paciente pa ON pa.identificacion=ci.id_paciente
             JOIN persona p   ON  p.identificacion=pa.identificacion
             JOIN medico m    ON  m.identificacion=ci.id_medico
             JOIN persona mp  ON mp.identificacion=m.identificacion
             WHERE ci.id_cita=?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    public function create(array $d): int|false {
        $stmt = $this->db->prepare(
            "INSERT INTO cita (id_paciente,id_medico,fecha_cita,hora_cita,motivo,estado,notas) VALUES (?,?,?,?,?,?,?)"
        );
        if ($stmt->execute([$d['id_paciente'],$d['id_medico'],$d['fecha_cita'],$d['hora_cita'],$d['motivo'],$d['estado']??'pendiente',$d['notas']??null])) {
            return (int)$this->db->lastInsertId();
        }
        return false;
    }
    public function update(int $id, array $d): bool {
        return $this->db->prepare(
            "UPDATE cita SET id_paciente=?,id_medico=?,fecha_cita=?,hora_cita=?,motivo=?,estado=?,notas=? WHERE id_cita=?"
        )->execute([$d['id_paciente'],$d['id_medico'],$d['fecha_cita'],$d['hora_cita'],$d['motivo'],$d['estado'],$d['notas']??null,$id]);
    }
    public function updateEstado(int $id, string $estado): bool {
        return $this->db->prepare("UPDATE cita SET estado=? WHERE id_cita=?")->execute([$estado,$id]);
    }
    public function delete(int $id): bool {
        return $this->db->prepare("DELETE FROM cita WHERE id_cita=?")->execute([$id]);
    }
    public function countHoy(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM cita WHERE fecha_cita=CURDATE()")->fetchColumn();
    }
    public function checkDisponibilidad(string $medico, string $fecha, string $hora, int $excludeId = 0): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM cita WHERE id_medico=? AND fecha_cita=? AND hora_cita=? AND estado NOT IN ('cancelada') AND id_cita != ?"
        );
        $stmt->execute([$medico,$fecha,$hora,$excludeId]);
        return (int)$stmt->fetchColumn() === 0;
    }
    public function getByFechaRange(string $inicio, string $fin): array {
        $stmt = $this->db->prepare(
            "SELECT ci.fecha_cita, COUNT(*) as total,
                    SUM(ci.estado='completada') as completadas,
                    SUM(ci.estado='cancelada') as canceladas
             FROM cita ci WHERE ci.fecha_cita BETWEEN ? AND ?
             GROUP BY ci.fecha_cita ORDER BY ci.fecha_cita"
        );
        $stmt->execute([$inicio,$fin]);
        return $stmt->fetchAll();
    }
}
