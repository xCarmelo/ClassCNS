<?php require_once "../view/header.php"; ?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Comparar Estudiantes (Excel vs. Base de Datos)</h2>
    <a href="../controller/getStudentController.php" class="btn btn-outline-secondary">Volver a Estudiantes</a>
  </div>

  <div class="alert alert-info">
    Sube un archivo Excel con dos columnas: <strong>name</strong> y <strong>seccion</strong>. La comparación se hará contra los estudiantes activos (no eliminados lógicamente).
  </div>

  <?php $last = $_SESSION['last_compare'] ?? null; $hasLast = !empty($last['fileOrderBySec']); ?>
  <form action="../controller/compareStudentsController.php?action=compare" method="post" enctype="multipart/form-data" class="card p-3 mb-4">
    <div class="mb-3">
      <label for="archivo" class="form-label">Archivo Excel (XLSX/XLS/CSV)</label>
      <div class="input-group">
        <input type="file" class="form-control" id="archivo" name="archivo" accept=".xlsx,.xls,.csv" <?= $hasLast ? '' : 'required' ?> <?= $hasLast ? 'disabled' : '' ?>>
        <?php if ($hasLast): ?>
          <span class="input-group-text bg-light">
            <i class="bi bi-file-earmark-excel"></i>
            <span class="ms-1">
              <?= htmlspecialchars($last['uploaded_name'] ?? 'Archivo en memoria') ?>
              <small class="text-muted ms-1">(cargado <?= isset($last['uploaded_time']) ? date('d/m/Y H:i', (int)$last['uploaded_time']) : '' ?>)</small>
            </span>
          </span>
        <?php endif; ?>
      </div>
      <?php if ($hasLast): ?>
      <div class="form-check mt-2">
        <input class="form-check-input" type="checkbox" value="1" id="chkReemplazarArchivo">
        <label class="form-check-label" for="chkReemplazarArchivo">
          Reemplazar archivo (habilitar selector para subir uno nuevo)
        </label>
      </div>
      <script>
        (function(){
          const chk = document.getElementById('chkReemplazarArchivo');
          const inp = document.getElementById('archivo');
          chk?.addEventListener('change', ()=>{
            if (chk.checked) {
              inp.removeAttribute('disabled');
              inp.setAttribute('required','required');
            } else {
              inp.setAttribute('disabled','disabled');
              inp.removeAttribute('required');
              inp.value = '';
            }
          });
        })();
      </script>
      <?php endif; ?>
    </div>
    <button type="submit" class="btn btn-primary">Comparar</button>
    <?php if ($hasLast): ?>
      <a href="../controller/compareStudentsController.php?action=recompare" class="btn btn-outline-info ms-2">Usar archivo en memoria</a>
    <?php endif; ?>
  </form>

  <?php if (!empty($_SESSION['action']) && $_SESSION['action']==='apply_order'): ?>
    <?php if (!empty($_SESSION['status']) && $_SESSION['status']==='success'): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        Orden aplicado correctamente.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php elseif (!empty($_SESSION['status']) && $_SESSION['status']==='error'): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        No se pudo aplicar el orden. <?= isset($_SESSION['error_msg']) ? htmlspecialchars($_SESSION['error_msg']) : '' ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    <?php unset($_SESSION['action'], $_SESSION['status'], $_SESSION['error_msg']); ?>
  <?php endif; ?>

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
          <?php if (!empty($_SESSION['last_compare']['fileOrderBySec'])): ?>
            <a href="../controller/compareStudentsController.php?action=recompare" class="btn btn-info" title="Recalcular con el último archivo cargado">
              <i class="bi bi-arrow-clockwise"></i> Volver a comparar
            </a>
          <?php endif; ?>
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
        <div class="col-md-2">
          <div class="card text-center border-dark">
            <div class="card-body">
              <div class="fw-bold text-dark">Duplicados BD</div>
              <div class="display-6 text-dark"><?= (int)($summary['db_duplicates'] ?? 0) ?></div>
            </div>
          </div>
        </div>
      </div>

      <div class="accordion" id="accordionDetalles">
        <div class="accordion-item">
          <h2 class="accordion-header" id="headOrden">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#secOrden">Orden por sección</button>
          </h2>
          <div id="secOrden" class="accordion-collapse collapse">
            <div class="accordion-body">
              <?php $order = $details['orderReport'] ?? null; ?>
              <?php if ($order): ?>
                <div class="mb-2">
                  <span class="badge bg-success">Secciones OK: <?= (int)($order['summary']['ok'] ?? 0) ?></span>
                  <span class="badge bg-warning text-dark">Con diferencias: <?= (int)($order['summary']['diff'] ?? 0) ?></span>
                </div>
        <div class="table-responsive">
                  <table class="table table-sm table-bordered">
                    <thead class="table-light">
          <tr><th>Sección</th><th>Estado</th><th>Conteo BD</th><th>Conteo Archivo</th><th>Primeras diferencias (posición, BD vs Archivo)</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                      <?php $fileOrderBySecLocal = $_SESSION['last_compare']['fileOrderBySec'] ?? []; ?>
                      <?php foreach (($order['bySection'] ?? []) as $secKey => $info): ?>
                        <tr>
                          <td><?= htmlspecialchars($secKey) ?></td>
                          <td>
                            <?php if (($info['status'] ?? '') === 'ok'): ?>
                              <span class="badge bg-success">OK</span>
                            <?php else: ?>
                              <span class="badge bg-warning text-dark">Diferente</span>
                            <?php endif; ?>
                          </td>
                          <td><?= (int)($info['dbCount'] ?? 0) ?></td>
                          <td><?= (int)($info['fileCount'] ?? 0) ?></td>
                          <td>
                            <?php if (!empty($info['mismatches'])): ?>
                              <ul class="mb-0">
                                <?php foreach ($info['mismatches'] as $mm): ?>
                                  <li>#<?= (int)$mm['pos'] ?>: <strong><?= htmlspecialchars($mm['db']) ?></strong> vs <strong><?= htmlspecialchars($mm['file']) ?></strong></li>
                                <?php endforeach; ?>
                              </ul>
                            <?php else: ?>
                              <em>—</em>
                            <?php endif; ?>
                          </td>
                          <td>
                            <?php
                              // Construir payload si existe (no obligatorio)
                              $fileOrderList = $fileOrderBySecLocal[$secKey] ?? null;
                              if (!$fileOrderList) {
                                $norm = function($s){
                                  $s = trim(mb_strtolower((string)$s));
                                  $s = strtr($s, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ú'=>'u','ñ'=>'n','Ñ'=>'n','ü'=>'u','Ü'=>'u','ï'=>'i','Ï'=>'i','ä'=>'a','Ä'=>'a','ë'=>'e','Ë'=>'e','ö'=>'o','Ö'=>'o']);
                                  $s = preg_replace('/\s+/', ' ', $s);
                                  return $s;
                                };
                                $secNorm = $norm($secKey);
                                foreach ($fileOrderBySecLocal as $k => $list) {
                                  if ($norm($k) === $secNorm) { $fileOrderList = $list; break; }
                                }
                              }
                              $payload = $fileOrderList ? base64_encode(json_encode($fileOrderList)) : '';
                              $showBtn = (($info['status'] ?? '') === 'diff');
                            ?>
                            <?php if ($showBtn): ?>
                              <button type="button" class="btn btn-sm btn-primary" data-action="apply-order" data-sec="<?= htmlspecialchars($secKey) ?>" data-order="<?= htmlspecialchars($payload) ?>">
                                Corregir N° lista
                              </button>
                            <?php else: ?>
                              <button type="button" class="btn btn-sm btn-secondary" disabled>—</button>
                            <?php endif; ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php else: ?>
                <em>Sin datos de orden.</em>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="accordion-item">
          <h2 class="accordion-header" id="headNuevos">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#secNuevos">Nuevos (<?= (int)$summary['new'] ?>)</button>
          </h2>
          <div id="secNuevos" class="accordion-collapse collapse show">
            <div class="accordion-body">
              <p class="text-muted small mb-2">Del archivo: estos nombres no existen en la base de datos (estudiantes activos).</p>
              <?php if (!empty($details['newStudents'])): ?>
              <div class="table-responsive">
                <table class="table table-sm table-striped table-hover table-bordered compare-table" data-section="nuevos"><thead class="table-light"><tr><th>Nombre</th><th>Sección</th><th>N° Lista (Archivo)</th><th>N° Lista (BD)</th></tr></thead><tbody>
                  <?php foreach ($details['newStudents'] as $r): ?>
                  <tr><td><?= htmlspecialchars($r['name']) ?></td><td><?= htmlspecialchars($r['seccion']) ?></td><td><?= isset($r['numArchivo']) && $r['numArchivo']!==null ? (int)$r['numArchivo'] : '' ?></td><td><?= isset($r['numero']) && $r['numero']!==null ? (int)$r['numero'] : '' ?></td></tr>
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
          <div id="secMovidos" class="accordion-collapse collapse">
            <div class="accordion-body">
              <p class="text-muted small mb-2">Mismo estudiante en la base de datos, pero con sección distinta entre la BD (Desde) y tu archivo (Hacia).</p>
              <?php if (!empty($details['movedStudents'])): ?>
              <div class="table-responsive">
                <table class="table table-sm table-striped table-hover table-bordered compare-table" data-section="movidos"><thead class="table-light"><tr><th>Nombre</th><th>Desde</th><th>Hacia</th><th>N° Lista (Archivo)</th><th>N° Lista (BD)</th></tr></thead><tbody>
                  <?php foreach ($details['movedStudents'] as $r): ?>
                  <tr><td><?= htmlspecialchars($r['name']) ?></td><td><?= htmlspecialchars($r['from']) ?></td><td><?= htmlspecialchars($r['to']) ?></td><td><?= isset($r['numArchivo']) && $r['numArchivo']!==null ? (int)$r['numArchivo'] : '' ?></td><td><?= !empty($r['list_from']) ? htmlspecialchars($r['list_from']) : '' ?></td></tr>
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
          <div id="secFaltantes" class="accordion-collapse collapse">
            <div class="accordion-body">
              <p class="text-muted small mb-2">Estudiantes activos que están en la base de datos, pero no aparecen en tu archivo para su misma sección.</p>
              <?php if (!empty($details['missingStudents'])): ?>
              <div class="table-responsive">
                <table class="table table-sm table-striped table-hover table-bordered compare-table" data-section="faltantes"><thead class="table-light"><tr><th>Nombre</th><th>Sección</th><th>N° Lista (Archivo)</th><th>N° Lista (BD)</th></tr></thead><tbody>
                  <?php foreach ($details['missingStudents'] as $r): ?>
                  <tr><td><?= htmlspecialchars($r['name']) ?></td><td><?= htmlspecialchars($r['seccion']) ?></td><td><?= isset($r['numArchivo']) && $r['numArchivo']!==null ? (int)$r['numArchivo'] : '' ?></td><td><?= isset($r['numero']) && $r['numero']!==null ? (int)$r['numero'] : '' ?></td></tr>
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
          <div id="secDup" class="accordion-collapse collapse">
            <div class="accordion-body">
              <p class="text-muted small mb-2">Registros repetidos dentro del archivo subido.</p>
              <?php if (!empty($details['duplicatesInFile'])): ?>
              <div class="table-responsive">
                <table class="table table-sm table-striped table-hover table-bordered compare-table" data-section="duplicados"><thead class="table-light"><tr><th>Nombre</th><th>Sección</th><th>N° Lista (Archivo)</th><th>N° Lista (BD)</th></tr></thead><tbody>
                  <?php foreach ($details['duplicatesInFile'] as $r): ?>
                  <tr><td><?= htmlspecialchars($r['name']) ?></td><td><?= htmlspecialchars($r['seccion']) ?></td><td><?= isset($r['numArchivo']) && $r['numArchivo']!==null ? (int)$r['numArchivo'] : '' ?></td><td></td></tr>
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
          <h2 class="accordion-header" id="headDupBD">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#secDupBD">Duplicados en BD (<?= (int)($summary['db_duplicates'] ?? 0) ?>)</button>
          </h2>
          <div id="secDupBD" class="accordion-collapse collapse">
            <div class="accordion-body">
              <p class="text-muted small mb-2">Estudiantes duplicados en la base de datos (misma clave: nombre canónico y sección). Revisa y depura la BD.</p>
              <?php if (!empty($details['dbDuplicates'])): ?>
              <div class="table-responsive">
                <table class="table table-sm table-striped table-hover table-bordered compare-table" data-section="duplicados_bd"><thead class="table-light"><tr><th>Nombre</th><th>Sección</th><th>N° Lista (BD)</th></tr></thead><tbody>
                  <?php foreach ($details['dbDuplicates'] as $r): ?>
                  <tr><td><?= htmlspecialchars($r['name']) ?></td><td><?= htmlspecialchars($r['seccion']) ?></td><td><?= htmlspecialchars($r['numeros_bd'] ?? '') ?></td></tr>
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
          <div id="secUnknown" class="accordion-collapse collapse">
            <div class="accordion-body">
              <p class="text-muted small mb-2">La sección indicada en el archivo no existe en la base de datos. Revisa nombres y acentos de las secciones.</p>
              <?php if (!empty($details['unknownSections'])): ?>
              <div class="table-responsive">
                <table class="table table-sm table-striped table-hover table-bordered compare-table" data-section="desconocidas"><thead class="table-light"><tr><th>Nombre</th><th>Sección en archivo</th><th>N° Lista (Archivo)</th><th>N° Lista (BD)</th></tr></thead><tbody>
                  <?php foreach ($details['unknownSections'] as $r): ?>
                  <tr><td><?= htmlspecialchars($r['name']) ?></td><td><?= htmlspecialchars($r['seccion']) ?></td><td><?= isset($r['numArchivo']) && $r['numArchivo']!==null ? (int)$r['numArchivo'] : '' ?></td><td></td></tr>
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
  
  <!-- Modal Aplicar Orden -->
  <div class="modal fade" id="modalApplyOrder" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Aplicar orden del archivo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
    <form action="../controller/compareStudentsController.php?action=apply_order" method="post">
          <div class="modal-body">
            <input type="hidden" name="sec" id="applyOrderSec" value="">
      <input type="hidden" name="order" id="applyOrderPayload" value="">
            <p>¿Deseas aplicar el orden del archivo para la sección <strong id="applyOrderSecLabel"></strong>? Esta acción ajustará el <em>Número de lista</em> en la base de datos siguiendo el orden del archivo. Los estudiantes no presentes en el archivo se moverán al final.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Aplicar</button>
          </div>
        </form>
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
          rows.push(['tipo','nombre','seccion','desde','hacia','num_lista_archivo','num_lista_bd']);
          const pushRow = (tipo, nombre, seccion, desde, hacia, numArchivo, numLista) => {
            rows.push([tipo||'', nombre||'', seccion||'', desde||'', hacia||'', (numArchivo??''), (numLista??'')]);
          };
          (d.newStudents||[]).forEach(r=> pushRow('Nuevo', r.name, r.seccion, '', '', r.numArchivo, r.numero));
          (d.movedStudents||[]).forEach(r=> pushRow('Movido', r.name, '', r.from, r.to, r.numArchivo, r.list_from));
          (d.missingStudents||[]).forEach(r=> pushRow('Faltante', r.name, r.seccion, '', '', r.numArchivo, r.numero));
          (d.duplicatesInFile||[]).forEach(r=> pushRow('Duplicado', r.name, r.seccion, '', '', r.numArchivo, ''));
          (d.unknownSections||[]).forEach(r=> pushRow('SeccionDesconocida', r.name, r.seccion, '', '', r.numArchivo, ''));
          (d.dbDuplicates||[]).forEach(r=> pushRow('DuplicadoBD', r.name, r.seccion, '', '', '', r.numeros_bd));

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
            if (window.bootstrap && bootstrap.Collapse) {
              const c = bootstrap.Collapse.getOrCreateInstance(el, {toggle:false});
              open ? c.show() : c.hide();
            } else {
              // Fallback: manipular clases directamente
              if (open) {
                el.classList.add('show');
              } else {
                el.classList.remove('show');
              }
              // Sync botón
              const id = el.getAttribute('id');
              document.querySelectorAll(`button.accordion-button[data-bs-target="#${id}"]`).forEach(btn=>{
                if (open) {
                  btn.classList.remove('collapsed');
                  btn.setAttribute('aria-expanded','true');
                } else {
                  btn.classList.add('collapsed');
                  btn.setAttribute('aria-expanded','false');
                }
              });
            }
          });
        }
        expandAllBtn?.addEventListener('click', ()=> setAll(true));
        collapseAllBtn?.addEventListener('click', ()=> setAll(false));

        // Control de clic en encabezados: usar Bootstrap si existe, si no, alternar clases manualmente
        document.querySelectorAll('#accordionDetalles button.accordion-button[data-bs-target]').forEach(btn=>{
          btn.addEventListener('click', (ev)=>{
            ev.preventDefault();
            ev.stopPropagation();
            const target = btn.getAttribute('data-bs-target');
            if (!target) return;
            const el = document.querySelector(target);
            if (!el) return;
            const isOpen = el.classList.contains('show');
            if (window.bootstrap && bootstrap.Collapse) {
              const c = bootstrap.Collapse.getOrCreateInstance(el, {toggle:false});
              isOpen ? c.hide() : c.show();
            } else {
              el.classList.toggle('show');
            }
            // Sync botón tras un pequeño delay si usa transición
            setTimeout(()=>{
              const nowOpen = el.classList.contains('show');
              if (nowOpen) {
                btn.classList.remove('collapsed');
                btn.setAttribute('aria-expanded','true');
              } else {
                btn.classList.add('collapsed');
                btn.setAttribute('aria-expanded','false');
              }
            }, 50);
          });
        });

        // Aplicar orden por sección (modal)
        const applyButtons = document.querySelectorAll('button[data-action="apply-order"]');
        const modalEl = document.getElementById('modalApplyOrder');
        const inputSec = document.getElementById('applyOrderSec');
    const labelSec = document.getElementById('applyOrderSecLabel');
    const inputPayload = document.getElementById('applyOrderPayload');
        applyButtons.forEach(btn => {
          btn.addEventListener('click', ()=>{
            const sec = btn.getAttribute('data-sec') || '';
            const payload = btn.getAttribute('data-order') || '';
            inputSec.value = sec;
            labelSec.textContent = sec;
            inputPayload.value = payload; // si está vacío, el backend usará la sesión
            if (window.bootstrap && bootstrap.Modal) {
              bootstrap.Modal.getOrCreateInstance(modalEl).show();
            } else {
              // Fallback: confirm nativo
              if (confirm('Aplicar orden del archivo para la sección ' + sec + '?')) {
                // crear y enviar form temporal
                const f = document.createElement('form');
                f.method = 'post';
                f.action = '../controller/compareStudentsController.php?action=apply_order';
                const i = document.createElement('input');
                i.type = 'hidden'; i.name = 'sec'; i.value = sec;
                f.appendChild(i);
        const j = document.createElement('input');
        j.type = 'hidden'; j.name = 'order'; j.value = payload;
        f.appendChild(j);
                document.body.appendChild(f);
                f.submit();
              }
            }
          });
        });
      </script>
    <?php endif; ?>
  <?php endif; ?>
</div>

<?php require_once "../view/footer.php"; ?>
