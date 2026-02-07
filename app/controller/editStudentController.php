<?php
session_start();
require_once '../model/student.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student = new Student();
    $student->id = $_POST['id'] ?? null;
    $student->name = $_POST['name'] ?? '';
    $student->idSeccion = $_POST['idSeccion'] ?? null;
    $student->NumerodeLista = $_POST['NumerodeLista'] ?? null;

    $fin = isset($_POST['fin']) ? 1 : 0;

if ($fin === 1) {
    $student->resetFinBySeccion($student->idSeccion);
}

$student->fin = $fin;


    $exito = $student->updateStudent();

    $_SESSION['status'] = $exito ? 'success' : 'error';
    $_SESSION['action'] = 'edit';

    header("Location: $base/app/view/estudiantes.php");
    exit();
}
