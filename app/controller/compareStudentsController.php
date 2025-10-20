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
    $summary = $data['summary'] ?? ($_SESSION['last_compare']['summary'] ?? null);
    $details = $data['details'] ?? ($_SESSION['last_compare']['details'] ?? null);
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

        // Firma de nombre independiente del orden de palabras (bolsa de palabras)
        $canonicalizeName = function (string $name) use ($normalize): string {
            $s = $normalize($name);
            // dejar solo letras y espacios
            $s = preg_replace('/[^a-z\s]/u', ' ', $s);
            $tokens = array_filter(explode(' ', $s), function($t){ return $t !== ''; });
            // eliminar stopwords cortas y partículas comunes
            $stop = ['de','del','la','las','los','y','e','da','das','do','dos','di'];
            $filtered = [];
            foreach ($tokens as $t) {
                if (strlen($t) < 2) continue;
                if (in_array($t, $stop, true)) continue;
                $filtered[] = $t;
            }
            sort($filtered, SORT_STRING);
            return implode(' ', $filtered);
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
        $dbKeyGroups = [];
        foreach ($dbStudents as $st) {
            $nName = $canonicalizeName($st['name'] ?? '');
            $nSec = $normalize($st['seccion_name'] ?? '');
            $key = $nName . '||' . $nSec;
            $dbByKey[$key] = $st;
            // para detectar movidos
            if (!isset($dbByName[$nName])) $dbByName[$nName] = [];
            $dbByName[$nName][$nSec] = $st;
            if (!isset($dbKeyGroups[$key])) $dbKeyGroups[$key] = [];
            $dbKeyGroups[$key][] = $st;
        }
        // Duplicados en BD: misma clave canónica y sección
        $dbDuplicates = [];
        foreach ($dbKeyGroups as $key => $arr) {
            if (count($arr) > 1) {
                // consolidar info (nombre, sección, números de lista)
                $name = $arr[0]['name'] ?? '';
                $sec = $arr[0]['seccion_name'] ?? '';
                $nums = [];
                foreach ($arr as $st) { if (isset($st['NumerodeLista'])) { $nums[] = (string)$st['NumerodeLista']; } }
                $dbDuplicates[] = [
                    'name' => $name,
                    'seccion' => $sec,
                    'numeros_bd' => implode(', ', $nums),
                ];
            }
        }

        // Filas del archivo
        $fileEntries = [];
        $duplicatesInFile = [];
        $unknownSections = [];
        $seenKeys = [];
        $seqBySection = [];
        foreach ($rows as $r) {
            $name = trim((string)($r[$colName] ?? ''));
            $sec  = trim((string)($r[$colSeccion] ?? ''));
            if ($name === '' && $sec === '') continue;

            $nName = $canonicalizeName($name);
            $nSec  = $normalize($sec);

            // Enumeración dinámica por sección tal como viene en el archivo
            $seqBySection[$nSec] = ($seqBySection[$nSec] ?? 0) + 1;
            $currNum = $seqBySection[$nSec];

            if (!isset($mapSeccionByNorm[$nSec])) {
                $unknownSections[] = ['name'=>$name, 'seccion'=>$sec, 'numArchivo'=>$currNum];
                // todavía lo incluimos para conteo de nuevos? Mejor omitir hasta que se normalice sección
                continue;
            }

            $key = $nName . '||' . $nSec;
            if (isset($seenKeys[$key])) {
                $duplicatesInFile[] = ['name'=>$name, 'seccion'=>$sec, 'numArchivo'=>$currNum];
                continue;
            }
            $seenKeys[$key] = true;
            $fileEntries[] = ['name'=>$name, 'seccion'=>$sec, 'nName'=>$nName, 'nSec'=>$nSec, 'numArchivo'=>$currNum];
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
                    // secciones y números de lista actuales en BD para ese nombre
                    $oldSec = implode(', ', $dbSecs);
                    $nums = [];
                    foreach ($dbSecs as $secKey) {
                        $st = $dbByName[$fe['nName']][$secKey] ?? null;
                        if ($st && isset($st['NumerodeLista'])) { $nums[] = (string)$st['NumerodeLista']; }
                    }
                    $oldNums = implode(', ', $nums);
                    $movedStudents[] = [
                        'name' => $fe['name'],
                        'from' => $oldSec,
                        'to'   => $fe['seccion'],
                        'list_from' => $oldNums,
                        'numArchivo' => $fe['numArchivo'] ?? null
                    ];
                } else {
                    $newStudents[] = ['name'=>$fe['name'], 'seccion'=>$fe['seccion'], 'numero'=>null, 'numArchivo'=>$fe['numArchivo'] ?? null];
                }
            }
        }

        // Faltantes: en BD activos pero no presentes en el archivo para su misma sección
        $missingStudents = [];
    foreach ($dbByKey as $key => $st) {
            if (!isset($seenKeys[$key])) {
        $missingStudents[] = ['name'=>$st['name'], 'seccion'=>$st['seccion_name'], 'numero'=>$st['NumerodeLista'] ?? null, 'numArchivo'=>null];
            }
        }

        // Comparación de orden por sección
        // Construir secuencias por sección en BD (ordenadas por NumerodeLista)
        $dbOrderBySec = [];
        foreach ($dbStudents as $st) {
            $nSec = $normalize($st['seccion_name'] ?? '');
            if ($nSec === '') continue;
            $dbOrderBySec[$nSec][] = [
                'canon' => $canonicalizeName($st['name'] ?? ''),
                'name'  => $st['name'] ?? '',
                'num'   => isset($st['NumerodeLista']) ? (int)$st['NumerodeLista'] : null,
            ];
        }
        foreach ($dbOrderBySec as $secKey => &$arr) {
            usort($arr, function($a,$b){ return ($a['num']??PHP_INT_MAX) <=> ($b['num']??PHP_INT_MAX); });
        }
        unset($arr);

        // Construir secuencias por sección en Archivo (orden por aparición)
        $fileOrderBySec = [];
        foreach ($fileEntries as $fe) {
            $fileOrderBySec[$fe['nSec']][] = [
                'canon' => $fe['nName'],
                'name'  => $fe['name'],
                'numArchivo' => $fe['numArchivo'] ?? null,
            ];
        }

        $orderBySection = [];
        $okCount = 0; $diffCount = 0; $skipCount = 0;
        foreach ($dbOrderBySec as $secKey => $dbSeq) {
            if (!isset($fileOrderBySec[$secKey])) { $diffCount++; $orderBySection[$secKey] = ['status'=>'diff','reason'=>'Sección no aparece en el archivo','mismatches'=>[], 'dbCount'=>count($dbSeq), 'fileCount'=>0]; continue; }
            $fileSeq = $fileOrderBySec[$secKey];
            $dbCanon = array_map(fn($x)=>$x['canon'], $dbSeq);
            $fileCanon = array_map(fn($x)=>$x['canon'], $fileSeq);
            $dbCount = count($dbCanon); $fileCount = count($fileCanon);
            $minLen = min($dbCount, $fileCount);
            $mismatches = [];
            for ($i=0; $i<$minLen; $i++) {
                if ($dbCanon[$i] !== $fileCanon[$i]) {
                    $mismatches[] = [
                        'pos' => $i+1,
                        'db'  => $dbSeq[$i]['name'],
                        'file'=> $fileSeq[$i]['name'],
                    ];
                    if (count($mismatches) >= 10) break;
                }
            }
            if (empty($mismatches) && $dbCount === $fileCount) {
                $okCount++; $orderBySection[$secKey] = ['status'=>'ok','mismatches'=>[], 'dbCount'=>$dbCount, 'fileCount'=>$fileCount];
            } else {
                $diffCount++; $orderBySection[$secKey] = ['status'=>'diff','mismatches'=>$mismatches, 'dbCount'=>$dbCount, 'fileCount'=>$fileCount];
            }
        }
    $orderSummary = ['ok'=>$okCount, 'diff'=>$diffCount, 'skipped'=>$skipCount];

        $summary = [
            'total_file' => count($fileEntries),
            'total_db'   => count($dbStudents),
            'new'        => count($newStudents),
            'missing'    => count($missingStudents),
            'moved'      => count($movedStudents),
            'duplicates' => count($duplicatesInFile),
            'unknown_sections' => count($unknownSections),
            'db_duplicates' => count($dbDuplicates),
        ];
    // Asegurar campos para consistencia
    foreach ($duplicatesInFile as &$d) { if (!isset($d['numero'])) { $d['numero'] = null; } if (!isset($d['numArchivo'])) { $d['numArchivo'] = null; } }
    foreach ($unknownSections as &$u) { if (!isset($u['numero'])) { $u['numero'] = null; } if (!isset($u['numArchivo'])) { $u['numArchivo'] = null; } }
    unset($d, $u);

    $details = compact('newStudents','missingStudents','movedStudents','duplicatesInFile','unknownSections','dbDuplicates');
    $details['orderReport'] = [
        'bySection' => $orderBySection,
        'summary'   => $orderSummary,
    ];

        // Guardar último resultado y orden de archivo en sesión para acciones posteriores
        // Construir displayName por sección (primer nombre visto en archivo)
        $sectionDisplay = [];
        foreach ($fileOrderBySec as $secKey => $list) {
            foreach ($rows as $r) { /* best-effort: tomar el primer crudo */ }
            // ya tenemos al menos un elemento en list con 'name', pero no sección; usamos la clave normalizada como display
            $sectionDisplay[$secKey] = $secKey;
        }
        $_SESSION['last_compare'] = [
            'summary' => $summary,
            'details' => $details,
            'fileOrderBySec' => $fileOrderBySec,
            'sectionDisplay' => $sectionDisplay,
            'uploaded_name' => $nameFile,
            'uploaded_time' => time(),
        ];

        $this->index(['summary'=>$summary, 'details'=>$details]);
    }

    public function applyOrder() {
        if (empty($_POST['sec'])) {
            $this->index(['summary' => ['error' => 'Sección no proporcionada.']]);
            return;
        }
    $secKey = trim((string)$_POST['sec']);
    // Decodificar posibles entidades HTML provenientes del atributo data-sec
    $secKey = html_entity_decode($secKey, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $session = $_SESSION['last_compare'] ?? null;
        $payload = $_POST['order'] ?? '';
        $fileOrderBySec = $session['fileOrderBySec'] ?? [];
        if ($payload) {
            // Rehidratar solo esta sección a partir del payload
            $decoded = json_decode(base64_decode((string)$payload), true);
            if (is_array($decoded)) {
                $fileOrderBySec[$secKey] = $decoded;
            }
        }
        if (empty($fileOrderBySec[$secKey])) {
            // Intentar localizar por clave normalizada
            $norm = function (string $s): string {
                $s = trim(mb_strtolower($s));
                $s = strtr($s, [
                    'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u',
                    'Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ú'=>'u',
                    'ñ'=>'n','Ñ'=>'n','ü'=>'u','Ü'=>'u','ï'=>'i','Ï'=>'i','ä'=>'a','Ä'=>'a','ë'=>'e','Ë'=>'e','ö'=>'o','Ö'=>'o',
                ]);
                $s = preg_replace('/\s+/', ' ', $s);
                return $s ?? '';
            };
            $secNorm = $norm($secKey);
            foreach ($fileOrderBySec as $k => $list) {
                if ($norm((string)$k) === $secNorm) {
                    $fileOrderBySec[$secKey] = $list;
                    break;
                }
            }
            if (empty($fileOrderBySec[$secKey])) {
                $this->index(['summary' => ['error' => 'No hay datos recientes para aplicar el orden. Vuelve a comparar.']]);
                return;
            }
        }

        // Encontrar idSeccion por nombre normalizado
        $normalize = function (string $s): string {
            $s = trim(mb_strtolower($s));
            $s = strtr($s, [
                'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u',
                'Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ú'=>'u',
                'ñ'=>'n','Ñ'=>'n','ü'=>'u','Ü'=>'u','ï'=>'i','Ï'=>'i','ä'=>'a','Ä'=>'a','ë'=>'e','Ë'=>'e','ö'=>'o','Ö'=>'o',
            ]);
            $s = preg_replace('/\s+/', ' ', $s);
            return $s ?? '';
        };
        $canonicalizeName = function (string $name) use ($normalize): string {
            $s = $normalize($name);
            $s = preg_replace('/[^a-z\s]/u', ' ', $s);
            $tokens = array_filter(explode(' ', $s), function($t){ return $t !== ''; });
            $stop = ['de','del','la','las','los','y','e','da','das','do','dos','di'];
            $filtered = [];
            foreach ($tokens as $t) { if (strlen($t) < 2) continue; if (in_array($t, $stop, true)) continue; $filtered[] = $t; }
            sort($filtered, SORT_STRING);
            return implode(' ', $filtered);
        };
        $nameTokens = function (string $name) use ($normalize): array {
            $s = $normalize($name);
            $s = preg_replace('/[^a-z\s]/u', ' ', $s);
            $parts = array_filter(explode(' ', $s), function($t){ return $t !== ''; });
            $stop = ['de','del','la','las','los','y','e','da','das','do','dos','di'];
            $out = [];
            foreach ($parts as $p) { if (strlen($p) < 2) continue; if (in_array($p, $stop, true)) continue; $out[] = $p; }
            return $out;
        };
        $liteCanon = function (array $tokens): string {
            if (empty($tokens)) return '';
            $sorted = $tokens;
            usort($sorted, function($a,$b){ return strlen($b) <=> strlen($a); });
            $top = array_slice($sorted, 0, 2);
            sort($top, SORT_STRING);
            return implode(' ', $top);
        };

        $secciones = $this->seccionModel->getAll();
        $idSeccion = null;
        foreach ($secciones as $sec) {
            if ($normalize($sec['name']) === $secKey) { $idSeccion = (int)$sec['id']; break; }
        }
        if (!$idSeccion) {
            $this->index(['summary' => ['error' => 'Sección no encontrada en la base de datos.']]);
            return;
        }

        // Orden del archivo para esta sección (lista de nombres en orden)
    $fileList = $fileOrderBySec[$secKey]; // array of ['canon','name','numArchivo']
        $fileCanonOrder = array_map(fn($x)=>$x['canon'], $fileList);

        // Cargar estudiantes activos de la sección
        $studentModel = new Student();
        $students = $studentModel->getBySeccion($idSeccion) ?? [];

        // Preparar estructuras de emparejamiento
        $bucketsStrict = []; // strictCanon => cola de estudiantes
        $bucketsLite   = []; // liteCanon => cola de estudiantes
        $remaining     = []; // lista de estudiantes restantes para búsqueda de similitud
        foreach ($students as $st) {
            if ((int)($st['status'] ?? 1) !== 1) continue;
            $tokens = $nameTokens($st['name'] ?? '');
            $strict = $canonicalizeName($st['name'] ?? '');
            $lite   = $liteCanon($tokens);
            $rec = [
                'id' => (int)$st['id'],
                'name' => $st['name'] ?? '',
                'num' => isset($st['NumerodeLista']) ? (int)$st['NumerodeLista'] : PHP_INT_MAX,
                'tokens' => $tokens,
                'strict' => $strict,
                'lite' => $lite,
            ];
            if (!isset($bucketsStrict[$strict])) $bucketsStrict[$strict] = [];
            $bucketsStrict[$strict][] = $rec;
            if (!isset($bucketsLite[$lite])) $bucketsLite[$lite] = [];
            $bucketsLite[$lite][] = $rec;
            $remaining[] = $rec;
        }
        // Ordenar colas por Número de lista actual
        foreach ($bucketsStrict as &$arr) { usort($arr, function($a,$b){ return $a['num'] <=> $b['num']; }); }
        unset($arr);
        foreach ($bucketsLite as &$arr) { usort($arr, function($a,$b){ return $a['num'] <=> $b['num']; }); }
        unset($arr);
        usort($remaining, function($a,$b){ return $a['num'] <=> $b['num']; });

        $newOrderIds = [];
        // Función para remover una ocurrencia de remaining y buckets
        $consume = function($rec) use (&$remaining, &$bucketsStrict, &$bucketsLite) {
            // quitar de remaining (primera coincidencia por id)
            foreach ($remaining as $i => $r) { if ($r['id'] === $rec['id']) { array_splice($remaining, $i, 1); break; } }
            // quitar de bucketsStrict
            $s = $rec['strict']; if (isset($bucketsStrict[$s])) {
                foreach ($bucketsStrict[$s] as $i => $r) { if ($r['id'] === $rec['id']) { array_splice($bucketsStrict[$s], $i, 1); break; } }
            }
            // quitar de bucketsLite
            $l = $rec['lite']; if (isset($bucketsLite[$l])) {
                foreach ($bucketsLite[$l] as $i => $r) { if ($r['id'] === $rec['id']) { array_splice($bucketsLite[$l], $i, 1); break; } }
            }
        };

        // Calcula score de solapamiento de tokens (0..1)
        $overlapScore = function(array $a, array $b): float {
            if (empty($a) || empty($b)) return 0.0;
            $sa = array_values(array_unique($a));
            $sb = array_values(array_unique($b));
            $ia = array_intersect($sa, $sb);
            $minDen = max(1, min(count($sa), count($sb)));
            return count($ia) / $minDen;
        };

    // Recorrer orden del archivo e intentar consumir estudiantes
    $fileSeqForSection = $fileOrderBySec[$secKey] ?? [];
    if (!is_array($fileSeqForSection)) { $fileSeqForSection = []; }
    foreach ($fileSeqForSection as $fileRec) {
            $fStrict = $fileRec['canon'];
            // reconstruir tokens del archivo a partir del nombre si está disponible; si no, a partir de strict
            $fileTokens = !empty($fileRec['name']) ? $nameTokens($fileRec['name']) : (($fStrict !== '') ? explode(' ', $fStrict) : []);
            $fLite = $liteCanon($fileTokens);

            // 1) strict exacto
            if (!empty($bucketsStrict[$fStrict])) {
                $rec = array_shift($bucketsStrict[$fStrict]);
                $newOrderIds[] = $rec['id'];
                $consume($rec);
                continue;
            }
            // 2) lite exacto
            if ($fLite !== '' && !empty($bucketsLite[$fLite])) {
                $rec = array_shift($bucketsLite[$fLite]);
                $newOrderIds[] = $rec['id'];
                $consume($rec);
                continue;
            }
            // 3) mejor solapamiento por tokens
            $best = null; $bestScore = 0.0;
            foreach ($remaining as $cand) {
                $score = $overlapScore($fileTokens, $cand['tokens']);
                if ($score > $bestScore) { $bestScore = $score; $best = $cand; }
            }
            if ($best && $bestScore >= 0.6) {
                $newOrderIds[] = $best['id'];
                $consume($best);
            }
            // si no hay buen match, omitimos y pasamos al siguiente (quedará al final)
        }

        // Anexar los no emparejados al final según su orden actual
        foreach ($remaining as $rec) { $newOrderIds[] = $rec['id']; }

        // Aplicar actualización segura en dos fases para evitar colisiones únicas
    $pdo = Student::$pdo ?? null;
    if (!$pdo) { $pdo = Database::getInstance()->getConnection(); }
    if (!$pdo) { throw new RuntimeException('No hay conexión a la base de datos.'); }
    if (!$pdo->inTransaction()) { $pdo->beginTransaction(); }
        try {
            $stmtTmp = $pdo->prepare('UPDATE student SET NumerodeLista = :num WHERE id = :id AND idSeccion = :sec');
            $offset = 10000;
            $pos = 1;
            foreach ($newOrderIds as $id) {
                $stmtTmp->execute([':num' => $offset + $pos, ':id' => $id, ':sec' => $idSeccion]);
                $pos++;
            }
            $stmt = $pdo->prepare('UPDATE student SET NumerodeLista = :num WHERE id = :id AND idSeccion = :sec');
            $pos = 1;
            foreach ($newOrderIds as $id) {
                $stmt->execute([':num' => $pos, ':id' => $id, ':sec' => $idSeccion]);
                $pos++;
            }
            if ($pdo->inTransaction()) { $pdo->commit(); }
            $_SESSION['status'] = 'success';
            $_SESSION['action'] = 'apply_order';

            // Recalcular reporte de orden usando DB actualizada y el orden del archivo en sesión
            try {
                $dbStudents = $studentModel->getAllStudents(1) ?? [];
                // DB order by section
                $dbOrderBySec = [];
                foreach ($dbStudents as $st) {
                    $nSec = $normalize($st['seccion_name'] ?? '');
                    if ($nSec === '') continue;
                    $dbOrderBySec[$nSec][] = [
                        'canon' => $canonicalizeName($st['name'] ?? ''),
                        'name'  => $st['name'] ?? '',
                        'num'   => isset($st['NumerodeLista']) ? (int)$st['NumerodeLista'] : null,
                    ];
                }
                foreach ($dbOrderBySec as $k => &$arr) {
                    usort($arr, function($a,$b){ return ($a['num']??PHP_INT_MAX) <=> ($b['num']??PHP_INT_MAX); });
                }
                unset($arr);

                // usar el fileOrderBySec efectivo que acabamos de usar (puede venir del payload)
                $orderBySection = [];
                $okCount = 0; $diffCount = 0; $skipCount = 0;
                // evaluar para todas las secciones conocidas por DB
                foreach ($dbOrderBySec as $key => $dbSeq) {
                    if (!isset($fileOrderBySec[$key])) { $diffCount++; $orderBySection[$key] = ['status'=>'diff','reason'=>'Sección no aparece en el archivo','mismatches'=>[], 'dbCount'=>count($dbSeq), 'fileCount'=>0]; continue; }
                    $fileSeq = $fileOrderBySec[$key];
                    $dbCanon = array_map(fn($x)=>$x['canon'], $dbSeq);
                    $fileCanon = array_map(fn($x)=>$x['canon'], $fileSeq);
                    $dbCount = count($dbCanon); $fileCount = count($fileCanon);
                    $minLen = min($dbCount, $fileCount);
                    $mismatches = [];
                    for ($i=0; $i<$minLen; $i++) {
                        if ($dbCanon[$i] !== $fileCanon[$i]) {
                            $mismatches[] = [
                                'pos' => $i+1,
                                'db'  => $dbSeq[$i]['name'],
                                'file'=> $fileSeq[$i]['name'],
                            ];
                            if (count($mismatches) >= 10) break;
                        }
                    }
                    if (empty($mismatches) && $dbCount === $fileCount) {
                        $okCount++; $orderBySection[$key] = ['status'=>'ok','mismatches'=>[], 'dbCount'=>$dbCount, 'fileCount'=>$fileCount];
                    } else {
                        $diffCount++; $orderBySection[$key] = ['status'=>'diff','mismatches'=>$mismatches, 'dbCount'=>$dbCount, 'fileCount'=>$fileCount];
                    }
                }
                $orderSummary = ['ok'=>$okCount, 'diff'=>$diffCount, 'skipped'=>$skipCount];
                $_SESSION['last_compare']['details']['orderReport'] = [
                    'bySection' => $orderBySection,
                    'summary' => $orderSummary,
                ];
                // Recalcular también nuevos/faltantes/movidos basados en fileOrderBySec guardado
                $this->refreshCompareFromSession();
            } catch (Throwable $e) {
                // silencioso: no bloquear por fallo de refresh
            }
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) { $pdo->rollBack(); }
            $_SESSION['status'] = 'error';
            $_SESSION['action'] = 'apply_order';
            $_SESSION['error_msg'] = 'No se pudo aplicar el orden: ' . $e->getMessage();
        }

        // Volver a la vista de comparación con los últimos resultados conservados
        $this->index();
    }

    // Recalcula comparación completa (nuevos, faltantes, movidos y orden) usando los datos del último archivo guardados en sesión
    public function refreshCompareFromSession(): void {
        $session = $_SESSION['last_compare'] ?? null;
        if (!$session || empty($session['fileOrderBySec'])) return;
        $fileOrderBySec = $session['fileOrderBySec'];

        // Normalizadores locales
        $normalize = function (string $s): string {
            $s = trim(mb_strtolower($s));
            $s = strtr($s, [
                'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u',
                'Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ú'=>'u',
                'ñ'=>'n','Ñ'=>'n','ü'=>'u','Ü'=>'u','ï'=>'i','Ï'=>'i','ä'=>'a','Ä'=>'a','ë'=>'e','Ë'=>'e','ö'=>'o','Ö'=>'o',
            ]);
            $s = preg_replace('/\s+/', ' ', $s);
            return $s ?? '';
        };
        $canonicalizeName = function (string $name) use ($normalize): string {
            $s = $normalize($name);
            $s = preg_replace('/[^a-z\s]/u', ' ', $s);
            $tokens = array_filter(explode(' ', $s), function($t){ return $t !== ''; });
            $stop = ['de','del','la','las','los','y','e','da','das','do','dos','di'];
            $filtered = [];
            foreach ($tokens as $t) { if (strlen($t) < 2) continue; if (in_array($t, $stop, true)) continue; $filtered[] = $t; }
            sort($filtered, SORT_STRING);
            return implode(' ', $filtered);
        };

        // Reconstruir entradas de archivo: lista plana de [name, nSec, numArchivo]
        $fileEntries = [];
        foreach ($fileOrderBySec as $secKey => $list) {
            foreach ($list as $idx => $r) {
                $fileEntries[] = [
                    'name' => $r['name'] ?? '',
                    'nName'=> $canonicalizeName($r['name'] ?? ''),
                    'nSec' => $secKey,
                    'numArchivo' => $r['numArchivo'] ?? ($idx+1),
                ];
            }
        }

        // BD actual
        $students = $this->studentModel->getAllStudents(1) ?? [];
        $dbByKey = [];
        $dbByName = [];
        $dbKeyGroups = [];
        foreach ($students as $st) {
            $nName = $canonicalizeName($st['name'] ?? '');
            $nSec  = $normalize($st['seccion_name'] ?? '');
            $dbByKey[$nName.'||'.$nSec] = $st;
            if (!isset($dbByName[$nName])) $dbByName[$nName] = [];
            $dbByName[$nName][$nSec] = $st;
            $k = $nName.'||'.$nSec;
            if (!isset($dbKeyGroups[$k])) $dbKeyGroups[$k] = [];
            $dbKeyGroups[$k][] = $st;
        }

        // Nuevos/Movidos
        $newStudents = [];
        $movedStudents = [];
        $seenKeys = [];
        foreach ($fileEntries as $fe) {
            $key = $fe['nName'].'||'.$fe['nSec'];
            if (isset($seenKeys[$key])) continue; // fileOrderBySec ya está deduplicado, pero por si acaso
            $seenKeys[$key] = true;
            if (!isset($dbByKey[$key])) {
                if (isset($dbByName[$fe['nName']])) {
                    $dbSecs = array_keys($dbByName[$fe['nName']]);
                    $nums = [];
                    foreach ($dbSecs as $sk) {
                        $st = $dbByName[$fe['nName']][$sk] ?? null;
                        if ($st && isset($st['NumerodeLista'])) { $nums[] = (string)$st['NumerodeLista']; }
                    }
                    $movedStudents[] = [
                        'name' => $fe['name'],
                        'from' => implode(', ', $dbSecs),
                        'to'   => $fe['nSec'],
                        'list_from' => implode(', ', $nums),
                        'numArchivo' => $fe['numArchivo']
                    ];
                } else {
                    $newStudents[] = ['name'=>$fe['name'], 'seccion'=>$fe['nSec'], 'numero'=>null, 'numArchivo'=>$fe['numArchivo']];
                }
            }
        }

        // Faltantes
        $missingStudents = [];
        foreach ($dbByKey as $key => $st) {
            if (!isset($seenKeys[$key])) {
                $missingStudents[] = [
                    'name' => $st['name'],
                    'seccion' => $st['seccion_name'],
                    'numero' => $st['NumerodeLista'] ?? null,
                    'numArchivo' => null
                ];
            }
        }

        // Duplicados en BD (misma clave canónica y sección)
        $dbDuplicates = [];
        foreach ($dbKeyGroups as $k => $arr) {
            if (count($arr) > 1) {
                $name = $arr[0]['name'] ?? '';
                $sec = $arr[0]['seccion_name'] ?? '';
                $nums = [];
                foreach ($arr as $st) { if (isset($st['NumerodeLista'])) { $nums[] = (string)$st['NumerodeLista']; } }
                $dbDuplicates[] = [ 'name'=>$name, 'seccion'=>$sec, 'numeros_bd'=>implode(', ', $nums) ];
            }
        }

        // Orden por sección (DB vs archivo)
        $dbOrderBySec = [];
        foreach ($students as $st) {
            $nSec = $normalize($st['seccion_name'] ?? '');
            if ($nSec === '') continue;
            $dbOrderBySec[$nSec][] = [
                'canon' => $canonicalizeName($st['name'] ?? ''),
                'name'  => $st['name'] ?? '',
                'num'   => isset($st['NumerodeLista']) ? (int)$st['NumerodeLista'] : null,
            ];
        }
        foreach ($dbOrderBySec as $secKey => &$arr) { usort($arr, function($a,$b){ return ($a['num']??PHP_INT_MAX) <=> ($b['num']??PHP_INT_MAX); }); }
        unset($arr);
        $orderBySection = [];
        $okCount = 0; $diffCount = 0; $skipCount = 0;
        foreach ($dbOrderBySec as $secKey => $dbSeq) {
            if (!isset($fileOrderBySec[$secKey])) { $diffCount++; $orderBySection[$secKey] = ['status'=>'diff','reason'=>'Sección no aparece en el archivo','mismatches'=>[], 'dbCount'=>count($dbSeq), 'fileCount'=>0]; continue; }
            $fileSeq = $fileOrderBySec[$secKey];
            $dbCanon = array_map(fn($x)=>$x['canon'], $dbSeq);
            $fileCanon = array_map(fn($x)=>$x['canon'], $fileSeq);
            $dbCount = count($dbCanon); $fileCount = count($fileCanon);
            $minLen = min($dbCount, $fileCount);
            $mismatches = [];
            for ($i=0; $i<$minLen; $i++) {
                if ($dbCanon[$i] !== $fileCanon[$i]) {
                    $mismatches[] = ['pos'=>$i+1, 'db'=>$dbSeq[$i]['name'], 'file'=>$fileSeq[$i]['name']];
                    if (count($mismatches) >= 10) break;
                }
            }
            if (empty($mismatches) && $dbCount === $fileCount) {
                $okCount++; $orderBySection[$secKey] = ['status'=>'ok','mismatches'=>[], 'dbCount'=>$dbCount, 'fileCount'=>$fileCount];
            } else {
                $diffCount++; $orderBySection[$secKey] = ['status'=>'diff','mismatches'=>$mismatches, 'dbCount'=>$dbCount, 'fileCount'=>$fileCount];
            }
        }
        $orderSummary = ['ok'=>$okCount, 'diff'=>$diffCount, 'skipped'=>$skipCount];

        // Reconstruir summary y details, manteniendo duplicados/unknown previos si existían
        $prevDetails = $session['details'] ?? [];
        $duplicatesInFile = $prevDetails['duplicatesInFile'] ?? [];
        $unknownSections  = $prevDetails['unknownSections'] ?? [];
        $summary = [
            'total_file' => array_reduce($fileOrderBySec, fn($c,$arr)=>$c+count($arr), 0),
            'total_db'   => count($students),
            'new'        => count($newStudents),
            'missing'    => count($missingStudents),
            'moved'      => count($movedStudents),
            'duplicates' => count($duplicatesInFile),
            'unknown_sections' => count($unknownSections),
            'db_duplicates' => count($dbDuplicates),
        ];
        $details = compact('newStudents','missingStudents','movedStudents','duplicatesInFile','unknownSections','dbDuplicates');
        $details['orderReport'] = [ 'bySection' => $orderBySection, 'summary' => $orderSummary ];

        $_SESSION['last_compare']['summary'] = $summary;
        $_SESSION['last_compare']['details'] = $details;
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
} elseif ($action === 'apply_order' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->applyOrder();
} elseif ($action === 'recompare') {
    // Recalcular comparación completa usando los datos del archivo cargados en sesión
    if (!empty($_SESSION['last_compare']['fileOrderBySec'])) {
        $controller->refreshCompareFromSession();
        $controller->index();
    } else {
        $controller->index(['summary' => ['error' => 'No hay archivo cargado en memoria. Sube un archivo para comparar.']]);
    }
} else {
    $controller->index();
}
