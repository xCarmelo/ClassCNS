<?php
require_once '../model/Database.php';

class Student {
    public int $id;
    public string $name; 
    public int $idSeccion;

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
    if($this->status === 1) {
        $sql = "SELECT s.id, s.name, s.idSeccion, sec.name AS seccion_name
                FROM " . self::$table . " s
                LEFT JOIN seccion sec ON s.idSeccion = sec.id";
        
        $stmt = self::$pdo->prepare($sql);
        if($stmt->execute()) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    return [];
}

# app/model/student.php
public function getBySeccion($idSeccion) {
    $sql = "SELECT * FROM student WHERE idSeccion = :idSeccion";
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
            $smt = self::$pdo->prepare('UPDATE ' . self::$table . ' SET name = :name, idSeccion = :idSeccion WHERE id = :id');
            $smt->bindParam(':name', $this->name, PDO::PARAM_STR);
            $smt->bindParam(':idSeccion', $this->idSeccion, PDO::PARAM_INT);
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
