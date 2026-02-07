<?php
session_start();
require_once '../model/student.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student = new Student();
    
    // Validar y asignar nombre
    $student->name = trim($_POST['name'] ?? '');
    if (empty($student->name)) {
        $_SESSION['status'] = 'error';
        $_SESSION['action'] = 'error';
        $_SESSION['error'] = 'El nombre del estudiante es requerido.';
        header('Location: getStudentController.php');
        exit();
    }
    
    // Validar que idSeccion esté presente
    $idSeccion = $_POST['idSeccion'] ?? null;
    if ($idSeccion === null || $idSeccion === '') {
        $_SESSION['status'] = 'error';
        $_SESSION['action'] = 'error';
        $_SESSION['error'] = 'La sección es requerida.';
        header('Location: getStudentController.php');
        exit();
    }
    $student->idSeccion = (int)$idSeccion;
    
    // Validar que NumerodeLista esté presente y sea válido
    $student->NumerodeLista = isset($_POST['NumerodeLista']) ? (int)$_POST['NumerodeLista'] : null;
    if ($student->NumerodeLista === null || $student->NumerodeLista < 1) {
        $_SESSION['status'] = 'error';
        $_SESSION['action'] = 'error';
        $_SESSION['error'] = 'El número de lista debe ser un número positivo.';
        header('Location: getStudentController.php');
        exit();
    }
    
    // Manejar el campo fin
    $fin = isset($_POST['fin']) ? 1 : 0;
    if ($fin === 1) {
        $student->resetFinBySeccion($student->idSeccion);
    }
    $student->fin = $fin;

    // Intentar agregar el estudiante
    try {
        $exito = $student->addStudent();
        
        if ($exito) {
            $_SESSION['status'] = 'success';
            $_SESSION['action'] = 'add';
            $_SESSION['message'] = 'Estudiante agregado correctamente.';
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['action'] = 'error';
            $_SESSION['error'] = 'No se pudo agregar el estudiante. Verifica que el número de lista no cause conflictos.';
        }
    } catch (Exception $e) {
        $_SESSION['status'] = 'error';
        $_SESSION['action'] = 'error';
        $_SESSION['error'] = 'Error del sistema: ' . $e->getMessage();
    }
} else {
    // Si no es POST, redirigir
    $_SESSION['status'] = 'error';
    $_SESSION['action'] = 'error';
    $_SESSION['error'] = 'Método de solicitud no válido.';
}

// Siempre redirige al controlador principal para mostrar la vista
header("Location: $base/app/view/estudiantes.php");
exit();
?>