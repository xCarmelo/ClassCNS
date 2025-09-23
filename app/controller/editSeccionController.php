<?php
session_start();
require_once '../model/seccion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seccion = new Seccion();
    $seccion->id = $_POST['id'] ?? null;
    $seccion->name = $_POST['name'] ?? '';

    $exito = $seccion->updateSeccion();

    $_SESSION['status'] = $exito ? 'success' : 'error';
    $_SESSION['action'] = 'edit';

    header('Location: getSeccionController.php');
    exit();
}
