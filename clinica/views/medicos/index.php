<?php
$pageTitle  = 'Médicos';
$activeMenu = 'medicos';
$breadcrumb = 'Médicos';
$successMsgs = ['1'=>'Médico registrado.','2'=>'Médico actualizado.','3'=>'Médico eliminado.'];
$flash = null;
if (isset($_GET['success']) && isset($successMsgs[$_GET['success']])) $flash = ['type'=>'success','msg'=>$successMsgs[$_GET['success']]];
ob_start();
?>
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div><h1><i class="bi bi-person-badge-fill text-primary me-2"></i>Médicos</h1><p>Gestión del cuerpo médico</p></div>
  <?php if ($_SESSION['user']['rol'] === 'admin'): ?>
  <a href="<?= APP_URL ?>/medicos/crear" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nuevo Médico</a>
  <?php endif; ?>
</div>
<div class="card">
  <div class="card-header p-3">
    <div class="search-box" style="max-width:360px">
      <i class="bi bi-search"></i>
      <input type="text" id="liveSearch" class="form-control" placeholder="Buscar por nombre o especialidad..." value="<?= htmlspecialchars($search) ?>">
    </div>
  </div>
  <div class="table-wrapper">
    <table class="table table-hover">
      <thead><tr><th>ID</th><th>Nombre</th><th>Especialidad</th><th>Licencia</th><th>Universidad</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
      <tbody>
        <?php if (empty($medicos)): ?>
        <tr><td colspan="7" class="text-center py-4 text-muted">No se encontraron médicos</td></tr>
        <?php else: foreach ($medicos as $m): ?>
        <tr>
          <td><span class="badge bg-light text-secondary"><?= htmlspecialchars($m['identificacion']) ?></span></td>
          <td><strong><?= htmlspecialchars($m['nombre']) ?></strong><div class="text-muted" style="font-size:.75rem"><?= htmlspecialchars($m['cargo']) ?></div></td>
          <td><span class="badge" style="background:#e8f0fa;color:#1a6fc4"><?= htmlspecialchars($m['especialidad']) ?></span></td>
          <td><code style="font-size:.8rem"><?= htmlspecialchars($m['nro_licencia']) ?></code></td>
          <td><?= htmlspecialchars($m['universidad'] ?? '—') ?></td>
          <td><?= $m['disponible'] ? '<span class="badge bg-success">Disponible</span>' : '<span class="badge bg-secondary">No disponible</span>' ?></td>
          <td>
            <div class="d-flex gap-1 justify-content-end">
              <?php if ($_SESSION['user']['rol'] === 'admin'): ?>
              <a href="<?= APP_URL ?>/medicos/<?= urlencode($m['identificacion']) ?>/editar" class="btn btn-sm btn-outline-primary btn-action" title="Editar"><i class="bi bi-pencil-fill"></i></a>
              <form method="POST" action="<?= APP_URL ?>/medicos/<?= urlencode($m['identificacion']) ?>/eliminar" class="d-inline">
                <input type="hidden" name="_token" value="<?= $csrf ?>">
                <button class="btn btn-sm btn-outline-danger btn-action" data-confirm="¿Eliminar al Dr. <?= htmlspecialchars($m['nombre']) ?>?" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
              </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer text-muted small p-3">Total: <strong><?= count($medicos) ?></strong> médico(s)</div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
