<?php
require_once "../view/header.php";

// Carpeta de respaldos
$backupDir = __DIR__ . '/../backups/';
$files = [];

if (is_dir($backupDir)) {
    foreach (scandir($backupDir) as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            $files[] = [
                'name' => $file,
                'time' => filemtime($backupDir . $file)
            ];
        }
    }
}

// Ordenar por fecha DESC
usort($files, fn($a, $b) => $b['time'] <=> $a['time']);
?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>
            <i class="bi bi-hdd-stack-fill"></i>
            Copias de seguridad
        </h3>

        <a href="/app/controller/BackupController.php?action=create"
           class="btn btn-success">
            <i class="bi bi-plus-circle"></i>
            Crear respaldo
        </a>
    </div>

    <?php if (empty($files)): ?>
        <div class="alert alert-info">
            No hay copias de seguridad registradas.
        </div>
    <?php else: ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Archivo</th>
                    <th>Fecha</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $i => $f): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($f['name']) ?></td>
                        <td><?= date('d/m/Y H:i', $f['time']) ?></td>
                        <td class="text-center">

                            <a href="/app/controller/BackupController.php?action=restore&file=<?= urlencode($f['name']) ?>"
                               class="btn btn-warning btn-sm"
                               onclick="return confirm('⚠️ Esto reemplazará TODOS los datos actuales.\n¿Deseás continuar?')">
                                <i class="bi bi-arrow-clockwise"></i>
                                Restaurar
                            </a>

                            <a href="/app/controller/BackupController.php?action=delete&file=<?= urlencode($f['name']) ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('¿Eliminar este respaldo?')">
                                <i class="bi bi-trash"></i>
                                Eliminar
                            </a>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php endif; ?>
</div>

<?php if (isset($_SESSION['status'], $_SESSION['message'])): ?>
<div class="modal fade" id="statusModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header <?= $_SESSION['status'] === 'success' ? 'bg-success' : 'bg-danger' ?> text-white">
        <h5 class="modal-title">
          <?= $_SESSION['status'] === 'success' ? 'Éxito' : 'Error' ?>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <?= htmlspecialchars($_SESSION['message']) ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>
<?php
unset($_SESSION['status'], $_SESSION['message']);
endif;
?>

<?php require_once "../view/footer.php"; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modalEl = document.getElementById('statusModal');
    if (modalEl) {
        new bootstrap.Modal(modalEl).show();
    }
});
</script>
