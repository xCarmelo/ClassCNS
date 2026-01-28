<?php

require_once __DIR__ . '/../model/student.php';
require_once __DIR__ . '/../model/seccion.php';

 function showStudent (){
        $studentModel = new Student();
        // status: 1 activos (default), 0 eliminados
        $status = isset($_GET['status']) && $_GET['status'] === '0' ? 0 : 1;
        $students = $studentModel->getAllStudents($status);

        $materiaModel = new Materia();
        $materias = $materiaModel->getAll();

        $corteModel = new Corte();
        $cortes = $corteModel->getAll();

        // Ya no se usan cortes en la vista de estudiantes

        require "../view/student.php";
}

showStudent();
