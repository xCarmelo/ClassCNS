<?php
// app/controller/asistenciaController.php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/asistencia.php';
require_once __DIR__ . '/../model/seccion.php';
require_once __DIR__ . '/../model/materia.php';
require_once __DIR__ . '/../model/corte.php';
require_once __DIR__ . '/../model/tipoAsistencia.php';

$db = Database::getInstance();
$pdo = $db->getConnection();

$asistenciaModel = new Asistencia($pdo);
$seccionModel = new Seccion();
$materiaModel = new Materia();
$corteModel = new Corte();
$tipoAsistenciaModel = new TipoAsistencia($pdo);

// Obtener filtros
$filtros = [
    'seccion' => isset($_GET['seccion']) ? $_GET['seccion'] : '',
    'corte'   => isset($_GET['corte']) ? $_GET['corte'] : '',
    'materia' => isset($_GET['materia']) ? $_GET['materia'] : ''
];

// Eliminar asistencia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'], $_POST['id'])) {
    $exito = $asistenciaModel->eliminar($_POST['id']);
    $_SESSION['status'] = $exito ? 'success' : 'error';
    $_SESSION['action'] = 'delete';
    header('Location: asistenciaController.php?' . http_build_query($filtros));
    exit();
}

// Actualizar tipo de asistencia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_asistencia'], $_POST['tipo_asistencia'])) {
    $exito = $asistenciaModel->actualizarTipoAsistencia($_POST['id_asistencia'], $_POST['tipo_asistencia']);
    $_SESSION['status'] = $exito ? 'success' : 'error';
    $_SESSION['action'] = 'update_tipo';
    // Redirigir para evitar reenvío de formulario
    header('Location: asistenciaController.php?' . http_build_query($filtros));
    exit();
}

// Listar asistencias filtradas
$asistencias = $asistenciaModel->obtenerConEstudiante($filtros);
$secciones = method_exists($seccionModel, 'getAllSeccion') ? $seccionModel->getAllSeccion() : [];
$materias = method_exists($materiaModel, 'getAll') ? $materiaModel->getAll() : [];
$cortes = method_exists($corteModel, 'getAll') ? $corteModel->getAll() : [];
$tiposAsistencia = $tipoAsistenciaModel->obtenerTodos();

require __DIR__ . '/../view/asistencia.php';
