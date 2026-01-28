<?php
session_start();
require_once '../model/asunto.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $asunto = new Asunto();
    $asunto->id = $_GET['id'];

    $exito = $asunto->deleteAsunto(); 

    $_SESSION['status'] = $exito ? 'success' : 'error';
    $_SESSION['action'] = 'delete';

    header("Location: $base/app/view/asunto.php");
    exit();
}
