<?php
session_start();
require_once '../model/materia.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $materia = new Materia();
    $materia->name = $_POST['name'] ?? '';

    $exito = $materia->addMateria();

    $_SESSION['status'] = $exito ? 'success' : 'error';
    $_SESSION['action'] = 'add';
}

// Siempre redirige al controlador principal para mostrar la vista
header('Location: getMateriaController.php');
exit();
