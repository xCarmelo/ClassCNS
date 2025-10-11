<?php
require_once __DIR__ . '/Database.php';

class Materia {
    public int $id;
    public string $name;

    protected static $pdo;
    protected static $table = 'materia';

    public function __construct() {
        $db = Database::getInstance();
        self::$pdo = $db->getConnection();
    }

    public function getAll(): array {
        $sql = "SELECT * FROM `" . self::$table . "` ORDER BY name ASC";
        $stmt = self::$pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array {
        $sql = "SELECT * FROM `" . self::$table . "` WHERE id = :id";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    public function create(string $name): bool {
        $sql = "INSERT INTO `" . self::$table . "` (name) VALUES (:name)";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([':name' => $name]);
    }

    public function updateMateria(int $id, string $name): bool { 
        $sql = "UPDATE `" . self::$table . "` SET name = :name WHERE id = :id";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([':name' => $name, ':id' => $id]);
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM `" . self::$table . "` WHERE id = :id";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
