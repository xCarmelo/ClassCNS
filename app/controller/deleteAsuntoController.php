<?php
session_start();
require_once '../model/asunto.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $asunto = new Asunto();
    $asunto->id = $_GET['id'];

    $exito = $asunto->deleteAsunto(); // Este método debería existir en tu modelo

    $_SESSION['status'] = $exito ? 'success' : 'error';
    $_SESSION['action'] = 'delete';

    header("Location: getAsuntoController.php");
    exit();
}
