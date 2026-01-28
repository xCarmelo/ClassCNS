<?php
ob_start();
// app/controller/NotasController.php

ini_set('display_errors', 0);  // evitar que salgan warnings/notices en la salida JSON
ini_set('log_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../model/notas.php';
require_once __DIR__ . '/../model/criterio.php';
require_once __DIR__ . '/../model/indicadorL.php';
require_once __DIR__ . '/../model/seccion.php';
require_once __DIR__ . '/../model/materia.php';
require_once __DIR__ . '/../model/corte.php';
require_once __DIR__ . '/../model/student.php';
require_once __DIR__ . '/../model/enlace.php';
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/nota.php';

// Acción (acepta GET o POST)
$action = $_REQUEST['action'] ?? null;

// --- Acción: guardar nota via AJAX (POST) ---
if ($action === 'save') {
    ob_end_clean(); // limpia cualquier salida previa
    header('Content-Type: application/json; charset=utf-8');

    $logFile = __DIR__ . '/../../log_nota.txt';
    function log_nota($msg) {
        global $logFile;
        file_put_contents($logFile, date('Y-m-d H:i:s') . ' ' . $msg . "\n", FILE_APPEND);
    }

    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            log_nota('Método no permitido: ' . $_SERVER['REQUEST_METHOD']);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }

        $idStudent  = isset($_POST['idStudent']) ? (int)$_POST['idStudent'] : 0;
        $idCriterio = isset($_POST['idCriterio']) ? (int)$_POST['idCriterio'] : 0;
        $cual       = isset($_POST['cualitativa']) ? trim($_POST['cualitativa']) : '';

        if (!$idStudent || !$idCriterio || $cual === '') {
            http_response_code(400);
            log_nota('Datos incompletos: idStudent=' . $idStudent . ', idCriterio=' . $idCriterio . ', cualitativa=' . $cual);
            echo json_encode(['error' => 'Datos incompletos (idStudent, idCriterio, cualitativa)']);
            exit;
        }

        $MAP = ['AA' => 5, 'AS' => 4, 'AF' => 3, 'AI' => 2];
        if (!isset($MAP[$cual])) {
            http_response_code(400);
            log_nota('Valor cualitativo inválido: ' . $cual);
            echo json_encode(['error' => 'Valor cualitativo inválido']);
            exit;
        }

        $notaModel = new Nota();
        $criterioModel = new Criterio();

        $crit = null;
        if (method_exists($criterioModel, 'get')) {
            $crit = $criterioModel->get($idCriterio);
        } elseif (method_exists($criterioModel, 'find')) {
            $crit = $criterioModel->find($idCriterio);
        } elseif (method_exists($criterioModel, 'getById')) {
            $crit = $criterioModel->getById($idCriterio);
        }

        if (!$crit) {
            try {
                $db = Database::getInstance();
                $pdo = $db->getConnection();
                $stmt = $pdo->prepare("SELECT id, name, puntos, puntaje FROM criterio WHERE id = :id LIMIT 1");
                $stmt->execute([':id' => $idCriterio]);
                $crit = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (\Throwable $e) {
                http_response_code(500);
                log_nota('Error DB criterio: ' . $e->getMessage());
                echo json_encode(['error' => 'No se pudo obtener información del criterio']);
                exit;
            }
        }

        if (!$crit) {
            http_response_code(404);
            log_nota('Criterio no encontrado: ' . $idCriterio);
            echo json_encode(['error' => 'Criterio no encontrado']);
            exit;
        }

        $puntosMax = 0;
        if (isset($crit['puntos']) && $crit['puntos'] !== null) $puntosMax = (int)$crit['puntos'];
        elseif (isset($crit['puntaje']) && $crit['puntaje'] !== null) $puntosMax = (int)$crit['puntaje'];

        $valorEscala = (int)$MAP[$cual];
        $notaCuant = ($puntosMax > 0) ? (int)round(($valorEscala / 5) * $puntosMax) : 0;

        $saved = false;
        $saveMsg = '';
        if (method_exists($notaModel, 'saveOrUpdate')) {
            $saved = $notaModel->saveOrUpdate($idStudent, $idCriterio, $cual, $notaCuant);
            $saveMsg = 'saveOrUpdate';
        } elseif (method_exists($notaModel, 'guardarNota')) {
            $saved = $notaModel->guardarNota($idStudent, $idCriterio, $notaCuant, $cual);
            $saveMsg = 'guardarNota';
        } else {
            $methods = get_class_methods($notaModel);
            foreach ($methods as $m) {
                if (stripos($m, 'save') !== false || stripos($m, 'guardar') !== false) {
                    try {
                        $res = $notaModel->$m($idStudent, $idCriterio, $notaCuant, $cual);
                        $saved = ($res === true || $res === 1 || $res !== false);
                        $saveMsg = $m;
                        break;
                    } catch (\Throwable $e) {
                        log_nota('Error en método ' . $m . ': ' . $e->getMessage());
                    }
                }
            }
        }

        if ($saved) {
            log_nota('Guardado OK: student=' . $idStudent . ', criterio=' . $idCriterio . ', nota=' . $notaCuant . ', cual=' . $cual . ', método=' . $saveMsg);
            echo json_encode(['ok' => true, 'nota' => $notaCuant, 'puntosMax' => $puntosMax]);
            exit;
        } else {
            http_response_code(500);
            log_nota('No se pudo guardar la nota: student=' . $idStudent . ', criterio=' . $idCriterio . ', nota=' . $notaCuant . ', cual=' . $cual . ', método=' . $saveMsg);
            echo json_encode(['error' => 'No se pudo guardar la nota']);
            exit;
        }
    } catch (Throwable $e) {
        http_response_code(500);
        log_nota('Excepción general: ' . $e->getMessage());
        echo json_encode(['error' => 'Excepción: ' . $e->getMessage()]);
        exit;
    }
}

