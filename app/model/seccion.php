<?php
require_once '../model/Database.php';

class Seccion {
    public int $id;
    public string $name; 

    public static $pdo;
    public static $table;
    public static $db;

    public function __construct() {
        self::$db = Database::getInstance();
        self::$pdo = self::$db->getConnection();
        $this->status = self::$db->getConnectionStatus();
        self::$table = 'seccion';
    }

    public function getAllSeccion() {
        if($this->status === 1){
            $smt = self::$pdo->prepare('SELECT * FROM ' . self::$table);

            if($smt->execute()){
                return $smt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        return [];
    }

    public function getSeccionById(int $id) {
        if($this->status === 1){
            $smt = self::$pdo->prepare('SELECT * FROM ' . self::$table . ' WHERE id = :id');
            $smt->bindParam(':id', $id, PDO::PARAM_INT);

            if($smt->execute()){
                return $smt->fetch(PDO::FETCH_ASSOC);
            }
        }
        return null;
    }

    public function addSeccion() {
        if($this->status === 1){
            $smt = self::$pdo->prepare('INSERT INTO ' . self::$table . ' (name) VALUES (:name)');
            $smt->bindParam(':name', $this->name, PDO::PARAM_STR);

            if($smt->execute()){
                return true;
            }
        }
        return false;
    }

    public function updateSeccion() {
        if($this->status === 1){
            $smt = self::$pdo->prepare('UPDATE ' . self::$table . ' SET name = :name WHERE id = :id');
            $smt->bindParam(':name', $this->name, PDO::PARAM_STR);
            $smt->bindParam(':id', $this->id, PDO::PARAM_INT);

            if($smt->execute()){
                return true;
            }
        }
        return false;
    }

        public function deleteSeccion(){
        if($this->status === 1){
            $smt = self::$pdo->prepare('DELETE FROM ' . self::$table . ' WHERE id = :id');
            $smt->bindParam(':id', $this->id, PDO::PARAM_INT);

            if($smt->execute()){
                return true;
            }
        }
        return false;
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

    public function create(string $name, ?int $idIndicador = null): bool {
        $sql = "INSERT INTO `" . self::$table . "` (name, idIndicador) VALUES (:name, :idIndicador)";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([':name' => $name, ':idIndicador' => $idIndicador]);
    }

    public function update(int $id, string $name, ?int $idIndicador = null): bool {
        $sql = "UPDATE `" . self::$table . "` SET name = :name, idIndicador = :idIndicador WHERE id = :id";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([':name' => $name, ':idIndicador' => $idIndicador, ':id' => $id]);
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM `" . self::$table . "` WHERE id = :id";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}



