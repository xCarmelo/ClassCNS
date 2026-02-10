<?php


$backupDir = __DIR__ . '/../backups/';

// Asegurar carpeta
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

$action = $_GET['action'] ?? 'index';

switch ($action) {

    /* ===============================
       CREAR RESPALDO
       =============================== */
    case 'create':

        $fecha = date('Y-m-d_H-i-s');
        $nombreArchivo = "backup_$fecha.sql";
        $rutaArchivo = $backupDir . $nombreArchivo;

        $mysqldump = 'G:/IIMPRIMIR/xampp/mysql/bin/mysqldump.exe';

        $dbName = 'cnsr_asunto';
        $dbUser = 'root';
        $dbPass = '';
        $dbHost = 'localhost';

        $command = "\"$mysqldump\" -h $dbHost -u $dbUser $dbName > \"$rutaArchivo\"";
        exec($command, $output, $result);

        if (file_exists($rutaArchivo) && filesize($rutaArchivo) > 0) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Respaldo creado correctamente.';
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'No se pudo crear el respaldo.';
        }

        header('Location: BackupController.php');
        exit;


    /* ===============================
       RESTAURAR RESPALDO
       =============================== */
    case 'restore':

        $file = $_GET['file'] ?? '';
        $rutaArchivo = realpath($backupDir . $file);

        if (!$rutaArchivo || !str_starts_with($rutaArchivo, realpath($backupDir))) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Archivo inválido.';
            header('Location: BackupController.php');
            exit;
        }

        if (!file_exists($rutaArchivo)) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'El archivo no existe.';
            header('Location: BackupController.php');
            exit;
        }

        $mysql = 'G:/IIMPRIMIR/xampp/mysql/bin/mysql.exe';

        $dbName = 'cnsr_asunto';
        $dbUser = 'root';
        $dbPass = '';
        $dbHost = 'localhost';

        $command = "\"$mysql\" -h $dbHost -u $dbUser $dbName < \"$rutaArchivo\"";
        exec($command, $output, $result);

        if ($result === 0) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Base de datos restaurada correctamente.';
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Error al restaurar la base de datos.';
        }

        header('Location: BackupController.php');
        exit;


    /* ===============================
       ELIMINAR RESPALDO
       =============================== */
    case 'delete':

        $file = $_GET['file'] ?? '';
        $rutaArchivo = realpath($backupDir . $file);

        if ($rutaArchivo && str_starts_with($rutaArchivo, realpath($backupDir))) {
            if (file_exists($rutaArchivo)) {
                unlink($rutaArchivo);
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = 'Respaldo eliminado correctamente.';
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'El archivo no existe.';
            }
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Archivo inválido.';
        }

        header('Location: BackupController.php');
        exit;


    /* ===============================
       LISTAR RESPALDOS (DESDE CARPETA)
       =============================== */
    case 'index':
    default:

        $backups = [];

        foreach (scandir($backupDir) as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                $backups[] = [
                    'file' => $file,
                    'fecha' => date('d/m/Y H:i', filemtime($backupDir . $file))
                ];
            }
        }

        rsort($backups); // más recientes primero
        require_once __DIR__ . '/../view/backup.php';
        break;
}
