<?php require_once "../view/header.php"; ?>

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
        <h2>Listado de Estudiantes</h2>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">
                + Nuevo Estudiante
            </button>
        </div> 
    </div>

    <div class="row mb-3">
    <div class="col-md-4">
            <label for="filtroSeccion" class="form-label">Filtrar por sección:</label>
            <select id="filtroSeccion" class="form-select">
                <option value="">Todas</option>
                <?php
                    $stmt = Student::$pdo->query("SELECT * FROM seccion");
                    while ($seccion = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value=\"{$seccion['id']}\">{$seccion['name']}</option>";
                    }
                ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="buscador" class="form-label">Buscar por nombre:</label>
            <input type="text" id="buscador" class="form-control" placeholder="Nombre del estudiante">
        </div>
    <div class="col-md-4">
      <label for="filtroEstado" class="form-label">Estado:</label>
      <select id="filtroEstado" class="form-select">
        <option value="1" <?= isset($_GET['status']) && $_GET['status']==='0' ? '' : 'selected' ?>>Activos</option>
        <option value="0" <?= isset($_GET['status']) && $_GET['status']==='0' ? 'selected' : '' ?>>Eliminados</option>
      </select>
    </div>
        


    </div>

    <div class="table-responsive">
        <div style="min-width: 600px">
            <table class="table table-hover align-middle table-bordered" id="tablaEstudiantes">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Sección</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($students)): ?>
                        <?php foreach ($students as $index => $student): ?>
                            <tr data-seccion="<?= (int)$student['idSeccion'] ?>">
                                <td><?= $student["NumerodeLista"] ?></td>
                                <td class="nombre"><?= htmlspecialchars($student['name']) ?></td>
                                <td><?= htmlspecialchars($student['seccion_name']) ?></td>
                                <td><?= (int)($student['status'] ?? 1) === 1 ? 'Activo' : 'Eliminado' ?></td>
                <td>
                  <?php $isEliminado = ((int)($student['status'] ?? 1) === 0); ?>
                  <?php if (!$isEliminado): ?>
                    <button class="btn btn-sm btn-warning btn-editar" data-id="<?= $student['id'] ?>">
                      <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-confirmar-eliminar" data-id="<?= $student['id'] ?>">
                      <i class="bi bi-trash"></i>
                    </button>
                    <button class="btn btn-sm btn-success btn-agregar" data-id="<?= $student['id'] ?>" data-nombre="<?= htmlspecialchars($student['name']) ?>">
                      <i class="bi bi-plus-circle"></i>
                    </button>
                  <?php else: ?>
                    <button class="btn btn-sm btn-secondary btn-confirmar-restaurar" data-id="<?= $student['id'] ?>">
                      <i class="bi bi-arrow-counterclockwise"></i> Restaurar
                    </button>
                  <?php endif; ?>
                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">No hay estudiantes registrados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="pagination-container mt-3 text-center">
                <nav>
                    <ul class="pagination justify-content-center" id="paginacionEstudiantes"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Estudiante -->
<!-- Modal Agregar Estudiante -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="modalAgregarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- ENCABEZADO -->
      <div class="modal-header">
        <h5 class="modal-title" id="modalAgregarLabel">Agregar Nuevo Estudiante</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <!-- CUERPO DEL MODAL -->
      <div class="modal-body">
        <!-- FORMULARIO INDIVIDUAL -->
        <form method="POST" action="/app/controller/addStudentController.php" id="formAgregarIndividual">
          <div class="mb-3">
            <label for="name" class="form-label">Nombre:</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>

          <div class="mb-3">
            <label for="numero-lista-add" class="form-label">Número de lista:</label>
            <input type="number" class="form-control" id="numero-lista-add" name="NumerodeLista" min="1" required>
            <div class="form-text">No puede repetirse en la misma sección.</div>
          </div>

          <div class="mb-3">
            <label for="idSeccion" class="form-label">Sección:</label>
            <select class="form-select" id="idSeccion" name="idSeccion" required>
              <option value="">Seleccione una sección</option>
              <?php
              $stmt = Student::$pdo->query("SELECT * FROM seccion");
              while ($seccion = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value=\"{$seccion['id']}\">{$seccion['name']}</option>";
              }
              ?>
            </select>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-success">Agregar Estudiante</button>
          </div>
        </form>

        <hr class="my-4">

        <!-- IMPORTAR DESDE EXCEL -->
        <form id="formImportarExcel" enctype="multipart/form-data" method="POST" action="/app/controller/importarEstudiantesController.php">
          <div class="mb-3">
            <label class="form-label">Importar desde Excel:</label>
            <input type="file" class="form-control" name="archivoExcel" accept=".xlsx,.xls" required>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-outline-primary">
              <i class="bi bi-upload"></i> Importar Estudiantes
            </button>
          </div>

          <div class="form-text mt-2">
            El archivo debe contener columnas con los encabezados <strong>nombre</strong>, <strong>seccion</strong>, <strong>NumerodeLista</strong>, <strong>status</strong>, <strong>idCorte</strong> y <strong>fin</strong>.
          </div>
        </form>
      </div>

      <!-- PIE DEL MODAL -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>

    </div>
  </div>
