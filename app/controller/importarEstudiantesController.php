<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../vendor/autoload.php'; // PhpSpreadsheet
require_once '../model/student.php';
require_once '../model/Database.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivoExcel'])) {
    $archivo = $_FILES['archivoExcel']['tmp_name'];

    if (!$archivo || $_FILES['archivoExcel']['error'] !== 0) {
        $_SESSION['status'] = 'error';
        $_SESSION['action'] = 'import';
        $_SESSION['error'] = 'Archivo no válido.';
        header("Location: ../view/student.php");
        exit;
    }

    try {
        $spreadsheet = IOFactory::load($archivo);
        $hoja = $spreadsheet->getActiveSheet();
        $filas = $hoja->toArray();

        if (count($filas) < 2) {
            throw new Exception("El archivo Excel está vacío o mal formado.");
        }

        $encabezados = array_map(fn($h) => strtolower(trim($h)), $filas[0]);

        if (!in_array('nombre', $encabezados) || !in_array('seccion', $encabezados)) {
            throw new Exception("El archivo debe contener columnas 'nombre' y 'seccion'.");
        }

        $colNombre = array_search('nombre', $encabezados);
        $colSeccion = array_search('seccion', $encabezados);

        $studentModel = new Student();
        $pdo = Student::$pdo;

        if (!$pdo) {
            throw new Exception("Error de conexión a la base de datos.");
        }

        $stmtInsert = $pdo->prepare("INSERT INTO student (name, idSeccion) VALUES (:name, :idSeccion)");
        $stmtSeccion = $pdo->prepare("SELECT id FROM seccion WHERE TRIM(name) = :name LIMIT 1");

        $insertados = 0;
        $noInsertados = [];

        foreach (array_slice($filas, 1) as $fila) {
            $nombre = trim($fila[$colNombre] ?? '');
            $nombreSeccion = trim($fila[$colSeccion] ?? '');

            if ($nombre !== '' && $nombreSeccion !== '') {
                $stmtSeccion->bindValue(':name', $nombreSeccion, PDO::PARAM_STR);
                $stmtSeccion->execute();
                $seccionData = $stmtSeccion->fetch(PDO::FETCH_ASSOC);

                if ($seccionData) {
                    $idSeccion = (int)$seccionData['id'];

                    $stmtInsert->bindValue(':name', $nombre, PDO::PARAM_STR);
                    $stmtInsert->bindValue(':idSeccion', $idSeccion, PDO::PARAM_INT);
                    $stmtInsert->execute();
                    $insertados++;
                } else {
                    $noInsertados[] = $nombre;
                }
            }
        }

        $_SESSION['status'] = $insertados > 0 ? 'success' : 'error';
        $_SESSION['action'] = 'import';
        $_SESSION['insertados'] = $insertados;
        $_SESSION['noInsertados'] = $noInsertados;

    } catch (Exception $e) {
        $_SESSION['status'] = 'error';
        $_SESSION['action'] = 'import';
        $_SESSION['error'] = $e->getMessage();
    }

    header("Location: /app/controller/getStudentController.php");
    header("Refresh: 0");

    exit;
}