// --- Acción: limpiar nota (borrar) via AJAX (POST) ---
if ($action === 'clear') {
    ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    $logFile = __DIR__ . '/../../log_nota.txt';
    function log_nota_clear($msg) {
        global $logFile;
        file_put_contents($logFile, date('Y-m-d H:i:s') . ' [CLEAR] ' . $msg . "\n", FILE_APPEND);
    }
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            log_nota_clear('Método no permitido: ' . $_SERVER['REQUEST_METHOD']);
            echo json_encode(['error' => 'Método no permitido']);
            exit;
        }
        $idStudent  = isset($_POST['idStudent']) ? (int)$_POST['idStudent'] : 0;
        $idCriterio = isset($_POST['idCriterio']) ? (int)$_POST['idCriterio'] : 0;
        if (!$idStudent || !$idCriterio) {
            http_response_code(400);
            log_nota_clear('Datos incompletos: idStudent=' . $idStudent . ', idCriterio=' . $idCriterio);
            echo json_encode(['error' => 'Datos incompletos (idStudent, idCriterio)']);
            exit;
        }
        try {
            $db = Database::getInstance();
            $pdo = $db->getConnection();
            $stmt = $pdo->prepare('DELETE FROM nota WHERE idStudent = :sid AND idCriterio = :cid');
            $ok = $stmt->execute([':sid' => $idStudent, ':cid' => $idCriterio]);
            if ($ok) {
                log_nota_clear('Borrado OK: student=' . $idStudent . ', criterio=' . $idCriterio);
                echo json_encode(['ok' => true]);
                exit;
            } else {
                http_response_code(500);
                log_nota_clear('Borrado falló: student=' . $idStudent . ', criterio=' . $idCriterio);
                echo json_encode(['error' => 'No se pudo borrar la nota']);
                exit;
            }
        } catch (Throwable $e) {
            http_response_code(500);
            log_nota_clear('Excepción DB: ' . $e->getMessage());
            echo json_encode(['error' => 'Excepción: ' . $e->getMessage()]);
            exit;
        }
    } catch (Throwable $e) {
        http_response_code(500);
        log_nota_clear('Excepción general: ' . $e->getMessage());
        echo json_encode(['error' => 'Excepción: ' . $e->getMessage()]);
        exit;
    }
}

// --- Acción por defecto: mostrar la vista ---
$seccionModel   = new Seccion();
$materiaModel   = new Materia();
$corteModel     = new Corte();
$indicadorModel = new IndicadorL();
$studentModel   = new Student();
$criterioModel  = new Criterio();
$notaModel      = new Nota();
$enlaceModel    = new Enlace();

