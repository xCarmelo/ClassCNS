<?php
require_once "Database.php";

class BackupModel {

    private PDO $db;

    public function __construct() {
        Database::getInstance();          // asegura la conexión
        $this->db = Database::getConnection();
    }

public function create(string $nombreArchivo, ?string $descripcion = null): bool {
    $sql = "INSERT INTO backup (nombre_archivo, fecha, descripcion)
            VALUES (:nombre, NOW(), :descripcion)";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':nombre', $nombreArchivo);
    $stmt->bindParam(':descripcion', $descripcion);
    return $stmt->execute();
}


    public function getAll(): array {
        $sql = "SELECT * FROM backup ORDER BY fecha DESC";
        return $this->db->query($sql)->fetchAll();
    }



    public function getById(int $id): array|false {
    $stmt = $this->db->prepare("SELECT * FROM backup WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

public function delete(int $id): bool {
    $stmt = $this->db->prepare("DELETE FROM backup WHERE id = :id");
    return $stmt->execute([':id' => $id]);
}

}