</div>


         <?php
if (isset($_SESSION['status']) && isset($_SESSION['action'])):

    $status = $_SESSION['status']; // success | warning | error
    $action = $_SESSION['action'];

    $message       = $_SESSION['message'] ?? null;
    $error         = $_SESSION['error'] ?? null;
    $noInsertados  = $_SESSION['noInsertados'] ?? [];
    $insertados    = $_SESSION['insertados'] ?? 0;

    // Limpiar sesión
    unset(
        $_SESSION['status'],
        $_SESSION['action'],
        $_SESSION['message'],
        $_SESSION['error'],
        $_SESSION['noInsertados'],
        $_SESSION['insertados']
    );

    // Configuración visual
    $config = [
        'success' => ['bg' => 'success', 'title' => '✅ Operación exitosa'],
        'warning' => ['bg' => 'warning', 'title' => '⚠ Atención'],
        'error'   => ['bg' => 'danger',  'title' => '❌ Error']
    ];

    $bg    = $config[$status]['bg']; 
    $title = $config[$status]['title'];
?>
<div class="modal fade" id="modalResultado" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-<?= $bg ?> text-dark <?= $status === 'error' ? 'text-white' : '' ?>">
      <div class="modal-header">
        <h5 class="modal-title"><?= $title ?></h5>
        <button type="button" class="btn-close <?= $status === 'error' ? 'btn-close-white' : '' ?>" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <?php if ($message): ?>
          <p><strong><?= htmlspecialchars($message) ?></strong></p>
        <?php endif; ?>

        <?php if ($status === 'error' && $error): ?>
          <div class="alert alert-light text-dark">
            <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($noInsertados)): ?>
          <hr>
          <p><strong>Detalles:</strong></p>
          <ul class="list-group">
            <?php foreach ($noInsertados as $detalle): ?>
              <li class="list-group-item">
                <?= htmlspecialchars($detalle) ?>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>


<!-- Modal Editar Estudiante (Reutilizable) -->
<div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="/app/controller/editStudentController.php" class="modal-content">
      <input type="hidden" name="id" id="edit-id">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarLabel">Editar Estudiante</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="edit-name" class="form-label">Nombre:</label>
          <input type="text" class="form-control" id="edit-name" name="name" required>
        </div>

        <div class="mb-3">
          <label for="edit-idSeccion" class="form-label">Sección:</label>
          <select class="form-select" id="edit-idSeccion" name="idSeccion" required>
            <option value="">Seleccione una sección</option>
            <?php
            $stmt = Student::$pdo->query("SELECT * FROM seccion");
            while ($seccion = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value=\"{$seccion['id']}\">{$seccion['name']}</option>";
            }
            ?>
          </select>
        </div>

        <!-- 🆕 Nuevo campo: Número de lista -->
        <div class="mb-3">
          <label for="edit-numero-lista" class="form-label">Número de lista:</label>
          <input type="number" class="form-control" id="edit-numero-lista" name="NumerodeLista" min="1" required>
        </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Actualizar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button> 
      </div>
    </form> 
  </div>
</div>


