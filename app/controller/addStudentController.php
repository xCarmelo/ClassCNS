<?php
session_start();
require_once '../model/student.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student = new Student();
    $student->name = $_POST['name'] ?? '';
    $student->idSeccion = $_POST['idSeccion'] ?? null;
    $student->NumerodeLista = isset($_POST['NumerodeLista']) ? (int)$_POST['NumerodeLista'] : null;
    $exito = $student->addStudent();

    if ($exito) {
        $_SESSION['status'] = 'success';
        $_SESSION['action'] = 'add';
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['action'] = 'error';
        $_SESSION['error_msg'] = 'El Número de lista ya existe en esa sección.';
    }
}

// Siempre redirige al controlador principal para mostrar la vista
header("Location: $base/app/view/estudiantes.php");
exit();
