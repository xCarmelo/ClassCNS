<?php
// app/controller/nuevaAsistenciaController.php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/asistencia.php';
require_once __DIR__ . '/../model/student.php';
require_once __DIR__ . '/../model/tipoAsistencia.php';

$db = Database::getInstance();
$pdo = $db->getConnection();
$asistenciaModel = new Asistencia($pdo);
$studentModel = new Student();
$tipoAsistenciaModel = new TipoAsistencia($pdo);

// Obtener filtros desde GET
$idSeccion = isset($_GET['seccion']) ? $_GET['seccion'] : '';
$idCorte = isset($_GET['corte']) ? $_GET['corte'] : '';
$idMateria = isset($_GET['materia']) ? $_GET['materia'] : '';

// Obtener estudiantes de la sección seleccionada
$estudiantes = $idSeccion ? $studentModel->getBySeccion($idSeccion) : [];
$tiposAsistencia = $tipoAsistenciaModel->obtenerTodos();

// Procesar guardado masivo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo_asistencia'])) {
    $idSeccion = $_POST['idSeccion'];
    $idCorte = $_POST['idCorte'];
    $idMateria = $_POST['idMateria'];
    $fecha = $_POST['fecha'];
    $nombreDelTema = $_POST['nombreDelTema'];
    $tipos = $_POST['tipo_asistencia'];
    $exitos = 0;
    foreach ($tipos as $idStudent => $idTipoAsistencia) {
        $data = [
            'idStudent' => $idStudent,
            'idCorte' => $idCorte,
            'idMateria' => $idMateria,
            'nombreDelTema' => $nombreDelTema,
            'Fecha' => $fecha,
            'idTipoAsistencia' => $idTipoAsistencia
        ];
        if ($asistenciaModel->crear($data)) {
            $exitos++;
        }
    }
    $mensaje = $exitos > 0 ? 'Asistencia guardada correctamente.' : 'Error al guardar asistencia.';
    echo "<script>alert('$mensaje');window.location.href='asistenciaController.php?seccion=$idSeccion&corte=$idCorte&materia=$idMateria';</script>";
    exit();
}

require __DIR__ . '/../view/nuevaAsistencia.php';
