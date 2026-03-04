<?php
class UsuarioModel {
    private PDO $db;
    public function __construct() {
        require_once __DIR__ . '/../Core/Database.php';
        $this->db = Database::getInstance();
    }

    public function getAll(): array {
        return $this->db->query("SELECT id,username,email,rol,activo,ultimo_login,created_at FROM usuarios ORDER BY username")->fetchAll();
    }

    public function getByUsername(string $username): ?array {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE username=? AND activo=1");
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    public function getByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email=?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function getById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $d): bool {
        return $this->db->prepare(
            "INSERT INTO usuarios (username,email,password_hash,rol,activo) VALUES (?,?,?,?,1)"
        )->execute([$d['username'], $d['email'], password_hash($d['password'], PASSWORD_BCRYPT), $d['rol']]);
    }

    public function update(int $id, array $d): bool {
        return $this->db->prepare(
            "UPDATE usuarios SET username=?,email=?,rol=?,activo=? WHERE id=?"
        )->execute([$d['username'], $d['email'], $d['rol'], $d['activo'], $id]);
    }

    public function updatePassword(int $id, string $hash): bool {
        $finalHash = (strlen($hash) < 60) ? password_hash($hash, PASSWORD_BCRYPT) : $hash;
        return $this->db->prepare("UPDATE usuarios SET password_hash=? WHERE id=?")->execute([$finalHash, $id]);
    }

    public function updateEmail(int $id, string $email): bool {
        return $this->db->prepare("UPDATE usuarios SET email=? WHERE id=?")->execute([$email, $id]);
    }

    public function updateLogin(int $id): void {
        $this->db->prepare("UPDATE usuarios SET ultimo_login=NOW() WHERE id=?")->execute([$id]);
    }

    public function delete(int $id): bool {
        return $this->db->prepare("DELETE FROM usuarios WHERE id=?")->execute([$id]);
    }

    public function setResetToken(string $email, string $token): bool {
        return $this->db->prepare(
            "UPDATE usuarios SET token_reset=?,token_expira=DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email=?"
        )->execute([$token, $email]);
    }

    public function getByResetToken(string $token): ?array {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE token_reset=? AND token_expira > NOW()");
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }

    public function clearResetToken(int $id): void {
        $this->db->prepare("UPDATE usuarios SET token_reset=NULL,token_expira=NULL WHERE id=?")->execute([$id]);
    }
}
