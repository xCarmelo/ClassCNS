<?php require_once "../view/header.php"; ?>

<style>
/* Columnas de criterios más angostas y tooltip */
th.criterio-celda, td[data-puntos] {
    min-width: 40px;
    max-width: 60px;
    width: 1%;
    padding-left: 2px;
    padding-right: 2px;
    text-align: center;
    position: relative;
}
.criterio-celda .criterio-tooltip {
    display: none;
    position: absolute;
    left: 50%;
    top: 100%;
    transform: translateX(-50%);
    background: #222;
    color: #fff;
    padding: 4px 12px;
    border-radius: 6px;
    white-space: nowrap;
    font-size: 0.95em;
    z-index: 10;
    margin-top: 2px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.criterio-celda:hover .criterio-tooltip {
    display: block;
}
/* Selects de calificación más pequeños y tipo caja de texto */
.select-qual {
    font-size: 0.85em !important;
    height: 1.7em !important;
    padding: 0 0.3em !important;
    border: 1px solid #bbb !important;
    border-radius: 4px !important;
    background: #fff !important;
    box-shadow: none !important;
    min-width: 3.5em;
    max-width: 5em;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    text-align: center;
    margin: 0 auto;
    display: block;
}
.select-qual:focus {
    outline: 1.5px solid #4c82ef;
    border-color: #4c82ef;
}
.indicador-celda {
    max-width: 110px;
    white-space: nowrap;
    overflow: visible; /* permitir que el tooltip se vea fuera del th */
    position: relative;
    cursor: pointer;
}
.indicador-label {
    display: inline-block;
    max-width: 100%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis; /* aplicar el ellipsis sólo al texto del indicador */
}
.indicador-tooltip {
    display: none;
    position: absolute;
    left: 50%;
    top: 100%;
    transform: translateX(-50%);
    background: #0d6efd; /* color de fondo cambiado para el tooltip del indicador */
    color: #fff;
    padding: 4px 12px;
    border-radius: 6px;
    white-space: pre-line;
    font-size: 0.95em;
    z-index: 1000;
    margin-top: 2px;
    box-shadow: 0 2px 8px rgba(13,110,253,0.35);
    min-width: 120px;
    max-width: 350px;
    text-align: left;
}
.indicador-celda:hover .indicador-tooltip {
    display: block;
}
</style>

<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Calificaciones</h3>
        <div class="d-flex gap-2">
            <button type="button" id="btnApplySelection" class="btn btn-primary"><i class="bi bi-people-fill"></i> Aplicar a selección</button>
            <button type="button" id="btnApplyAll" class="btn btn-secondary"><i class="bi bi-people"></i> Aplicar a todos</button>
            <button type="button" id="btnExportarNotas" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Exportar Notas</button>
        </div>
    </div>

    <script>
    // Encabezados completos para exportar (generados en PHP)
    window.EXPORT_NOTAS_HEADERS = [
        'Nombre del Estudiante',
        <?php
        // Generar encabezados: por cada indicador, por cada criterio
        $headers = [];
        foreach ($indicadores as $ind) {
            $indName = str_replace("'", "\'", $ind['name']);
            $lista = $criterios[$ind['id']] ?? [];
            $maxC = max(3, count($lista));
            for ($j = 0; $j < $maxC; $j++) {
                $c = $lista[$j] ?? null;
                if ($c) {
                    $critName = str_replace("'", "\'", $c['name'] ?? $c['descripcion'] ?? '');
                    $puntos = isset($c['puntos']) ? $c['puntos'] : (isset($c['puntaje']) ? $c['puntaje'] : 0);
                    $headers[] = "'{$indName} - {$critName} ({$puntos})'";
                } else {
                    $headers[] = "'-'";
                }
            }
        }
        echo implode(",\n        ", $headers);
        ?>,
        'Total Numérico',
        'Cualitativa'
    ];
    </script>

    <!-- Filtros -->
    <form id="filtrosForm" method="GET" action="">
        <div class="row mb-3">
            <div class="col-md-3">
                <label>Sección</label>
                <select id="filterSeccion" name="seccion" class="form-select" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($secciones as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= (isset($_GET['seccion']) && $_GET['seccion']==$s['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label>Materia</label>
                <select id="filterMateria" name="materia" class="form-select" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($materias as $m): ?>
                        <option value="<?= $m['id'] ?>" <?= (isset($_GET['materia']) && $_GET['materia']==$m['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label>Año</label>
                <input id="filterAnio" type="number" name="anio" class="form-control" value="<?= htmlspecialchars($_GET['anio'] ?? '') ?>" required>
            </div>

            <div class="col-md-2">
                <label>Corte</label>
                <select id="filterCorte" name="corte" class="form-select" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($cortes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= (isset($_GET['corte']) && $_GET['corte']==$c['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">Filtrar</button>
            </div>
        </div>
    </form>

    <?php
    $tieneFiltros = isset($_GET['seccion'], $_GET['materia'], $_GET['anio'], $_GET['corte'])
                    && $_GET['seccion'] !== '' && $_GET['materia'] !== '' && $_GET['anio'] !== '' && $_GET['corte'] !== '';
    if (!$tieneFiltros): ?>
        <div class="alert alert-info">Seleccione los 4 filtros (Sección, Materia, Año y Corte) para ver la tabla.</div>
    <?php elseif (empty($estudiantes) || empty($indicadores)): ?>
        <div class="alert alert-warning">No hay datos para los filtros seleccionados. Verifique que existan Indicadores vinculados a la Sección mediante Enlace y que haya estudiantes activos.</div>
    <?php else: ?>

        <?php
        // Paleta dinámica por indicador: colorea encabezados y celdas del grupo
        $nInd = is_array($indicadores) ? count($indicadores) : 0;
        if ($nInd > 0): ?>
        <style>
        <?php for ($i = 0; $i < $nInd; $i++):
                $h = round(($i * 360) / max(1, $nInd));
                // Tonos coordinados: header indicador (más oscuro), header criterio (medio), cuerpo (muy claro)
                $hdrInd = "hsl($h, 70%, 35%)";
                $hdrCrit = "hsl($h, 70%, 45%)";
                $bodyBg = "hsl($h, 70%, 96%)";
                $bodyBorder = "hsl($h, 60%, 85%)";
        ?>
            th.indicador-celda.col-ind<?= $i ?> { background: <?= $hdrInd ?>; color: #fff; }
            th.criterio-celda.col-ind<?= $i ?> { background: <?= $hdrCrit ?>; color: #fff; }
            td.col-ind<?= $i ?> { background: <?= $bodyBg ?>; border-color: <?= $bodyBorder ?>; }
        <?php endfor; ?>
        </style>
        <?php endif; ?>

    <!-- Filtro de búsqueda por nombre y checkboxes de columnas -->
    <div class="row mb-2">
        <div class="col-md-4 mb-2">
            <input type="text" id="filtroNombre" class="form-control" placeholder="Buscar por nombre de estudiante...">
        </div>
        <div class="col-md-8 d-flex align-items-center flex-wrap" id="columnToggles">
            <?php if (!empty($indicadores)): ?>
                <?php foreach ($indicadores as $i => $ind): ?>
                    <div class="form-check me-3">
                        <input class="form-check-input toggle-col" type="checkbox" id="toggleInd<?= $i ?>" data-colgroup="ind<?= $i ?>" checked>
                        <label class="form-check-label" for="toggleInd<?= $i ?>">
                            <?= htmlspecialchars(mb_strimwidth($ind['name'], 0, 10, '...')) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
                
            <?php endif; ?>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <label class="form-label me-2">Filas por página:</label>
            <select id="selectRowsPerPage" class="form-select d-inline-block" style="width:120px;">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option value="40">40</option>
                <option value="50">50</option>
            </select>
        </div>
        <div id="pagination" class="d-flex justify-content-center"></div>
    </div>

    <div class="table-responsive">
        <table id="tablaCalif" class="table table-bordered align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th rowspan="2"><input type="checkbox" id="selectAllStudents" title="Seleccionar todo"></th>
                    <th rowspan="2">Nombre del Estudiante</th>
                    <?php foreach ($indicadores as $i => $ind):
                        $numC = max(3, count($criterios[$ind['id']] ?? []));
                        $nombreInd = htmlspecialchars($ind['name']);
                        $corto = mb_strimwidth($nombreInd, 0, 13, '...');
                    ?>
                        <th colspan="<?= $numC ?>" class="indicador-celda col-ind<?= $i ?>">
                            <span class="indicador-label"><?= $corto ?></span>
                            <span class="indicador-tooltip"><?= $nombreInd ?></span>
                        </th>
                    <?php endforeach; ?>
                    <th rowspan="2">Total Numérico</th>
                    <th rowspan="2">Cualitativa</th>
                </tr>

                <tr>
                    <?php foreach ($indicadores as $i => $ind):
                        $lista = $criterios[$ind['id']] ?? [];
                        for ($j = 0; $j < max(3, count($lista)); $j++):
                            $c = $lista[$j] ?? null;
                    ?>
                        <?php
                            // Preparar datos de tooltip antes de imprimir el <th>
                            $descr = '';
                            $puntos = 0;
                            if ($c) {
                                $descr = $c['name'] ?? $c['descripcion'] ?? '';
                                $puntos = isset($c['puntos']) ? $c['puntos'] : (isset($c['puntaje']) ? $c['puntaje'] : 0);
                            }
                        ?>
                        <th class="criterio-celda col-ind<?= $i ?>" data-descr="<?= htmlspecialchars($descr) ?>" data-puntos="<?= (int)$puntos ?>" <?= $c ? 'data-crit-id="'.(int)$c['id'].'"' : '' ?> >
                            <?php if ($c): ?>
                                <span class="criterio-num"> <?= ($j+1) ?>(<?= (int)$puntos ?>) </span>
                            <?php else: ?>
                                <div class="small text-muted">-</div>
                            <?php endif; ?>
                        </th>
                    <?php endfor; endforeach; ?>
                </tr>
            </thead>

            <tbody>
                <?php
                // Si la materia seleccionada es Informática, aplicamos la división de lista:
                $selectedMateria = isset($_GET['materia']) ? (int)$_GET['materia'] : 0;
                // Asumimos que Informática tiene id = 2 (ajustar si es otro id en la BD)
                $isInformatica = ($selectedMateria === 2);

                foreach ($estudiantes as $stu):
                    $rowClass = ($isInformatica && isset($stu['fin']) && (int)$stu['fin'] === 1) ? 'table-warning' : '';
                ?>
                    <tr data-student="<?= $stu['id'] ?>" class="<?= $rowClass ?>">
                        <td class="text-center"><input type="checkbox" class="select-student" data-student-id="<?= $stu['id'] ?>"></td>
                        <td class="text-start"><?= htmlspecialchars($stu['name']) ?></td>

                        <?php
                        foreach ($indicadores as $i => $ind):
                            $lista = $criterios[$ind['id']] ?? [];
                            for ($j = 0; $j < max(3, count($lista)); $j++):
                                $c = $lista[$j] ?? null;
                                if ($c):
                                    $idC = (int)$c['id'];
                                    $puntosC = isset($c['puntos']) ? (int)$c['puntos'] : (isset($c['puntaje']) ? (int)$c['puntaje'] : 0);
                                    $notaExisting = $notas[$stu['id']][$idC]['nota'] ?? null;
                                    $qualExisting = $notas[$stu['id']][$idC]['cualitativa'] ?? '';
                                ?>
                                    <td data-puntos="<?= $puntosC ?>" data-criterio-id="<?= $idC ?>" class="col-ind<?= $i ?>">
                                        <div class="d-flex flex-column">
                                            <select class="form-select select-qual" 
                                                    data-student="<?= $stu['id'] ?>"
                                                    data-criterio="<?= $idC ?>">
                                                <option value="">--</option>
                                                <?php foreach (['AA','AS','AF','AI'] as $opt): ?>
                                                    <option value="<?= $opt ?>" <?= ($qualExisting === $opt) ? 'selected' : '' ?>><?= $opt ?></option>
                                                <?php endforeach; ?>
                                            </select>

                                            <input type="text" readonly class="form-control form-control-sm mt-2 nota-read"
                                                   value="<?= $notaExisting !== null ? (int)$notaExisting : '' ?>">
                                        </div>
                                    </td>
                                <?php else: ?>
                                    <td class="col-ind<?= $i ?>"></td>
                                <?php endif;
                            endfor;
                        endforeach;
                        ?>
                        <td class="total-num">0</td>
                        <td class="total-qual">--</td>
                    </tr>
                    <?php
                        // Si estamos en Informática y este estudiante marca fin=1, detenemos la generación de la lista
                        if ($isInformatica && isset($stu['fin']) && (int)$stu['fin'] === 1) {
                            // romper el foreach para no mostrar más estudiantes
                            break;
                        }
                    endforeach;
                    ?>
            </tbody>
        </table>
    </div>

                <!-- Modal para aplicar indicadores/criterios masivamente -->
                <div class="modal fade" id="modalApplyNotas" tabindex="-1" aria-labelledby="modalApplyNotasLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="modalApplyNotasLabel">Aplicar indicadores y criterios</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <p>Selecciona los criterios a aplicar y la escala cualitativa para cada criterio. Luego confirma para guardar.</p>
                                <div class="mb-3">
                                    <label class="form-label">Indicadores y criterios</label>
                                    <button type="button" id="selectAllCriteria" class="btn btn-outline-primary btn-sm mb-2">Seleccionar todos</button>
                                    <div style="max-height:320px; overflow:auto; border:1px solid #ddd; padding:8px; border-radius:6px;">
                                        <?php foreach ($indicadores as $i => $ind): ?>
                                            <div class="mb-2">
                                                <strong><?= htmlspecialchars($ind['name']) ?></strong>
                                                <div class="d-flex flex-wrap gap-2 mt-1">
                                                    <?php foreach (($criterios[$ind['id']] ?? []) as $c): ?>
                                                        <div class="form-check">
                                                            <input class="form-check-input modal-crit" type="checkbox" value="<?= (int)$c['id'] ?>" id="crit<?= (int)$c['id'] ?>">
                                                            <label class="form-check-label" for="crit<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name'] ?? $c['descripcion'] ?? '') ?> (<?= (int)($c['puntos'] ?? ($c['puntaje'] ?? 0)) ?>)</label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                                    <label class="form-label">Escala cualitativa a aplicar</label>
                                                    <select id="modalCualitativa" class="form-select">
                                                        <option value="">--</option>
                                        <option value="AA">AA</option>
                                        <option value="AS">AS</option>
                                        <option value="AF">AF</option>
                                        <option value="AI">AI</option>
                                    </select>
                                </div>

                                <div class="form-text text-muted">Se realizará una petición por estudiante/criterio. El sistema intentará guardar todo y luego mostrará un resumen.</div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" id="modalApplyBtn" class="btn btn-primary">Aplicar y Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>

    <!-- Contenedor de paginación -->
    <div id="pagination" class="d-flex justify-content-center mt-3"></div>

    <?php endif; ?>
</div>

<!-- Toast de notificaciones -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
    <div id="toastNotas" class="toast align-items-center text-bg-primary border-0" role="status" aria-live="polite" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastNotasBody">Notificación</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
  
</div>

<!-- Modal flotante para criterio -->
<style>
.criterio-modal {
    position: absolute;
    z-index: 9999;
    background: #fff;
    border: 2px solid #333;
    border-radius: 10px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.25);
    padding: 18px 28px;
    min-width: 320px;
    max-width: 500px;
    font-size: 20px;
    color: #222;
    display: none;
    font-family: inherit;
}
</style>

<script>
(function(){
    // Utilidad: toast para esta vista
    window.showToastNotas = function(message, variant = 'primary') {
        const toastEl = document.getElementById('toastNotas');
        const bodyEl = document.getElementById('toastNotasBody');
        if (!toastEl || !bodyEl) return;
        bodyEl.textContent = message;
        toastEl.className = 'toast align-items-center border-0 text-bg-' + variant;
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
    }
})();
(function () {
    // --- modal flotante para criterios ---
    let criterioModal = null;
    function showCriterioModal(e) {
        const celda = e.currentTarget;
        if (!criterioModal) {
            criterioModal = document.createElement('div');
            criterioModal.className = 'criterio-modal';
            document.body.appendChild(criterioModal);
        }
        const descr = celda.getAttribute('data-descr') || '';
        const puntos = celda.getAttribute('data-puntos') || '';
        criterioModal.innerHTML = `<strong>Criterio:</strong> ${descr}<br><strong>Puntos:</strong> ${puntos}`;
        criterioModal.style.display = 'block';
        const rect = celda.getBoundingClientRect();
        criterioModal.style.top = (window.scrollY + rect.bottom + 8) + 'px';
        criterioModal.style.left = (window.scrollX + rect.left + 8) + 'px';
    }

    function hideCriterioModal() {
        if (criterioModal) criterioModal.style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.criterio-celda').forEach(celda => {
            celda.addEventListener('mouseenter', showCriterioModal);
            celda.addEventListener('mouseleave', hideCriterioModal);
        });
    });
    // Escalas cualitativas -> porcentaje de puntos
    const MAP = { 'AA':1, 'AS':0.85, 'AF':0.70, 'AI':0.60 };

    // --- persistencia de filtros con localStorage ---
    const keyPrefix = 'cal_';
    const form = document.getElementById('filtrosForm');
    const seccionEl = document.getElementById('filterSeccion');
    const materiaEl = document.getElementById('filterMateria');
    const anioEl = document.getElementById('filterAnio');
    const corteEl = document.getElementById('filterCorte');

    function saveFiltersToStorage() {
        try {
            localStorage.setItem(keyPrefix + 'seccion', seccionEl.value || '');
            localStorage.setItem(keyPrefix + 'materia', materiaEl.value || '');
            localStorage.setItem(keyPrefix + 'anio', anioEl.value || '');
            localStorage.setItem(keyPrefix + 'corte', corteEl.value || '');
        } catch(e) {}
    }

    [seccionEl, materiaEl, anioEl, corteEl].forEach(el => {
        if (el) el.addEventListener('change', saveFiltersToStorage);
    });

    form.addEventListener('submit', saveFiltersToStorage);

    const urlParams = new URLSearchParams(window.location.search);
    const hasAllParams = urlParams.has('seccion') && urlParams.has('materia') && urlParams.has('anio') && urlParams.has('corte');

    if (!hasAllParams) {
        try {
            const s = localStorage.getItem(keyPrefix + 'seccion') || '';
            const m = localStorage.getItem(keyPrefix + 'materia') || '';
            const a = localStorage.getItem(keyPrefix + 'anio') || '';
            const c = localStorage.getItem(keyPrefix + 'corte') || '';

            let shouldSubmit = false;
            if (s && seccionEl && !seccionEl.value) { seccionEl.value = s; shouldSubmit = true; }
            if (m && materiaEl && !materiaEl.value) { materiaEl.value = m; shouldSubmit = true; }
            if (a && anioEl && !anioEl.value) { anioEl.value = a; shouldSubmit = true; }
            if (c && corteEl && !corteEl.value) { corteEl.value = c; shouldSubmit = true; }

            if (shouldSubmit) form.submit();
        } catch(e) {}
    }

    // --- cálculo de totales ---
    function calcularTotales() {
        document.querySelectorAll('#tablaCalif tbody tr').forEach(tr => {
            let total = 0;
            let maxPossible = 0;

            tr.querySelectorAll('td[data-puntos]').forEach(td => {
                const puntos = parseInt(td.dataset.puntos, 10) || 0;
                maxPossible += puntos;
                const input = td.querySelector('.nota-read');
                if (input && input.value.trim() !== '') {
                    const val = parseInt(input.value, 10) || 0;
                    total += val;
                }
            });

            const tdTotal = tr.querySelector('.total-num');
            if (tdTotal) tdTotal.innerText = total;

            const tdQual = tr.querySelector('.total-qual');
            let qual = '--';
            if (maxPossible > 0) {
                const ratio = (total / maxPossible) * 100; // %
                if (ratio >= 85) qual = 'AA';
                else if (ratio >= 70) qual = 'AS';
                else if (ratio >= 60) qual = 'AF';
                else if (ratio > 0) qual = 'AI';
            }
            if (tdQual) tdQual.innerText = qual;
        });
    }

    // Exponer para uso fuera de este bloque (modales, etc.)
    window.recalcNotasTotales = calcularTotales;

    // --- guardado via AJAX ---
    async function onSelectChange(e) {
        const sel = e.target;
        const idStudent = sel.dataset.student;
        const idCriterio = sel.dataset.criterio;
        const cual = sel.value;
        const cell = sel.closest('td');
        const inputNota = cell ? cell.querySelector('.nota-read') : null;
        const puntos = parseInt(cell.dataset.puntos, 10) || 0;

        if (!cual) {
            // Limpiar en backend
            try {
                const body = new URLSearchParams();
                body.append('idStudent', idStudent);
                body.append('idCriterio', idCriterio);
                const resp = await fetch('/app/controller/NotasController.php?action=clear', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: body.toString()
                });
                const json = await resp.json();
                if (resp.ok && json.ok) {
                    if (inputNota) inputNota.value = '';
                    window.recalcNotasTotales && window.recalcNotasTotales();
                    showToastNotas('Nota eliminada.', 'success');
                } else {
                    showToastNotas('No se pudo eliminar la nota.', 'danger');
                }
            } catch(err) {
                showToastNotas('Error de red al eliminar nota: ' + err.message, 'danger');
            }
            return;
        }

        const escala = MAP[cual] || 0;
        const notaFinal = Math.round(puntos * escala);
        const body = new URLSearchParams();
        body.append('idStudent', idStudent);
        body.append('idCriterio', idCriterio);
        body.append('cualitativa', cual);
        body.append('nota', notaFinal);

        try {
            const url = '/app/controller/NotasController.php?action=save';
            const resp = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body.toString()
            });
            let json;
            try { json = await resp.json(); } catch(parseErr) {
                throw new Error('Respuesta no JSON (' + resp.status + ').');
            }
            if (resp.ok && json.ok) {
                if (inputNota) inputNota.value = json.nota;
                window.recalcNotasTotales && window.recalcNotasTotales();
                showToastNotas('Nota guardada.', 'success');
            } else {
                showToastNotas('Error guardando nota: ' + (json.error || 'error desconocido'), 'danger');
            }
        } catch (err) {
            showToastNotas('Error de red al guardar nota: ' + err.message, 'danger');
        }
    }

    document.querySelectorAll('.select-qual').forEach(s => s.addEventListener('change', onSelectChange));
    calcularTotales();

    // --- paginación y filtro por nombre ---
    const table = document.getElementById('tablaCalif');
    const tbody = table ? table.querySelector('tbody') : null;
    const pagination = document.getElementById('pagination');
    const filtroNombre = document.getElementById('filtroNombre');
    const storageKeyRows = keyPrefix + 'rowsPerPage';
    let rowsPerPage = parseInt(localStorage.getItem(storageKeyRows) || '10', 10) || 10;
    const selectRows = document.getElementById('selectRowsPerPage');
    if (selectRows) {
        selectRows.value = String(rowsPerPage);
        selectRows.addEventListener('change', function(){
            rowsPerPage = parseInt(this.value, 10) || 10;
            try { localStorage.setItem(storageKeyRows, String(rowsPerPage)); } catch(e) {}
            if (tbody) renderPage(1);
        });
    }

    if (tbody && pagination) {
        let rows = Array.from(tbody.querySelectorAll('tr'));
        let currentPage = 1;
        let filteredRows = rows;

        function renderPage(page) {
            currentPage = page;
            filteredRows.forEach((row, i) => {
                row.style.display = (i >= (page-1)*rowsPerPage && i < page*rowsPerPage) ? '' : 'none';
            });
            rows.filter(r => !filteredRows.includes(r)).forEach(r => r.style.display = 'none');
            renderPagination();
        }

        function renderPagination() {
            pagination.innerHTML = '';
            const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
            if (totalPages <= 1) return;

            const ul = document.createElement('ul');
            ul.className = 'pagination';

            for (let i=1; i<=totalPages; i++) {
                const li = document.createElement('li');
                li.className = 'page-item ' + (i===currentPage ? 'active' : '');
                const a = document.createElement('a');
                a.className = 'page-link';
                a.href = '#';
                a.textContent = i;
                a.addEventListener('click', (e) => {
                    e.preventDefault();
                    renderPage(i);
                });
                li.appendChild(a);
                ul.appendChild(li);
            }
            pagination.appendChild(ul);
        }

        function filtrarPorNombre() {
            const texto = filtroNombre.value.trim().toLowerCase();
            if (!texto) {
                filteredRows = rows;
            } else {
                filteredRows = rows.filter(row => {
                    const nombreTd = row.querySelector('td.text-start');
                    return nombreTd && nombreTd.textContent.toLowerCase().includes(texto);
                });
            }
            renderPage(1);
        }

        filtroNombre.addEventListener('input', filtrarPorNombre);
        renderPage(1);
    }

})();
// --- Ocultar/mostrar columnas por checkbox ---
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-col').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const colgroup = this.dataset.colgroup;
            const checked = this.checked;
            if (colgroup.startsWith('ind')) {
                document.querySelectorAll('.col-' + colgroup).forEach(function(col) {
                    col.style.display = checked ? '' : 'none';
                });
            } else if (colgroup === 'disciplina') {
                document.querySelectorAll('.col-disciplina').forEach(function(col) {
                    col.style.display = checked ? '' : 'none';
                });
            }
        });
    });
});
</script>


