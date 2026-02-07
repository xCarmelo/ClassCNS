<?php
session_start();

/* ===========================
   CONFIGURACIÓN DE ERRORES
   =========================== */
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

/* ===========================
   DEPENDENCIAS
   =========================== */
require_once '../../vendor/autoload.php';
require_once '../model/student.php';
require_once '../model/seccion.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

/* ===========================
   VALIDAR REQUEST
   =========================== */
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['archivoExcel'])) {
    $_SESSION['status'] = 'error';
    $_SESSION['action'] = 'import';
    $_SESSION['error']  = 'Acceso no permitido.';
    header("Location: /app/controller/getStudentController.php");
    exit;
}

if ($_FILES['archivoExcel']['error'] !== 0) {
    $_SESSION['status'] = 'error';
    $_SESSION['action'] = 'import';
    $_SESSION['error']  = 'El archivo Excel es inválido.';
    header("Location: /app/controller/getStudentController.php"); 
    exit;
}
 
try {
    /* ===========================
       CARGAR EXCEL
       =========================== */
    $spreadsheet = IOFactory::load($_FILES['archivoExcel']['tmp_name']);
    $hoja = $spreadsheet->getActiveSheet();
    $filas = $hoja->toArray();

    if (count($filas) < 2) {
        throw new Exception("El archivo Excel no contiene datos.");
    }

    /* ===========================
       ENCABEZADOS - COLUMNAS NUEVAS
       =========================== */
    $encabezados = array_map(
        fn($h) => trim($h), // No convertir a minúsculas para mantener exactitud
        $filas[0]
    );

    // Buscar columnas con nombres específicos
    $colNombre = null;
    $colSeccion = null;
    $colNumeroLista = null;
    $colDivision = null; // Opcional

    // Buscar cada columna por posibles variaciones de nombre
    foreach ($encabezados as $index => $encabezado) {
        $encabezadoLower = strtolower($encabezado);
        
        if (in_array($encabezadoLower, ['nombre', 'name', 'estudiante', 'alumno'])) {
            $colNombre = $index;
        } elseif (in_array($encabezadoLower, ['sección', 'seccion', 'section', 'grupo'])) {
            $colSeccion = $index;
        } elseif (in_array($encabezadoLower, ['numero de lista', 'numerodelista', 'número', 'no', 'lista', 'número de lista'])) {
            $colNumeroLista = $index;
        } elseif (in_array($encabezadoLower, ['división de la lista', 'division', 'fin', 'división', 'separación'])) {
            $colDivision = $index;
        }
    }

    // Validar columnas requeridas
    if ($colNombre === null) {
        throw new Exception("Falta la columna 'Nombre' (puede llamarse: Nombre, Name, Estudiante, Alumno)");
    }
    if ($colSeccion === null) {
        throw new Exception("Falta la columna 'Sección' (puede llamarse: Sección, Seccion, Section, Grupo)");
    }
    if ($colNumeroLista === null) {
        throw new Exception("Falta la columna 'Numero de lista' (puede llamarse: Numero de lista, Numerodelista, Número, No, Lista)");
    }

    /* ===========================
       VALORES POR DEFECTO
       =========================== */
    $STATUS_DEFAULT = 1;   // Todos los estudiantes importados estarán activos
    $ID_CORTE_DEFAULT = 1; // Todos los estudiantes tendrán idCorte = 1

    /* ===========================
       MODELOS Y CONEXIÓN
       =========================== */
    $studentModel = new Student();
    $seccionModel = new Seccion();
    $pdo = Student::$pdo;

    if (!$pdo) {
        throw new Exception("No se pudo conectar a la base de datos.");
    }

    /* ===========================
       FUNCIONES AUXILIARES
       =========================== */
    // Función para verificar si un número de lista existe y obtener el ID del estudiante que lo tiene
    function obtenerEstudianteConNumeroLista($pdo, $idSeccion, $numeroLista) {
        $sql = "SELECT id FROM student 
                WHERE idSeccion = :idSeccion 
                AND NumerodeLista = :numeroLista 
                AND COALESCE(status,1) = 1 
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':idSeccion' => $idSeccion,
            ':numeroLista' => $numeroLista
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Función para desplazar números de lista en una sección
    function desplazarNumerosLista($pdo, $idSeccion, $numeroDesde) {
        $sql = "UPDATE student 
                SET NumerodeLista = NumerodeLista + 1 
                WHERE idSeccion = :idSeccion 
                AND NumerodeLista >= :numeroDesde 
                AND COALESCE(status,1) = 1
                ORDER BY NumerodeLista DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':idSeccion' => $idSeccion,
            ':numeroDesde' => $numeroDesde
        ]);
        return $stmt->rowCount();
    }

    // Función para resetear fin en una sección
    function resetearFinSeccion($pdo, $idSeccion) {
        $sql = "UPDATE student SET fin = 0 WHERE idSeccion = :idSeccion";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':idSeccion' => $idSeccion]);
    }

    /* ===========================
       PROCESAR FILAS CON LÓGICA DE DESPLAZAMIENTO
       =========================== */
    $insertados = 0;
    $noInsertados = [];
    $estudiantesConFin = []; // Para trackear qué estudiantes tienen fin=1 por sección
    $numerosListaProcesados = []; // Para trackear números de lista ya procesados por sección

    // Ordenar las filas por sección y número de lista para procesamiento ordenado
    $filasProcesar = [];
    foreach (array_slice($filas, 1) as $i => $fila) {
        $nombre = trim($fila[$colNombre] ?? '');
        $seccionNombre = trim($fila[$colSeccion] ?? '');
        
        if ($nombre === '' || $seccionNombre === '') {
            $noInsertados[] = "Fila " . ($i + 2) . ": Fila vacía o sin nombre/sección";
            continue;
        }
        
        // Buscar sección en la base de datos
        $stmtSeccion = $pdo->prepare("SELECT id FROM seccion WHERE TRIM(name) = :name LIMIT 1");
        $stmtSeccion->execute([':name' => $seccionNombre]);
        $seccionData = $stmtSeccion->fetch(PDO::FETCH_ASSOC);
        
        if (!$seccionData) {
            $noInsertados[] = "Fila " . ($i + 2) . ": la sección '{$seccionNombre}' no existe en el sistema";
            continue;
        }
        
        // Obtener número de lista
        $numeroListaRaw = $fila[$colNumeroLista] ?? '';
        $numeroLista = (int)$numeroListaRaw;
        
        if ($numeroLista < 1) {
            $noInsertados[] = "Fila " . ($i + 2) . ": el número de lista debe ser mayor a 0 (valor: '{$numeroListaRaw}')";
            continue;
        }
        
        // Obtener valor de división de lista (opcional, default 0)
        $fin = 0; // Valor por defecto
        if ($colDivision !== null) {
            $divisionRaw = $fila[$colDivision] ?? '';
            $divisionStr = strtolower(trim($divisionRaw));
            
            // Convertir diferentes formas de indicar "sí" a 1
            if (in_array($divisionStr, ['sí', 'si', 'yes', '1', 'true', 'verdadero', 'v'])) {
                $fin = 1;
            } elseif ($divisionStr !== '') {
                $fin = (int)$divisionRaw;
            }
        }
        
        // Validar que el número de lista no sea demasiado grande
        if ($numeroLista > 999) {
            $noInsertados[] = "Fila " . ($i + 2) . ": el número de lista es demasiado grande (máximo 999)";
            continue;
        }
        
        $filasProcesar[] = [
            'indice' => $i + 2,
            'nombre' => $nombre,
            'idSeccion' => (int)$seccionData['id'],
            'seccionNombre' => $seccionNombre,
            'numeroLista' => $numeroLista,
            'status' => $STATUS_DEFAULT,
            'idCorte' => $ID_CORTE_DEFAULT,
            'fin' => $fin
        ];
    }

    // Si no hay filas válidas para procesar
    if (empty($filasProcesar)) {
        throw new Exception("No se encontraron filas válidas para procesar. Verifica el formato del archivo.");
    }

    // Agrupar por sección para procesamiento ordenado
    $agrupadosPorSeccion = [];
    foreach ($filasProcesar as $fila) {
        $idSeccion = $fila['idSeccion'];
        if (!isset($agrupadosPorSeccion[$idSeccion])) {
            $agrupadosPorSeccion[$idSeccion] = [];
        }
        $agrupadosPorSeccion[$idSeccion][] = $fila;
    }

    // Procesar cada sección
    foreach ($agrupadosPorSeccion as $idSeccion => $estudiantesSeccion) {
        // Ordenar por número de lista
        usort($estudiantesSeccion, function($a, $b) {
            return $a['numeroLista'] <=> $b['numeroLista'];
        });

        // Verificar si hay algún estudiante con fin=1 en esta sección
        $tieneFinUno = false;
        foreach ($estudiantesSeccion as $est) {
            if ($est['fin'] == 1) {
                $tieneFinUno = true;
                break;
            }
        }
        
        // Si hay algún estudiante con fin=1, resetear todos los fin de esta sección
        if ($tieneFinUno) {
            resetearFinSeccion($pdo, $idSeccion);
        }

        // Procesar estudiantes de esta sección
        foreach ($estudiantesSeccion as $est) {
            $numeroLista = $est['numeroLista'];
            $indice = $est['indice'];
            
            // Verificar si el número de lista ya existe en la base de datos
            $existente = obtenerEstudianteConNumeroLista($pdo, $idSeccion, $numeroLista);
            
            // Verificar si ya hemos procesado este número en esta importación
            $claveSeccionNumero = $idSeccion . '_' . $numeroLista;
            $yaProcesado = isset($numerosListaProcesados[$claveSeccionNumero]);
            
            // Si ya existe o ya fue procesado, desplazar
            if ($existente || $yaProcesado) {
                desplazarNumerosLista($pdo, $idSeccion, $numeroLista);
                
                // Actualizar números en nuestra memoria local para estudiantes ya procesados
                foreach ($numerosListaProcesados as $clave => $valor) {
                    list($sId, $sNum) = explode('_', $clave);
                    if ($sId == $idSeccion && $sNum >= $numeroLista) {
                        $nuevoNum = $sNum + 1;
                        $nuevaClave = $idSeccion . '_' . $nuevoNum;
                        $numerosListaProcesados[$nuevaClave] = true;
                        unset($numerosListaProcesados[$clave]);
                    }
                }
            }
            
            // Marcar este número como procesado
            $numerosListaProcesados[$idSeccion . '_' . $numeroLista] = true;
            
            // Insertar el estudiante
            $sqlInsert = "INSERT INTO student
                         (name, idSeccion, NumerodeLista, status, idCorte, fin)
                         VALUES
                         (:name, :idSeccion, :NumerodeLista, :status, :idCorte, :fin)";
            
            $stmtInsert = $pdo->prepare($sqlInsert);
            $resultado = $stmtInsert->execute([
                ':name' => $est['nombre'],
                ':idSeccion' => $idSeccion,
                ':NumerodeLista' => $numeroLista,
                ':status' => $est['status'],
                ':idCorte' => $est['idCorte'],
                ':fin' => $est['fin']
            ]);
            
            if ($resultado) {
                $insertados++;
            } else {
                $noInsertados[] = "Fila {$indice}: Error al insertar al estudiante '{$est['nombre']}'";
            }
        }
    }

    /* ===========================
       MENSAJES A LA VISTA
       =========================== */
    $_SESSION['action'] = 'import';
    $_SESSION['insertados'] = $insertados;
    $_SESSION['noInsertados'] = $noInsertados;

    if ($insertados > 0) {
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = "✅ Importación exitosa. Se insertaron {$insertados} estudiantes.";
        
        if (!empty($noInsertados)) {
            $_SESSION['status'] = 'warning';
            $_SESSION['message'] .= " Hubo " . count($noInsertados) . " errores que se omitieron.";
        }
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = "❌ No se insertó ningún estudiante. Verifica el formato del archivo.";
    }

} catch (Throwable $e) {
    $_SESSION['status'] = 'error';
    $_SESSION['action'] = 'import';
    $_SESSION['error']  = 'Error durante la importación: ' . $e->getMessage();
    error_log("Error en importarEstudiantesController.php: " . $e->getMessage());
}

/* ===========================
   REDIRECCIÓN FINAL
   =========================== */
header("Location: /app/controller/getStudentController.php");
exit;