$secciones = method_exists($seccionModel, 'getAll') ? $seccionModel->getAll() : [];
$materias  = method_exists($materiaModel, 'getAll') ? $materiaModel->getAll() : [];
$cortes    = method_exists($corteModel, 'getAll') ? $corteModel->getAll() : [];

$idSeccion = isset($_GET['seccion']) ? (int)$_GET['seccion'] : 0;
$idMateria = isset($_GET['materia']) ? (int)$_GET['materia'] : 0;
$anio      = isset($_GET['anio']) ? (int)$_GET['anio'] : 0;
$idCorte   = isset($_GET['corte']) ? (int)$_GET['corte'] : 0;

$estudiantes = [];
$indicadores = [];
$criterios   = [];
$notas       = [];

if ($idSeccion && $idMateria && $anio && $idCorte) {
    // Preferir método que considera la sección (enlace) si existe
    if (method_exists($indicadorModel, 'getAllFiltered')) {
        $indicadores = $indicadorModel->getAllFiltered($anio, $idCorte, $idMateria, $idSeccion);
    } elseif (method_exists($indicadorModel, 'getByFilters')) {
        $indicadores = $indicadorModel->getByFilters($idMateria, $anio, $idCorte);
    } elseif (method_exists($indicadorModel, 'getAll')) {
        $tmp = $indicadorModel->getAll();
        foreach ($tmp as $t) {
            $tAno = $t['anio'] ?? ($t['año'] ?? null);
            if ((int)($t['idMateria'] ?? 0) === $idMateria && (int)$tAno === $anio && (int)($t['idCorte'] ?? 0) === $idCorte) {
                $indicadores[] = $t;
            }
        }
    }

    if (method_exists($studentModel, 'getBySeccion')) {
        $estudiantes = $studentModel->getBySeccion($idSeccion);
    } elseif (method_exists($studentModel, 'getAllStudents')) {
        $all = $studentModel->getAllStudents();
        foreach ($all as $s) {
            if (isset($s['idSeccion']) && (int)$s['idSeccion'] === $idSeccion) $estudiantes[] = $s;
        }
    }

    $allCriteriosIds = [];
    foreach ($indicadores as $ind) {
        $idInd = (int)$ind['id'];
        $lista = [];
        if (method_exists($criterioModel, 'getByIndicador')) {
            $lista = $criterioModel->getByIndicador($idInd);
        } elseif (method_exists($criterioModel, 'getByIndicadorL')) {
            $lista = $criterioModel->getByIndicadorL($idInd);
        } else {
            try {
                $db = Database::getInstance();
                $pdo = $db->getConnection();
                $stmt = $pdo->prepare("SELECT * FROM criterio WHERE idIndicadorL = :idInd ORDER BY id");
                $stmt->execute([':idInd' => $idInd]);
                $lista = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Throwable $e) {
                $lista = [];
            }
        }
        $criterios[$idInd] = $lista;
        foreach ($lista as $c) {
            if (isset($c['id'])) $allCriteriosIds[] = (int)$c['id'];
        }
    }
    $allCriteriosIds = array_values(array_unique($allCriteriosIds));

    if (!empty($estudiantes) && !empty($allCriteriosIds)) {
        foreach ($estudiantes as $stu) {
            $idStu = (int)$stu['id'];
            if (method_exists($notaModel, 'getNotasByStudentAndCriterios')) {
                $notas[$idStu] = $notaModel->getNotasByStudentAndCriterios($idStu, $allCriteriosIds);
            } else {
                try {
                    $db = Database::getInstance();
                    $pdo = $db->getConnection();
                    $in = str_repeat('?,', count($allCriteriosIds) - 1) . '?';
                    $sql = "SELECT * FROM nota WHERE idStudent = ? AND idCriterio IN ($in)";
                    $stmt = $pdo->prepare($sql);
                    $params = array_merge([$idStu], $allCriteriosIds);
                    $stmt->execute($params);
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $map = [];
                    foreach ($rows as $r) $map[$r['idCriterio']] = $r;
                    $notas[$idStu] = $map;
                } catch (\Throwable $e) {
                    $notas[$idStu] = [];
                }
            }
        }
    }
}

// Renderizar vista
require "../view/notas.php";
exit;