<script>
// Exportar notas filtradas/visibles a Excel
document.getElementById('btnExportarNotas').addEventListener('click', function() {
    const tabla = document.getElementById('tablaCalif');
    if (!tabla) return;
    // Solo filas visibles
    const filas = Array.from(tabla.querySelectorAll('tbody tr')).filter(tr => tr.style.display !== 'none');
    if (filas.length === 0) {
    showToastNotas('No hay notas para exportar con los filtros actuales.', 'warning');
        return;
    }
    // Usar encabezados completos generados en PHP
    const headerNames = window.EXPORT_NOTAS_HEADERS;
    // Datos
    const data = filas.map(tr => {
        const tds = Array.from(tr.children);
        let arr = [];
        // Nombre del estudiante
        arr.push(tds[0] ? tds[0].innerText.trim() : '');
        // Criterios (todos los td con select.select-qual, en orden)
        for (let i = 1; i < tds.length - 2; i++) {
            const td = tds[i];
            const select = td.querySelector && td.querySelector('select.select-qual');
            if (select) {
                arr.push(select.value || '');
            } else {
                arr.push('');
            }
        }
        // Total numérico y cualitativa
        arr.push(tds[tds.length-2] ? tds[tds.length-2].innerText.trim() : '');
        arr.push(tds[tds.length-1] ? tds[tds.length-1].innerText.trim() : '');
        return arr;
    });
    // Crear hoja y libro
    const ws = XLSX.utils.aoa_to_sheet([headerNames, ...data]);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Notas');
    XLSX.writeFile(wb, 'notas_filtradas.xlsx');
});
</script>

