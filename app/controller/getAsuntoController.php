<?php

require_once '../model/asunto.php';
require_once '../model/student.php';
require_once '../model/seccion.php';
require_once '../model/materia.php';
require_once '../model/corte.php';

 function showSeccion (){
        $asuntoModel = new Asunto();
        $asuntos = $asuntoModel->getAllAsunto();

        $corteModel = new Corte();
        $cortes = $corteModel->getAll();

        $studentModel = new Student();
        $students = $studentModel->getAllStudents(1); // solo activos en selector

        $seccionModel = new Seccion();
        $secciones = $seccionModel->getAllSeccion();

        $materiaModel = new Materia();
        $materias = $materiaModel->getAll();

        require "../view/asunto.php";
}

showSeccion();