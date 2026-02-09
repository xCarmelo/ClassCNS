<?php

require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/asistencia.php';
require_once __DIR__ . '/../model/student.php';
require_once __DIR__ . '/../model/tipoAsistencia.php';

$db = Database::getInstance();
$pdo = method_exists($db, 'getConnection') ? $db->getConnection() : $db;

$asistenciaModel = new Asistencia($pdo);
$studentModel = new Student();
$tipoAsistenciaModel = new TipoAsistencia($pdo);

// ==================
// DATOS PARA LA VISTA
// ==================
$idSeccion = $_GET['seccion'] ?? '';
$idCorte   = $_GET['corte'] ?? '';
$idMateria = isset($_GET['materia']) ? (int)$_GET['materia'] : '';

$estudiantes = $idSeccion ? $studentModel->getBySeccion($idSeccion) : [];
$tiposAsistencia = $tipoAsistenciaModel->obtenerTodos();

// ==================
// GUARDAR ASISTENCIA
// ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $postedIdSeccion = $_POST['idSeccion'] ?? '';
    $postedIdCorte   = $_POST['idCorte'] ?? '';
    $postedIdMateria = isset($_POST['idMateria']) ? (int)$_POST['idMateria'] : 0;
    $fecha           = $_POST['fecha'] ?? '';
    $nombreDelTema   = trim($_POST['nombreDelTema'] ?? '');
    $tiposPost       = $_POST['tipo_asistencia'] ?? [];

    // Validaciones mínimas
    if (!$postedIdSeccion || !$postedIdCorte || !$postedIdMateria || !$fecha || $nombreDelTema === '') {
        $_SESSION['flash'] = [
            'type' => 'warning',
            'message' => 'Faltan datos obligatorios.'
        ];
        header("Location: nuevaAsistenciaController.php?seccion=$postedIdSeccion&corte=$postedIdCorte&materia=$postedIdMateria");
        exit;
    }

    // 👉 Filtrar SOLO estudiantes seleccionados
    $estudiantesSeleccionados = [];

    foreach ($tiposPost as $idStudent => $idTipo) {
        if ((int)$idTipo > 0) {
            $estudiantesSeleccionados[] = [
                'idStudent' => (int)$idStudent,
                'idTipo'    => (int)$idTipo
            ];
        }
    }

    // ⚠️ Si no seleccionó ninguno
    if (empty($estudiantesSeleccionados)) {
        $_SESSION['flash'] = [
            'type' => 'warning',
            'message' => 'Debes seleccionar al menos un estudiante.'
        ];
        header("Location: nuevaAsistenciaController.php?seccion=$postedIdSeccion&corte=$postedIdCorte&materia=$postedIdMateria");
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Crear sesión
        $idSesion = $asistenciaModel->crearSesion([
            'idCorte' => $postedIdCorte,
            'idMateria' => $postedIdMateria,
            'nombreDelTema' => $nombreDelTema,
            'Fecha' => $fecha
        ]);

        if (!$idSesion) {
            throw new Exception('No se pudo crear la sesión.');
        }

        // Insertar SOLO los seleccionados
        foreach ($estudiantesSeleccionados as $row) {
            $ok = $asistenciaModel->crear(
                $idSesion,
                $row['idStudent'],
                $row['idTipo']
            );

            if (!$ok) {
                throw new Exception('Error al guardar asistencia.');
            }
        }

        $pdo->commit();

        $_SESSION['flash'] = [
            'type' => 'success',
            'message' => 'Asistencia guardada correctamente.'
        ];

        header("Location: asistenciaController.php?seccion=$postedIdSeccion&corte=$postedIdCorte&materia=$postedIdMateria");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();

        $_SESSION['flash'] = [
            'type' => 'danger',
            'message' => $e->getMessage()
        ];

        header("Location: nuevaAsistenciaController.php?seccion=$postedIdSeccion&corte=$postedIdCorte&materia=$postedIdMateria");
        exit;
    }
}

// ==================
// MOSTRAR VISTA
// ==================
require __DIR__ . '/../view/nuevaAsistencia.php';
