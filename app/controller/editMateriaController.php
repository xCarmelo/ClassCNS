<?php
session_start();
require_once '../model/materia.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $materia = new Materia();
    $materia->id = $_POST['id'] ?? null;
    $materia->name = $_POST['name'] ?? '';

    $exito = $materia->updateMateria();

    $_SESSION['status'] = $exito ? 'success' : 'error';
    $_SESSION['action'] = 'edit';

    header('Location: getMateriaController.php');
    exit();
}
