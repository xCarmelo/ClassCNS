<?php
session_start();
$base = '/SanJose'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>San José</title>
    <link rel="icon" type="image/png" href="<?= $base ?>/public/assets/logo-pestaña.png">
    <link rel="stylesheet" href="<?= $base ?>/public/cssB/bootstrap.min.css">
    <link href="<?= $base ?>/public/cssB/bootstrap-icons-1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base ?>/public/css/styles.css"> 
</head>
<body>
    <div class="container-fluid p-0 min-vh-100 d-flex flex-column">
    <?php require __DIR__ . "/main.php"; ?>