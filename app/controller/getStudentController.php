<?php

require_once '../model/student.php';
require_once '../model/materia.php';
require_once '../model/corte.php';

 function showStudent (){
        $studentModel = new Student();
        $students = $studentModel->getAllStudents();

        $materiaModel = new Materia();
        $materias = $materiaModel->getAll();

        $corteModel = new Corte();
        $cortes = $corteModel->getAll();

        require "../view/student.php";
}

showStudent();
    