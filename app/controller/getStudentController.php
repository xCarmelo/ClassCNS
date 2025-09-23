<?php

require_once '../model/student.php';
require_once '../model/materia.php';

 function showStudent (){
        $studentModel = new Student();
        $students = $studentModel->getAllStudents();

        $materiaModel = new Materia();
        $materias = $materiaModel->getAll();

        require "../view/student.php";
}

showStudent();
    