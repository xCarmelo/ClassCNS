<?php
session_start();
require_once '../model/student.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student = new Student();
    $student->name = $_POST['name'] ?? '';
    $student->idSeccion = $_POST['idSeccion'] ?? null;

    $exito = $student->addStudent();

    $_SESSION['status'] = $exito ? 'success' : 'error';
    $_SESSION['action'] = 'add';
}

// Siempre redirige al controlador principal para mostrar la vista
header('Location: getStudentController.php');
exit();
