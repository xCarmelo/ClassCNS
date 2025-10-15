<?php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/asistencia.php';
require_once __DIR__ . '/../model/tipoAsistencia.php';

$db = Database::getInstance();
$pdo = method_exists($db, 'getConnection') ? $db->getConnection() : $db;
$asistenciaModel = new Asistencia($pdo);
$tipoAsistenciaModel = new TipoAsistencia($pdo);


$idSesion = isset($_GET['idSesion']) ? intval($_GET['idSesion']) : 0;
$seccion = $_GET['seccion'] ?? '';
$corte = $_GET['corte'] ?? '';
$materia = $_GET['materia'] ?? '';

if ($idSesion <= 0) {
    $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Sesión inválida.'];
    header('Location: asistenciaController.php?seccion=' . urlencode($seccion) . '&corte=' . urlencode($corte) . '&materia=' . urlencode($materia));
    exit();
}

$tiposAsistencia = $tipoAsistenciaModel->obtenerTodos();

// Guardado unificado: tema, fecha y tipos de asistencia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevoTema = trim($_POST['nombreDelTema'] ?? '');
    $nuevaFecha = trim($_POST['Fecha'] ?? '');
    $postAsistencia = $_POST['asistencia'] ?? [];
    try {
        if ($idSesion <= 0) { throw new Exception('Sesión inválida.'); }
        if ($nuevoTema === '') { throw new Exception('El tema no puede estar vacío.'); }
        if ($nuevaFecha === '') { throw new Exception('La fecha es obligatoria.'); }

        $pdo->beginTransaction();

        // Actualizar metadatos de la sesión
        if (!$asistenciaModel->actualizarSesion($idSesion, $nuevoTema, $nuevaFecha)) {
            throw new Exception('No se pudo actualizar la sesión.');
        }

        // Actualizar tipos de asistencia por fila
        if (is_array($postAsistencia)) {
            foreach ($postAsistencia as $idAsistencia => $idTipo) {
                $idAsistencia = intval($idAsistencia);
                $idTipo = intval($idTipo);
                if ($idAsistencia > 0 && $idTipo > 0) {
                    $asistenciaModel->actualizarTipoPorFila($idAsistencia, $idTipo);
                }
            }
        }

        $pdo->commit();
        header('Location: asistenciaController.php?seccion=' . urlencode($seccion) . '&corte=' . urlencode($corte) . '&materia=' . urlencode($materia) . '&flashType=success&flashMsg=' . urlencode('Asistencia actualizada correctamente.'));
        exit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        $msg = $e->getMessage();
        header('Location: editAsistenciaController.php?idSesion=' . urlencode($idSesion) . '&seccion=' . urlencode($seccion) . '&corte=' . urlencode($corte) . '&materia=' . urlencode($materia) . '&flashType=danger&flashMsg=' . urlencode('Error al actualizar: ' . $msg));
        exit();
    }
}

$sesion = $asistenciaModel->obtenerSesion($idSesion);
$filas = $asistenciaModel->obtenerFilasPorSesion($idSesion);

require __DIR__ . '/../view/editAsistencia.php';
