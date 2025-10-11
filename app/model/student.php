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

public function getAllStudents() {
    if ($this->status === 1) {
        $sql = "SELECT 
                    s.id, 
                    s.name, 
                    s.idSeccion, 
                    s.NumerodeLista,  
                    s.idCorte,
                    sec.name AS seccion_name,
                    c.name AS corte_name
                FROM " . self::$table . " s
                LEFT JOIN seccion sec ON s.idSeccion = sec.id
                LEFT JOIN corte c ON s.idCorte = c.id
                ORDER BY s.id ASC";
        
        $stmt = self::$pdo->prepare($sql);
        if ($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    return [];
}


public function getBySeccion($idSeccion) {
    $sql = "SELECT * FROM student WHERE idSeccion = :idSeccion ORDER BY NumerodeLista ASC";
    $stmt = self::$pdo->prepare($sql);
    $stmt->execute([':idSeccion' => $idSeccion]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function deleteStudent(){
        if($this->status === 1){
            $smt = self::$pdo->prepare('DELETE FROM ' . self::$table . ' WHERE id = :id');
            $smt->bindParam(':id', $this->id, PDO::PARAM_INT);

            if($smt->execute()){
                return true;
            }
        }
        return false;
    }

    public function addStudent() {
        if($this->status === 1){
            $smt = self::$pdo->prepare('INSERT INTO ' . self::$table . ' (name, idSeccion) VALUES (:name, :idSeccion)');
            $smt->bindParam(':name', $this->name, PDO::PARAM_STR);
            $smt->bindParam(':idSeccion', $this->idSeccion, PDO::PARAM_INT);

            if($smt->execute()){
                return true;
            }
        }
        return false;
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
