<?php
require_once __DIR__ . '/../model/student.php';

// Asegúrate de que sea una petición GET con un ID válido
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $student = new Student();
    $data = $student->getStudentById($id);

    if ($data) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'student' => $data]);
        exit;
    }
}

// Si falla:
header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Estudiante no encontrado']);
exit;
