<?php
require_once __DIR__ . '/Database.php';

class Enlace {
    public int $id;
    public int $idIndicador;
    public int $idSeccion;

    protected static $pdo;
    protected static $table = 'enlace';

    public function __construct() {
        $db = Database::getInstance();
        self::$pdo = $db->getConnection();
    }

    // obtener todos los registros de la tabla enlace
    public function getAll(): array {
        $sql = "SELECT * FROM `" . self::$table . "`";
        $stmt = self::$pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // buscar por id (clave primaria compuesta en tu DDL: idIndicador,idSeccion)
    public function find($idIndicador, $idSeccion): ?array {
        $sql = "SELECT * FROM `" . self::$table . "` WHERE idIndicador = :idIndicador AND idSeccion = :idSeccion";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':idIndicador' => $idIndicador, ':idSeccion' => $idSeccion]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    // obtener secciones (nombre) vinculadas a un indicador
    public function getByIndicador(int $idIndicador): array {
        $sql = "SELECT e.idIndicador, e.idSeccion, s.name as name
                FROM `" . self::$table . "` e
                INNER JOIN seccion s ON e.idSeccion = s.id
                WHERE e.idIndicador = :idIndicador";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':idIndicador' => $idIndicador]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // alias para compatibilidad con controladores
    public function getSeccionesByIndicador(int $idIndicador): array {
        return $this->getByIndicador($idIndicador);
    }

    // crear enlace (retorna true/false)
    public function create(int $idIndicador, int $idSeccion): bool {
        $sql = "INSERT INTO `" . self::$table . "` (idIndicador, idSeccion) VALUES (:idIndicador, :idSeccion)";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([':idIndicador' => $idIndicador, ':idSeccion' => $idSeccion]);
    }

    // eliminar por PK compuesta
    public function delete(int $idIndicador, int $idSeccion): bool {
        $sql = "DELETE FROM `" . self::$table . "` WHERE idIndicador = :idIndicador AND idSeccion = :idSeccion";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([':idIndicador' => $idIndicador, ':idSeccion' => $idSeccion]);
    }

    // eliminar todos los enlaces de un indicador
    public function deleteByIndicador(int $idIndicador): bool {
        $sql = "DELETE FROM `" . self::$table . "` WHERE idIndicador = :idIndicador";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([':idIndicador' => $idIndicador]);
    }

    
}
