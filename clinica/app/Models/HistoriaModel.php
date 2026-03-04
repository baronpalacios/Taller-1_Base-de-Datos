<?php
class HistoriaModel {
    private PDO $db;
    public function __construct() {
        require_once __DIR__ . '/../Core/Database.php';
        $this->db = Database::getInstance();
    }
    public function getByPaciente(string $idPaciente): ?array {
        $stmt = $this->db->prepare("SELECT * FROM historia_clinica WHERE id_paciente=?");
        $stmt->execute([$idPaciente]);
        return $stmt->fetch() ?: null;
    }
    public function crearHistoria(string $idPaciente): int {
        $this->db->prepare("INSERT INTO historia_clinica (id_paciente) VALUES (?)")->execute([$idPaciente]);
        return (int)$this->db->lastInsertId();
    }
    public function getConsultas(int $idHistoria): array {
        $stmt = $this->db->prepare(
            "SELECT c.*, mp.nombre AS nombre_medico, m.especialidad
             FROM consulta c
             JOIN medico m  ON m.identificacion=c.id_medico
             JOIN persona mp ON mp.identificacion=m.identificacion
             WHERE c.id_historia=? ORDER BY c.fecha_consulta DESC, c.nro_consulta DESC"
        );
        $stmt->execute([$idHistoria]);
        return $stmt->fetchAll();
    }
    public function addConsulta(int $idHistoria, array $d): bool {
        $nro = $this->getNextNroConsulta($idHistoria);
        return $this->db->prepare(
            "INSERT INTO consulta (id_historia,nro_consulta,fecha_consulta,precio,resumen,id_medico,diagnostico,tratamiento) VALUES (?,?,?,?,?,?,?,?)"
        )->execute([$idHistoria,$nro,$d['fecha_consulta'],$d['precio'],$d['resumen'],$d['id_medico'],$d['diagnostico']??null,$d['tratamiento']??null]);
    }
    public function updateConsulta(int $idHistoria, int $nroConsulta, array $d): bool {
        return $this->db->prepare(
            "UPDATE consulta SET fecha_consulta=?,precio=?,resumen=?,diagnostico=?,tratamiento=? WHERE id_historia=? AND nro_consulta=?"
        )->execute([$d['fecha_consulta'],$d['precio'],$d['resumen'],$d['diagnostico']??null,$d['tratamiento']??null,$idHistoria,$nroConsulta]);
    }
    private function getNextNroConsulta(int $idHistoria): int {
        $stmt = $this->db->prepare("SELECT COALESCE(MAX(nro_consulta),0)+1 FROM consulta WHERE id_historia=?");
        $stmt->execute([$idHistoria]);
        return (int)$stmt->fetchColumn();
    }
}
