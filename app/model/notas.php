<?php
require_once __DIR__ . '/Database.php';

class Nota {
    public int $id;
    public int $nota;  // ✅ ahora es entero
    public int $idStudent;
    public int $idCriterio;
    public string $cualitativa;

    protected static $pdo;
    protected static $table = "nota"; 

    public function __construct() {
        $db = Database::getInstance();
        self::$pdo = $db->getConnection();
    }

    // Guardar o actualizar nota de un estudiante para un criterio
    public function saveOrUpdate($idStudent, $idCriterio, $cualitativa, $nota): bool {
        $nota = (int)$nota; // ✅ forzamos entero

        // Verificar si ya existe
        $sql = "SELECT id FROM " . self::$table . " WHERE idStudent = :idStudent AND idCriterio = :idCriterio";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([
            ':idStudent' => $idStudent,
            ':idCriterio' => $idCriterio
        ]);
        $existente = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existente) {
            // Update
            $sql = "UPDATE " . self::$table . " 
                    SET nota = :nota, cualitativa = :cualitativa 
                    WHERE idStudent = :idStudent AND idCriterio = :idCriterio";
            $stmt = self::$pdo->prepare($sql);
            return $stmt->execute([
                ':nota' => $nota,
                ':cualitativa' => $cualitativa,
                ':idStudent' => $idStudent,
                ':idCriterio' => $idCriterio
            ]);
        } else {
            // Insert
            $sql = "INSERT INTO " . self::$table . " (nota, idStudent, idCriterio, cualitativa) 
                    VALUES (:nota, :idStudent, :idCriterio, :cualitativa)";
            $stmt = self::$pdo->prepare($sql);
            return $stmt->execute([
                ':nota' => $nota,
                ':idStudent' => $idStudent,
                ':idCriterio' => $idCriterio,
                ':cualitativa' => $cualitativa
            ]);
        }
    }
// ... dentro de la clase Nota

    // Obtener todas las notas según filtros
    public function getNotasByFiltros($idSeccion, $idMateria, $anio, $idCorte) {
        $sql = "SELECT n.*, s.name as studentName, c.name as criterio, 
                       c.puntos as puntaje, ind.name as indicador
                FROM " . self::$table . " n
                INNER JOIN student s ON n.idStudent = s.id AND COALESCE(s.status,1) = 1
                INNER JOIN criterio c ON n.idCriterio = c.id
                INNER JOIN indicadorl ind ON c.idIndicadorL = ind.id
                INNER JOIN enlace e ON e.idSeccion = s.idSeccion AND e.idIndicador = ind.id
                WHERE s.idSeccion = :idSeccion 
                  AND ind.idMateria = :idMateria 
                  AND ind.`año` = :anio 
                  AND ind.idCorte = :idCorte
                ORDER BY s.name ASC, ind.id ASC, c.id ASC"; // ✅ Forzamos el orden ascendente
        
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([
            ':idSeccion' => $idSeccion,
            ':idMateria' => $idMateria,
            ':anio'      => $anio,
            ':idCorte'   => $idCorte
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// ... resto de la clase

    // Obtener notas de un estudiante para varios criterios
    public function getNotasByStudentAndCriterios($idStudent, array $criteriosIds) {
        if (empty($criteriosIds)) return [];

        $in  = str_repeat('?,', count($criteriosIds) - 1) . '?';
        $sql = "SELECT * FROM " . self::$table . " 
                WHERE idStudent = ? 
                  AND idCriterio IN ($in)";
        $stmt = self::$pdo->prepare($sql);

        $params = array_merge([$idStudent], $criteriosIds);
        $stmt->execute($params);

        // Devuelve un array asociativo [ idCriterio => filaNota ]
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($rows as $r) {
            $r['nota'] = (int)$r['nota']; // ✅ cast a entero
            $result[$r['idCriterio']] = $r;
        }
        return $result;
    }

    // Insertar o actualizar directo (alternativa a saveOrUpdate)
    public function guardarNota($idStudent, $idCriterio, $nota, $cual) {
        $nota = (int)$nota; // ✅ forzamos entero

        $sql = "INSERT INTO " . self::$table . " (idStudent, idCriterio, nota, cualitativa)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE nota = VALUES(nota), cualitativa = VALUES(cualitativa)";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([$idStudent, $idCriterio, $nota, $cual]);
    }
}
