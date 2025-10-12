<?php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/../model/student.php';
require_once __DIR__ . '/../model/seccion.php';

class CompareStudentsController {
    private Student $studentModel;
    private Seccion $seccionModel;

    public function __construct() {
        $this->studentModel = new Student();
        $this->seccionModel = new Seccion();
        
    }

    public function index(array $data = []) {
        // Renderizar vista con formulario y (opcional) resultados
        $secciones = $this->seccionModel->getAll();
        $summary = $data['summary'] ?? null;
        $details = $data['details'] ?? null;
        require __DIR__ . '/../view/compare_students.php';
    }

    public function compare() {
        if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
            $this->index(['summary' => ['error' => 'Archivo no recibido o inválido.']]);
            return;
        }

        $tmp = $_FILES['archivo']['tmp_name'];
        $nameFile = $_FILES['archivo']['name'] ?? '';
        $ext = strtolower(pathinfo($nameFile, PATHINFO_EXTENSION));
        if (!in_array($ext, ['xlsx','xls','csv'])) {
            $this->index(['summary' => ['error' => 'Formato no soportado. Use XLSX, XLS o CSV.']]);
            return;
        }

        // Cargar filas según extensión
        $rows = [];
        if ($ext === 'csv') {
            if (($fh = fopen($tmp, 'r')) !== false) {
                while (($data = fgetcsv($fh, 0, ',')) !== false) {
                    // Normalizamos valores a strings
                    $rows[] = array_map(fn($v) => is_string($v) ? $v : (string)$v, $data);
                }
                fclose($fh);
            }
        } elseif ($ext === 'xlsx') {
            // Lectura nativa de XLSX usando ZipArchive (sin librerías externas)
            if (!class_exists('ZipArchive')) {
                $this->index(['summary' => ['error' => 'Extensión ZipArchive no disponible. Activa php_zip en tu PHP o sube CSV.']]);
                return;
            }
            try {
                $rows = $this->parseXlsxSimple($tmp);
            } catch (Throwable $e) {
                $this->index(['summary' => ['error' => 'No se pudo leer el XLSX: ' . $e->getMessage()]]);
                return;
            }
        } else { // 'xls'
            $this->index(['summary' => ['error' => 'Archivos XLS (formato antiguo) no son soportados sin librerías externas en este entorno. Usa XLSX o CSV.']]);
            return;
        }

        if (empty($rows)) {
            $this->index(['summary' => ['error' => 'El archivo está vacío.']]);
            return;
        }

        // Mapear encabezados: buscar columnas name y seccion
    $header = array_shift($rows);
    $colName = null; $colSeccion = null;
    foreach ($header as $col => $val) {
            $v = strtolower(trim((string)$val));
            if (in_array($v, ['name','nombre','estudiante'])) $colName = $col;
            if (in_array($v, ['seccion','sección','seccion_name'])) $colSeccion = $col;
    }
    if ($colName === null || $colSeccion === null) {
            $this->index(['summary' => ['error' => 'Encabezados requeridos no encontrados. Debe tener columnas name y seccion.']]);
            return;
        }

        // Normalizadores
        $normalize = function (string $s): string {
            $s = trim(mb_strtolower($s));
            // remover acentos
            $from = 'áéíóúñüïäëïöüàèìòù';
            $to   = 'aeiounuiaeiu aeiou';
            $s = strtr($s, [
                'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u',
                'Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ú'=>'u',
                'ñ'=>'n','Ñ'=>'n','ü'=>'u','Ü'=>'u','ï'=>'i','Ï'=>'i','ä'=>'a','Ä'=>'a','ë'=>'e','Ë'=>'e','ö'=>'o','Ö'=>'o',
            ]);
            $s = preg_replace('/\s+/', ' ', $s);
            return $s ?? '';
        };

        // Secciones desde BD
        $secciones = $this->seccionModel->getAll();
        $mapSeccionByNorm = [];
        foreach ($secciones as $sec) {
            $mapSeccionByNorm[$normalize($sec['name'])] = ['id'=>$sec['id'], 'name'=>$sec['name']];
        }

        // Estudiantes activos desde BD (excluir eliminados)
        $dbStudents = $this->studentModel->getAllStudents(1) ?? [];
        $dbByKey = [];
        $dbByName = [];
        foreach ($dbStudents as $st) {
            $nName = $normalize($st['name'] ?? '');
            $nSec = $normalize($st['seccion_name'] ?? '');
            $key = $nName . '||' . $nSec;
            $dbByKey[$key] = $st;
            // para detectar movidos
            if (!isset($dbByName[$nName])) $dbByName[$nName] = [];
            $dbByName[$nName][$nSec] = $st;
        }

        // Filas del archivo
        $fileEntries = [];
        $duplicatesInFile = [];
        $unknownSections = [];
        $seenKeys = [];
        foreach ($rows as $r) {
            $name = trim((string)($r[$colName] ?? ''));
            $sec  = trim((string)($r[$colSeccion] ?? ''));
            if ($name === '' && $sec === '') continue;

            $nName = $normalize($name);
            $nSec  = $normalize($sec);

            if (!isset($mapSeccionByNorm[$nSec])) {
                $unknownSections[] = ['name'=>$name, 'seccion'=>$sec];
                // todavía lo incluimos para conteo de nuevos? Mejor omitir hasta que se normalice sección
                continue;
            }

            $key = $nName . '||' . $nSec;
            if (isset($seenKeys[$key])) {
                $duplicatesInFile[] = ['name'=>$name, 'seccion'=>$sec];
                continue;
            }
            $seenKeys[$key] = true;
            $fileEntries[] = ['name'=>$name, 'seccion'=>$sec, 'nName'=>$nName, 'nSec'=>$nSec];
        }

