<?php
// app/model/asistencia.php
class Asistencia {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // ✅ Crear nueva sesión de asistencia
    public function crearSesion($data) {
        $sql = "INSERT INTO asistencia_sesion (idCorte, idMateria, nombreDelTema, Fecha)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['idCorte'],
            $data['idMateria'],
            $data['nombreDelTema'],
            $data['Fecha']
        ]);
        return $this->db->lastInsertId();
    }

    // ✅ Insertar asistencias de todos los estudiantes en una sesión
    public function crear($idSesion, $idStudent, $idTipoAsistencia) {
        $sql = "INSERT INTO asistencia (idSesion, idStudent, idTipoAsistencia)
                VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idSesion, $idStudent, $idTipoAsistencia]);
    }

    // ✅ Obtener todas las asistencias (con datos de la sesión)
    public function obtenerConEstudiante($filtros = []) {
        $sql = "SELECT a.*, st.name AS estudiante, s.name as seccion, 
                       m.name as materia, ta.name AS tipo_asistencia,
                       ses.nombreDelTema, ses.Fecha
                FROM asistencia a
                JOIN asistencia_sesion ses ON a.idSesion = ses.id
                JOIN student st ON a.idStudent = st.id 
                JOIN seccion s ON st.idSeccion = s.id 
                JOIN materia m ON ses.idMateria = m.id 
                JOIN tipoasistencia ta ON a.idTipoAsistencia = ta.id 
                WHERE 1=1";
        $params = [];

        if (!empty($filtros['seccion'])) {
            $sql .= " AND s.id = ?";
            $params[] = $filtros['seccion'];
        }
        if (!empty($filtros['corte'])) {
            $sql .= " AND ses.idCorte = ?";
            $params[] = $filtros['corte'];
        }
        if (!empty($filtros['materia'])) {
            $sql .= " AND ses.idMateria = ?";
            $params[] = $filtros['materia'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // ✅ Eliminar una sesión completa (y sus asistencias)
    public function eliminarSesion($idSesion) {
        $this->db->prepare("DELETE FROM asistencia WHERE idSesion = ?")->execute([$idSesion]);
        return $this->db->prepare("DELETE FROM asistencia_sesion WHERE id = ?")->execute([$idSesion]);
    }

    // ✅ Actualizar tipo de asistencia
    public function actualizarTipoAsistencia($id, $tipoNombre) {
    $sqlTipo = "SELECT id FROM tipoasistencia WHERE name = ? LIMIT 1";
        $stmtTipo = $this->db->prepare($sqlTipo);
        $stmtTipo->execute([$tipoNombre]);
        $row = $stmtTipo->fetch();
        if (!$row) return false;
        $idTipo = $row['id'];
        $sql = "UPDATE asistencia SET idTipoAsistencia = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$idTipo, $id]);
    }
}
