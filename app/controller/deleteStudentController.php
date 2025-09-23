<?php
session_start();
require_once '../model/student.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $student = new Student();
    $student->id = $_GET['id'];

    $exito = $student->deleteStudent(); // Este método debería existir en tu modelo

    $_SESSION['status'] = $exito ? 'success' : 'error';
    $_SESSION['action'] = 'delete';

    header("Location: getStudentController.php");
    exit();
}
