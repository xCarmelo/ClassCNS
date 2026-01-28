<?php

require_once __DIR__ . '/../model/materia.php';

 function showMateria (){
        $materiaModel = new Materia();
        $materias = $materiaModel->getAll();

        require "../view/materia.php";
}

showMateria();
