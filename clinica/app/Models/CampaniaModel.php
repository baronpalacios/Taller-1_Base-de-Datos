<?php
require_once __DIR__ . '/../Core/Database.php';

class CampaniaModel {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getAll(string $search = ''): array {
        $sql = "
            SELECT c.cod_campania, c.nombre, c.objetivo, c.fecha_realizacion,
                   c.id_medico_resp, p.nombre AS nombre_medico,
                   COUNT(pc.id_paciente) AS total_participantes
            FROM campania c
            JOIN medico  m ON m.identificacion = c.id_medico_resp
            JOIN persona p ON p.identificacion = c.id_medico_resp
            LEFT JOIN participacion_campania pc ON pc.cod_campania = c.cod_campania
            WHERE 1=1
        ";
        $params = [];
        if ($search) { $sql .= " AND (c.nombre LIKE ? OR c.cod_campania LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        $sql .= " GROUP BY c.cod_campania ORDER BY c.fecha_realizacion DESC";
        $s = $this->db->prepare($sql);
        $s->execute($params);
        return $s->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(string $id): array|false {
        $s = $this->db->prepare("
            SELECT c.*, p.nombre AS nombre_medico
            FROM campania c
            JOIN persona p ON p.identificacion = c.id_medico_resp
            WHERE c.cod_campania = ?
        ");
        $s->execute([$id]);
        return $s->fetch(PDO::FETCH_ASSOC);
    }

    public function getParticipantes(string $cod): array {
        $s = $this->db->prepare("
            SELECT pc.id_paciente, p.nombre, pa.eps, pa.nss
            FROM participacion_campania pc
            JOIN persona  p  ON p.identificacion  = pc.id_paciente
            JOIN paciente pa ON pa.identificacion = pc.id_paciente
            WHERE pc.cod_campania = ?
            ORDER BY p.nombre
        ");
        $s->execute([$cod]);
        return $s->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $d): bool {
        $s = $this->db->prepare("INSERT INTO campania (cod_campania, nombre, objetivo, fecha_realizacion, id_medico_resp) VALUES (?,?,?,?,?)");
        return $s->execute([$d['cod_campania'], $d['nombre'], $d['objetivo'], $d['fecha_realizacion'], $d['id_medico_resp']]);
    }

    public function update(string $id, array $d): bool {
        $s = $this->db->prepare("UPDATE campania SET nombre=?, objetivo=?, fecha_realizacion=?, id_medico_resp=? WHERE cod_campania=?");
        return $s->execute([$d['nombre'], $d['objetivo'], $d['fecha_realizacion'], $d['id_medico_resp'], $id]);
    }

    public function delete(string $id): bool {
        return $this->db->prepare("DELETE FROM campania WHERE cod_campania = ?")->execute([$id]);
    }

    public function addParticipante(string $cod, string $pacienteId): bool {
        $s = $this->db->prepare("INSERT IGNORE INTO participacion_campania (id_paciente, cod_campania) VALUES (?,?)");
        return $s->execute([$pacienteId, $cod]);
    }

    public function removeParticipante(string $cod, string $pacienteId): bool {
        $s = $this->db->prepare("DELETE FROM participacion_campania WHERE id_paciente=? AND cod_campania=?");
        return $s->execute([$pacienteId, $cod]);
    }

    public function count(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM campania")->fetchColumn();
    }
}
