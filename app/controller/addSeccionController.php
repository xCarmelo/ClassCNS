<?php
session_start();
require_once '../model/seccion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seccion = new Seccion();
    $seccion->name = $_POST['name'] ?? '';

    $exito = $seccion->addSeccion();

    $_SESSION['status'] = $exito ? 'success' : 'error';
    $_SESSION['action'] = 'add';
}

// Siempre redirige al controlador principal para mostrar la vista
header('Location: getSeccionController.php');
exit();
