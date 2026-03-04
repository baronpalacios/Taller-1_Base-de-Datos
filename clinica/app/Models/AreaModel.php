<?php
require_once __DIR__ . '/../Core/Database.php';

class AreaModel {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function getAll(): array {
        return $this->db->query("
            SELECT a.cod_area, a.nombre_area,
                   COUNT(c.nro_cama) AS total_camas,
                   SUM(c.estado = 'ocupada') AS camas_ocupadas,
                   SUM(c.estado = 'libre')   AS camas_libres,
                   COUNT(e.identificacion)   AS total_enfermeras
            FROM area a
            LEFT JOIN cama c     ON c.cod_area = a.cod_area
            LEFT JOIN enfermera e ON e.cod_area = a.cod_area
            GROUP BY a.cod_area, a.nombre_area
            ORDER BY a.nombre_area
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(string $id): array|false {
        $s = $this->db->prepare("SELECT * FROM area WHERE cod_area = ?");
        $s->execute([$id]);
        return $s->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllSimple(): array {
        return $this->db->query("SELECT cod_area, nombre_area FROM area ORDER BY nombre_area")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $d): bool {
        $s = $this->db->prepare("INSERT INTO area (cod_area, nombre_area) VALUES (?, ?)");
        return $s->execute([$d['cod_area'], $d['nombre_area']]);
    }

    public function update(string $id, array $d): bool {
        $s = $this->db->prepare("UPDATE area SET nombre_area = ? WHERE cod_area = ?");
        return $s->execute([$d['nombre_area'], $id]);
    }

    public function delete(string $id): bool {
        $s = $this->db->prepare("DELETE FROM area WHERE cod_area = ?");
        return $s->execute([$id]);
    }

    public function exists(string $id): bool {
        $s = $this->db->prepare("SELECT 1 FROM area WHERE cod_area = ?");
        $s->execute([$id]);
        return (bool)$s->fetch();
    }
}
