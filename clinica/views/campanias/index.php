<?php
$pageTitle  = 'Campañas';
$activeMenu = 'campanias';
$breadcrumb = 'Campañas de Prevención';
$successMsgs = ['1'=>'Campaña creada correctamente.','2'=>'Campaña actualizada.','3'=>'Campaña eliminada.'];
$flash = null;
if (isset($_GET['success']) && isset($successMsgs[$_GET['success']])) $flash = ['type'=>'success','msg'=>$successMsgs[$_GET['success']]];
ob_start();
?>
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div><h1><i class="bi bi-megaphone-fill text-primary me-2"></i>Campañas de Prevención</h1><p>Gestión de campañas y participantes</p></div>
  <?php if ($_SESSION['user']['rol'] === 'admin'): ?>
  <a href="<?= APP_URL ?>/campanias/crear" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nueva Campaña</a>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-header p-3">
    <form method="GET" class="search-box" style="max-width:360px">
      <i class="bi bi-search"></i>
      <input type="text" name="q" class="form-control" placeholder="Buscar campaña..." value="<?= htmlspecialchars($search) ?>">
    </form>
  </div>
  <div class="table-wrapper">
    <table class="table table-hover">
      <thead><tr><th>Código</th><th>Nombre</th><th>Médico Resp.</th><th>Fecha</th><th>Participantes</th><th class="text-end">Acciones</th></tr></thead>
      <tbody>
        <?php if (empty($campanias)): ?>
        <tr><td colspan="6" class="text-center py-4 text-muted">No hay campañas registradas</td></tr>
        <?php else: foreach ($campanias as $c): ?>
        <tr>
          <td><span class="badge bg-light text-secondary"><?= htmlspecialchars($c['cod_campania']) ?></span></td>
          <td>
            <strong><?= htmlspecialchars($c['nombre']) ?></strong>
            <?php if ($c['objetivo']): ?><div class="text-muted" style="font-size:.75rem"><?= htmlspecialchars(substr($c['objetivo'],0,60)) ?>...</div><?php endif; ?>
          </td>
          <td><?= htmlspecialchars($c['nombre_medico']) ?></td>
          <td><?= htmlspecialchars($c['fecha_realizacion']) ?></td>
          <td><span class="badge bg-info"><?= $c['total_participantes'] ?> paciente(s)</span></td>
          <td>
            <div class="d-flex gap-1 justify-content-end">
              <a href="<?= APP_URL ?>/campanias/<?= urlencode($c['cod_campania']) ?>" class="btn btn-sm btn-outline-info btn-action" title="Ver detalle"><i class="bi bi-eye-fill"></i></a>
              <?php if ($_SESSION['user']['rol'] === 'admin'): ?>
              <a href="<?= APP_URL ?>/campanias/<?= urlencode($c['cod_campania']) ?>/editar" class="btn btn-sm btn-outline-primary btn-action" title="Editar"><i class="bi bi-pencil-fill"></i></a>
              <form method="POST" action="<?= APP_URL ?>/campanias/<?= urlencode($c['cod_campania']) ?>/eliminar" class="d-inline">
                <input type="hidden" name="_token" value="<?= $csrf ?>">
                <button class="btn btn-sm btn-outline-danger btn-action" data-confirm="¿Eliminar la campaña '<?= htmlspecialchars($c['nombre']) ?>'?"><i class="bi bi-trash-fill"></i></button>
              </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer text-muted small p-3">Total: <strong><?= count($campanias) ?></strong> campaña(s)</div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
