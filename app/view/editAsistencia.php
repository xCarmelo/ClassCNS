<?php require_once "../view/header.php"; ?>
<div class="container">
  <?php if (!empty($_GET['flashType']) && isset($_GET['flashMsg'])):
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
  <?php endif; ?>
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Editar Asistencia</h2>
    <div class="d-flex align-items-center gap-2">
      <div class="input-group" style="min-width: 260px; max-width: 360px;">
        <span class="input-group-text"><i class="bi bi-search"></i></span>
        <input type="text" id="buscadorNombre" class="form-control" placeholder="Buscar estudiante...">
        <button class="btn btn-outline-secondary" type="button" id="btnLimpiarBusqueda" title="Limpiar búsqueda">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
      <a class="btn btn-secondary" href="asistenciaController.php?seccion=<?= urlencode($_GET['seccion'] ?? '') ?>&corte=<?= urlencode($_GET['corte'] ?? '') ?>&materia=<?= urlencode($_GET['materia'] ?? '') ?>">Volver</a>
    </div>
  </div>

  <?php if (!$sesion): ?>
    <div class="alert alert-danger">Sesión no encontrada.</div>
  <?php else: ?>
    <form method="post" class="card p-3">
      <div class="row g-2 align-items-end mb-3">
        <div class="col-md-3">
          <label class="form-label">Fecha</label>
          <input type="date" name="Fecha" class="form-control" value="<?= htmlspecialchars(substr($sesion['Fecha'],0,10)) ?>" required>
        </div>
        <div class="col-md-9">
          <label class="form-label">Tema</label>
          <input type="text" name="nombreDelTema" class="form-control" value="<?= htmlspecialchars($sesion['nombreDelTema']) ?>" required>
        </div>
      </div>

      <table class="table table-bordered">
        <thead>
          <tr>
            <th>#</th>
            <th>Estudiante</th>
            <th>Tipo de Asistencia</th>
          </tr>
        </thead>
        <tbody>
        <?php $i=1; foreach ($filas as $f): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($f['estudiante']) ?></td>
            <td>
              <select name="asistencia[<?= intval($f['id']) ?>]" class="form-select">
                <?php foreach ($tiposAsistencia as $ta): ?>
                  <option value="<?= intval($ta['id']) ?>" <?= (intval($ta['id']) === intval($f['idTipoAsistencia'])) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($ta['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary btn-lg">Guardar cambios</button>
      </div>
    </form>
  <?php endif; ?>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function(){
    const inputBuscar = document.getElementById('buscadorNombre');
    const btnLimpiar = document.getElementById('btnLimpiarBusqueda');

    function filtrarPorNombre(){
      const q = (inputBuscar?.value || '').trim().toLowerCase();
      const filas = document.querySelectorAll('table.table tbody tr');
      filas.forEach(function(row){
        const celdas = row.getElementsByTagName('td');
        const nombre = celdas && celdas[1] ? (celdas[1].textContent || '').toLowerCase() : '';
        if (!q) {
          row.style.display = '';
        } else {
          row.style.display = nombre.includes(q) ? '' : 'none';
        }
      });
    }

    let debounce;
    if (inputBuscar) {
      inputBuscar.addEventListener('input', function(){
        clearTimeout(debounce);
        debounce = setTimeout(filtrarPorNombre, 150);
      });
    }
    if (btnLimpiar) {
      btnLimpiar.addEventListener('click', function(){
        if (inputBuscar) { inputBuscar.value = ''; }
        filtrarPorNombre();
        inputBuscar?.focus();
      });
    }
  });
</script>
<?php require_once "../view/footer.php"; ?>
