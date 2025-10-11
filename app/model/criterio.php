<?php
// app/model/criterio.php
require_once __DIR__ . '/Database.php';

class Criterio {
    private $pdo;
    private $table = 'criterio';

    public function __construct() {
        $db = Database::getInstance();
        $this->pdo = $db->getConnection();
    }

    /**
     * Crear criterio asociado a un indicador
     * @param int $idIndicadorL
     * @param string $name
     * @param float|int $puntos
     * @return bool
     */
    public function create(int $idIndicadorL, string $name, $puntos): bool {
        $sql = "INSERT INTO {$this->table} (name, puntos, idIndicadorL)
                VALUES (:name, :puntos, :idIndicadorL)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':name' => $name,
            ':puntos' => $puntos,
            ':idIndicadorL' => $idIndicadorL
        ]);
    }

    # app/model/criterio.php
    public function getByIndicadorL($idIndicadorL) {
        return $this->getByIndicador($idIndicadorL);
    }


    /**
     * Obtener criterios por indicador
     * @param int $idIndicadorL
     * @return array
     */
    public function getByIndicador(int $idIndicadorL): array {
        $sql = "SELECT id, name, puntos, idIndicadorL FROM {$this->table}
                WHERE idIndicadorL = :idIndicadorL ORDER BY id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':idIndicadorL' => $idIndicadorL]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Obtener un criterio por id
     */
    public function get(int $id): ?array {
        $sql = "SELECT id, name, puntos, idIndicadorL FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    /**
     * Actualizar criterio
     */
    public function update(int $id, string $name, $puntos): bool {
        $sql = "UPDATE {$this->table} SET name = :name, puntos = :puntos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id, ':name' => $name, ':puntos' => $puntos]);
    }

    /**
     * Eliminar criterio por id
     */
    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Eliminar todos los criterios de un indicador (útil antes de reinsertar)
     */
    public function deleteByIndicador(int $idIndicadorL): bool {
        $sql = "DELETE FROM {$this->table} WHERE idIndicadorL = :idIndicadorL";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':idIndicadorL' => $idIndicadorL]);
    }

    public function getById(int $idCriterio): ?array {
        $sql = "SELECT id, name, puntos, idIndicadorL FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $idCriterio]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Alias find (igual que getById, por conveniencia)
    public function find(int $idCriterio): ?array {
        return $this->getById($idCriterio);
    }
}
