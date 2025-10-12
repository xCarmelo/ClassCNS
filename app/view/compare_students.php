<?php require_once "../view/header.php"; ?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Comparar Estudiantes (Excel vs. Base de Datos)</h2>
    <a href="../controller/getStudentController.php" class="btn btn-outline-secondary">Volver a Estudiantes</a>
  </div>

  <div class="alert alert-info">
    Sube un archivo Excel con dos columnas: <strong>name</strong> y <strong>seccion</strong>. La comparación se hará contra los estudiantes activos (no eliminados lógicamente).
  </div>

  <form action="../controller/compareStudentsController.php?action=compare" method="post" enctype="multipart/form-data" class="card p-3 mb-4">
    <div class="mb-3">
      <label for="archivo" class="form-label">Archivo Excel (XLSX/XLS/CSV)</label>
      <input type="file" class="form-control" id="archivo" name="archivo" accept=".xlsx,.xls,.csv" required>
    </div>
    <button type="submit" class="btn btn-primary">Comparar</button>
  </form>

  <?php if (!empty($summary)): ?>
    <?php if (!empty($summary['error'])): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($summary['error']) ?></div>
    <?php else: ?>
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center gap-2 mb-2">
        <div class="input-group" style="max-width:480px;">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input id="compareSearch" type="text" class="form-control" placeholder="Buscar por nombre o sección...">
          <button class="btn btn-outline-secondary" type="button" id="btnClearSearch" title="Limpiar">
            <i class="bi bi-x-circle"></i>
          </button>
        </div>
        <div class="d-flex gap-2">
          <button id="btnExpandAll" class="btn btn-outline-primary" type="button"><i class="bi bi-arrows-angle-expand"></i> Expandir todo</button>
          <button id="btnCollapseAll" class="btn btn-outline-secondary" type="button"><i class="bi bi-arrows-angle-contract"></i> Contraer todo</button>
          <button id="btnExportCompare" class="btn btn-success" type="button">
            <i class="bi bi-file-earmark-spreadsheet"></i> Exportar resultados (CSV)
          </button>
        </div>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-md-2">
          <div class="card text-center">
            <div class="card-body">
              <div class="fw-bold">En archivo</div>
              <div class="display-6"><?= (int)$summary['total_file'] ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card text-center">
            <div class="card-body">
              <div class="fw-bold">En BD</div>
              <div class="display-6"><?= (int)$summary['total_db'] ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card text-center border-success">
            <div class="card-body">
              <div class="fw-bold text-success">Nuevos</div>
              <div class="display-6 text-success"><?= (int)$summary['new'] ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card text-center border-warning">
            <div class="card-body">
              <div class="fw-bold text-warning">Movidos</div>
              <div class="display-6 text-warning"><?= (int)$summary['moved'] ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card text-center border-danger">
            <div class="card-body">
              <div class="fw-bold text-danger">Faltantes</div>
              <div class="display-6 text-danger"><?= (int)$summary['missing'] ?></div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card text-center border-secondary">
            <div class="card-body">
              <div class="fw-bold text-secondary">Duplicados</div>
              <div class="display-6 text-secondary"><?= (int)$summary['duplicates'] ?></div>
            </div>
          </div>
        </div>
      </div>

      <div class="accordion" id="accordionDetalles">
        <div class="accordion-item">
          <h2 class="accordion-header" id="headNuevos">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#secNuevos">Nuevos (<?= (int)$summary['new'] ?>)</button>
          </h2>
          <div id="secNuevos" class="accordion-collapse collapse show" data-bs-parent="#accordionDetalles">
            <div class="accordion-body">
              <?php if (!empty($details['newStudents'])): ?>
              <div class="table-responsive">
                <table class="table table-sm table-striped table-hover table-bordered compare-table" data-section="nuevos"><thead class="table-light"><tr><th>Nombre</th><th>Sección</th></tr></thead><tbody>
                  <?php foreach ($details['newStudents'] as $r): ?>
                  <tr><td><?= htmlspecialchars($r['name']) ?></td><td><?= htmlspecialchars($r['seccion']) ?></td></tr>
                  <?php endforeach; ?>
                </tbody></table>
              </div>
              <?php else: ?>
                <em>Sin registros.</em>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header" id="headMovidos">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#secMovidos">Movidos (<?= (int)$summary['moved'] ?>)</button>
          </h2>
          <div id="secMovidos" class="accordion-collapse collapse" data-bs-parent="#accordionDetalles">
            <div class="accordion-body">
              <?php if (!empty($details['movedStudents'])): ?>
              <div class="table-responsive">
                <table class="table table-sm table-striped table-hover table-bordered compare-table" data-section="movidos"><thead class="table-light"><tr><th>Nombre</th><th>Desde</th><th>Hacia</th></tr></thead><tbody>
                  <?php foreach ($details['movedStudents'] as $r): ?>
                  <tr><td><?= htmlspecialchars($r['name']) ?></td><td><?= htmlspecialchars($r['from']) ?></td><td><?= htmlspecialchars($r['to']) ?></td></tr>
                  <?php endforeach; ?>
                </tbody></table>
              </div>
              <?php else: ?>
                <em>Sin registros.</em>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header" id="headFaltantes">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#secFaltantes">Faltantes (<?= (int)$summary['missing'] ?>)</button>
          </h2>
          <div id="secFaltantes" class="accordion-collapse collapse" data-bs-parent="#accordionDetalles">
            <div class="accordion-body">
              <?php if (!empty($details['missingStudents'])): ?>
              <div class="table-responsive">
                <table class="table table-sm table-striped table-hover table-bordered compare-table" data-section="faltantes"><thead class="table-light"><tr><th>Nombre</th><th>Sección</th></tr></thead><tbody>
                  <?php foreach ($details['missingStudents'] as $r): ?>
                  <tr><td><?= htmlspecialchars($r['name']) ?></td><td><?= htmlspecialchars($r['seccion']) ?></td></tr>
                  <?php endforeach; ?>
                </tbody></table>
              </div>
              <?php else: ?>
                <em>Sin registros.</em>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header" id="headDup">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#secDup">Duplicados en archivo (<?= (int)$summary['duplicates'] ?>)</button>
          </h2>
          <div id="secDup" class="accordion-collapse collapse" data-bs-parent="#accordionDetalles">
            <div class="accordion-body">
              <?php if (!empty($details['duplicatesInFile'])): ?>
              <div class="table-responsive">
                <table class="table table-sm table-striped table-hover table-bordered compare-table" data-section="duplicados"><thead class="table-light"><tr><th>Nombre</th><th>Sección</th></tr></thead><tbody>
                  <?php foreach ($details['duplicatesInFile'] as $r): ?>
                  <tr><td><?= htmlspecialchars($r['name']) ?></td><td><?= htmlspecialchars($r['seccion']) ?></td></tr>
                  <?php endforeach; ?>
                </tbody></table>
              </div>
              <?php else: ?>
                <em>Sin registros.</em>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="accordion-item">
          <h2 class="accordion-header" id="headUnknown">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#secUnknown">Secciones desconocidas (<?= (int)$summary['unknown_sections'] ?>)</button>
          </h2>
          <div id="secUnknown" class="accordion-collapse collapse" data-bs-parent="#accordionDetalles">
            <div class="accordion-body">
              <?php if (!empty($details['unknownSections'])): ?>
              <div class="table-responsive">
                <table class="table table-sm table-striped table-hover table-bordered compare-table" data-section="desconocidas"><thead class="table-light"><tr><th>Nombre</th><th>Sección en archivo</th></tr></thead><tbody>
                  <?php foreach ($details['unknownSections'] as $r): ?>
                  <tr><td><?= htmlspecialchars($r['name']) ?></td><td><?= htmlspecialchars($r['seccion']) ?></td></tr>
                  <?php endforeach; ?>
                </tbody></table>
              </div>
              <?php else: ?>
                <em>Sin registros.</em>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
  <script>
        // Datos para exportación desde PHP
        window.COMPARE_DETAILS = <?php
          $safeDetails = $details ?? [];
          echo json_encode($safeDetails, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT);
        ?>;

        function csvEscape(val){
          const v = (val===null||val===undefined) ? '' : String(val);
          // Escapar comillas dobles y envolver en comillas
          return '"' + v.replace(/"/g, '""') + '"';
        }

        function exportCompareCSV(){
          const d = window.COMPARE_DETAILS || {};
          const rows = [];
          // Encabezados
          rows.push(['tipo','nombre','seccion','desde','hacia']);
          const pushRow = (tipo, nombre, seccion, desde, hacia) => {
            rows.push([tipo||'', nombre||'', seccion||'', desde||'', hacia||'']);
          };
          (d.newStudents||[]).forEach(r=> pushRow('Nuevo', r.name, r.seccion, '', ''));
          (d.movedStudents||[]).forEach(r=> pushRow('Movido', r.name, '', r.from, r.to));
          (d.missingStudents||[]).forEach(r=> pushRow('Faltante', r.name, r.seccion, '', ''));
          (d.duplicatesInFile||[]).forEach(r=> pushRow('Duplicado', r.name, r.seccion, '', ''));
          (d.unknownSections||[]).forEach(r=> pushRow('SeccionDesconocida', r.name, r.seccion, '', ''));

          const csv = rows.map(cols => cols.map(csvEscape).join(',')).join('\r\n');
          const blob = new Blob([csv], {type: 'text/csv;charset=utf-8;'});
          const url = URL.createObjectURL(blob);
          const a = document.createElement('a');
          a.href = url;
          a.download = 'comparacion_estudiantes.csv';
          document.body.appendChild(a);
          a.click();
          document.body.removeChild(a);
          URL.revokeObjectURL(url);
        }

        // Exportar CSV
        document.getElementById('btnExportCompare').addEventListener('click', exportCompareCSV);

        // Buscar en vivo sobre todas las tablas
        const searchInput = document.getElementById('compareSearch');
        const clearBtn = document.getElementById('btnClearSearch');
        const tables = Array.from(document.querySelectorAll('table.compare-table'));

        function normalizeTxt(s){ return (s||'').toString().toLowerCase(); }

        function applyFilter(){
          const q = normalizeTxt(searchInput.value);
          tables.forEach(tbl => {
            let visibleCount = 0;
            const rows = tbl.tBodies[0]?.rows || [];
            Array.from(rows).forEach(tr => {
              const txt = normalizeTxt(tr.innerText);
              const show = q === '' || txt.includes(q);
              tr.style.display = show ? '' : 'none';
              if (show) visibleCount++;
            });
            // Mensaje "Sin registros." si no hay visibles
            const body = tbl.parentElement?.parentElement; // accordion-body
            if (body){
              let emptyMsg = body.querySelector('[data-empty-msg]');
              const hasRows = rows.length > 0;
              const noneVisible = visibleCount === 0;
              if (hasRows && noneVisible){
                if (!emptyMsg){
                  emptyMsg = document.createElement('div');
                  emptyMsg.setAttribute('data-empty-msg','');
                  emptyMsg.className = 'text-muted fst-italic mt-2';
                  emptyMsg.textContent = 'Sin resultados con el filtro actual.';
                  body.appendChild(emptyMsg);
                }
                emptyMsg.style.display = '';
              } else if (emptyMsg){
                emptyMsg.style.display = 'none';
              }
            }
            // Actualizar contador en encabezado del acordeón
            const sectionId = tbl.getAttribute('data-section');
            let headBtn;
            switch(sectionId){
              case 'nuevos': headBtn = document.querySelector('#headNuevos button'); break;
              case 'movidos': headBtn = document.querySelector('#headMovidos button'); break;
              case 'faltantes': headBtn = document.querySelector('#headFaltantes button'); break;
              case 'duplicados': headBtn = document.querySelector('#headDup button'); break;
              case 'desconocidas': headBtn = document.querySelector('#headUnknown button'); break;
            }
            if (headBtn){
              headBtn.innerHTML = headBtn.innerHTML.replace(/\(.*?\)/, '('+visibleCount+')');
            }
          });
        }

        searchInput?.addEventListener('input', applyFilter);
        clearBtn?.addEventListener('click', ()=>{ searchInput.value=''; applyFilter(); searchInput.focus(); });

        // Expandir / Contraer todo
        const expandAllBtn = document.getElementById('btnExpandAll');
        const collapseAllBtn = document.getElementById('btnCollapseAll');
        function setAll(open){
          document.querySelectorAll('#accordionDetalles .accordion-collapse').forEach(el=>{
            const c = bootstrap.Collapse.getOrCreateInstance(el, {toggle:false});
            open ? c.show() : c.hide();
          });
        }
        expandAllBtn?.addEventListener('click', ()=> setAll(true));
        collapseAllBtn?.addEventListener('click', ()=> setAll(false));
      </script>
    <?php endif; ?>
  <?php endif; ?>
</div>

<?php require_once "../view/footer.php"; ?>
