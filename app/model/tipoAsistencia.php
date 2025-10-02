<?php
// app/model/tipoAsistencia.php
class TipoAsistencia {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function obtenerTodos() {
        $sql = "SELECT * FROM tipoAsistencia";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