<!-- Modal Agregar Asunto -->
<div class="modal fade" id="modalAgregarAsunto" tabindex="-1" aria-labelledby="modalAgregarAsuntoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="/app/controller/addAsuntoController.php" class="modal-content">
      <input type="hidden" name="idStudent" id="add-idStudent">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAgregarAsuntoLabel">Nuevo Asunto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Estudiante:</label>
          <div class="d-flex align-items-center">
            <span id="add-selected-student" class="me-2"></span>
          </div>
        </div>
        <div class="mb-3">
          <label for="add-idMateria" class="form-label">Materia:</label>
          <select class="form-select" id="add-idMateria" name="idMateria" required>
            <option value="">Seleccione una materia</option>
            <?php foreach ($materias as $materia): ?>
              <option value="<?= $materia['id'] ?>"><?= htmlspecialchars($materia['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label for="add-idCorte" class="form-label">Corte:</label>
          <select class="form-select" id="add-idCorte" name="idCorte" required>
            <option value="">Seleccione un corte</option>
            <?php foreach ($cortes as $corte): ?>
              <option value="<?= $corte['id'] ?>"><?= htmlspecialchars($corte['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label for="add-tema" class="form-label">Tema:</label>
          <input type="text" class="form-control" id="add-tema" name="tema" required>
        </div>
        <div class="mb-3">
          <label for="add-nota" class="form-label">Nota:</label>
          <input type="text" class="form-control" id="add-nota" name="nota" required>
        </div>
        <div class="mb-3">
          <label for="add-fecha" class="form-label">Fecha:</label>
          <input type="datetime-local" class="form-control" id="add-fecha" name="fecha" required>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="1" name="statuss" id="add-statuss" checked>
          <label class="form-check-label" for="add-statuss">Activo</label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Agregar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>


<?php require_once "../view/footer.php";?><!-- cierre de header y link de js-->

<script>
  // Mostrar modal de resultado si existe
  const modalEl = document.getElementById('modalResultado');
  if (modalEl) {
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
  }

  const rowsPerPage = 10;
  let currentPage = 1;

function filtrarYPaginar() {
  const filtroSeccion = (document.getElementById('filtroSeccion').value || '').trim();
  const filtroCorte = '';
  const filtroEstado = document.getElementById('filtroEstado').value;
  const buscador = document.getElementById('buscador').value.toLowerCase();

  const rows = Array.from(document.querySelectorAll('#tablaEstudiantes tbody tr'));
  let visibles = [];

  rows.forEach(row => {
    const seccion = (row.dataset.seccion || '').trim();
  const corte = '';
    const nombre = (row.querySelector('.nombre')?.textContent || '').toLowerCase();

  const coincideSeccion = !filtroSeccion || seccion === filtroSeccion;
    const coincideCorte = !filtroCorte || corte === filtroCorte;
  const coincideNombre = nombre.includes(buscador);
  const estadoCelda = row.querySelector('td:nth-child(4)')?.textContent.trim() || 'Activo';
  const coincideEstado = (filtroEstado === '1' && estadoCelda === 'Activo') || (filtroEstado === '0' && estadoCelda === 'Eliminado');

  if (coincideSeccion && coincideCorte && coincideNombre && coincideEstado) {
      row.dataset.visible = "true";
      visibles.push(row);
    } else {
      row.dataset.visible = "false";
    }
  });

  aplicarPaginacion(visibles);
}


  function aplicarPaginacion(rows) {
    const paginacion = document.getElementById('paginacionEstudiantes');
    const totalPages = Math.ceil(rows.length / rowsPerPage);

    document.querySelectorAll('#tablaEstudiantes tbody tr').forEach(tr => {
      tr.style.display = 'none';
    });

    rows.forEach((row, i) => {
      if (i >= (currentPage - 1) * rowsPerPage && i < currentPage * rowsPerPage) {
        row.style.display = '';
      }
    });

    // Construcción del control de paginación
    paginacion.innerHTML = '';
    for (let i = 1; i <= totalPages; i++) {
      const li = document.createElement('li');
      li.className = 'page-item' + (i === currentPage ? ' active' : '');
      li.innerHTML = `<button class="page-link">${i}</button>`;
      li.querySelector('button').addEventListener('click', () => {
        currentPage = i;
        aplicarPaginacion(rows);
      });
      paginacion.appendChild(li);
    }
  }

  document.getElementById('filtroSeccion').addEventListener('change', () => {
    currentPage = 1;
    filtrarYPaginar();
  });

  document.getElementById('buscador').addEventListener('input', () => {
    currentPage = 1;
    filtrarYPaginar();
  });

  document.getElementById('filtroEstado').addEventListener('change', () => {
    const url = new URL(window.location.href);
    url.searchParams.set('status', document.getElementById('filtroEstado').value);
    // Recarga para que el backend traiga Activos/Eliminados según corresponda
    window.location.href = url.toString();
  });

  


  // Ejecutar al cargar
  window.addEventListener('DOMContentLoaded', () => {
    filtrarYPaginar();
  });

// Manejo de edición de estudiantes
document.querySelectorAll('.btn-editar').forEach(btn => {
  btn.addEventListener('click', function () {
    const studentId = this.getAttribute('data-id');
    fetch(`/app/controller/getByIdStudentController.php?id=${studentId}`) 
      .then(response => response.json())
      .then(data => {
  if (data.success) {
          const student = data.student;
          document.getElementById('edit-id').value = student.id;
          document.getElementById('edit-name').value = student.name;
          document.getElementById('edit-idSeccion').value = student.idSeccion;

          // 🆕 Mostrar número de lista actual en el modal
          document.getElementById('edit-numero-lista').value = student.NumerodeLista ?? '';

          const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
          modal.show();
        } else {
          showToast('No se pudo obtener la información del estudiante.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showToast('Ocurrió un error al intentar obtener los datos del estudiante.');
      });
  });
});


  // Para agregar un nuevo asunto
  let modalAgregar = new bootstrap.Modal(document.getElementById('modalAgregarAsunto'));

  document.querySelectorAll('.btn-agregar').forEach(btn => {
    btn.addEventListener('click', function () {
      const id = this.dataset.id;
      const nombre = this.dataset.nombre;
      document.getElementById('add-idStudent').value = id;
      document.getElementById('add-selected-student').textContent = nombre;
      modalAgregar.show();
    });
  });

  document.getElementById('btnSeleccionarEstudianteNuevo')?.addEventListener('click', function () {
    const modalSelect = new bootstrap.Modal(document.getElementById('modalSeleccionarEstudiante'));
    modalSelect.show();

    document.querySelectorAll('.seleccionar-estudiante').forEach(btn => {
      btn.onclick = function () {
        const id = this.dataset.id;
        const nombre = this.dataset.nombre;
        document.getElementById('add-idStudent').value = id;
        document.getElementById('add-selected-student').textContent = nombre;
        modalSelect.hide();
        modalAgregar.show();
      };
    });
  });
</script>

<!-- Modal confirmar eliminar -->
<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirmar eliminación</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Marcar como eliminado este estudiante?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a href="#" id="btnEliminarConfirmado" class="btn btn-danger">Eliminar</a>
      </div>
    </div>
  </div>
 </div>

<!-- Modal confirmar restaurar -->
<div class="modal fade" id="modalConfirmarRestaurar" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-secondary text-white">
        <h5 class="modal-title">Confirmar restauración</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        ¿Restaurar este estudiante?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
        <a href="#" id="btnRestaurarConfirmado" class="btn btn-secondary">Restaurar</a>
      </div>
    </div>
  </div>
 </div>

<!-- Toast de notificaciones -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080">
  <div id="appToast" class="toast align-items-center text-bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="appToastBody">Mensaje</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
 </div>

<script>
// Helpers Bootstrap
function showToast(msg) {
  const el = document.getElementById('appToast');
  document.getElementById('appToastBody').textContent = msg;
  const toast = bootstrap.Toast.getOrCreateInstance(el);
  toast.show();
}

// Confirmar eliminar (modal)
document.querySelectorAll('.btn-confirmar-eliminar').forEach(btn => {
  btn.addEventListener('click', function() {
    const id = this.dataset.id;
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));
    const link = document.getElementById('btnEliminarConfirmado');
    link.href = `/app/controller/deleteStudentController.php?id=${id}`;
    modal.show();
  });
});

// Confirmar restaurar (modal)
document.querySelectorAll('.btn-confirmar-restaurar').forEach(btn => {
  btn.addEventListener('click', function() {
    const id = this.dataset.id;
    const modal = new bootstrap.Modal(document.getElementById('modalConfirmarRestaurar'));
    const link = document.getElementById('btnRestaurarConfirmado');
    link.href = `/app/controller/restoreStudentController.php?id=${id}`;
    modal.show();
  });
});
</script>

