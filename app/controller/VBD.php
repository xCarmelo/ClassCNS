<?php
session_start();
header('Content-Type: application/json');

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Método no permitido'
    ]);
    exit;
}

require_once '../model/DatabaseCleaner.php';

try {
    $cleaner = new DatabaseCleaner();
    
    // Usar el método simple
    $resultado = $cleaner->vaciarDatos();
    
    // Log opcional
    error_log("VBD - Datos vaciados: " . json_encode($resultado));
    
    echo json_encode($resultado);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
exit;
?>