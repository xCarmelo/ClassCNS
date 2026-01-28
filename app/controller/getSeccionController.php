<?php

require_once __DIR__ . '/../model/seccion.php';

 function showSeccion (){
        $seccionModel = new Seccion();
        $secciones = $seccionModel->getAllSeccion();

        require "../view/seccion.php";
}

showSeccion();