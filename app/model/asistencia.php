<?php
// app/model/asistencia.php
class Asistencia {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Obtener una sesión por id
    public function obtenerSesion($idSesion) {
        $stmt = $this->db->prepare("SELECT * FROM asistencia_sesion WHERE id = ? LIMIT 1");
        $stmt->execute([$idSesion]);
        return $stmt->fetch();
    }

    // Obtener filas de asistencia de una sesión con datos de estudiante y tipo
    public function obtenerFilasPorSesion($idSesion) {
        $sql = "SELECT a.id, a.idStudent, a.idTipoAsistencia, st.name AS estudiante, st.idSeccion,
                       ta.id AS idTipo, ta.name AS tipoNombre
                FROM asistencia a
                JOIN student st ON st.id = a.idStudent
                JOIN tipoasistencia ta ON ta.id = a.idTipoAsistencia
                WHERE a.idSesion = ?
                ORDER BY st.NumerodeLista ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idSesion]);
        return $stmt->fetchAll();
    }

    // Actualizar tipo de asistencia por fila
    public function actualizarTipoPorFila($idAsistencia, $idTipo) {
        $stmt = $this->db->prepare("UPDATE asistencia SET idTipoAsistencia = ? WHERE id = ?");
        return $stmt->execute([$idTipo, $idAsistencia]);
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

    // ✅ Actualizar datos de la sesión (tema y fecha)
    public function actualizarSesion(int $idSesion, string $nombreDelTema, string $fecha): bool {
        $sql = "UPDATE asistencia_sesion SET nombreDelTema = ?, Fecha = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$nombreDelTema, $fecha, $idSesion]);
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
                WHERE COALESCE(st.status,1) = 1";
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
