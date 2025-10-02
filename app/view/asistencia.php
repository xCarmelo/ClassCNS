<?php require_once dirname(__DIR__) . '/model/student.php'; ?>
<?php require_once "../view/header.php"; ?>
<div class="container">
    <h2>Asistencia</h2>
    <?php
    // Definir variable de filtros completos antes de usarla
    $filtrosCompletos = isset($_GET['seccion']) && $_GET['seccion'] !== '' &&
                       isset($_GET['corte']) && $_GET['corte'] !== '' &&
                       isset($_GET['materia']) && $_GET['materia'] !== '';
    ?>
    <form method="get" class="mb-3" id="filtroForm">
        <div class="row">
            <div class="col">
                <label>Sección:</label>
                <select name="seccion" id="filtroSeccion" class="form-control filtro-auto" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($secciones as $sec): ?>
                        <option value="<?= $sec['id'] ?>" <?= (isset($_GET['seccion']) && $_GET['seccion'] == $sec['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sec['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col">
                <label>Corte:</label>
                <select name="corte" id="filtroCorte" class="form-control filtro-auto" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($cortes as $corte): ?>
                        <option value="<?= $corte['id'] ?>" <?= (isset($_GET['corte']) && $_GET['corte'] == $corte['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($corte['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col">
                <label>Materia:</label>
                <select name="materia" id="filtroMateria" class="form-control filtro-auto" required>
                    <option value="">Seleccione</option>
                    <?php foreach ($materias as $mat): ?>
                        <option value="<?= $mat['id'] ?>" <?= (isset($_GET['materia']) && $_GET['materia'] == $mat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($mat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>

    <!-- Botón Nueva Asistencia (deshabilitado si faltan filtros) -->
    <button 
        id="btnNuevaAsistencia"
        class="btn btn-success mb-3"
        onclick="location.href='nuevaAsistenciaController.php?seccion=<?= urlencode($_GET['seccion'] ?? '') ?>&corte=<?= urlencode($_GET['corte'] ?? '') ?>&materia=<?= urlencode($_GET['materia'] ?? '') ?>'"
        <?= !$filtrosCompletos ? 'disabled' : '' ?>>
        Nueva Asistencia
    </button>

    <?php
    // Agrupar asistencias por fecha y tema (cada combinación es una columna)
    $columnas = [];
    if ($filtrosCompletos && !empty($asistencias)) {
        foreach ($asistencias as $a) {
            $key = $a['Fecha'] . '|' . $a['nombreDelTema'];
            $columnas[$key] = [
                'fecha' => $a['Fecha'],
                'tema' => $a['nombreDelTema']
            ];
        }
    }
    ?>
    <table class="table table-bordered" id="tablaAsistencia">
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre y Apellidos</th>
                <?php foreach ($columnas as $col): ?>
                    <th style="writing-mode: vertical-rl; text-align: center; vertical-align: bottom; white-space: nowrap; font-size:1em; font-weight:bold;">
                        <?= htmlspecialchars($col['fecha']) ?><br>
                        <span style="font-size:0.9em;font-style:italic;">(<?= htmlspecialchars($col['tema']) ?>)</span>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $estudiantes = [];
            $studentModel = new Student();
            $estudiantesDB = isset($_GET['seccion']) && $_GET['seccion'] !== '' ? $studentModel->getBySeccion($_GET['seccion']) : [];
            foreach ($estudiantesDB as $est) {
                $estudiantes[$est['id']] = $est;
            }

            // Detectar nombre de la sección seleccionada
            $nombreSeccionSeleccionada = '';
            foreach ($secciones as $sec) {
                if (isset($_GET['seccion']) && $_GET['seccion'] == $sec['id']) {
                    $nombreSeccionSeleccionada = $sec['name'];
                    break;
                }
            }

            foreach ($estudiantes as $idStudent => $est):
                ?>
                <tr class="<?= (isset($est['fin']) && $est['fin'] == 1) ? 'table-danger' : '' ?>">
                    <td><?= htmlspecialchars($est['NumerodeLista']) ?></td>
                    <td><?= htmlspecialchars($est['name']) ?></td>
                    <?php foreach ($columnas as $key => $col): ?>
                        <td>
                            <?php
                            $tipo = '';
                            foreach ($asistencias as $a) {
                                if ($a['idStudent'] == $idStudent && $a['Fecha'] == $col['fecha'] && $a['nombreDelTema'] == $col['tema']) {
                                    $tipo = isset($a['tipo_asistencia']) ? $a['tipo_asistencia'] : $a['idTipoAsistencia'];
                                    break;
                                }
                            }
                            echo '<span style="font-size:1.2em;font-weight:bold;">' . htmlspecialchars($tipo) . '</span>';
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <?php
                // 🚨 Si es Informática y fin=1, cortamos el bucle
                if ($nombreSeccionSeleccionada === "Informatica" && isset($est['fin']) && $est['fin'] == 1) {
                    break;
                }
            endforeach;
            ?>
        </tbody>
    </table>
    <nav>
        <ul class="pagination justify-content-center" id="paginacionAsistencia"></ul>
    </nav>
</div>

<?php require_once "../view/footer.php"; ?>

<style>
.fin-resaltado {
    background-color: #ffcccc !important;
    color: #a94442 !important;
}
</style>

<script>
// Guardar y restaurar filtros con localStorage
const keyPrefix = 'asistencia_';
const seccionEl = document.getElementById('filtroSeccion');
const corteEl = document.getElementById('filtroCorte');
const materiaEl = document.getElementById('filtroMateria');
const btnNueva = document.getElementById('btnNuevaAsistencia');

function saveFiltersToStorage() {
    localStorage.setItem(keyPrefix + 'seccion', seccionEl.value || '');
    localStorage.setItem(keyPrefix + 'corte', corteEl.value || '');
    localStorage.setItem(keyPrefix + 'materia', materiaEl.value || '');
}

function restoreFiltersFromStorage() {
    const s = localStorage.getItem(keyPrefix + 'seccion') || '';
    const c = localStorage.getItem(keyPrefix + 'corte') || '';
    const m = localStorage.getItem(keyPrefix + 'materia') || '';
    let shouldSubmit = false;
    if (s && seccionEl && !seccionEl.value) { seccionEl.value = s; shouldSubmit = true; }
    if (c && corteEl && !corteEl.value) { corteEl.value = c; shouldSubmit = true; }
    if (m && materiaEl && !materiaEl.value) { materiaEl.value = m; shouldSubmit = true; }
    if (shouldSubmit) document.getElementById('filtroForm').submit();
}

// Validar botón según filtros
function validarBotonNuevaAsistencia() {
    if (seccionEl.value && corteEl.value && materiaEl.value) {
        btnNueva.disabled = false;
    } else {
        btnNueva.disabled = true;
    }
}

window.addEventListener('DOMContentLoaded', () => {
    restoreFiltersFromStorage();
    validarBotonNuevaAsistencia();
});

// Envío automático del formulario al cambiar cualquier filtro y guardar
const filtros = document.querySelectorAll('.filtro-auto');
filtros.forEach(filtro => {
    filtro.addEventListener('change', function() {
        saveFiltersToStorage();
        validarBotonNuevaAsistencia();
        document.getElementById('filtroForm').submit();
    });
});

// Paginación
const rowsPerPage = 10;
let currentPage = 1;

function aplicarPaginacion() {
    const tabla = document.getElementById('tablaAsistencia');
    const tbody = tabla.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const paginacion = document.getElementById('paginacionAsistencia');
    const totalPages = Math.ceil(rows.length / rowsPerPage);

    rows.forEach(row => row.style.display = 'none');
    rows.forEach((row, i) => {
        if (i >= (currentPage - 1) * rowsPerPage && i < currentPage * rowsPerPage) {
            row.style.display = '';
        }
    });

    paginacion.innerHTML = '';
    if (totalPages > 1) {
        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = 'page-item' + (i === currentPage ? ' active' : '');
            const btn = document.createElement('button');
            btn.className = 'page-link';
            btn.textContent = i;
            btn.addEventListener('click', function() {
                currentPage = i;
                aplicarPaginacion();
            });
            li.appendChild(btn);
            paginacion.appendChild(li);
        }
    }
}

window.addEventListener('DOMContentLoaded', aplicarPaginacion);
</script>
