<?php
require_once __DIR__ . '/Database.php';

class Student {
    public int $id;
    public string $name; 
    public int $idSeccion;
    public int $NumerodeLista;
    private int $status = 0;

    public static $pdo;
    public static $table;
    public static $db;

    public function __construct() {
    self::$db = Database::getInstance();
    self::$pdo = self::$db->getConnection();
    $this->status = self::$db->getConnectionStatus();
    self::$table = 'student';
    }

public function getAllStudents(?int $statusFilter = 1) {
    if ($this->status === 1) {
        $sql = "SELECT 
                    s.id, 
                    s.name, 
                    s.idSeccion, 
                    s.NumerodeLista,
                    COALESCE(s.status,1) AS status,
                    sec.name AS seccion_name
                FROM " . self::$table . " s
                LEFT JOIN seccion sec ON s.idSeccion = sec.id";

        $params = [];
        if ($statusFilter === 1) {
            $sql .= " WHERE COALESCE(s.status,1) = 1"; // Activos (NULL tratado como activo)
        } elseif ($statusFilter === 0) {
            $sql .= " WHERE s.status = 0"; // Eliminados
        }
        $sql .= " ORDER BY s.id ASC";
        
        $stmt = self::$pdo->prepare($sql);
        if ($stmt->execute($params)) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    return [];
}


public function getBySeccion($idSeccion) {
    $sql = "SELECT * FROM student WHERE idSeccion = :idSeccion AND COALESCE(status,1) = 1 ORDER BY NumerodeLista ASC";
    $stmt = self::$pdo->prepare($sql);
    $stmt->execute([':idSeccion' => $idSeccion]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function deleteStudent(){
        // Borrado lógico: status = 0
        if($this->status === 1){
            $smt = self::$pdo->prepare('UPDATE ' . self::$table . ' SET status = 0 WHERE id = :id');
            $smt->bindParam(':id', $this->id, PDO::PARAM_INT);
            if($smt->execute()){
                return true;
            }
        }
        return false;
    }

    public function restoreStudent(){
        // Restaurar lógico: status = 1
        if($this->status === 1){
            $smt = self::$pdo->prepare('UPDATE ' . self::$table . ' SET status = 1 WHERE id = :id');
            $smt->bindParam(':id', $this->id, PDO::PARAM_INT);
            if($smt->execute()){
                return true;
            }
        }
        return false;
    }

    public function addStudent() {
        if($this->status === 1){
            // Validación de unicidad por sección
            if ($this->numeroListaExiste($this->idSeccion, $this->NumerodeLista)) {
                return false;
            }

            $smt = self::$pdo->prepare('INSERT INTO ' . self::$table . ' (name, idSeccion, NumerodeLista, status) VALUES (:name, :idSeccion, :NumerodeLista, 1)');
            $smt->bindParam(':name', $this->name, PDO::PARAM_STR);
            $smt->bindParam(':idSeccion', $this->idSeccion, PDO::PARAM_INT);
            $smt->bindParam(':NumerodeLista', $this->NumerodeLista, PDO::PARAM_INT);

            if($smt->execute()){
                return true;
            }
        }
        return false;
    }

    // Verifica si ya existe un Número de lista en la misma sección (solo activos)
    public function numeroListaExiste(int $idSeccion, int $numero): bool {
        $sql = 'SELECT 1 FROM ' . self::$table . ' WHERE idSeccion = :idSeccion AND NumerodeLista = :numero AND COALESCE(status,1) = 1 LIMIT 1';
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':idSeccion' => $idSeccion, ':numero' => $numero]);
        return (bool)$stmt->fetchColumn();
    }

    public function updateStudent() {
        if($this->status === 1){
            $smt = self::$pdo->prepare('UPDATE ' . self::$table . ' SET name = :name, idSeccion = :idSeccion, NumerodeLista = :NumerodeLista WHERE id = :id');
            $smt->bindParam(':name', $this->name, PDO::PARAM_STR);
            $smt->bindParam(':idSeccion', $this->idSeccion, PDO::PARAM_INT);
            $smt->bindParam(':NumerodeLista', $this->NumerodeLista, PDO::PARAM_INT);
            $smt->bindParam(':id', $this->id, PDO::PARAM_INT);

            if($smt->execute()){
                return true;
            }
        }
        return false;
    }

    public function getStudentById($id) {
        if($this->status === 1){
            $smt = self::$pdo->prepare('SELECT * FROM ' . self::$table . ' WHERE id = :id');
            $smt->bindParam(':id', $id, PDO::PARAM_INT);

            if($smt->execute()){
                return $smt->fetch(PDO::FETCH_ASSOC);
            }
        }
        return null;
    }

}
