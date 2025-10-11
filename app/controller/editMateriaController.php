<?php
session_start();
require_once '../model/materia.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $materia = new Materia();

    // Capturar datos enviados por POST
    $materia->id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $materia->name = trim($_POST['name'] ?? '');

    // Validar datos antes de actualizar
    if ($materia->id && !empty($materia->name)) {
        $exito = $materia->updateMateria($materia->id, $materia->name);
        $_SESSION['status'] = $exito ? 'success' : 'error';
    } else {
        $_SESSION['status'] = 'error';
    }

    $_SESSION['action'] = 'edit';
    header('Location: getMateriaController.php');
    exit();
}
