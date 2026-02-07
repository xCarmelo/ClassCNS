<?php
// app/controller/nuevaAsistenciaController.php

require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/asistencia.php';
require_once __DIR__ . '/../model/student.php';
require_once __DIR__ . '/../model/tipoAsistencia.php';
require_once __DIR__ . '/../model/seccion.php';
require_once __DIR__ . '/../model/materia.php';


$db = Database::getInstance();
$pdo = method_exists($db, 'getConnection') ? $db->getConnection() : $db;
$asistenciaModel = new Asistencia($pdo);
$studentModel = new Student();
$tipoAsistenciaModel = new TipoAsistencia($pdo);
$seccionModel = new Seccion();
$materiaModel = new Materia();

// ID real de Informática en tu BD
$idInformatica = 2;

// Obtener filtros desde GET (para mostrar la vista)
$idSeccion = isset($_GET['seccion']) ? $_GET['seccion'] : '';
$idCorte   = isset($_GET['corte']) ? $_GET['corte'] : '';
$idMateria = isset($_GET['materia']) ? intval($_GET['materia']) : '';

// Obtener nombres para mostrar en la vista
$nombreSeccion = '';
$nombreMateria = '';

if ($idSeccion) {
    // Si tu modelo Seccion tiene método getById o similar, ajústalo
    // Por ahora, asumamos que existe un método getSeccionById o similar
    // Si no existe, puedes usar una consulta directa temporal
    $seccionData = $seccionModel->getSeccionById($idSeccion) ?? [];
    $nombreSeccion = $seccionData['name'] ?? '';
}

if ($idMateria) {
    // Usar el método correcto: find() en lugar de getMateriaById()
    $materiaData = $materiaModel->find($idMateria) ?? [];
    $nombreMateria = $materiaData['name'] ?? '';
}

// Obtener estudiantes de la sección seleccionada (para la vista)
$estudiantes = [];
if ($idSeccion) {
    $estudiantes = $studentModel->getBySeccion($idSeccion);
}

$tiposAsistencia = $tipoAsistenciaModel->obtenerTodos();

// ---- Procesar guardado masivo ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tipo_asistencia'])) {
    // Recibir datos enviados
    $postedIdSeccion = $_POST['idSeccion'] ?? '';
    $postedIdCorte   = $_POST['idCorte'] ?? '';
    $postedIdMateria = isset($_POST['idMateria']) ? intval($_POST['idMateria']) : '';
    $fecha           = $_POST['fecha'] ?? '';
    $nombreDelTema   = trim($_POST['nombreDelTema'] ?? '');
    $tiposPost       = $_POST['tipo_asistencia'];

    // Validaciones mínimas
    if (empty($postedIdSeccion) || empty($postedIdCorte) || empty($postedIdMateria) || empty($fecha) || $nombreDelTema === '') {
        $_SESSION['flash'] = [
            'type' => 'warning', 
            'message' => 'Faltan datos obligatorios (sección, corte, materia, fecha o tema).'
        ];
        header('Location: nuevaAsistenciaController.php?seccion=' . urlencode($postedIdSeccion) . 
                '&corte=' . urlencode($postedIdCorte) . 
                '&materia=' . urlencode($postedIdMateria) .
                '&nombre_seccion=' . urlencode($nombreSeccion) .
                '&nombre_materia=' . urlencode($nombreMateria));
        exit();
    }

    // Reconstruir la lista de estudiantes "mostrados" (la misma lógica que la vista)
    $estudiantesActivos = $postedIdSeccion ? $studentModel->getBySeccion($postedIdSeccion) : [];
    $allowedStudents = [];
    foreach ($estudiantesActivos as $est) {
        $allowedStudents[] = $est;
        if ($postedIdMateria === $idInformatica && isset($est['fin']) && intval($est['fin']) === 1) {
            // Si es Informática y encontramos fin=1, detenemos la lista (solo los mostrados)
            break;
        }
    }

    // Validar que todos los estudiantes mostrados tengan tipo seleccionado
    $faltantes = [];
    foreach ($allowedStudents as $est) {
        $sid = $est['id'];
        if (!isset($tiposPost[$sid]) || $tiposPost[$sid] === '' ) {
            $faltantes[] = $est['name'];
        }
    }
    if (!empty($faltantes)) {
        $primero = $faltantes[0];
        $_SESSION['flash'] = [
            'type' => 'warning', 
            'message' => 'Debes seleccionar el tipo de asistencia para todos los estudiantes mostrados. Falta: ' . $primero . ' (y otros si aplica).'
        ];
        header('Location: nuevaAsistenciaController.php?seccion=' . urlencode($postedIdSeccion) . 
                '&corte=' . urlencode($postedIdCorte) . 
                '&materia=' . urlencode($postedIdMateria) .
                '&nombre_seccion=' . urlencode($nombreSeccion) .
                '&nombre_materia=' . urlencode($nombreMateria));
        exit();
    }

    // Permitir múltiples sesiones aunque coincidan fecha/tema/materia/corte.
    // No se realiza verificación de duplicados.

    try {
        $pdo->beginTransaction();

        // Crear SIEMPRE una nueva sesión de asistencia
        $idSesion = $asistenciaModel->crearSesion([
            'idCorte' => $postedIdCorte,
            'idMateria' => $postedIdMateria,
            'nombreDelTema' => $nombreDelTema,
            'Fecha' => $fecha
        ]);
        
        if (!$idSesion) {
            throw new Exception('Error al crear la sesión de asistencia.');
        }

        // Insertar cada fila de asistencia apuntando a idSesion
        $inserted = 0;
        foreach ($allowedStudents as $est) {
            $sid = $est['id'];
            // Desde el form enviamos el idTipo (por ejemplo 1,2,3)
            $idTipo = isset($tiposPost[$sid]) ? intval($tiposPost[$sid]) : 0;
            if ($idTipo <= 0) {
                throw new Exception('Tipo de asistencia inválido para el estudiante ID ' . $sid);
            }

            // Usamos el método crear($idSesion, $idStudent, $idTipoAsistencia)
            $ok = $asistenciaModel->crear($idSesion, $sid, $idTipo);
            if (!$ok) {
                throw new Exception('Error al insertar asistencia para estudiante ID ' . $sid);
            }
            $inserted++;
        }

        $pdo->commit();

        // Éxito: redirigir al listado con mensaje flash
        $_SESSION['flash'] = [
            'type' => 'success', 
            'message' => 'Asistencia guardada correctamente. Filas insertadas: ' . $inserted
        ];
        header('Location: asistenciaController.php?seccion=' . urlencode($postedIdSeccion) . 
                '&corte=' . urlencode($postedIdCorte) . 
                '&materia=' . urlencode($postedIdMateria));
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        // enviar mensaje de error
        $err = $e->getMessage();
        $_SESSION['flash'] = [
            'type' => 'danger', 
            'message' => 'Error al guardar asistencia: ' . $err
        ];
        header('Location: nuevaAsistenciaController.php?seccion=' . urlencode($postedIdSeccion) . 
                '&corte=' . urlencode($postedIdCorte) . 
                '&materia=' . urlencode($postedIdMateria) .
                '&nombre_seccion=' . urlencode($nombreSeccion) .
                '&nombre_materia=' . urlencode($nombreMateria));
        exit();
    }
}

// Si no hay POST, mostramos la vista
// Pasar las variables necesarias a la vista
require __DIR__ . '/../view/nuevaAsistencia.php';