<?php
session_start();
require_once '../model/student.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student = new Student();
    $student->id = $_POST['id'] ?? null;
    $student->name = $_POST['name'] ?? '';
    $student->idSeccion = $_POST['idSeccion'] ?? null;

    $exito = $student->updateStudent();

    $_SESSION['status'] = $exito ? 'success' : 'error';
    $_SESSION['action'] = 'edit';

    header('Location: getStudentController.php');
    exit();
}
