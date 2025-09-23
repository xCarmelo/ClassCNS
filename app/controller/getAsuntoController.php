<?php

require_once '../model/asunto.php';
require_once '../model/student.php';
require_once '../model/seccion.php';
require_once '../model/materia.php';

 function showSeccion (){
        $asuntoModel = new Asunto();
        $asuntos = $asuntoModel->getAllAsunto();

        $studentModel = new Student();
        $students = $studentModel->getAllStudents();

        $seccionModel = new Seccion();
        $secciones = $seccionModel->getAllSeccion();

        $materiaModel = new Materia();
        $materias = $materiaModel->getAll();

        require "../view/asunto.php";
}

showSeccion();