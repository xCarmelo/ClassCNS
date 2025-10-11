<?php 
class Database{
    private static $instance;
    private static $pdo;
    private static int $status = 0;

    private function __construct()
    {   
        try {
            // Usar utf8mb4 para compatibilidad con el esquema y emojis
            self::$pdo = new PDO("mysql:host=localhost;dbname=cnsr_asunto;charset=utf8mb4", "root", "");
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Manejo de errores
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            self::$status = 1;
        } catch (PDOException $e) {
            // Mantener estado 0 y registrar de forma segura en el log de PHP
            self::$status = 0;
            error_log('[Database] Error de conexión PDO: ' . $e->getMessage());
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