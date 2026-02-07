<?php
require_once __DIR__ . '/Database.php';

class DatabaseCleaner {
    public static $pdo;
    
    public function __construct() {
        self::$pdo = Database::getInstance()->getConnection();
    }
    
    /**
     * Vacía las tablas de datos PERO PRESERVA las tablas maestras
     * Tablas preservadas: corte, seccion, materia, tipoasistencia
     */
    public function vaciarBaseDatos(): array {
        $resultado = [
            'status' => 'success',
            'message' => 'Base de datos vaciada exitosamente',
            'resumen' => 'Datos eliminados, configuración preservada',
            'details' => []
        ];
        
        // Tablas que NO se vacían (CONFIGURACIÓN DEL SISTEMA)
        $tablasPreservadas = ['corte', 'seccion', 'materia', 'tipoasistencia'];
        
        // Tablas que SÍ se vacían (DATOS)
        $tablasAVaciar = [
            'asistencia',
            'nota', 
            'asunto',
            'criterio',
            'enlace',
            'indicadorl',
            'student',
            'asistencia_sesion'
        ];
        
        try {
            // Desactivar verificaciones de claves foráneas
            self::$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
            
            $eliminados = [];
            $preservadas = [];
            
            // 1. Primero registrar tablas preservadas
            foreach ($tablasPreservadas as $tabla) {
                try {
                    $stmt = self::$pdo->prepare("SHOW TABLES LIKE ?");
                    $stmt->execute([$tabla]);
                    
                    if ($stmt->rowCount() > 0) {
                        // Contar registros pero NO vaciar
                        $stmtCount = self::$pdo->prepare("SELECT COUNT(*) as total FROM `{$tabla}`");
                        $stmtCount->execute();
                        $count = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
                        
                        $preservadas[] = $tabla;
                        $resultado['details'][$tabla] = "🛡️ Preservada ({$count} registros)";
                    }
                } catch (Exception $e) {
                    // Ignorar errores
                }
            }
            
            // 2. Vaciar tablas de datos
            foreach ($tablasAVaciar as $tabla) {
                try {
                    $stmt = self::$pdo->prepare("SHOW TABLES LIKE ?");
                    $stmt->execute([$tabla]);
                    
                    if ($stmt->rowCount() > 0) {
                        // Contar antes
                        $stmtCount = self::$pdo->prepare("SELECT COUNT(*) as total FROM `{$tabla}`");
                        $stmtCount->execute();
                        $countBefore = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
                        
                        if ($countBefore > 0) {
                            // Vaciar la tabla
                            $stmtDelete = self::$pdo->prepare("DELETE FROM `{$tabla}`");
                            $stmtDelete->execute();
                            
                            // Reiniciar auto_increment si es estudiante
                            if ($tabla === 'student') {
                                try {
                                    $stmtReset = self::$pdo->prepare("ALTER TABLE `{$tabla}` AUTO_INCREMENT = 1");
                                    $stmtReset->execute();
                                } catch (Exception $e) {
                                    // Ignorar
                                }
                            }
                            
                            $eliminados[] = $tabla;
                            $resultado['details'][$tabla] = "✅ Vacía ({$countBefore} eliminados)";
                        } else {
                            $resultado['details'][$tabla] = "✅ Ya vacía";
                        }
                    }
                } catch (Exception $e) {
                    $resultado['details'][$tabla] = "❌ Error: " . $e->getMessage();
                }
            }
            
            // Reactivar verificaciones
            self::$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
            
            // Resumen
            $resultado['resumen'] = count($eliminados) . " tablas vaciadas, " . 
                                   count($preservadas) . " tablas preservadas";
            
        } catch (Exception $e) {
            // Asegurar reactivación
            try {
                self::$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
            } catch (Exception $e2) {
                // Ignorar
            }
            
            $resultado['status'] = 'error';
            $resultado['message'] = 'Error: ' . $e->getMessage();
        }
        
        return $resultado;
    }
    
    /**
     * Método más simple y directo
     */
    public function vaciarDatos(): array {
        $resultado = [
            'status' => 'success',
            'message' => 'Datos eliminados, configuración preservada',
            'details' => []
        ];
        
        try {
            self::$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
            
            // Solo vaciar estas tablas (en orden correcto)
            $vaciar = [
                "DELETE FROM asistencia",
                "DELETE FROM nota", 
                "DELETE FROM asunto",
                "DELETE FROM criterio",
                "DELETE FROM enlace",
                "DELETE FROM indicadorl",
                "DELETE FROM student",
                "DELETE FROM asistencia_sesion"
            ];
            
            foreach ($vaciar as $sql) {
                try {
                    $stmt = self::$pdo->prepare($sql);
                    $stmt->execute();
                    $affected = $stmt->rowCount();
                    
                    // Extraer nombre de tabla
                    preg_match('/FROM\s+(\w+)/i', $sql, $matches);
                    $tabla = $matches[1] ?? 'desconocida';
                    
                    if ($affected > 0) {
                        $resultado['details'][$tabla] = "✅ {$affected} registros eliminados";
                    } else {
                        $resultado['details'][$tabla] = "✅ Ya vacía";
                    }
                } catch (Exception $e) {
                    // Ignorar errores menores
                }
            }
            
            // Reiniciar auto_increment de student
            try {
                self::$pdo->exec("ALTER TABLE student AUTO_INCREMENT = 1");
            } catch (Exception $e) {
                // Ignorar
            }
            
            self::$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
            
        } catch (Exception $e) {
            try {
                self::$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
            } catch (Exception $e2) {
                // Ignorar
            }
            
            $resultado['status'] = 'error';
            $resultado['message'] = 'Error: ' . $e->getMessage();
        }
        
        return $resultado;
    }
}
?>