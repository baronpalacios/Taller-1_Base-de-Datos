<?php
class PacienteModel {
    private PDO $db;
    public function __construct() {
        require_once __DIR__ . '/../Core/Database.php';
        $this->db = Database::getInstance();
    }
    public function getAll(string $search = ''): array {
        $term = "%$search%";
        $stmt = $this->db->prepare(
            "SELECT p.identificacion, p.nombre, p.telefono, p.direccion,
                    pa.nss, pa.eps, pa.labor,
                    TIMESTAMPDIFF(YEAR, pa.fecha_nacimiento, CURDATE()) AS edad,
                    pa.fecha_nacimiento
             FROM persona p JOIN paciente pa ON pa.identificacion = p.identificacion
             WHERE p.nombre LIKE ? OR p.identificacion LIKE ? OR pa.nss LIKE ?
             ORDER BY p.nombre"
        );
        $stmt->execute([$term, $term, $term]);
        return $stmt->fetchAll();
    }
    public function getById(string $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT p.*, pa.nss, pa.fecha_nacimiento, pa.labor, pa.eps, pa.fecha_afiliacion,
                    TIMESTAMPDIFF(YEAR, pa.fecha_nacimiento, CURDATE()) AS edad
             FROM persona p JOIN paciente pa ON pa.identificacion = p.identificacion
             WHERE p.identificacion = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    public function create(array $d): bool {
        $this->db->beginTransaction();
        try {
            $this->db->prepare("INSERT INTO persona (identificacion,nombre,direccion,telefono) VALUES (?,?,?,?)")
                ->execute([$d['identificacion'],$d['nombre'],$d['direccion'],$d['telefono']]);
            $this->db->prepare("INSERT INTO paciente (identificacion,nss,fecha_nacimiento,labor,eps,fecha_afiliacion) VALUES (?,?,?,?,?,?)")
                ->execute([$d['identificacion'],$d['nss'],$d['fecha_nacimiento'],$d['labor'],$d['eps'],$d['fecha_afiliacion']]);
            $this->db->commit(); return true;
        } catch (PDOException $e) { $this->db->rollBack(); return false; }
    }
    public function update(string $id, array $d): bool {
        $this->db->beginTransaction();
        try {
            $this->db->prepare("UPDATE persona SET nombre=?,direccion=?,telefono=? WHERE identificacion=?")
                ->execute([$d['nombre'],$d['direccion'],$d['telefono'],$id]);
            $this->db->prepare("UPDATE paciente SET nss=?,fecha_nacimiento=?,labor=?,eps=?,fecha_afiliacion=? WHERE identificacion=?")
                ->execute([$d['nss'],$d['fecha_nacimiento'],$d['labor'],$d['eps'],$d['fecha_afiliacion'],$id]);
            $this->db->commit(); return true;
        } catch (PDOException $e) { $this->db->rollBack(); return false; }
    }
    public function delete(string $id): bool {
        return $this->db->prepare("DELETE FROM persona WHERE identificacion=?")->execute([$id]);
    }
    public function exists(string $id): bool {
        $stmt = $this->db->prepare("SELECT 1 FROM paciente WHERE identificacion=?");
        $stmt->execute([$id]); return (bool)$stmt->fetch();
    }
    public function countTotal(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM paciente")->fetchColumn();
    }
    public function countNewThisMonth(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM paciente WHERE fecha_afiliacion >= DATE_FORMAT(CURDATE(),'%Y-%m-01')")->fetchColumn();
    }
}
