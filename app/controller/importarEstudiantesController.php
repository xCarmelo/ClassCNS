<?php
session_start();

/* ===========================
   CONFIGURACIÓN DE ERRORES
   =========================== */
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

/* ===========================
   DEPENDENCIAS
   =========================== */
require_once '../../vendor/autoload.php';
require_once '../model/student.php';
require_once '../model/seccion.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

/* ===========================
   VALIDAR REQUEST
   =========================== */
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['archivoExcel'])) {
    $_SESSION['status'] = 'error';
    $_SESSION['action'] = 'import';
    $_SESSION['error']  = 'Acceso no permitido.';
    header("Location: /app/controller/getStudentController.php");
    exit;
}

if ($_FILES['archivoExcel']['error'] !== 0) {
    $_SESSION['status'] = 'error';
    $_SESSION['action'] = 'import';
    $_SESSION['error']  = 'El archivo Excel es inválido.';
    header("Location: /app/controller/getStudentController.php");
    exit;
}

try {

    /* ===========================
       CARGAR EXCEL
       =========================== */
    $spreadsheet = IOFactory::load($_FILES['archivoExcel']['tmp_name']);
    $hoja = $spreadsheet->getActiveSheet();
    $filas = $hoja->toArray();

    if (count($filas) < 2) {
        throw new Exception("El archivo Excel no contiene datos.");
    }

    /* ===========================
       ENCABEZADOS
       =========================== */
    $encabezados = array_map(
        fn($h) => strtolower(trim($h)),
        $filas[0]
    );

    $requeridos = ['nombre','seccion','numerodelista','status','idcorte','fin'];
    foreach ($requeridos as $campo) {
        if (!in_array($campo, $encabezados)) {
            throw new Exception("Falta la columna requerida: {$campo}");
        }
    }

    $colNombre        = array_search('nombre', $encabezados);
    $colSeccion       = array_search('seccion', $encabezados);
    $colNumeroLista   = array_search('numerodelista', $encabezados);
    $colStatus        = array_search('status', $encabezados);
    $colIdCorte       = array_search('idcorte', $encabezados);
    $colFin           = array_search('fin', $encabezados);

    /* ===========================
       MODELOS Y CONEXIÓN
       =========================== */
    $studentModel = new Student();
    $pdo = Student::$pdo;

    if (!$pdo) {
        throw new Exception("No se pudo conectar a la base de datos.");
    }

    /* ===========================
       PREPARED STATEMENTS
       =========================== */
    $stmtSeccion = $pdo->prepare(
        "SELECT id FROM seccion WHERE TRIM(name) = :name LIMIT 1"
    );

    $stmtInsert = $pdo->prepare(
        "INSERT INTO student
        (name, idSeccion, NumerodeLista, status, idCorte, fin)
        VALUES
        (:name, :idSeccion, :NumerodeLista, :status, :idCorte, :fin)"
    );

    /* ===========================
       PROCESAR FILAS
       =========================== */
    $insertados = 0;
    $noInsertados = [];

    foreach (array_slice($filas, 1) as $i => $fila) {

        $nombre        = trim($fila[$colNombre] ?? '');
        $seccionNombre = trim($fila[$colSeccion] ?? '');

        if ($nombre === '' || $seccionNombre === '') {
            continue;
        }

        // Buscar sección
        $stmtSeccion->execute([':name' => $seccionNombre]);
        $seccionData = $stmtSeccion->fetch(PDO::FETCH_ASSOC);

        if (!$seccionData) {
            $noInsertados[] = "Fila " . ($i + 2) . ": la sección '{$seccionNombre}' no existe";
            continue;
        }

        $numeroLista = (int)($fila[$colNumeroLista] ?? 0);
        $status      = (int)($fila[$colStatus] ?? 1);
        $idCorte     = $fila[$colIdCorte] !== '' ? (int)$fila[$colIdCorte] : null;
        $fin         = (int)($fila[$colFin] ?? 0);

        // Validar número de lista duplicado
        if ($studentModel->numeroListaExiste($seccionData['id'], $numeroLista)) {
            $noInsertados[] = "Fila " . ($i + 2) . ": Número de lista {$numeroLista} ya existe en esa sección";
            continue;
        }

        // Insertar
        $stmtInsert->execute([
            ':name'          => $nombre,
            ':idSeccion'     => (int)$seccionData['id'],
            ':NumerodeLista' => $numeroLista,
            ':status'        => $status,
            ':idCorte'       => $idCorte,
            ':fin'           => $fin
        ]);

        $insertados++;
    }

    /* ===========================
       MENSAJES A LA VISTA
       =========================== */
    $_SESSION['action'] = 'import';
    $_SESSION['insertados'] = $insertados;
    $_SESSION['noInsertados'] = $noInsertados;

    if ($insertados > 0) {
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = "Importación finalizada. Estudiantes insertados: {$insertados}.";
    } else {
        $_SESSION['status'] = 'warning';
        $_SESSION['message'] = "No se insertaron estudiantes. Revisa los detalles.";
    }

} catch (Throwable $e) {

    $_SESSION['status'] = 'error';
    $_SESSION['action'] = 'import';
    $_SESSION['error']  = $e->getMessage();
}

/* ===========================
   REDIRECCIÓN FINAL
   =========================== */
header("Location: /app/controller/getStudentController.php");
exit;
