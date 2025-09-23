<?php
require_once '../model/materia.php';

// Asegúrate de que sea una petición GET con un ID válido
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $materia = new materia();
    $data = $materia->find($id);

    if ($data) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'materia' => $data]); 
        exit;
    }
}

// Si falla:
header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Materia no encontrado']);
exit;
