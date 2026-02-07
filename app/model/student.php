<?php
require_once __DIR__ . '/Database.php';

class Student {

    public int $id;
    public string $name;
    public int $idSeccion;
    public int $NumerodeLista;
    public int $fin = 0;

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

    /* =========================================================
       LISTAR
    ========================================================= */

    public function getAllStudents(?int $statusFilter = 1) {
        if ($this->status !== 1) {
            return [];
        }

        $sql = "SELECT 
                    s.id,
                    s.name,
                    s.idSeccion,
                    s.NumerodeLista,
                    COALESCE(s.fin, 0) AS fin,
                    COALESCE(s.status, 1) AS status,
                    sec.name AS seccion_name
                FROM student s
                LEFT JOIN seccion sec ON s.idSeccion = sec.id";

        if ($statusFilter === 1) {
            $sql .= " WHERE COALESCE(s.status,1) = 1";
        } elseif ($statusFilter === 0) {
            $sql .= " WHERE s.status = 0";
        }

        $sql .= " ORDER BY s.idSeccion ASC, s.NumerodeLista ASC";

        $stmt = self::$pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBySeccion(int $idSeccion) {
        $sql = "SELECT * 
                FROM student 
                WHERE idSeccion = :idSeccion 
                  AND COALESCE(status,1) = 1
                ORDER BY NumerodeLista ASC";

        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':idSeccion' => $idSeccion]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentById(int $id) {
        $sql = "SELECT * FROM student WHERE id = :id LIMIT 1";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /* =========================================================
       NUMERO DE LISTA
    ========================================================= */

    public function getNextNumeroListaBySeccion(int $idSeccion): int {
        $sql = "SELECT MAX(NumerodeLista) AS ultimo
                FROM student
                WHERE idSeccion = :idSeccion
                  AND COALESCE(status,1) = 1";

        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':idSeccion' => $idSeccion]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($row && $row['ultimo'] !== null)
            ? ((int)$row['ultimo'] + 1)
            : 1;
    }

    /* =========================================================
       FIN (DIVISIÓN DE LISTA)
    ========================================================= */

    public function resetFinBySeccion(int $idSeccion): void {
        $sql = "UPDATE student SET fin = 0 WHERE idSeccion = :idSeccion";
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute([':idSeccion' => $idSeccion]);
    }

    /* =========================================================
       CREAR
    ========================================================= */

    public function addStudent(): bool {
        if ($this->status !== 1) {
            return false;
        }

        self::$pdo->beginTransaction();

        try {
            // Si marcará fin, resetear antes
            if ($this->fin === 1) {
                $this->resetFinBySeccion($this->idSeccion);
            }

            // Desplazar números si se repite
            $sqlCheck = "SELECT id 
                         FROM student 
                         WHERE idSeccion = :idSeccion
                           AND NumerodeLista = :numero
                           AND COALESCE(status,1) = 1
                         LIMIT 1";

            $stmtCheck = self::$pdo->prepare($sqlCheck);
            $stmtCheck->execute([
                ':idSeccion' => $this->idSeccion,
                ':numero' => $this->NumerodeLista
            ]);

            if ($stmtCheck->fetchColumn()) {
                $sqlShift = "UPDATE student
                             SET NumerodeLista = NumerodeLista + 1
                             WHERE idSeccion = :idSeccion
                               AND NumerodeLista >= :numero
                               AND COALESCE(status,1) = 1
                             ORDER BY NumerodeLista DESC";

                $stmtShift = self::$pdo->prepare($sqlShift);
                $stmtShift->execute([
                    ':idSeccion' => $this->idSeccion,
                    ':numero' => $this->NumerodeLista
                ]);
            }

            // Insertar
            $sql = "INSERT INTO student
                    (name, idSeccion, NumerodeLista, status, fin)
                    VALUES
                    (:name, :idSeccion, :NumerodeLista, 1, :fin)";

            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([
                ':name' => $this->name,
                ':idSeccion' => $this->idSeccion,
                ':NumerodeLista' => $this->NumerodeLista,
                ':fin' => $this->fin
            ]);

            self::$pdo->commit();
            return true;

        } catch (Throwable $e) {
            self::$pdo->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    /* =========================================================
       ACTUALIZAR
    ========================================================= */

    public function updateStudent(): bool {
        if ($this->status !== 1) {
            return false;
        }

        self::$pdo->beginTransaction();

        try {
            if ($this->fin === 1) {
                $this->resetFinBySeccion($this->idSeccion);
            }

            $sql = "UPDATE student
                    SET name = :name,
                        idSeccion = :idSeccion,
                        NumerodeLista = :NumerodeLista,
                        fin = :fin
                    WHERE id = :id";

            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([
                ':name' => $this->name,
                ':idSeccion' => $this->idSeccion,
                ':NumerodeLista' => $this->NumerodeLista,
                ':fin' => $this->fin,
                ':id' => $this->id
            ]);

            self::$pdo->commit();
            return true;

        } catch (Throwable $e) {
            self::$pdo->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    /* =========================================================
       BORRADO LÓGICO
    ========================================================= */

    public function deleteStudent(): bool {
        $sql = "UPDATE student SET status = 0 WHERE id = :id";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([':id' => $this->id]);
    }

    public function restoreStudent(): bool {
        $sql = "UPDATE student SET status = 1 WHERE id = :id";
        $stmt = self::$pdo->prepare($sql);
        return $stmt->execute([':id' => $this->id]);
    }

    /* =========================================================
       IMPORTACIÓN (NO TOCAR)
    ========================================================= */

    public function importarDesdeExcel(array $estudiantes): void {
        $sql = "INSERT INTO student
                (name, idSeccion, NumerodeLista, status, idCorte, fin)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = self::$pdo->prepare($sql);

        foreach ($estudiantes as $est) {
            $stmt->execute([
                $est['nombre'],
                $est['idSeccion'],
                $est['NumerodeLista'],
                $est['status'],
                $est['idCorte'],
                $est['fin']
            ]);
        }
    }

    public function numeroListaExiste(int $idSeccion, int $numero): bool {
    $sql = "SELECT 1 
            FROM student 
            WHERE idSeccion = :idSeccion
              AND NumerodeLista = :numero
              AND COALESCE(status,1) = 1
            LIMIT 1";

    $stmt = self::$pdo->prepare($sql);
    $stmt->execute([
        ':idSeccion' => $idSeccion,
        ':numero' => $numero
    ]);

    return (bool)$stmt->fetchColumn();
}

} 
