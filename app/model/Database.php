<?php 
class Database{
    private static $instance;
    private static $pdo;
    private static int $status = 0;

    private function __construct()
    {   
        try {
            self::$pdo = new PDO("mysql:host=localhost;dbname=cnsr_asunto;charset=utf8", "root", "");
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Manejo de errores
        
            self::$status = 1;
        } catch (PDOException $e) {
            self::$status = 0;
        }
    }

    //crear conexión 
    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new self();
        }
        return self::$instance;
    }


    //obtener la conexión
    public static function getConnection(){
        return self::$pdo;
    }

    public static function getConnectionStatus(): int{
        return self::$status;
    }
}