        // Comparaciones
        $newStudents = [];
        $movedStudents = [];
        foreach ($fileEntries as $fe) {
            $key = $fe['nName'] . '||' . $fe['nSec'];
            if (!isset($dbByKey[$key])) {
                // ¿existe por nombre en otra sección?
                if (isset($dbByName[$fe['nName']])) {
                    $dbSecs = array_keys($dbByName[$fe['nName']]);
                    // elegir una sección cualquiera (primera) para mostrar origen -> destino
                    $oldSec = implode(', ', $dbSecs);
                    $movedStudents[] = [
                        'name' => $fe['name'],
                        'from' => $oldSec,
                        'to'   => $fe['seccion']
                    ];
                } else {
                    $newStudents[] = ['name'=>$fe['name'], 'seccion'=>$fe['seccion']];
                }
            }
        }

        // Faltantes: en BD activos pero no presentes en el archivo para su misma sección
        $missingStudents = [];
        foreach ($dbByKey as $key => $st) {
            if (!isset($seenKeys[$key])) {
                $missingStudents[] = ['name'=>$st['name'], 'seccion'=>$st['seccion_name']];
            }
        }

        $summary = [
            'total_file' => count($fileEntries),
            'total_db'   => count($dbStudents),
            'new'        => count($newStudents),
            'missing'    => count($missingStudents),
            'moved'      => count($movedStudents),
            'duplicates' => count($duplicatesInFile),
            'unknown_sections' => count($unknownSections),
        ];
        $details = compact('newStudents','missingStudents','movedStudents','duplicatesInFile','unknownSections');

        $this->index(['summary'=>$summary, 'details'=>$details]);
    }

    // ===== Helpers XLSX mínimos dentro de la clase =====
    private function parseXlsxSimple(string $filepath): array {
        $zip = new ZipArchive();
        if ($zip->open($filepath) !== true) {
            throw new RuntimeException('No se pudo abrir el ZIP del XLSX.');
        }
        // sharedStrings
        $shared = [];
        $ssiIdx = $zip->locateName('xl/sharedStrings.xml');
        if ($ssiIdx !== false) {
            $xml = $zip->getFromIndex($ssiIdx);
            if ($xml !== false) {
                $sx = @simplexml_load_string($xml);
                if ($sx && isset($sx->si)) {
                    foreach ($sx->si as $si) {
                        // concatenar posibles varios <t>
                        $text = '';
                        if (isset($si->t)) { $text .= (string)$si->t; }
                        if (isset($si->r)) {
                            foreach ($si->r as $r) { $text .= (string)$r->t; }
                        }
                        $shared[] = (string)$text;
                    }
                }
            }
        }
        // localizar primera hoja
        $sheetPath = 'xl/worksheets/sheet1.xml';
        if ($zip->locateName($sheetPath) === false) {
            // buscar la primera coincidencia en xl/worksheets/
            $first = null;
            for ($i=0; $i<$zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (strpos($name, 'xl/worksheets/sheet') === 0 && substr($name, -4) === '.xml') {
                    $first = $name; break;
                }
            }
            if ($first) { $sheetPath = $first; }
        }
        $sheetXml = $zip->getFromName($sheetPath);
        if ($sheetXml === false) { throw new RuntimeException('No se encontró la hoja principal.'); }
        $sx = @simplexml_load_string($sheetXml);
        if (!$sx) { throw new RuntimeException('XML de hoja inválido.'); }
        // namespaces pueden interferir; accedemos directamente
        $rows = [];
        $maxCols = 0;
        foreach ($sx->sheetData->row as $row) {
            $line = [];
            foreach ($row->c as $c) {
                $r = (string)$c['r']; // ej A1
                $colLetters = preg_replace('/\d+/', '', $r);
                $colIdx = $this->colLettersToIndex($colLetters);
                $t = (string)$c['t'];
                $val = '';
                if ($t === 's') {
                    $idx = (int)$c->v;
                    $val = $shared[$idx] ?? '';
                } elseif ($t === 'inlineStr') {
                    if (isset($c->is->t)) $val = (string)$c->is->t; else $val = '';
                } else {
                    $val = isset($c->v) ? (string)$c->v : '';
                }
                $line[$colIdx] = $val;
                if ($colIdx+1 > $maxCols) $maxCols = $colIdx+1;
            }
            // normalizar a arreglo secuencial
            $rowVals = [];
            for ($i=0; $i<$maxCols; $i++) { $rowVals[$i] = isset($line[$i]) ? (string)$line[$i] : ''; }
            $rows[] = $rowVals;
        }
        $zip->close();
        return $rows;
    }

    private function colLettersToIndex(string $letters): int {
        $letters = strtoupper($letters);
        $n = 0;
        for ($i=0; $i<strlen($letters); $i++) {
            $n = $n*26 + (ord($letters[$i]) - 64); // A=1
        }
        return $n-1; // 0-based
    }
}

$controller = new CompareStudentsController();
$action = $_GET['action'] ?? 'index';
if ($action === 'compare' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->compare();
} else {
    $controller->index();
}
