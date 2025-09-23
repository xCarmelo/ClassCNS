<?php
session_start();
require_once '../model/seccion.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $Seccion = new Seccion();
    $Seccion->id = $_GET['id'];

    $exito = $Seccion->deleteSeccion(); // Este método debería existir en tu modelo

    $_SESSION['status'] = $exito ? 'success' : 'error';
    $_SESSION['action'] = 'delete';

    header("Location: getSeccionController.php");
    exit();
}