<script>
// --- Selección de estudiantes ---
const selectAll = document.getElementById('selectAllStudents');
if (selectAll) {
    selectAll.addEventListener('change', function(){
        document.querySelectorAll('.select-student').forEach(cb => cb.checked = this.checked);
    });
}

// Manejo de botones aplicar
const btnApplySelection = document.getElementById('btnApplySelection');
const btnApplyAll = document.getElementById('btnApplyAll');
let modalApply;
document.addEventListener('DOMContentLoaded', function(){
    const modalEl = document.getElementById('modalApplyNotas');
    if (modalEl) { modalApply = new bootstrap.Modal(modalEl); }
});

btnApplySelection && btnApplySelection.addEventListener('click', function(e){
    e.preventDefault();
    const selected = Array.from(document.querySelectorAll('.select-student:checked')).map(i => i.dataset.studentId);
    if (selected.length === 0) { showToastNotas('Selecciona al menos un estudiante.', 'warning'); return; }
    // Guardamos la selección en el modal para usarla al confirmar
    document.getElementById('modalApplyNotas').dataset.targets = JSON.stringify(selected);
    if (modalApply) modalApply.show();
});

btnApplyAll && btnApplyAll.addEventListener('click', function(e){
    e.preventDefault();
    const all = Array.from(document.querySelectorAll('#tablaCalif tbody tr')).map(tr => tr.dataset.student);
    if (all.length === 0) { showToastNotas('No hay estudiantes para aplicar.', 'warning'); return; }
    document.getElementById('modalApplyNotas').dataset.targets = JSON.stringify(all);
    if (modalApply) modalApply.show();
});

