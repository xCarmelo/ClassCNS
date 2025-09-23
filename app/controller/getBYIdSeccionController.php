<?php
require_once '../model/seccion.php';

// Asegúrate de que sea una petición GET con un ID válido
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $seccion = new Seccion();
    $data = $seccion->getSeccionById($id);

    if ($data) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'seccion' => $data]);
        exit;
    }
}

// Si falla:
header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Seccion no encontrado']);
exit;
