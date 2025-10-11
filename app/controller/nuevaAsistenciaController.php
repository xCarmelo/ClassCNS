<?php
// app/controller/nuevaAsistenciaController.php

require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/asistencia.php';
require_once __DIR__ . '/../model/student.php';
require_once __DIR__ . '/../model/tipoAsistencia.php';

$db = Database::getInstance();
$pdo = method_exists($db, 'getConnection') ? $db->getConnection() : $db; // soporte flexible
$asistenciaModel = new Asistencia($pdo);
$studentModel = new Student();
$tipoAsistenciaModel = new TipoAsistencia($pdo);

// ID real de Informática en tu BD
$idInformatica = 2;

// Obtener filtros desde GET (para mostrar la vista)
$idSeccion = isset($_GET['seccion']) ? $_GET['seccion'] : '';
$idCorte   = isset($_GET['corte']) ? $_GET['corte'] : '';
$idMateria = isset($_GET['materia']) ? intval($_GET['materia']) : '';

// Obtener estudiantes de la sección seleccionada (para la vista)
$estudiantes = $idSeccion ? $studentModel->getBySeccion($idSeccion) : [];
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
        echo "<script>alert('Faltan datos obligatorios (sección, corte, materia, fecha o tema).'); history.back();</script>";
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
        $primero = htmlspecialchars($faltantes[0]);
        echo "<script>alert('Debes seleccionar el tipo de asistencia para todos los estudiantes mostrados. Falta: {$primero} (y otros si aplica).'); history.back();</script>";
        exit();
    }

    // Evitar duplicar sesiones: comprobar si ya existe una sesión igual
    $stmtCheck = $pdo->prepare("SELECT id FROM asistencia_sesion WHERE idCorte = ? AND idMateria = ? AND nombreDelTema = ? AND Fecha = ? LIMIT 1");
    $stmtCheck->execute([$postedIdCorte, $postedIdMateria, $nombreDelTema, $fecha]);
    $existingSesionId = $stmtCheck->fetchColumn();

    if ($existingSesionId) {
        // Si ya existe y ya tiene filas asociadas, abortamos para evitar duplicados
        $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM asistencia WHERE idSesion = ?");
        $stmtCount->execute([$existingSesionId]);
        $count = (int)$stmtCount->fetchColumn();
        if ($count > 0) {
            echo "<script>alert('Ya existe una asistencia guardada para esa sesión (fecha/tema). Si quieres modificar, editala en el listado.'); window.location.href='asistenciaController.php?seccion={$postedIdSeccion}&corte={$postedIdCorte}&materia={$postedIdMateria}';</script>";
            exit();
        }
        // Si existe pero no tiene filas (caso raro), reutilizamos $existingSesionId
        $idSesion = (int)$existingSesionId;
    }

    try {
        $pdo->beginTransaction();

        // Si no existe la sesión, la creamos
        if (empty($existingSesionId)) {
            // Usamos el método del modelo (crearSesion) — devuelve id
            $idSesion = $asistenciaModel->crearSesion([
                'idCorte' => $postedIdCorte,
                'idMateria' => $postedIdMateria,
                'nombreDelTema' => $nombreDelTema,
                'Fecha' => $fecha
            ]);

            if (!$idSesion) {
                throw new Exception('Error al crear la sesión de asistencia.');
            }
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

        // Éxito: redirigir al listado con mensaje
        $mensaje = rawurlencode("Asistencia guardada correctamente. Filas insertadas: $inserted");
        echo "<script>alert(decodeURIComponent('{$mensaje}')); window.location.href='asistenciaController.php?seccion={$postedIdSeccion}&corte={$postedIdCorte}&materia={$postedIdMateria}';</script>";
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        // enviar mensaje de error
        $err = addslashes($e->getMessage());
        echo "<script>alert('Error al guardar asistencia: {$err}'); history.back();</script>";
        exit();
    }
}

// Si no hay POST, mostramos la vista
require __DIR__ . '/../view/nuevaAsistencia.php';
