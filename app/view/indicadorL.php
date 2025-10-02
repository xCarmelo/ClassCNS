<?php require_once "../view/header.php"; ?>

<?php if (!isset($_SESSION)) { /* por seguridad, pero header.php ya hace session_start() */ } ?>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION['flash_error']) ?>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>


<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Indicadores de Logro</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddIndicador">
            + Agregar Indicador de Logro
        </button>
    </div>

    <table class="table table-bordered table-hover" id="tablaIndicadores">
        <thead class="table-dark">
            <tr>
                <th>Nombre</th>
                <th>Año</th>
                <th>Corte</th>
                <th>Materia</th>
                <th>Secciones</th>
                <th>Acciones</th>
                <th>Criterios</th> <!-- nueva columna -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($indicadores as $ind): ?>
                <tr>
                    <td><?= htmlspecialchars($ind['name']) ?></td>
                    <td><?= htmlspecialchars($ind['anio']) ?></td>
                    <td><?= htmlspecialchars($ind['corte']) ?></td>
                    <td><?= htmlspecialchars($ind['materia']) ?></td>
                    <td>
                        <select class="form-select">
                            <?php
                            $seccionesInd = $this->enlaceModel->getByIndicador($ind['id']);
                            foreach ($seccionesInd as $sec): ?>
                                <option><?= htmlspecialchars($sec['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <!-- Botón editar -->
                        <button class="btn btn-sm btn-info"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditIndicador<?= $ind['id'] ?>">
                            Editar
                        </button>

                        <!-- Botón eliminar -->
                        <a href="../controller/getIndicadorDeLogroController.php?action=delete&id=<?= $ind['id'] ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('¿Seguro que deseas eliminar este indicador?')">
                            Eliminar
                        </a>
                    </td>

                    <td>
                        <!-- Botón Criterios -->
                        <button
                            type="button"
                            class="btn btn-sm btn-warning btn-criterios"
                            data-id="<?= $ind['id'] ?>"
                            data-name="<?= htmlspecialchars($ind['name'], ENT_QUOTES) ?>"
                            data-bs-toggle="modal"
                            data-bs-target="#criteriosModal">
                            Criterios
                        </button>
                    </td>
                </tr>

                <!-- Modal Editar Indicador -->
                <div class="modal fade" id="modalEditIndicador<?= $ind['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form method="POST" action="../controller/getIndicadorDeLogroController.php?action=update">
                                <div class="modal-header">
                                    <h5 class="modal-title">Editar Indicador: <?= htmlspecialchars($ind['name']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body row">
                                    <input type="hidden" name="id" value="<?= $ind['id'] ?>">

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label>Año</label>
                                            <input type="number" class="form-control" name="anio"
                                                   value="<?= htmlspecialchars($ind['anio']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Corte</label>
                                            <select class="form-select" name="idCorte" required>
                                                <?php foreach ($cortes as $c): ?>
                                                    <option value="<?= $c['id'] ?>"
                                                        <?= ($ind['idCorte'] == $c['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($c['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Materia</label>
                                            <select class="form-select" name="idMateria" required>
                                                <?php foreach ($materias as $m): ?>
                                                    <option value="<?= $m['id'] ?>"
                                                        <?= ($ind['idMateria'] == $m['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($m['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Nombre del Indicador</label>
                                            <input type="text" class="form-control" name="name"
                                                   value="<?= htmlspecialchars($ind['name']) ?>" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <h6>Secciones</h6>
                                        <?php
                                        $seccionesInd = $this->enlaceModel->getByIndicador($ind['id']);
                                        $idsSeccionesInd = array_column($seccionesInd, 'idSeccion');
                                        foreach ($secciones as $sec): ?>
                                            <div class="form-check">
                                                <input type="checkbox"
                                                       class="form-check-input"
                                                       name="secciones[]"
                                                       value="<?= $sec['id'] ?>"
                                                    <?= in_array($sec['id'], $idsSeccionesInd) ? 'checked' : '' ?>>
                                                <label class="form-check-label"><?= htmlspecialchars($sec['name']) ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div id="pagination" class="d-flex justify-content-center mt-3"></div>
</div>

<!-- Modal Agregar Indicador -->
<div class="modal fade" id="modalAddIndicador" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="../controller/getIndicadorDeLogroController.php?action=store">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Indicador de Logro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label>Año</label>
                            <input type="number" class="form-control" name="anio" required>
                        </div>
                        <div class="mb-3">
                            <label>Corte</label>
                            <select class="form-select" name="idCorte" required>
                                <?php foreach ($cortes as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Materia</label>
                            <select class="form-select" name="idMateria" required>
                                <?php foreach ($materias as $m): ?>
                                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Nombre del Indicador</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Secciones</h6>
                        <?php foreach ($secciones as $sec): ?>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="secciones[]" value="<?= $sec['id'] ?>">
                                <label class="form-check-label"><?= htmlspecialchars($sec['name']) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Criterios (para 3 criterios) -->
<div class="modal fade" id="criteriosModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <!-- NOTA: usamos fetch para enviar al controlador ../controller/criterioController.php?action=store -->
      <form id="criteriosForm">
        <div class="modal-header">
          <h5 class="modal-title">
            Criterios para: <span id="indicadorNombre"></span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="idIndicador" id="idIndicador">

          <div class="row g-3">
            <?php for ($i = 1; $i <= 3; $i++): ?>
              <div class="col-12 col-md-6">
                <label class="form-label">Criterio <?= $i ?></label>
                <input id="criterio<?= $i ?>" type="text" class="form-control" name="criterio<?= $i ?>" placeholder="Descripción del criterio <?= $i ?>">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label">Puntaje <?= $i ?></label>
                <input id="puntaje<?= $i ?>" type="number" class="form-control" name="puntaje<?= $i ?>" min="0" step="0.1" placeholder="Ej: 2.5">
              </div>
            <?php endfor; ?>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Guardar Criterios</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Script para manejar modal de criterios -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('criteriosModal');
    const nombreSpan = document.getElementById('indicadorNombre');
    const idInput = document.getElementById('idIndicador');
    const form = document.getElementById('criteriosForm');

    // Cuando se abre el modal desde el botón, rellenar valores y resetear campos
    document.querySelectorAll('.btn-criterios').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const name = this.dataset.name || '';

            nombreSpan.textContent = name;
            idInput.value = id;

            // reset campos
            for (let i = 1; i <= 3; i++) {
                const c = document.getElementById('criterio' + i);
                const p = document.getElementById('puntaje' + i);
                if (c) c.value = '';
                if (p) p.value = '';
            }

            // Intentar cargar criterios existentes (opcional - si tu controlador lo soporta)
            fetch('../controller/criterioController.php?action=get&id=' + encodeURIComponent(id))
                .then(r => {
                    if (!r.ok) throw new Error('no data');
                    return r.json();
                })
                .then(data => {
                    // se espera un array de objetos { id, name, puntos, idIndicadorL } o similar
                    if (Array.isArray(data)) {
                        for (let i = 0; i < 3; i++) {
                            if (!data[i]) break;
                            const item = data[i];
                            const nameVal = item.name ?? item.descripcion ?? item.criterio ?? '';
                            const puntosVal = item.puntos ?? item.puntaje ?? '';
                            const c = document.getElementById('criterio' + (i+1));
                            const p = document.getElementById('puntaje' + (i+1));
                            if (c) c.value = nameVal;
                            if (p) p.value = puntosVal;
                        }
                    }
                })
                .catch(() => {
                    // silenciar: puede que el endpoint get no exista y esté bien
                });
        });
    });

    // Envío del formulario por fetch (AJAX)
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(form);
        // idIndicador ya está en el hidden

        fetch('../controller/criterioController.php?action=store', {
            method: 'POST',
            body: formData,
        })
        .then(response => {
            if (response.ok) {
                // cerrar modal y recargar para ver cambios
                location.reload();
            } else {
                return response.text().then(t => { throw new Error(t || 'Error al guardar'); });
            }
        })
        .catch(err => {
            alert('Error guardando criterios: ' + err.message);
        });
    });
});
</script>


<script>
// Paginación para tabla de indicadores
document.addEventListener('DOMContentLoaded', function () {
    const tabla = document.getElementById('tablaIndicadores');
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

    renderPage(1);
});
</script>

<?php require_once "../view/footer.php"; ?>
