<?php
require_once __DIR__ . '/../Core/Database.php';

class CamaModel {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getAll(string $area = '', string $estado = ''): array {
        $sql = "
            SELECT c.nro_cama, c.cod_area, c.estado,
                   a.nombre_area,
                   ac.id_paciente, ac.fecha_asignacion, ac.duracion_dias,
                   p.nombre AS nombre_paciente
            FROM cama c
            JOIN area a ON a.cod_area = c.cod_area
            LEFT JOIN asignacion_cama ac ON ac.nro_cama = c.nro_cama AND ac.cod_area = c.cod_area
                AND ac.fecha_asignacion = (
                    SELECT MAX(fecha_asignacion) FROM asignacion_cama
                    WHERE nro_cama = c.nro_cama AND cod_area = c.cod_area
                )
            LEFT JOIN persona p ON p.identificacion = ac.id_paciente
            WHERE 1=1
        ";
        $params = [];
        if ($area)   { $sql .= " AND c.cod_area = ?";  $params[] = $area; }
        if ($estado) { $sql .= " AND c.estado = ?";    $params[] = $estado; }
        $sql .= " ORDER BY a.nombre_area, c.nro_cama";
        $s = $this->db->prepare($sql);
        $s->execute($params);
        return $s->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResumenPorArea(): array {
        return $this->db->query("
            SELECT a.cod_area, a.nombre_area,
                   COUNT(c.nro_cama)           AS total,
                   SUM(c.estado='ocupada')      AS ocupadas,
                   SUM(c.estado='libre')        AS libres,
                   ROUND(SUM(c.estado='ocupada')/COUNT(c.nro_cama)*100,1) AS pct_ocupacion
            FROM area a
            LEFT JOIN cama c ON c.cod_area = a.cod_area
            GROUP BY a.cod_area, a.nombre_area
            ORDER BY a.nombre_area
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $d): bool {
        $s = $this->db->prepare("INSERT INTO cama (nro_cama, cod_area, estado) VALUES (?,?,?)");
        return $s->execute([$d['nro_cama'], $d['cod_area'], $d['estado'] ?? 'libre']);
    }

    public function updateEstado(int $nro, string $area, string $estado): bool {
        $s = $this->db->prepare("UPDATE cama SET estado = ? WHERE nro_cama = ? AND cod_area = ?");
        return $s->execute([$estado, $nro, $area]);
    }

    public function delete(int $nro, string $area): bool {
        $s = $this->db->prepare("DELETE FROM cama WHERE nro_cama = ? AND cod_area = ?");
        return $s->execute([$nro, $area]);
    }

    public function asignar(array $d): bool {
        try {
            $this->db->beginTransaction();
            $s = $this->db->prepare("INSERT INTO asignacion_cama (id_paciente, nro_cama, cod_area, fecha_asignacion, duracion_dias) VALUES (?,?,?,?,?)");
            $s->execute([$d['id_paciente'], $d['nro_cama'], $d['cod_area'], $d['fecha_asignacion'], $d['duracion_dias']]);
            $this->db->prepare("UPDATE cama SET estado = 'ocupada' WHERE nro_cama = ? AND cod_area = ?")
                ->execute([$d['nro_cama'], $d['cod_area']]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function liberar(int $nro, string $area): bool {
        try {
            $this->db->beginTransaction();
            $this->db->prepare("UPDATE cama SET estado = 'libre' WHERE nro_cama = ? AND cod_area = ?")
                ->execute([$nro, $area]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getHistorial(int $nro, string $area): array {
        $s = $this->db->prepare("
            SELECT ac.*, p.nombre AS nombre_paciente
            FROM asignacion_cama ac
            JOIN persona p ON p.identificacion = ac.id_paciente
            WHERE ac.nro_cama = ? AND ac.cod_area = ?
            ORDER BY ac.fecha_asignacion DESC
        ");
        $s->execute([$nro, $area]);
        return $s->fetchAll(PDO::FETCH_ASSOC);
    }

    public function estadisticas(): array {
        return $this->db->query("
            SELECT COUNT(*) AS total,
                   SUM(estado='libre')   AS libres,
                   SUM(estado='ocupada') AS ocupadas
            FROM cama
        ")->fetch(PDO::FETCH_ASSOC);
    }
}