// Lógica para aplicar masivamente
document.getElementById('modalApplyBtn').addEventListener('click', async function(){
    const modalEl = document.getElementById('modalApplyNotas');
    const checkedCrit = Array.from(modalEl.querySelectorAll('.modal-crit:checked')).map(i => parseInt(i.value,10));
    const cual = document.getElementById('modalCualitativa').value;
    if (checkedCrit.length === 0) { showToastNotas('Selecciona al menos un criterio.', 'warning'); return; }

    let targets = [];
    try { targets = JSON.parse(modalEl.dataset.targets || '[]'); } catch(e){ targets = []; }
    if (targets.length === 0) { showToastNotas('No hay estudiantes seleccionados.', 'warning'); return; }

        // Recoger puntos por criterio (necesario para calcular nota numérica)
        const critPoints = {};
        checkedCrit.forEach(cid => {
            // Buscar cualquier celda de ese criterio y leer sus puntos
            const td = document.querySelector('td[data-criterio-id="' + cid + '"]');
            const th = document.querySelector('th[data-crit-id="' + cid + '"]');
            let p = 0;
            if (td) p = parseInt(td.dataset.puntos,10) || 0;
            if (!p && th) p = parseInt(th.getAttribute('data-puntos') || '0', 10) || 0;
            critPoints[cid] = p;
        });

    // Map de escalas
    const MAP = { 'AA':1, 'AS':0.85, 'AF':0.70, 'AI':0.60 };

    // Confirmar y proceder: enviamos peticiones por cada student x criterio
    const totalOps = targets.length * checkedCrit.length;
    let successOps = 0;
    let failedOps = 0;

    showToastNotas('Guardando ' + totalOps + ' calificación(es)...', 'primary');
    document.getElementById('modalApplyBtn').disabled = true;

        for (const sid of targets) {
            for (const cid of checkedCrit) {
                // Si cual está vacío -> limpiar (dejar en "--")
                if (!cual) {
                    const body = new URLSearchParams();
                    body.append('idStudent', sid);
                    body.append('idCriterio', cid);
                    try {
                        const resp = await fetch('/app/controller/NotasController.php?action=clear', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: body.toString()
                        });
                        const json = await resp.json();
                        if (resp.ok && json.ok) {
                            successOps++;
                            const sel = document.querySelector('select.select-qual[data-student="' + sid + '"][data-criterio="' + cid + '"]');
                            if (sel) { sel.value = ''; const inp = sel.closest('td').querySelector('.nota-read'); if (inp) inp.value = ''; }
                        } else {
                            failedOps++;
                        }
                    } catch(err) {
                        failedOps++;
                    }
                    continue;
                }

                // Guardado normal
                const puntos = critPoints[cid] || 0;
                const escala = MAP[cual] || 0;
                const nota = Math.round(puntos * escala);
                const body = new URLSearchParams();
                body.append('idStudent', sid);
                body.append('idCriterio', cid);
                body.append('cualitativa', cual);
                body.append('nota', nota);

                try {
                    const resp = await fetch('/app/controller/NotasController.php?action=save', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: body.toString()
                    });
                    const json = await resp.json();
                    if (resp.ok && json.ok) {
                        successOps++;
                        const celda = document.querySelector('select.select-qual[data-student="' + sid + '"][data-criterio="' + cid + '"]');
                        if (celda) { celda.value = cual; const inp = celda.closest('td').querySelector('.nota-read'); if (inp) inp.value = json.nota; }
                    } else {
                        failedOps++;
                    }
                } catch (err) {
                    failedOps++;
                }
            }
        }

        document.getElementById('modalApplyBtn').disabled = false;
        modalApply.hide();
        window.recalcNotasTotales && window.recalcNotasTotales();
    showToastNotas('Guardado finalizado. OK: ' + successOps + ', Fallidos: ' + failedOps, failedOps ? 'warning' : 'success');
});

document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad para el botón "Seleccionar todos"
    const selectAllButton = document.getElementById('selectAllCriteria');
    if (selectAllButton) {
        selectAllButton.addEventListener('click', function() {
            document.querySelectorAll('.modal-crit').forEach(checkbox => {
                checkbox.checked = true;
            });
        });
    }
});
</script>

<?php require_once "../view/footer.php"; ?>
