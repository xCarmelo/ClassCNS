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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Nueva Asistencia</h2>
        <button type="button" class="btn btn-primary btn-lg" id="btnAplicarTipo">
            <i class="bi bi-check2-square"></i> Aplicar tipo de asistencia
        </button>
    </div>

    <?php
    // Recibir los filtros por GET
    $idSeccion = isset($_GET['seccion']) ? $_GET['seccion'] : '';
    $idCorte = isset($_GET['corte']) ? $_GET['corte'] : '';
    $idMateria = isset($_GET['materia']) ? intval($_GET['materia']) : '';
    $fecha = date('Y-m-d');

    // ID real de Informática (según tu BD)
    $idInformatica = 2;
    ?>

    <form method="post" id="formNuevaAsistencia" onsubmit="return confirmarGuardarNuevaAsistencia()">
        <input type="hidden" name="idSeccion" value="<?= htmlspecialchars($idSeccion) ?>">
        <input type="hidden" name="idCorte" value="<?= htmlspecialchars($idCorte) ?>">
        <input type="hidden" name="idMateria" value="<?= htmlspecialchars($idMateria) ?>">

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
                    <th>Estudiante</th>
                    <th>Tipo de Asistencia</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($estudiantes as $est) {
                    // Colorear si tiene fin = 1
                    $claseFila = (!empty($est['fin']) && intval($est['fin']) === 1) ? ' table-danger' : '';
                    echo '<tr class="fila-estudiante' . $claseFila . '">';
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

                    // 🚨 Si la materia es Informática (id=2) y tiene fin=1 → se corta la lista
                    if ($idMateria === $idInformatica && !empty($est['fin']) && intval($est['fin']) === 1) {
                        break;
                    }
                }
                ?>
            </tbody>
        </table>

        <button type="submit" class="btn btn-success mb-3">Guardar</button>

        <!-- Modal Bootstrap -->
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
    </form>
</div>

<script>
function confirmarGuardarNuevaAsistencia() {
    var fecha = document.querySelector('input[name="fecha"]');
    var tema = document.querySelector('input[name="nombreDelTema"]');
    if (!fecha.value) {
        alert('Debes seleccionar una fecha.');
        fecha.focus();
        return false;
    }
    if (!tema.value.trim()) {
        alert('Debes ingresar el nombre del tema.');
        tema.focus();
        return false;
    }
    var selects = document.querySelectorAll('.tipo-asistencia-select');
    for (var i = 0; i < selects.length; i++) {
        if (!selects[i].value) {
            alert('Debes seleccionar el tipo de asistencia para todos los estudiantes mostrados.');
            selects[i].focus();
            return false;
        }
    }
    return confirm('¿Seguro que deseas guardar la asistencia?');
}

document.addEventListener('DOMContentLoaded', function() {
    var fechaInput = document.querySelector('input[name="fecha"]');
    if (fechaInput) {
        var now = new Date();
        var yyyy = now.getFullYear();
        var mm = String(now.getMonth() + 1).padStart(2, '0');
        var dd = String(now.getDate()).padStart(2, '0');
        var hoy = yyyy + '-' + mm + '-' + dd;
        fechaInput.value = hoy;
    }

    // Selección múltiple tipo Excel con Ctrl
    document.querySelectorAll('.fila-estudiante').forEach(function(row) {
        row.addEventListener('click', function(e) {
            if (e.ctrlKey) {
                row.classList.toggle('selected');
            } else {
                document.querySelectorAll('.fila-estudiante').forEach(r => r.classList.remove('selected'));
                row.classList.add('selected');
            }
        });
    });

    // Abrir modal Bootstrap 5
    const btnAplicarTipo = document.getElementById('btnAplicarTipo');
    btnAplicarTipo.addEventListener('click', function() {
        var modal = new bootstrap.Modal(document.getElementById('modalTipoAsistencia'));
        modal.show();
    });

    // Aplicar tipo de asistencia masivo
    const btnAplicarModal = document.getElementById('btnAplicarModal');
    btnAplicarModal.addEventListener('click', function() {
        const tipo = document.getElementById('tipoAsistenciaMasivo').value;
        if (!tipo) {
            alert('Selecciona un tipo de asistencia.');
            return;
        }
        document.querySelectorAll('.fila-estudiante.selected').forEach(function(row) {
            const select = row.querySelector('.tipo-asistencia-select');
            if (select) select.value = tipo;
        });
        var modal = bootstrap.Modal.getInstance(document.getElementById('modalTipoAsistencia'));
        modal.hide();
    });
});
</script>

<?php require_once "../view/footer.php"; ?>
