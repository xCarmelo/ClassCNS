<?php
require_once __DIR__ . '/Database.php';

class IndicadorL {
    public int $id;
    public string $name;
    public int $anio;     // usaremos variable 'anio' en PHP, en SQL la columna es `año`
    public int $idCorte;
    public int $idMateria;

    protected static $pdo;
    protected static $table = 'indicadorl';

    public function __construct() {
        $db = Database::getInstance();
        self::$pdo = $db->getConnection();
    }

    // obtener todos con nombre del corte y materia
    public function getAll(): array {
        $sql = "SELECT i.id, i.name, i.`año` AS anio, c.name AS corte, m.name AS materia, i.idCorte, i.idMateria
                FROM `" . self::$table . "` i
                LEFT JOIN corte c ON i.idCorte = c.id
                LEFT JOIN materia m ON i.idMateria = m.id
                ORDER BY i.id DESC";
        $stmt = self::$pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    # app/model/indicadorl.php
public function getByFilters($idMateria, $anio, $idCorte) {
    $sql = "SELECT * FROM indicadorl 
            WHERE idMateria = :idMateria 
              AND año = :anio 
              AND idCorte = :idCorte";
    $stmt = self::$pdo->prepare($sql);
    $stmt->execute([
        ':idMateria' => $idMateria,
        ':anio'      => $anio,   // 👈 aunque la columna sea "año", el placeholder puede llamarse ":anio"
        ':idCorte'   => $idCorte
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



    public function find(int $id): ?array {
        $sql = "SELECT * FROM `" . self::$table . "` WHERE id = :id";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    // Crear indicador — devuelve el id insertado (int) o false si falla
public function create(string $name, int $anio, ?int $idCorte, ?int $idMateria) {
    $sql = "INSERT INTO indicadorl (name, año, idMateria, idCorte)
            VALUES (:name, :anio, :idMateria, :idCorte)";
    $stmt = self::$pdo->prepare($sql);
    $ok = $stmt->execute([
        ':name'      => $name,
        ':anio'      => $anio,
        ':idMateria' => $idMateria,
        ':idCorte'   => $idCorte
    ]);

    if ($ok) {
        return (int) self::$pdo->lastInsertId(); // Devuelve el ID autogenerado
    }
    error_log("IndicadorL::create error: " . json_encode($stmt->errorInfo()));
    return false;
}



    public function update(int $id, string $name, int $anio, ?int $idCorte = null, ?int $idMateria = null): bool {
        $sql = "UPDATE `" . self::$table . "` SET name = :name, `año` = :anio, idCorte = :idCorte, idMateria = :idMateria WHERE id = :id";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([
            ':name' => $name,
            ':anio' => $anio,
            ':idCorte' => $idCorte,
            ':idMateria' => $idMateria,
            ':id' => $id
        ]);
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM `" . self::$table . "` WHERE id = :id";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
