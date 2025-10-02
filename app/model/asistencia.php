<?php
// app/model/asistencia.php
class Asistencia {
    public function obtenerTodos() {
        $sql = "SELECT a.id, s.name as seccion, m.name as materia FROM asistencia a
                JOIN student st ON a.idStudent = st.id
                JOIN seccion s ON st.idSeccion = s.id
                JOIN materia m ON a.idMateria = m.id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function crear($data) {
        $sql = "INSERT INTO asistencia (idStudent, idCorte, idMateria, nombreDelTema, Fecha, idTipoAsistencia) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['idStudent'],
            $data['idCorte'],
            $data['idMateria'],
            $data['nombreDelTema'],
            $data['Fecha'],
            $data['idTipoAsistencia']
        ]);
        return $this->db->lastInsertId();
    }

    public function obtener($filtros = []) {
        $sql = "SELECT a.*, s.name as seccion, m.name as materia FROM asistencia a 
                JOIN student st ON a.idStudent = st.id 
                JOIN seccion s ON st.idSeccion = s.id 
                JOIN materia m ON a.idMateria = m.id 
                WHERE 1=1";
        $params = [];
        if (!empty($filtros['seccion'])) {
            $sql .= " AND s.id = ?";
            $params[] = $filtros['seccion']; 
        }
        if (!empty($filtros['corte'])) {
            $sql .= " AND a.idCorte = ?";
            $params[] = $filtros['corte'];
        }
        if (!empty($filtros['materia'])) {
            $sql .= " AND a.idMateria = ?";
            $params[] = $filtros['materia'];
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function eliminar($id) {
        $sql = "DELETE FROM asistencia WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM asistencia WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function actualizar($id, $data) {
        $sql = "UPDATE asistencia SET idStudent=?, idCorte=?, idMateria=?, nombreDelTema=?, Fecha=?, idTipoAsistencia=? WHERE id=?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['idStudent'],
            $data['idCorte'],
            $data['idMateria'],
            $data['nombreDelTema'],
            $data['Fecha'],
            $data['idTipoAsistencia'],
            $id
        ]);
    }

    public function obtenerConEstudiante($filtros = []) {
        $sql = "SELECT a.*, st.name AS estudiante, s.name as seccion, m.name as materia, ta.name AS tipo_asistencia FROM asistencia a 
                JOIN student st ON a.idStudent = st.id 
                JOIN seccion s ON st.idSeccion = s.id 
                JOIN materia m ON a.idMateria = m.id 
                JOIN tipoAsistencia ta ON a.idTipoAsistencia = ta.id 
                WHERE 1=1";
        $params = [];
        if (!empty($filtros['seccion'])) {
            $sql .= " AND s.id = ?";
            $params[] = $filtros['seccion']; 
        }
        if (!empty($filtros['corte'])) {
            $sql .= " AND a.idCorte = ?";
            $params[] = $filtros['corte'];
        }
        if (!empty($filtros['materia'])) {
            $sql .= " AND a.idMateria = ?";
            $params[] = $filtros['materia'];
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function actualizarTipoAsistencia($id, $tipoNombre) {
        // Buscar el id del tipo de asistencia por nombre
        $sqlTipo = "SELECT id FROM tipoAsistencia WHERE name = ? LIMIT 1";
        $stmtTipo = $this->db->prepare($sqlTipo);
        $stmtTipo->execute([$tipoNombre]);
        $row = $stmtTipo->fetch();
        if (!$row) return false;
        $idTipo = $row['id'];
        // Actualizar asistencia
        $sql = "UPDATE asistencia SET idTipoAsistencia = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idTipo, $id]);
    }
}
