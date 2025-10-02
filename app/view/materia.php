<?php require_once "../view/header.php"; ?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Listado de Materias</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">
      + Nueva Materia
    </button>
  </div>

  <div class="row mb-3">
    <div class="col-md-4">
      <label for="buscador" class="form-label">Buscar por nombre:</label>
      <input type="text" id="buscador" class="form-control" placeholder="Nombre de la materia">
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle table-bordered" id="tablaMaterias">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Nombre</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($materias)): ?>
          <?php foreach ($materias as $index => $materia): ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td class="nombre"><?= htmlspecialchars($materia['name']) ?></td>
              <td>
                <button class="btn btn-sm btn-warning btn-editar" data-id="<?= $materia['id'] ?>">
                  <i class="bi bi-pencil-square"></i>
                </button>
                <a href="/app/controller/deleteMateriaController.php?id=<?= $materia['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta materia?');">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="3" class="text-center">No hay materias registradas.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div id="pagination" class="d-flex justify-content-center mt-3"></div>
  </div>
</div>

<!-- Modal Agregar -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="/app/controller/addMateriaController.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAgregarLabel">Agregar Nueva Materia</h5>
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
    <form method="POST" action="/app/controller/editMateriaController.php" class="modal-content">
      <input type="hidden" name="id" id="edit-id">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarLabel">Editar Materia</h5>
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
        'add' => ['¡Materia agregada!', 'La materia ha sido registrada correctamente.'],
        'delete' => ['¡Materia eliminada!', 'La materia ha sido eliminada correctamente.'],
        'edit' => ['¡Materia actualizada!', 'Los datos de la materia se han actualizado.'],
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



<?php require_once "../view/footer.php"; ?>

<script>
  const modalEl = document.getElementById('modalResultado');
  if (modalEl) {
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
  }

  // Buscador por nombre y paginación
  const buscador = document.getElementById('buscador');
  const tabla = document.getElementById('tablaMaterias');
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

  document.querySelectorAll('.btn-editar').forEach(btn => {
    btn.addEventListener('click', function () {
      const materiaId = this.getAttribute('data-id');
      fetch(`/app/controller/getByIdMateriaController.php?id=${materiaId}`)
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            document.getElementById('edit-id').value = data.materia.id;
            document.getElementById('edit-name').value = data.materia.name;
            new bootstrap.Modal(document.getElementById('modalEditar')).show();
          } else {
            alert('No se pudo obtener la información de la materia.');
          }
        })
        .catch(() => alert('Error al cargar los datos.'));
    });
  });
</script>
