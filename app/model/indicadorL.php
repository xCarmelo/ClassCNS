<?php
require_once __DIR__ . '/Database.php';

class IndicadorL {

    protected static $pdo;
    protected static $table = 'indicadorl';

    public function __construct() {
        $db = Database::getInstance();
        self::$pdo = $db->getConnection();
    }

    /* ======================================================
       OBTENER TODOS (con filtros)
    ====================================================== */
public function getAllFiltered($anio = null, $idCorte = null, $idMateria = null, $idSeccion = null) {
    $sql = "
        SELECT 
            ind.id,
            ind.name,
            ind.`año` AS anio,
            ind.orden,
            ind.idMateria,
            ind.idCorte,
            m.name AS materia,
            c.name AS corte
        FROM indicadorl ind
        INNER JOIN materia m ON ind.idMateria = m.id
        INNER JOIN corte c ON ind.idCorte = c.id
        INNER JOIN enlace e ON e.idIndicador = ind.id
        INNER JOIN seccion s ON s.id = e.idSeccion
        WHERE 1 = 1
    ";

    $params = [];

    if ($anio !== null) {
        $sql .= " AND ind.`año` = :anio";
        $params[':anio'] = $anio;
    }

    if ($idCorte !== null) {
        $sql .= " AND ind.idCorte = :idCorte";
        $params[':idCorte'] = $idCorte;
    }

    if ($idMateria !== null) {
        $sql .= " AND ind.idMateria = :idMateria";
        $params[':idMateria'] = $idMateria;
    }

    if ($idSeccion !== null) {
        $sql .= " AND s.id = :idSeccion";
        $params[':idSeccion'] = $idSeccion;
    }

    $sql .= " ORDER BY ind.orden ASC";

    $stmt = self::$pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function getAll() {
    $sql = "SELECT * FROM indicadorl ORDER BY orden ASC";
    $stmt = self::$pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function getByFilters($idMateria, $anio, $idCorte) {

    $sql = "
        SELECT *
        FROM indicadorl
        WHERE idMateria = :idMateria
          AND anio = :anio
          AND idCorte = :idCorte
        ORDER BY orden ASC
    ";

    $stmt = self::$pdo->prepare($sql);
    $stmt->bindParam(':idMateria', $idMateria, PDO::PARAM_INT);
    $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
    $stmt->bindParam(':idCorte', $idCorte, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    /* ======================================================
       OBTENER EL SIGUIENTE ORDEN DISPONIBLE
    ====================================================== */
    public function getNextOrdenByMateria($idMateria, $anio, $idCorte) {

        $sql = "SELECT COALESCE(MAX(`orden`), 0) + 1
                FROM indicadorl
                WHERE idMateria = :idMateria
                  AND `año` = :anio
                  AND idCorte = :idCorte";

        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([
            ':idMateria' => $idMateria,
            ':anio'      => $anio,
            ':idCorte'   => $idCorte
        ]);

        return (int)$stmt->fetchColumn();
    }

    /* ======================================================
       CREAR INDICADOR (YA CON ORDEN)
    ====================================================== */
    public function create($name, $anio, $idCorte, $idMateria, $orden) {

        $sql = "INSERT INTO indicadorl (name, `año`, idCorte, idMateria, `orden`)
                VALUES (:name, :anio, :idCorte, :idMateria, :orden)";

        $stmt = self::$pdo->prepare($sql);
        $ok = $stmt->execute([
            ':name'      => $name,
            ':anio'      => $anio,
            ':idCorte'   => $idCorte,
            ':idMateria' => $idMateria,
            ':orden'     => $orden
        ]);

        return $ok ? self::$pdo->lastInsertId() : false;
    }

    /* ======================================================
       ACTUALIZAR INDICADOR
    ====================================================== */
    public function update($id, $name, $anio, $idCorte, $idMateria) {

        $sql = "UPDATE indicadorl
                SET name = :name,
                    `año` = :anio,
                    idCorte = :idCorte,
                    idMateria = :idMateria
                WHERE id = :id";

        $stmt = self::$pdo->prepare($sql);

        return $stmt->execute([
            ':id'        => $id,
            ':name'      => $name,
            ':anio'      => $anio,
            ':idCorte'   => $idCorte,
            ':idMateria' => $idMateria
        ]);
    }

    /* ======================================================
       ELIMINAR INDICADOR
    ====================================================== */
    public function delete($id) {
        $sql = "DELETE FROM indicadorl WHERE id = ?";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    /* ======================================================
       OBTENER POR ID
    ====================================================== */
    public function getById($id) {
        $sql = "SELECT * FROM indicadorl WHERE id = ?";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
