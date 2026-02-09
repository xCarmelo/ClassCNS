<?php require_once dirname(__DIR__) . '/model/student.php'; ?>
<?php require_once "../view/header.php"; ?>
<div class="container">
    <h2>Asistencia</h2>
        <?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
            <?php
            if (!empty($_GET['flashType']) && isset($_GET['flashMsg'])):
                    $type = preg_replace('/[^a-z]/', '', $_GET['flashType']);
                    if ($type === '') { $type = 'primary'; }
                    $msg = $_GET['flashMsg'];
                    $title = ($type === 'success') ? 'Operación exitosa' : (($type === 'warning') ? 'Atención' : (($type === 'danger') ? 'Error' : 'Información'));
                    $headerClass = ($type === 'success') ? 'bg-success text-white' : (($type === 'warning') ? 'bg-warning' : (($type === 'danger') ? 'bg-danger text-white' : 'bg-primary text-white'));
            ?>
                <div class="modal fade" id="modalFlash" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header <?= $headerClass ?>">
                                <h5 class="modal-title"><?= htmlspecialchars($title) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <?= htmlspecialchars($msg) ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function(){
                        var m = document.getElementById('modalFlash');
                        if (m) { new bootstrap.Modal(m).show(); }
                    });
                </script>
            <?php
            elseif (!empty($_SESSION['flash'])): $flash = $_SESSION['flash']; unset($_SESSION['flash']);
                $type = $flash['type'] ?? 'primary';
                $title = ($type === 'success') ? 'Operación exitosa' : (($type === 'warning') ? 'Atención' : (($type === 'danger') ? 'Error' : 'Información'));
                $headerClass = ($type === 'success') ? 'bg-success text-white' : (($type === 'warning') ? 'bg-warning' : (($type === 'danger') ? 'bg-danger text-white' : 'bg-primary text-white'));
        ?>
            <div class="modal fade" id="modalFlash" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header <?= $headerClass ?>">
                            <h5 class="modal-title"><?= htmlspecialchars($title) ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <?= htmlspecialchars($flash['message']) ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function(){
                    var m = document.getElementById('modalFlash');
                    if (m) { new bootstrap.Modal(m).show(); }
                });
            </script>
                <?php elseif (!empty($_SESSION['status'])):
                        $status = $_SESSION['status'];
                        $action = $_SESSION['action'] ?? '';
                        unset($_SESSION['status'], $_SESSION['action']);
                        $type = ($status === 'success') ? 'success' : 'danger';
                        $title = ($status === 'success') ? 'Operación exitosa' : 'Error';
                        $headerClass = ($type === 'success') ? 'bg-success text-white' : 'bg-danger text-white';
                        $msgMap = [
                                'update_tipo' => 'Tipo de asistencia actualizado.',
                                'delete_sesion' => 'Sesión eliminada correctamente.',
                                'error' => 'Ocurrió un error durante la operación.'
                        ];
                        $message = $msgMap[$action] ?? (($status === 'success') ? 'Operación realizada correctamente.' : 'Ocurrió un error.');
                ?>
                    <div class="modal fade" id="modalFlash" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header <?= $headerClass ?>">
                                    <h5 class="modal-title"><?= htmlspecialchars($title) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <?= htmlspecialchars($message) ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function(){
                            var m = document.getElementById('modalFlash');
                            if (m) { new bootstrap.Modal(m).show(); }
                        });
                    </script>
                <?php endif; ?>
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
                        <option 
                            value="<?= $mat['id'] ?>"
                            data-nombre="<?= htmlspecialchars($mat['name']) ?>"
                            <?= (isset($_GET['materia']) && $_GET['materia'] == $mat['id']) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($mat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <input type="hidden" name="materia_nombre" id="materiaNombre">

            </div>
        </div>
    </form>

    <!-- Botón Nueva Asistencia (deshabilitado si faltan filtros) -->
<button 
    id="btnNuevaAsistencia"
    class="btn btn-success mb-3"
    <?= !$filtrosCompletos ? 'disabled' : '' ?>>
    Nueva Asistencia
</button>



        <div class="d-flex justify-content-between align-items-center mt-3">
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
        <nav>
            <ul class="pagination justify-content-center" id="paginacionAsistencia"></ul>
        </nav>
    </div>

    <?php
        // Agrupar asistencias por fecha y tema (cada combinación es una columna)
        $columnas = [];
        if ($filtrosCompletos && !empty($asistencias)) {
            foreach ($asistencias as $a) {
                $idSesion = isset($a['idSesion']) ? (int)$a['idSesion'] : 0;
                $key = $idSesion . '|' . $a['Fecha'] . '|' . $a['nombreDelTema'];
                if (!isset($columnas[$key])) {
                    $columnas[$key] = [
                        'idSesion' => $idSesion,
                        'fecha' => $a['Fecha'],
                        'tema' => $a['nombreDelTema']
                    ];
                }
            }

            // Ordenar columnas por fecha
            usort($columnas, function ($a, $b) {
                return strtotime($a['fecha']) - strtotime($b['fecha']);
            });
        }
?>
<table class="table table-bordered" id="tablaAsistencia">
    <thead>
        <tr>
            <th>#</th>
            <th>Nombre y Apellidos</th>
            <?php foreach ($columnas as $col): ?>
                <th class="th-sesion position-relative" data-id-sesion="<?= htmlspecialchars($col['idSesion'] ?? '') ?>" style="writing-mode: vertical-rl; text-align: center; vertical-align: bottom; white-space: nowrap; font-size:1em; font-weight:bold;">
                    <div class="th-content">
                        <?= htmlspecialchars($col['fecha']) ?><br>
                        <span style="font-size:0.9em;font-style:italic;">(<?= htmlspecialchars($col['tema']) ?>)</span>
                    </div>
                    <div class="th-overlay d-flex align-items-center justify-content-center">
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="editAsistenciaController.php?idSesion=<?= urlencode($col['idSesion'] ?? '') ?>&seccion=<?= urlencode($_GET['seccion'] ?? '') ?>&corte=<?= urlencode($_GET['corte'] ?? '') ?>&materia=<?= urlencode($_GET['materia'] ?? '') ?>" class="btn btn-warning" title="Editar sesión">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button type="button" class="btn btn-danger btn-eliminar-sesion" title="Eliminar sesión">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </div>
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
            if ((int)($est['status'] ?? 1) === 1) {
                $estudiantes[$est['id']] = $est; // activos
            }
        }

        // 🚨 Guardamos el id de la materia seleccionada
        $materiaSeleccionada = isset($_GET['materia']) ? intval($_GET['materia']) : null;

        // ✅ id real de Informática en tu BD
        $idInformatica = $materiaBD ? intval($materiaBD['id']) : null;

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
                            if ($a['idStudent'] == $idStudent && isset($col['idSesion']) && isset($a['idSesion']) && (int)$a['idSesion'] === (int)$col['idSesion']) {
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
            // 🚨 Si la materia seleccionada es Informática (id=2) y el estudiante tiene fin=1, se corta la lista
            if ($materiaSeleccionada === $idInformatica && isset($est['fin']) && $est['fin'] == 1) { 
                break; // rompe el foreach de estudiantes
            }
        endforeach;
        ?>
    </tbody>
</table>





<?php require_once "../view/footer.php"; ?>

<style>
.fin-resaltado {
    background-color: #ffcccc !important;
    color: #a94442 !important;
}
/* Overlay para eliminar sesión en encabezado */
.th-sesion { position: relative; }
.th-sesion .th-content { opacity: 1; transition: opacity .15s ease-in-out; }
/* Oculto por defecto: sin color ni botón, no intercepta eventos */
.th-sesion .th-overlay {
    position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
    background: rgba(220,53,69,.15); opacity: 0; pointer-events: none; transition: opacity .15s ease-in-out;
}
/* Solo al hover/focus se muestra color e icono */
.th-sesion:hover .th-overlay, .th-sesion:focus-within .th-overlay { opacity: 1; pointer-events: auto; }
.th-sesion:hover .th-content, .th-sesion:focus-within .th-content { opacity: .2; }
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
const storageRowsKey = keyPrefix + 'rowsPerPage';
let rowsPerPage = parseInt(localStorage.getItem(storageRowsKey) || '10', 10) || 10;
let currentPage = 1;

// Inicializar selector de filas
const selectRowsPerPage = document.getElementById('selectRowsPerPage');
if (selectRowsPerPage) {
    selectRowsPerPage.value = String(rowsPerPage);
    selectRowsPerPage.addEventListener('change', function(){
        rowsPerPage = parseInt(this.value, 10) || 10;
        localStorage.setItem(storageRowsKey, String(rowsPerPage));
        currentPage = 1;
        aplicarPaginacion();
    });
}

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

// Eliminar sesión de asistencia (modal de confirmación)
document.addEventListener('click', function(e){
    const btn = e.target.closest('.btn-eliminar-sesion');
    if (!btn) return;
    const th = btn.closest('.th-sesion');
    const idSesion = th?.dataset.idSesion;
    if (!idSesion) return;
    // Crear modal si no existe
    let modal = document.getElementById('modalEliminarSesion');
    if (!modal) {
        const html = `
        <div class="modal fade" id="modalEliminarSesion" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Eliminar sesión de asistencia</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ¿Seguro que deseas eliminar esta sesión de asistencia? Esta acción no se puede deshacer.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <form id="formEliminarSesion" method="POST" action="asistenciaController.php" class="d-inline">
                            <input type="hidden" name="id_sesion" id="inputIdSesion">
                            <input type="hidden" name="eliminar_sesion" value="1">
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', html);
        modal = document.getElementById('modalEliminarSesion');
    }
    document.getElementById('inputIdSesion').value = idSesion;
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
});




//para enviar el nombre de la materia
function actualizarNombreMateria() {
    const selectedOption = materiaEl.options[materiaEl.selectedIndex];
    document.getElementById('materiaNombre').value =
        selectedOption?.dataset?.nombre || '';
}


materiaEl.addEventListener('change', function () {
    actualizarNombreMateria();
});


window.addEventListener('DOMContentLoaded', () => {
    actualizarNombreMateria();
});

btnNueva.addEventListener('click', function () {
    const seccion = seccionEl.value;
    const corte = corteEl.value;
    const materia = materiaEl.value;

    const selectedOption = materiaEl.options[materiaEl.selectedIndex];
    const materiaNombre = selectedOption?.dataset?.nombre || '';

    const url =
        'nuevaAsistenciaController.php' +
        '?seccion=' + encodeURIComponent(seccion) +
        '&corte=' + encodeURIComponent(corte) +
        '&materia=' + encodeURIComponent(materia) +
        '&materia_nombre=' + encodeURIComponent(materiaNombre);

    window.location.href = url;
});



</script>
