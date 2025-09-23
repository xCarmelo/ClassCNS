<?php require_once "../view/header.php"; ?>

<div class="container mt-4">
    <h3>Calificaciones</h3>

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

    <?php if (empty($estudiantes) || empty($indicadores)): ?>
        <div class="alert alert-info">Seleccione los 4 filtros (Sección, Materia, Año y Corte) para ver la tabla.</div>
    <?php else: ?>

    <!-- Filtro de búsqueda por nombre -->
    <div class="row mb-2">
        <div class="col-md-4">
            <input type="text" id="filtroNombre" class="form-control" placeholder="Buscar por nombre de estudiante...">
        </div>
    </div>
    <div class="table-responsive">
        <table id="tablaCalif" class="table table-bordered align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th rowspan="2">Nombre del Estudiante</th>
                    <?php foreach ($indicadores as $ind):
                        $numC = max(3, count($criterios[$ind['id']] ?? []));
                        ?>
                        <th colspan="<?= $numC ?>"><?= htmlspecialchars($ind['name']) ?></th>
                    <?php endforeach; ?>
                    <th rowspan="2">Total Numérico</th>
                    <th rowspan="2">Cualitativa</th>
                </tr>

                <tr>
                    <?php foreach ($indicadores as $ind):
                        $lista = $criterios[$ind['id']] ?? [];
                        for ($i = 0; $i < max(3, count($lista)); $i++):
                            $c = $lista[$i] ?? null;
                    ?>
                        <th class="criterio-celda" data-descr="<?= isset($c) ? htmlspecialchars($c['name'] ?? $c['descripcion'] ?? '') : '' ?>" data-puntos="<?= isset($c) ? (int)(isset($c['puntos']) ? $c['puntos'] : (isset($c['puntaje']) ? $c['puntaje'] : 0)) : '' ?>">
                            <?php if ($c): ?>
                                <span class="criterio-num"> C_<?= ($i+1) ?> (<?= (int)(isset($c['puntos']) ? $c['puntos'] : (isset($c['puntaje']) ? $c['puntaje'] : 0)) ?>) </span>
                            <?php else: ?>
                                <div class="small text-muted">-</div>
                            <?php endif; ?>
                        </th>
                    <?php endfor; endforeach; ?>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($estudiantes as $stu): ?>
                    <tr data-student="<?= $stu['id'] ?>">
                        <td class="text-start"><?= htmlspecialchars($stu['name']) ?></td>

                        <?php
                        foreach ($indicadores as $ind):
                            $lista = $criterios[$ind['id']] ?? [];
                            for ($i = 0; $i < max(3, count($lista)); $i++):
                                $c = $lista[$i] ?? null;
                                if ($c):
                                    $idC = (int)$c['id'];
                                    $puntosC = isset($c['puntos']) ? (int)$c['puntos'] : (isset($c['puntaje']) ? (int)$c['puntaje'] : 0);
                                    $notaExisting = $notas[$stu['id']][$idC]['nota'] ?? null;
                                    $qualExisting = $notas[$stu['id']][$idC]['cualitativa'] ?? '';
                                ?>
                                    <td data-puntos="<?= $puntosC ?>">
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
                                    <td></td>
                                <?php endif;
                            endfor;
                        endforeach;
                        ?>

                        <td class="total-num">0</td>
                        <td class="total-qual">--</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Contenedor de paginación -->
    <div id="pagination" class="d-flex justify-content-center mt-3"></div>

    <?php endif; ?>
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
            if (inputNota) inputNota.value = '';
            calcularTotales();
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
            const resp = await fetch('../controller/NotasController.php?action=save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: body.toString()
            });
            const json = await resp.json();
            if (resp.ok && json.ok) {
                if (inputNota) inputNota.value = json.nota;
                calcularTotales();
            } else {
                alert('Error guardando nota: ' + (json.error || 'error desconocido'));
            }
        } catch (err) {
            alert('Error de red al guardar nota: ' + err.message);
        }
    }

    document.querySelectorAll('.select-qual').forEach(s => s.addEventListener('change', onSelectChange));
    calcularTotales();

    // --- paginación y filtro por nombre ---
    const rowsPerPage = 10;
    const table = document.getElementById('tablaCalif');
    const tbody = table ? table.querySelector('tbody') : null;
    const pagination = document.getElementById('pagination');
    const filtroNombre = document.getElementById('filtroNombre');

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
</script>

<?php require_once "../view/footer.php"; ?>
