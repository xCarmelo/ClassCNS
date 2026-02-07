<?php

require_once '../model/student.php'; 
require_once '../model/materia.php';
require_once '../model/corte.php';
require_once '../model/seccion.php';



/**
 * Vista principal de estudiantes (NO SE TOCA)
 */
function showStudent ()
{
    $studentModel = new Student();

    $status = isset($_GET['status']) && $_GET['status'] === '0' ? 0 : 1;
    $students = $studentModel->getAllStudents($status);

    $materiaModel = new Materia();
    $materias = $materiaModel->getAll();

    $corteModel = new Corte();
    $cortes = $corteModel->getAll();

    $seccionModel = new Seccion();
    $secciones = $seccionModel->getAll();

    require "../view/student.php";
}

/**
 * AJAX: siguiente número de lista
 */
function getNextNumeroLista()
{
    header('Content-Type: application/json');

    if (empty($_GET['idSeccion'])) {
        echo json_encode(['error' => 'Sección no enviada']);
        exit;
    }

    $idSeccion = (int) $_GET['idSeccion'];

    $studentModel = new Student();
    $nextNumero = $studentModel->getNextNumeroListaBySeccion($idSeccion);

    echo json_encode(['next' => $nextNumero]);
    exit;
}

/**
 * ✅ RESET DE NÚMERO DE LISTA (CORREGIDO PARA MOSTRAR MODAL)
 */
function resetNumeroListaBySeccion()
{
    header('Content-Type: application/json');

    $idSeccion = (int)($_GET['idSeccion'] ?? 0);

    if ($idSeccion <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Sección inválida'
        ]);
        exit;
    }

    try {
        $studentModel = new Student();
        $pdo = Student::$pdo; 

        $pdo->beginTransaction();

        // Obtener estudiantes activos de la sección
        $stmt = $pdo->prepare("
            SELECT id 
            FROM student
            WHERE idSeccion = :idSeccion
              AND COALESCE(status,1) = 1
            ORDER BY NumerodeLista ASC, id ASC
        ");
        $stmt->execute([':idSeccion' => $idSeccion]);

        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $numero = 1;
        $update = $pdo->prepare("
            UPDATE student
            SET NumerodeLista = :numero
            WHERE id = :id
        ");

        foreach ($ids as $idStudent) {
            $update->execute([
                ':numero' => $numero++,
                ':id'     => $idStudent
            ]);
        }

        $pdo->commit();

        // --- CLAVE DEL ÉXITO: GUARDAR EN SESIÓN ANTES DE RESPONDER ---
        $_SESSION['status']  = 'success';
        $_SESSION['action']  = 'reset';
        $_SESSION['message'] = "¡Éxito! Se han reordenado los números de lista para los " . count($ids) . " estudiantes de la sección.";

        echo json_encode(['status' => 'success']);
        exit;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        // También guardamos el error en sesión por si acaso
        $_SESSION['status']  = 'error';
        $_SESSION['action']  = 'reset';
        $_SESSION['message'] = 'No se pudo completar el reseteo.';
        $_SESSION['error']   = $e->getMessage();

        echo json_encode([
            'status'  => 'error',
            'message' => $e->getMessage()
        ]);
        exit;
    }
}


/**
 * Router
 */
$action = $_GET['action'] ?? null;

switch ($action) {

    case 'nextNumeroLista':
        getNextNumeroLista();
        break;

    case 'resetNumeroLista':
        resetNumeroListaBySeccion();
        break;

    default:
        showStudent();
        break;
}