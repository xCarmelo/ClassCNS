<?php require_once "../view/header.php"; ?>
<style>
.fila-estudiante {
    cursor: pointer;
    transition: background 0.2s;
}
.fila-estudiante.selected {
    background: #d1e7dd !important;
    color: #0a3622;
    font-weight: bold;
}
.tipo-asistencia-select {
    font-size: 1.3em;
    font-weight: bold;
}
.table-danger {
    background-color: #f8d7da !important;
}
</style>

<div class="container">
            <?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
            <?php if (!empty($_SESSION['flash'])): $flash = $_SESSION['flash']; unset($_SESSION['flash']);
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
            <?php endif; ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Nueva Asistencia (<?php echo $_GET['materia_nombre'] ?? '' ?>)</h2>
        <div class="d-flex align-items-center gap-2">
                <div class="input-group" style="min-width: 260px; max-width: 360px;">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="buscadorNombre" class="form-control" placeholder="Buscar estudiante...">
                    <button class="btn btn-outline-secondary" type="button" id="btnLimpiarBusqueda" title="Limpiar búsqueda">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            <div class="input-group" style="min-width: 260px; max-width: 360px;">
                <label class="input-group-text" for="tipoAsistenciaTodos" title="Selecciona para aplicar a todos">Tipo</label>
                <select id="tipoAsistenciaTodos" class="form-select">
                    <option value="">...</option>
                    <?php foreach ($tiposAsistencia as $tipoA): ?>
                        <option value="<?= htmlspecialchars($tipoA['id']) ?>"><?= htmlspecialchars($tipoA['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="btn btn-success" id="btnAplicarTodos" title="Aplicar a todos">
                    <i class="bi bi-check2-all"></i>
                </button>
            </div>
            <button type="button" class="btn btn-primary btn-lg" id="btnAplicarTipo" title="Aplicar al conjunto seleccionado (Ctrl-click)">
                <i class="bi bi-check2-square"></i> Aplicar tipo de asistencia
            </button>
        </div>
    </div>

    <?php
    // Recibir los filtros por GET
    $idSeccion = isset($_GET['seccion']) ? $_GET['seccion'] : '';
    $idCorte = isset($_GET['corte']) ? $_GET['corte'] : '';
    $idMateria = isset($_GET['materia']) ? intval($_GET['materia']) : '';
    $fecha = date('Y-m-d');
    $nombreMateria = $_GET['materia_nombre'] ?? 'null';
    // ID real de Informática (según tu BD)
    $idInformatica = "Informática";
    ?>

    <form method="post" id="formNuevaAsistencia" onsubmit="return confirmarGuardarNuevaAsistencia()">
        <input type="hidden" name="idSeccion" value="<?= htmlspecialchars($idSeccion) ?>">
        <input type="hidden" name="idCorte" value="<?= htmlspecialchars($idCorte) ?>">
        <input type="hidden" name="idMateria" value="<?= htmlspecialchars($idMateria) ?>">
        <input type="hidden" name="estudiantes_visibles" id="estudiantes_visibles">

        <div class="row mb-3">
            <div class="col">
                <label>Fecha:</label>
                <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col">
                <label>Nombre del tema:</label>
                <input type="text" name="nombreDelTema" class="form-control" required>
            </div>
        </div>

    <table class="table table-bordered">
            <thead>
                <tr>
            <th style="width:90px;">N° Lista</th> 
            <th>Estudiante</th>
                    <th>Tipo de Asistencia</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($estudiantes as $est) {
                    if (isset($est['status']) && (int)$est['status'] === 0) { continue; }
                    // Colorear si tiene fin = 1
                    $claseFila = (!empty($est['fin']) && intval($est['fin']) === 1) ? ' table-danger' : '';
                    echo '<tr class="fila-estudiante' . $claseFila . '" data-valido="1"> ';
            // N° Lista (si existe)
            $numLista = isset($est['NumerodeLista']) && $est['NumerodeLista'] !== '' ? (int)$est['NumerodeLista'] : '';
            echo '<td class="text-center">' . htmlspecialchars((string)$numLista) . '</td>';
                    echo '<td>' . htmlspecialchars($est['name']) . '</td>';
                    echo '<td>';
                    echo '<select name="tipo_asistencia[' . $est['id'] . ']" class="form-control tipo-asistencia-select">';
                    echo '<option value="">...</option>';
                    foreach ($tiposAsistencia as $tipoA) {
                        echo '<option value="' . htmlspecialchars($tipoA['id']) . '">' . htmlspecialchars($tipoA['name']) . '</option>';
                    }
                    echo '</select>';
                    echo '</td>';
                    echo '</tr>';

                    // 🚨 Si la materia es Informática (id=1) y tiene fin=1 → se corta la lista
                    if ($nombreMateria === $idInformatica && !empty($est['fin']) && intval($est['fin']) === 1) {
                        break;
                    }
                }
                ?>
            </tbody>
        </table>

        <button type="submit" class="btn btn-success mb-3">Guardar</button>

    <!-- Modal aplicar tipo asistencia masivo -->
        <div class="modal fade" id="modalTipoAsistencia" tabindex="-1" aria-labelledby="modalTipoLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTipoLabel">Aplicar tipo de asistencia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
              </div>
              <div class="modal-body">
                <select id="tipoAsistenciaMasivo" class="form-select form-select-lg mb-3">
                  <option value="">...</option>
                  <?php foreach ($tiposAsistencia as $tipoA): ?>
                    <option value="<?= htmlspecialchars($tipoA['id']) ?>"><?= htmlspecialchars($tipoA['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="modal-footer bg-light">
                <button type="button" class="btn btn-success" id="btnAplicarModal">
                  <i class="bi bi-check-circle"></i> Aplicar
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              </div>
            </div>
          </div>
        </div>
        
                <!-- Modal confirmar guardado -->
                <div class="modal fade" id="modalConfirmGuardar" tabindex="-1" aria-labelledby="modalConfirmGuardarLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title" id="modalConfirmGuardarLabel">Confirmar guardado</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                ¿Seguro que deseas guardar la asistencia?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-primary" id="btnConfirmarGuardar">Sí, guardar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Toast de notificaciones -->
                <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
                    <div id="toastAsistencia" class="toast align-items-center text-bg-primary border-0" role="status" aria-live="polite" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body" id="toastAsistenciaBody">Notificación</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
    </form>
</div>

<script>
/* ==========================
   TOAST
========================== */
function showToastAsistencia(message, variant = 'primary') {
    const toastEl = document.getElementById('toastAsistencia');
    const bodyEl = document.getElementById('toastAsistenciaBody');
    bodyEl.textContent = message;
    toastEl.className = 'toast align-items-center border-0 text-bg-' + variant;
    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
    toast.show();
}

/* ==========================
   CONTROL SUBMIT
========================== */
let modalConfirmGuardar;
let permitirSubmit = false;

/* ==========================
   CONFIRMAR GUARDADO
========================== */
function confirmarGuardarNuevaAsistencia() {

    const filas = Array.from(document.querySelectorAll('tr.fila-estudiante'))
        .filter(row => row.style.display !== 'none');

    if (filas.length === 0) {
        showToastAsistencia('No hay estudiantes visibles para guardar.', 'warning');
        return false;
    }

    let idsVisibles = [];
    for (let row of filas) {
        const select = row.querySelector('.tipo-asistencia-select');
        if (!select || !select.value) {
            showToastAsistencia(
                'Debes seleccionar el tipo de asistencia solo para los estudiantes visibles.',
                'warning'
            );
            select?.focus();
            return false;
        }

        const match = select.name.match(/\[(\d+)\]/);
        if (match) {
            idsVisibles.push(match[1]);
        }
    }

    // Guardar SOLO los visibles
    document.getElementById('estudiantes_visibles').value = idsVisibles.join(',');

    // Confirmación final
    modalConfirmGuardar.show();
    return false;
}

// Confirmar envío
document.getElementById('btnConfirmarGuardar').addEventListener('click', function () {
    modalConfirmGuardar.hide();
    document.getElementById('formNuevaAsistencia').submit();
});





/* ==========================
   DOM READY
========================== */
document.addEventListener('DOMContentLoaded', function () {

    modalConfirmGuardar = new bootstrap.Modal(
        document.getElementById('modalConfirmGuardar')
    );

    /* ==========================
       FECHA HOY
    ========================== */
    const fechaInput = document.querySelector('input[name="fecha"]');
    if (fechaInput) {
        const hoy = new Date().toISOString().split('T')[0];
        fechaInput.value = hoy;
    }

    /* ==========================
       SELECCIÓN TIPO EXCEL
    ========================== */
    document.querySelectorAll('.fila-estudiante').forEach(row => {
        row.addEventListener('click', function (e) {
            if (e.ctrlKey) {
                row.classList.toggle('selected');
            } else {
                document.querySelectorAll('.fila-estudiante')
                    .forEach(r => r.classList.remove('selected'));
                row.classList.add('selected');
            }
        });
    });

    /* ==========================
       MODAL APLICAR TIPO
    ========================== */
    document.getElementById('btnAplicarTipo').addEventListener('click', () => {
        new bootstrap.Modal(
            document.getElementById('modalTipoAsistencia')
        ).show();
    });

    /* ==========================
       APLICAR A TODOS
    ========================== */
    document.getElementById('btnAplicarTodos').addEventListener('click', () => {
        const tipo = document.getElementById('tipoAsistenciaTodos').value;
        if (!tipo) {
            showToastAsistencia('Selecciona un tipo para aplicar a todos.', 'warning');
            return;
        }

        let count = 0;
        document.querySelectorAll(
            'tr.fila-estudiante[data-valido="1"] .tipo-asistencia-select'
        ).forEach(sel => {
            sel.value = tipo;
            count++;
        });

        showToastAsistencia(
            'Tipo aplicado a ' + count + ' estudiante(s).',
            'success'
        );
    });

    /* ==========================
       APLICAR A SELECCIÓN
    ========================== */
    document.getElementById('btnAplicarModal').addEventListener('click', () => {
        const tipo = document.getElementById('tipoAsistenciaMasivo').value;
        if (!tipo) {
            showToastAsistencia('Selecciona un tipo de asistencia.', 'warning');
            return;
        }

        document.querySelectorAll(
            '.fila-estudiante.selected[data-valido="1"]'
        ).forEach(row => {
            const sel = row.querySelector('.tipo-asistencia-select');
            if (sel) sel.value = tipo;
        });

        bootstrap.Modal.getInstance(
            document.getElementById('modalTipoAsistencia')
        ).hide();

        showToastAsistencia('Tipo aplicado a la selección.', 'success');
    });

    /* ==========================
       CONFIRMAR GUARDADO
    ========================== */
    document.getElementById('btnConfirmarGuardar').addEventListener('click', () => {
        permitirSubmit = true;
        modalConfirmGuardar.hide();
        document.getElementById('formNuevaAsistencia').submit();
    });

    /* ==========================
       BUSCADOR
    ========================== */
    const inputBuscar = document.getElementById('buscadorNombre');
    const btnLimpiar = document.getElementById('btnLimpiarBusqueda');

    function filtrar() {
        const q = inputBuscar.value.toLowerCase();
        document.querySelectorAll('tr.fila-estudiante').forEach(row => {
            const nombre = row.children[1].textContent.toLowerCase();
            row.style.display = nombre.includes(q) ? '' : 'none';
        });
    }

    let debounce;
    inputBuscar.addEventListener('input', () => {
        clearTimeout(debounce);
        debounce = setTimeout(filtrar, 150);
    });

    btnLimpiar.addEventListener('click', () => {
        inputBuscar.value = '';
        filtrar();
        inputBuscar.focus();
    });
});
</script>


<?php require_once "../view/footer.php"; ?>
