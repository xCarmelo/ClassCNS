<?php
require_once __DIR__ . '/Database.php';

class Asunto {
    public int $id;
    // En BD es datetime; usar string ISO 'Y-m-d H:i:s' o DateTime según controlador
    public string $fecha;
    public string $nota;
    public string $tema;
    public int $idStudent; 
    public int $statuss; 
    public int $idMateria; 

    public static $pdo;
    public static $table;
    public static $db;
    private int $status = 0;

    public function __construct() {
        self::$db = Database::getInstance();
        self::$pdo = self::$db->getConnection();
    $this->status = self::$db->getConnectionStatus();
    // Nombre real de tabla en minúsculas
    self::$table = 'asunto';
    }

public function getAllAsunto() {
    if ($this->status === 1) {
        $sql = 'SELECT 
                    a.id,
                    a.nota,
                    a.tema,
                    a.fecha,
                    a.statuss,
                    a.idMateria,
                    m.name AS materia_name,
                    a.idStudent,
                    st.name AS student_name,
                    s.name AS seccion_name
                FROM asunto a
                JOIN materia m ON a.idMateria = m.id
                JOIN student st ON a.idStudent = st.id
                JOIN seccion s ON st.idSeccion = s.id';

        $smt = self::$pdo->prepare($sql);

        if ($smt->execute()) {
            return $smt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    return [];
}

    public function addAsunto() {
        if ($this->status === 1) {
            $smt = self::$pdo->prepare('INSERT INTO ' . self::$table . ' (fecha, nota, tema, idStudent, statuss, idMateria) VALUES (:fecha, :nota, :tema, :idStudent, :statuss, :idMateria)');
            $smt->bindParam(':fecha', $this->fecha, PDO::PARAM_STR);
            $smt->bindParam(':nota', $this->nota, PDO::PARAM_STR);
            $smt->bindParam(':tema', $this->tema, PDO::PARAM_STR);
            $smt->bindParam(':idStudent', $this->idStudent, PDO::PARAM_INT);
            $smt->bindParam(':statuss', $this->statuss, PDO::PARAM_INT);
            $smt->bindParam(':idMateria', $this->idMateria, PDO::PARAM_INT);

            if ($smt->execute()) {
                return true;
            }
        }
        return false;
    } 


    public function getAsuntoById(int $id) {
        if($this->status === 1){
            $smt = self::$pdo->prepare('SELECT * FROM ' . self::$table . ' WHERE id = :id');
            $smt->bindParam(':id', $id, PDO::PARAM_INT);

            if($smt->execute()){
                return $smt->fetch(PDO::FETCH_ASSOC);
            }
        }
        return null;
    }


    public function updateAsunto() {
        if ($this->status === 1) {
            $smt = self::$pdo->prepare('UPDATE ' . self::$table . ' SET fecha = :fecha, nota = :nota, tema = :tema, idStudent = :idStudent, statuss = :statuss, idMateria = :idMateria WHERE id = :id');
            $smt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $smt->bindParam(':fecha', $this->fecha);
            $smt->bindParam(':nota', $this->nota, PDO::PARAM_STR);
            $smt->bindParam(':tema', $this->tema, PDO::PARAM_STR);
            $smt->bindParam(':idStudent', $this->idStudent, PDO::PARAM_INT);
            $smt->bindParam(':statuss', $this->statuss, PDO::PARAM_INT);
            $smt->bindParam(':idMateria', $this->idMateria, PDO::PARAM_INT);

            if ($smt->execute()) {
                return true;
            }
        }
        return false;
    }

        public function deleteAsunto(){
        if($this->status === 1){
            $smt = self::$pdo->prepare('DELETE FROM ' . self::$table . ' WHERE id = :id');
            $smt->bindParam(':id', $this->id, PDO::PARAM_INT);

            if($smt->execute()){
                return true;
            }
        }
        return false;
    }

}
