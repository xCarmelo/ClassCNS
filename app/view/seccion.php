<?php require_once "../view/header.php"; ?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Listado de Secciones</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">
      + Nueva Sección
    </button>
  </div>

  <div class="row mb-3">
    <div class="col-md-4">
      <label for="buscador" class="form-label">Buscar por nombre:</label>
      <input type="text" id="buscador" class="form-control" placeholder="Nombre de la sección">
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle table-bordered" id="tablaSecciones">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Nombre</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($secciones)): ?>
          <?php foreach ($secciones as $index => $seccion): ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td class="nombre"><?= htmlspecialchars($seccion['name']) ?></td>
              <td>
                <button class="btn btn-sm btn-warning btn-editar" data-id="<?= $seccion['id'] ?>">
                  <i class="bi bi-pencil-square"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger btn-eliminar" data-id="<?= $seccion['id'] ?>">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="3" class="text-center">No hay secciones registradas.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div id="pagination" class="d-flex justify-content-center mt-3"></div>
  </div>
</div>

<!-- Modal Agregar -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="/app/controller/addSeccionController.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAgregarLabel">Agregar Nueva Sección</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="name" class="form-label">Nombre:</label>
          <input type="text" class="form-control" id="name" name="name" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Agregar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="/app/controller/editSeccionController.php" class="modal-content">
      <input type="hidden" name="id" id="edit-id">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarLabel">Editar Sección</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="edit-name" class="form-label">Nombre:</label>
          <input type="text" class="form-control" id="edit-name" name="name" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Actualizar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<?php
if (isset($_SESSION['status'])):
    $modalStatus = $_SESSION['status'];
    $action = $_SESSION['action'];
    unset($_SESSION['status'], $_SESSION['action']);

    $mensajes = [
        'add' => ['Sección agregada!', 'La sección ha sido registrado correctamente.'],
        'delete' => ['Sección eliminada!', 'La sección ha sido eliminado correctamente.'],
        'edit' => ['Sección actualizada!', 'Los datos de la sección se han actualizado.'],
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


<!-- Modal Confirmar Eliminación Sección -->
<div class="modal fade" id="modalConfirmEliminarSeccion" tabindex="-1" aria-labelledby="modalConfirmEliminarSeccionLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalConfirmEliminarSeccionLabel">Eliminar sección</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        ¿Estás seguro de eliminar esta sección?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="btnConfirmEliminarSeccion">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<!-- Toast Notificaciones Sección -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
  <div id="toastSeccion" class="toast align-items-center text-bg-primary border-0" role="status" aria-live="polite" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toastSeccionBody">Notificación</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<?php require_once "../view/footer.php"; ?>

<script>
  // Toast
  function showToastSeccion(message, variant = 'primary') {
    let toastEl = document.getElementById('toastSeccion');
    let bodyEl = document.getElementById('toastSeccionBody');
    if (!toastEl) return;
    bodyEl.textContent = message;
    toastEl.className = 'toast align-items-center border-0 text-bg-' + variant;
    new bootstrap.Toast(toastEl, { delay: 3000 }).show();
  }

  // Modal de resultado si existe
  const modalEl = document.getElementById('modalResultado');
  if (modalEl) {
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
  }

  // Buscador por nombre y paginación
  const buscador = document.getElementById('buscador');
  const tabla = document.getElementById('tablaSecciones');
  const tbody = tabla.querySelector('tbody');
  const pagination = document.getElementById('pagination');
  let rows = Array.from(tbody.querySelectorAll('tr'));
  let filteredRows = rows;
  let currentPage = 1;
  const rowsPerPage = 10;

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
    const texto = buscador.value.trim().toLowerCase();
    if (!texto) {
      filteredRows = rows;
    } else {
      filteredRows = rows.filter(row => {
        const nombreTd = row.querySelector('.nombre');
        return nombreTd && nombreTd.textContent.toLowerCase().includes(texto);
      });
    }
    renderPage(1);
  }

  buscador.addEventListener('input', filtrarPorNombre);
  renderPage(1);

  // Cargar datos en modal editar
  document.querySelectorAll('.btn-editar').forEach(btn => {
    btn.addEventListener('click', function () {
      const seccionId = this.getAttribute('data-id');
      fetch(`/app/controller/getByIdSeccionController.php?id=${seccionId}`)
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            document.getElementById('edit-id').value = data.seccion.id;
            document.getElementById('edit-name').value = data.seccion.name;
            new bootstrap.Modal(document.getElementById('modalEditar')).show();
          } else {
            showToastSeccion('No se pudo obtener la información de la sección.', 'danger');
          }
        })
        .catch(() => showToastSeccion('Error al cargar los datos.', 'danger'));
    });
  });

  // Confirmar eliminación
  let idSeccionEliminar = null;
  document.querySelectorAll('.btn-eliminar').forEach(btn => {
    btn.addEventListener('click', function() {
      idSeccionEliminar = this.getAttribute('data-id');
      new bootstrap.Modal(document.getElementById('modalConfirmEliminarSeccion')).show();
    });
  });

  document.getElementById('btnConfirmEliminarSeccion').addEventListener('click', function() {
    if (idSeccionEliminar) {
      window.location.href = `/app/controller/deleteSeccionController.php?id=${idSeccionEliminar}`;
    }
  });
</script>
