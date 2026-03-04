<?php
class MedicoModel {
    private PDO $db;
    public function __construct() {
        require_once __DIR__ . '/../Core/Database.php';
        $this->db = Database::getInstance();
    }
    public function getAll(string $search = ''): array {
        $term = "%$search%";
        $stmt = $this->db->prepare(
            "SELECT p.identificacion, p.nombre, p.telefono, p.direccion,
                    e.cargo, e.fecha_ingreso, e.salario,
                    m.especialidad, m.nro_licencia, m.universidad, m.disponible
             FROM persona p JOIN empleado e ON e.identificacion=p.identificacion
             JOIN medico m ON m.identificacion=e.identificacion
             WHERE p.nombre LIKE ? OR m.especialidad LIKE ? OR p.identificacion LIKE ?
             ORDER BY p.nombre"
        );
        $stmt->execute([$term,$term,$term]);
        return $stmt->fetchAll();
    }
    public function getById(string $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT p.*, e.cargo, e.fecha_ingreso, e.salario, e.jefe_id,
                    m.especialidad, m.nro_licencia, m.universidad, m.disponible
             FROM persona p JOIN empleado e ON e.identificacion=p.identificacion
             JOIN medico m ON m.identificacion=e.identificacion
             WHERE p.identificacion=?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    public function getDisponibles(): array {
        $stmt = $this->db->query(
            "SELECT p.identificacion, p.nombre, m.especialidad
             FROM persona p JOIN empleado e ON e.identificacion=p.identificacion
             JOIN medico m ON m.identificacion=e.identificacion
             WHERE m.disponible=1 ORDER BY p.nombre"
        );
        return $stmt->fetchAll();
    }
    public function create(array $d): bool {
        $this->db->beginTransaction();
        try {
            $this->db->prepare("INSERT INTO persona (identificacion,nombre,direccion,telefono) VALUES (?,?,?,?)")
                ->execute([$d['identificacion'],$d['nombre'],$d['direccion'],$d['telefono']]);
            $this->db->prepare("INSERT INTO empleado (identificacion,cargo,fecha_ingreso,salario,jefe_id) VALUES (?,?,?,?,?)")
                ->execute([$d['identificacion'],$d['cargo'],$d['fecha_ingreso'],$d['salario'],$d['jefe_id']??null]);
            $this->db->prepare("INSERT INTO medico (identificacion,especialidad,nro_licencia,universidad,disponible) VALUES (?,?,?,?,?)")
                ->execute([$d['identificacion'],$d['especialidad'],$d['nro_licencia'],$d['universidad'],$d['disponible']??1]);
            $this->db->commit(); return true;
        } catch (PDOException $e) { $this->db->rollBack(); return false; }
    }
    public function update(string $id, array $d): bool {
        $this->db->beginTransaction();
        try {
            $this->db->prepare("UPDATE persona SET nombre=?,direccion=?,telefono=? WHERE identificacion=?")
                ->execute([$d['nombre'],$d['direccion'],$d['telefono'],$id]);
            $this->db->prepare("UPDATE empleado SET cargo=?,fecha_ingreso=?,salario=? WHERE identificacion=?")
                ->execute([$d['cargo'],$d['fecha_ingreso'],$d['salario'],$id]);
            $this->db->prepare("UPDATE medico SET especialidad=?,nro_licencia=?,universidad=?,disponible=? WHERE identificacion=?")
                ->execute([$d['especialidad'],$d['nro_licencia'],$d['universidad'],$d['disponible']??1,$id]);
            $this->db->commit(); return true;
        } catch (PDOException $e) { $this->db->rollBack(); return false; }
    }
    public function delete(string $id): bool {
        return $this->db->prepare("DELETE FROM persona WHERE identificacion=?")->execute([$id]);
    }
    public function countActivos(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM medico WHERE disponible=1")->fetchColumn();
    }
}
