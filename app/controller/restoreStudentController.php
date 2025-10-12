<?php
session_start();
require_once '../model/student.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $student = new Student();
    $student->id = (int)$_GET['id'];

    $exito = $student->restoreStudent();

    $_SESSION['status'] = $exito ? 'success' : 'error';
    $_SESSION['action'] = 'edit'; // reutilizamos modal genérico

    header('Location: getStudentController.php?status=0'); // volver a la vista de Eliminados
    exit();
}

http_response_code(400);
echo 'Bad Request';
