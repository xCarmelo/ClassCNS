<?php
require_once '../model/asunto.php';

// Asegúrate de que sea una petición GET con un ID válido
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $asunto = new Asunto();
    $data = $asunto->getAsuntoById($id);

    if ($data) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'asunto' => $data]);
        exit;
    }
}

// Si falla:
header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Asunto no encontrado']);
exit;
