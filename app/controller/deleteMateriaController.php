<?php
session_start();
require_once '../model/materia.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $materia = new Materia();
    $materia->id = $_GET['id'];

    $exito = $materia->deletemateria(); // Este método debería existir en tu modelo

    $_SESSION['status'] = $exito ? 'success' : 'error';
    $_SESSION['action'] = 'delete';

    header("Location: getMateriaController.php");
    exit();
}
