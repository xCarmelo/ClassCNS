<?php

require_once '../model/materia.php';

 function showMateria (){
        $materiaModel = new Materia();
        $materias = $materiaModel->getAll();

        require "../view/materia.php";
}

showMateria();
    