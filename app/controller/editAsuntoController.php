<?php
session_start();
require_once '../model/asunto.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asunto = new Asunto();
    $asunto->id = $_POST['id'] ?? null;
    $asunto->fecha = $_POST['fecha'] ?? null;
    $asunto->nota = $_POST['nota'] ?? '';
    $asunto->tema = $_POST['tema'] ?? '';
    $asunto->idStudent = $_POST['idStudent'] ?? null;
    $asunto->statuss = isset($_POST['statuss']) ? (int) $_POST['statuss'] : 0;
    $asunto->idMateria = $_POST['idMateria'] ?? null;

    $exito = $asunto->updateAsunto();

    $_SESSION['status'] = $exito ? 'success' : 'error';
    $_SESSION['action'] = 'edit';

    $filtros = http_build_query($_GET); // o $_POST si los filtros vienen desde formulario
    header('Location: getAsuntoController.php?$filtros');
    exit();
}

