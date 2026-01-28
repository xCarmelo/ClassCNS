<?php require_once "../view/header.php"; ?>

<link rel="stylesheet" href="<?= $base ?>/public/cssB/bootstrap.min.css">
<link href="<?= $base ?>/public/cssB/bootstrap-icons-1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="<?= $base ?>/public/css/styles.css">
<link rel="icon" type="image/png" href="<?= $base ?>/public/assets/logo-pestaña.png">

<style>
  .pagination-container {
    overflow-x: auto;
    white-space: nowrap;
  }

  .pagination-container ul.pagination {
    display: inline-flex;
    flex-wrap: wrap;
  }
</style>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Listado de Asuntos</h2>
    <button id="btnExportarAsuntos" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Exportar Asuntos</button>
  </div>

  <div class="row mb-3">
    <div class="col-md-3">
      <label for="buscador" class="form-label">Buscar por estudiante:</label>
      <input type="text" id="buscador" class="form-control" placeholder="Nombre del estudiante">
    </div>
    <div class="col-md-3">
      <label for="filtroMateria" class="form-label">Filtrar por materia:</label>
      <select id="filtroMateria" class="form-select">
        <option value="">Todas</option>
        <?php foreach ($materias as $materia): ?>
          <option value="<?= htmlspecialchars($materia['name']) ?>"><?= htmlspecialchars($materia['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label for="filtroStatus" class="form-label">Filtrar por estado:</label>
      <select id="filtroStatus" class="form-select">
        <option value="">Todos</option>
        <option value="1">Activo</option>
        <option value="0">Inactivo</option>
      </select>
    </div>
    <div class="col-md-3">
      <label for="filtroCorte" class="form-label">Filtrar por corte:</label>
      <select id="filtroCorte" class="form-select">
        <option value="">Todos</option>
        <?php foreach ($cortes as $corte): ?>
          <option value="<?= htmlspecialchars($corte['id']) ?>"><?= htmlspecialchars($corte['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label for="filtroSeccionAsunto" class="form-label">Filtrar por sección:</label>
      <select id="filtroSeccionAsunto" class="form-select">
        <option value="">Todas</option>
        <?php foreach ($secciones as $seccion): ?>
          <option value="<?= strtolower($seccion['name']) ?>"><?= htmlspecialchars($seccion['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label for="filtroFechaInicio" class="form-label">Desde:</label>
      <input type="date" id="filtroFechaInicio" class="form-control">
    </div>
    <div class="col-md-3 mt-2">
      <label for="filtroFechaFin" class="form-label">Hasta:</label>
      <input type="date" id="filtroFechaFin" class="form-control">
    </div>
  </div>

  <div class="table-responsive">
    <div style="min-width: 700px">
      <table class="table table-hover align-middle table-bordered" id="tablaAsuntos">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Estudiante</th>
            <th>Materia</th>
            <th>Tema</th>
            <th>Nota</th>
            <th>Sección</th>
            <th>Status</th>
            <th>Fecha</th>
            <th>Corte</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($asuntos)): ?>
            <?php foreach ($asuntos as $index => $asunto): ?>
              <tr data-estudiante="<?= strtolower($asunto['student_name']) ?>"
                  data-materia="<?= strtolower($asunto['materia_name']) ?>"
                  data-status="<?= $asunto['statuss'] ?>"
                  data-fecha="<?= $asunto['fecha'] ?>"
                  data-seccion="<?= strtolower($asunto['seccion_name']) ?>"
                  data-corte="<?= isset($asunto['idCorte']) ? $asunto['idCorte'] : '' ?>">
                <td><?= isset($asunto['NumerodeLista']) ? (int)$asunto['NumerodeLista'] : '' ?></td>
                <td class="nombre-estudiante"><?= htmlspecialchars($asunto['student_name']) ?></td>
                <td><?= htmlspecialchars($asunto['materia_name']) ?></td>
                <td><?= htmlspecialchars($asunto['tema']) ?></td>
                <td><?= htmlspecialchars($asunto['nota']) ?></td>
                <td><?= htmlspecialchars($asunto['seccion_name']) ?></td>
                <td class="text-center">
                  <?php if ($asunto['statuss'] == 1): ?>
                    <i class="bi bi-check-circle-fill text-success"></i>
                  <?php else: ?>
                    <i class="bi bi-x-circle-fill text-danger"></i>
                  <?php endif; ?>
                </td>
                <td><?= date('d/m/Y H:i', strtotime($asunto['fecha'])) ?></td>
                <td><?= htmlspecialchars($asunto['corte_name']) ?></td>
                <td>
                  <button class="btn btn-sm btn-warning btn-editar" data-id="<?= $asunto['id'] ?>">
                    <i class="bi bi-pencil-square"></i>
                  </button>
                  <button type="button" class="btn btn-sm btn-danger btn-eliminar" data-id="<?= $asunto['id'] ?>">
                    <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="9" class="text-center">No hay asuntos registrados.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="pagination-container mt-3 text-center">
      <nav>
        <ul class="pagination justify-content-center" id="paginacionAsuntos"></ul>
      </nav>
    </div>
      <!-- Modal Confirmar Eliminación -->
      <div class="modal fade" id="modalConfirmEliminar" tabindex="-1" aria-labelledby="modalConfirmEliminarLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-danger text-white">
              <h5 class="modal-title" id="modalConfirmEliminarLabel">Eliminar asunto</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
              ¿Estás seguro de eliminar este asunto? Esta acción no se puede deshacer.
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="button" class="btn btn-danger" id="btnConfirmEliminar">Eliminar</button>
            </div>
          </div>
        </div>
  
      </div>

      <!-- Toast de notificaciones -->
      <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="appToast" class="toast align-items-center text-bg-primary border-0" role="status" aria-live="polite" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body" id="appToastBody">Notificación</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>
      </div>

  </div>
</div>




<!-- Modal Editar Asunto -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="/app/controller/editAsuntoController.php" class="modal-content">
      <input type="hidden" name="id" id="edit-id">
      <input type="hidden" name="idStudent" id="edit-idStudent">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarLabel">Editar Asunto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Estudiante: <?= htmlspecialchars($asunto['student_name'])?>. <strong>Cambiar por: </strong></label>
          <div class="d-flex align-items-center">
            <span id="selected-student" class="me-2"></span>
            <button type="button" class="btn btn-outline-primary btn-sm" id="btnCambiarEstudiante">Cambiar</button>
          </div>
        </div>
        <div class="mb-3">
          <label for="edit-idMateria" class="form-label">Materia:</label>
          <select class="form-select" id="edit-idMateria" name="idMateria" required>
            <option value="">Seleccione una materia</option>
            <?php foreach ($materias as $materia): ?>
              <option value="<?= $materia['id'] ?>"><?= htmlspecialchars($materia['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label for="edit-tema" class="form-label">Tema:</label>
          <textarea class="form-control" id="edit-tema" name="tema" rows="2" required></textarea>
        </div>
        <div class="mb-3">
          <label for="edit-nota" class="form-label">Nota:</label>
          <textarea class="form-control" id="edit-nota" name="nota" rows="4" required></textarea>
        </div>
        <div class="mb-3">
          <label for="edit-fecha" class="form-label">Fecha:</label>
          <input type="datetime-local" class="form-control" id="edit-fecha" name="fecha" required>
        </div>
        <div class="mb-3">
          <label for="edit-idCorte" class="form-label">Corte:</label>
          <select class="form-select" id="edit-idCorte" name="idCorte" required>
            <option value="">Seleccione un corte</option>
            <?php foreach ($cortes as $corte): ?>
              <option value="<?= $corte['id'] ?>"><?= htmlspecialchars($corte['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="1" name="statuss" id="edit-statuss">
          <label class="form-check-label" for="edit-statuss">Activo</label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Actualizar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Seleccionar Estudiante -->
<div class="modal fade" id="modalSeleccionarEstudiante" tabindex="-1" aria-labelledby="modalSeleccionarEstudianteLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalSeleccionarEstudianteLabel">Seleccionar Estudiante</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="filtroSeccion" class="form-label">Filtrar por sección:</label>
            <select id="filtroSeccion" class="form-select">
              <option value="">Todas</option>
              <?php foreach ($secciones as $seccion): ?>
                <option value="<?= $seccion['id'] ?>"><?= htmlspecialchars($seccion['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label for="buscadorEstudiante" class="form-label">Buscar por nombre:</label>
            <input type="text" id="buscadorEstudiante" class="form-control" placeholder="Nombre del estudiante">
          </div>
        </div>
        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
          <table class="table table-bordered table-hover">
            <thead class="table-light">
              <tr>
                <th>Nombre</th>
                <th>Sección</th>
                <th>Acción</th>
              </tr>
            </thead>
            <tbody id="listaEstudiantes">
              <?php foreach ($students as $student): ?>
                <tr data-seccion="<?= $student['idSeccion'] ?>" data-nombre="<?= strtolower($student['name']) ?>">
                  <td><?= htmlspecialchars($student['name']) ?></td>
                  <td><?= htmlspecialchars($student['seccion_name']) ?></td>
                  <td>
                    <button type="button" class="btn btn-sm btn-primary seleccionar-estudiante" data-id="<?= $student['id'] ?>" data-nombre="<?= htmlspecialchars($student['name']) ?>">Seleccionar</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>


<?php
if (isset($_SESSION['status'])):
    $modalStatus = $_SESSION['status'];
    $action = $_SESSION['action'];
    unset($_SESSION['status'], $_SESSION['action']);

    $mensajes = [
        'add' => ['Asunto agregado!', 'El asunto ha sido registrado correctamente.'],
        'delete' => ['Asunto eliminado!', 'El asunto ha sido eliminado correctamente.'],
        'edit' => ['Asunto actualizado!', 'Los datos del asunto se han actualizado.'],
        'error' => ['Error', 'Ocurrió un problema. Inténtalo de nuevo.']
    ];
    $titulo = $modalStatus === 'success' ? $mensajes[$action][0] : $mensajes['error'][0];
    $mensaje = $modalStatus === 'success' ? $mensajes[$action][1] : $mensajes['error'][1];
?>
<div class="modal fade" id="modalResultado" tabindex="-1" aria-labelledby="modalResultadoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-<?= $modalStatus === 'success' ? 'success' : 'danger' ?> text-white">
      <div class="modal-header">
        <h5 class="modal-title" id="modalResultadoLabel"><?= $titulo ?></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <?= $mensaje ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>



<script>
  // Utilidad: mostrar toast
  function showToast(message, variant = 'primary') {
    const toastEl = document.getElementById('appToast');
    const bodyEl = document.getElementById('appToastBody');
    bodyEl.textContent = message;
    // reset classes
    toastEl.className = 'toast align-items-center border-0 text-bg-' + variant;
    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
    toast.show();
  }

// Exportar asuntos filtrados a Excel (sin columna Status)
document.getElementById('btnExportarAsuntos').addEventListener('click', function() {
  const tabla = document.getElementById('tablaAsuntos');
  const filas = Array.from(tabla.querySelectorAll('tbody tr')).filter(tr => tr.dataset.visible === 'true');
  if (filas.length === 0) {
    showToast('No hay asuntos para exportar con los filtros actuales.', 'warning');
    return;
  }
  // Encabezados (excepto Status)
  const ths = Array.from(tabla.querySelectorAll('thead th'));
  const headers = ths.map((th, i) => ({text: th.innerText.trim(), idx: i}))
    .filter(h => h.text.toLowerCase() !== 'status');
  const headerNames = headers.map(h => h.text);
  // Datos
  const data = filas.map(tr => {
    const tds = Array.from(tr.children);
    return headers.map(h => tds[h.idx].innerText.trim());
  });
  // Crear hoja y libro
  const ws = XLSX.utils.aoa_to_sheet([headerNames, ...data]);
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, ws, 'Asuntos');
  XLSX.writeFile(wb, 'asuntos_filtrados.xlsx');
});
</script>

<?php require_once "../view/footer.php"; ?>

 <script>
  // Mostrar modal de resultado si existe
  const modalEl = document.getElementById('modalResultado');
  if (modalEl) {
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
  }

  // Modal Editar
  let modalEditar = new bootstrap.Modal(document.getElementById('modalEditar'));
  let modalEliminar = new bootstrap.Modal(document.getElementById('modalConfirmEliminar'));
  let idAsuntoAEliminar = null;

  document.querySelectorAll('.btn-editar').forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.getAttribute('data-id');
      fetch(`/app/controller/getByIdAsuntoController.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            const asunto = data.asunto;
            document.getElementById('edit-id').value = asunto.id;
            document.getElementById('edit-idMateria').value = asunto.idMateria;
            document.getElementById('edit-idStudent').value = asunto.idStudent;
            document.getElementById('selected-student').textContent = asunto.student_name;
            document.getElementById('edit-tema').value = asunto.tema;
            document.getElementById('edit-nota').value = asunto.nota;
            document.getElementById('edit-fecha').value = asunto.fecha.replace(" ", "T");
            document.getElementById('edit-statuss').checked = asunto.statuss == 1;
            document.getElementById('edit-idCorte').value = asunto.idCorte;
            modalEditar.show();
          } else {
            showToast('No se pudo obtener la información del asunto.', 'danger');
          }
        })
        .catch(() => showToast('Error al cargar los datos.', 'danger'));
    });
  });

  // Abrir modal confirmar eliminación
  document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', function () {
      idAsuntoAEliminar = this.getAttribute('data-id');
      modalEliminar.show();
    });
  });

  // Confirmar eliminación
  document.getElementById('btnConfirmEliminar').addEventListener('click', function() {
    if (idAsuntoAEliminar) {
      window.location.href = `/app/controller/deleteAsuntoController.php?id=${idAsuntoAEliminar}`;
    }
  });

  // Modal seleccionar estudiante
  document.getElementById('btnCambiarEstudiante').addEventListener('click', function () {
    const modalSelect = new bootstrap.Modal(document.getElementById('modalSeleccionarEstudiante'));
    modalSelect.show();
  });

  // Filtro dentro del modal seleccionar estudiante
  document.getElementById('buscadorEstudiante').addEventListener('input', function () {
    const nombre = this.value.toLowerCase();
    document.querySelectorAll('#listaEstudiantes tr').forEach(tr => {
      tr.style.display = tr.dataset.nombre.includes(nombre) ? '' : 'none';
    });
  });

  document.getElementById('filtroSeccion').addEventListener('change', function () {
    const idSeccion = this.value;
    document.querySelectorAll('#listaEstudiantes tr').forEach(tr => {
      tr.style.display = !idSeccion || tr.dataset.seccion === idSeccion ? '' : 'none';
    });
  });

  document.querySelectorAll('.seleccionar-estudiante').forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.dataset.id;
      const nombre = this.dataset.nombre;
      document.getElementById('edit-idStudent').value = id;
      document.getElementById('selected-student').textContent = nombre;
      bootstrap.Modal.getInstance(document.getElementById('modalSeleccionarEstudiante')).hide();
      modalEditar.show();
    });
  });

  // Filtro + paginación
  let currentPage = 1;
  const rowsPerPage = 10;

  function paginarTabla() {
    const rows = Array.from(document.querySelectorAll('#tablaAsuntos tbody tr'));
    const visibles = rows.filter(row => row.dataset.visible === "true");
    const totalPages = Math.ceil(visibles.length / rowsPerPage);

    rows.forEach(row => row.style.display = "none"); // ocultar todo

    visibles.forEach((row, i) => {
      row.style.display = (i >= (currentPage - 1) * rowsPerPage && i < currentPage * rowsPerPage) ? '' : 'none';
    });

    const paginacion = document.getElementById('paginacionAsuntos');
    paginacion.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
      const li = document.createElement('li');
      li.className = 'page-item' + (i === currentPage ? ' active' : '');
      li.innerHTML = `<button class="page-link">${i}</button>`;
      li.addEventListener('click', () => {
        currentPage = i;
        paginarTabla();
      });
      paginacion.appendChild(li);
    }
  }

  function filtrarAsuntos() {
    const nombre = document.getElementById('buscador').value.toLowerCase();
    const materia = document.getElementById('filtroMateria').value.toLowerCase();
    const status = document.getElementById('filtroStatus').value;
    const fechaInicio = document.getElementById('filtroFechaInicio').value;
    const fechaFin = document.getElementById('filtroFechaFin').value;
    const seccion = document.getElementById('filtroSeccionAsunto').value.toLowerCase();
    const corte = document.getElementById('filtroCorte').value;

    document.querySelectorAll('#tablaAsuntos tbody tr').forEach(tr => {
      const estudiante = tr.dataset.estudiante;
      const mat = tr.dataset.materia;
      const stat = tr.dataset.status;
      const fecha = tr.dataset.fecha;
      const secc = tr.dataset.seccion; 

      let visible = true;
      if (nombre && !estudiante.includes(nombre)) visible = false;
      if (materia && mat !== materia) visible = false;
      if (status !== '' && stat !== status) visible = false;
      if (seccion && secc !== seccion) visible = false;
      if (fechaInicio && new Date(fecha) < new Date(fechaInicio)) visible = false;
      if (fechaFin && new Date(fecha) > new Date(fechaFin)) visible = false;
      // Ajustar comparación para manejar valores vacíos y no numéricos
      if (corte && (!tr.dataset.corte || isNaN(tr.dataset.corte) || parseInt(tr.dataset.corte) !== parseInt(corte))) visible = false;

      tr.dataset.visible = visible ? "true" : "false";
    });

    currentPage = 1;
    paginarTabla();
  }

  // Asignar eventos a los filtros
  ['buscador', 'filtroMateria', 'filtroStatus', 'filtroFechaInicio', 'filtroFechaFin', 'filtroSeccionAsunto'].forEach(id => {
    document.getElementById(id).addEventListener('input', filtrarAsuntos);
  });

  // Inicializar al cargar
  window.addEventListener('DOMContentLoaded', () => {
    filtrarAsuntos();
  });


  // --- Persistencia de filtros con localStorage ---

const filtrosIds = [
  'buscador',
  'filtroMateria',
  'filtroStatus',
  'filtroFechaInicio',
  'filtroFechaFin',
  'filtroSeccionAsunto',
  'filtroCorte' // Añadido filtroCorte
];

// Guardar cambios en localStorage y aplicar filtros inmediatamente
filtrosIds.forEach(id => {
  const el = document.getElementById(id);
  el.addEventListener('change', () => {
    localStorage.setItem(id, el.value);
    filtrarAsuntos(); // Aplicar filtro inmediatamente
  });
  el.addEventListener('input', () => {
    localStorage.setItem(id, el.value);
    filtrarAsuntos(); // Aplicar filtro inmediatamente
  });
});

// Restaurar valores y aplicar filtros al cargar
window.addEventListener('DOMContentLoaded', () => {
  filtrosIds.forEach(id => {
    const valor = localStorage.getItem(id);
    if (valor !== null) {
      document.getElementById(id).value = valor;
    }
  });
  filtrarAsuntos(); // Aplicar los filtros restaurados
});

</script>

