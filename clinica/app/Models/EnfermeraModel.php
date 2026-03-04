<?php
require_once __DIR__ . '/../Core/Database.php';

class EnfermeraModel {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getAll(string $search = '', string $area = ''): array {
        $sql = "
            SELECT p.identificacion, p.nombre, p.telefono, p.direccion,
                   e.cargo, e.fecha_ingreso, e.salario,
                   en.tipo, en.anios_experiencia, en.cod_area,
                   a.nombre_area
            FROM enfermera en
            JOIN empleado  e ON e.identificacion = en.identificacion
            JOIN persona   p ON p.identificacion = en.identificacion
            LEFT JOIN area a ON a.cod_area = en.cod_area
            WHERE 1=1
        ";
        $params = [];
        if ($search) { $sql .= " AND (p.nombre LIKE ? OR p.identificacion LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if ($area)   { $sql .= " AND en.cod_area = ?"; $params[] = $area; }
        $sql .= " ORDER BY p.nombre";
        $s = $this->db->prepare($sql);
        $s->execute($params);
        return $s->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(string $id): array|false {
        $s = $this->db->prepare("
            SELECT p.identificacion, p.nombre, p.telefono, p.direccion,
                   e.cargo, e.fecha_ingreso, e.salario, e.jefe_id,
                   en.tipo, en.anios_experiencia, en.cod_area,
                   a.nombre_area
            FROM enfermera en
            JOIN empleado  e ON e.identificacion = en.identificacion
            JOIN persona   p ON p.identificacion = en.identificacion
            LEFT JOIN area a ON a.cod_area = en.cod_area
            WHERE en.identificacion = ?
        ");
        $s->execute([$id]);
        return $s->fetch(PDO::FETCH_ASSOC);
    }

    public function getHabilidades(string $id): array {
        $s = $this->db->prepare("SELECT habilidad FROM habilidad_enfermera WHERE identificacion = ? ORDER BY habilidad");
        $s->execute([$id]);
        return $s->fetchAll(PDO::FETCH_COLUMN);
    }

    public function create(array $d): bool {
        try {
            $this->db->beginTransaction();
            // persona
            $s = $this->db->prepare("INSERT INTO persona (identificacion, nombre, direccion, telefono) VALUES (?,?,?,?)");
            $s->execute([$d['identificacion'], $d['nombre'], $d['direccion'], $d['telefono']]);
            // empleado
            $s = $this->db->prepare("INSERT INTO empleado (identificacion, cargo, fecha_ingreso, salario) VALUES (?,?,?,?)");
            $s->execute([$d['identificacion'], $d['cargo'] ?? 'Enfermera', $d['fecha_ingreso'], $d['salario']]);
            // enfermera
            $s = $this->db->prepare("INSERT INTO enfermera (identificacion, anios_experiencia, tipo, cod_area) VALUES (?,?,?,?)");
            $s->execute([$d['identificacion'], $d['anios_experiencia'] ?: 0, $d['tipo'], $d['cod_area']]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function update(string $id, array $d): bool {
        try {
            $this->db->beginTransaction();
            $this->db->prepare("UPDATE persona SET nombre=?, direccion=?, telefono=? WHERE identificacion=?")
                ->execute([$d['nombre'], $d['direccion'], $d['telefono'], $id]);
            $this->db->prepare("UPDATE empleado SET cargo=?, fecha_ingreso=?, salario=? WHERE identificacion=?")
                ->execute([$d['cargo'] ?? 'Enfermera', $d['fecha_ingreso'], $d['salario'], $id]);
            $this->db->prepare("UPDATE enfermera SET anios_experiencia=?, tipo=?, cod_area=? WHERE identificacion=?")
                ->execute([$d['anios_experiencia'] ?: 0, $d['tipo'], $d['cod_area'], $id]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function delete(string $id): bool {
        $s = $this->db->prepare("DELETE FROM persona WHERE identificacion = ?");
        return $s->execute([$id]);
    }

    public function saveHabilidades(string $id, array $habilidades): void {
        $this->db->prepare("DELETE FROM habilidad_enfermera WHERE identificacion = ?")->execute([$id]);
        $s = $this->db->prepare("INSERT INTO habilidad_enfermera (identificacion, habilidad) VALUES (?,?)");
        foreach (array_filter(array_unique($habilidades)) as $h) {
            $s->execute([$id, trim($h)]);
        }
    }

    public function count(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM enfermera")->fetchColumn();
    }
}
