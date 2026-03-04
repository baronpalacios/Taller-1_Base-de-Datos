<?php
$pageTitle  = 'Camas';
$activeMenu = 'camas';
$breadcrumb = 'Hospitalización / Camas';
$successMsgs = ['1'=>'Cama creada correctamente.','2'=>'Cama asignada correctamente.','3'=>'Cama liberada correctamente.','4'=>'Cama eliminada.'];
$flash = null;
if (isset($_GET['success']) && isset($successMsgs[$_GET['success']])) $flash = ['type'=>'success','msg'=>$successMsgs[$_GET['success']]];
ob_start();
?>
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div><h1><i class="bi bi-hospital text-primary me-2"></i>Hospitalización / Camas</h1><p>Estado y asignación de camas</p></div>
  <?php if ($_SESSION['user']['rol'] === 'admin'): ?>
  <a href="<?= APP_URL ?>/camas/crear" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nueva Cama</a>
  <?php endif; ?>
</div>

<!-- Resumen por área -->
<div class="row g-3 mb-4">
  <?php foreach ($resumen as $r): ?>
  <div class="col-md-3 col-6">
    <div class="card border-0 shadow-sm text-center p-3">
      <div class="fw-bold mb-1"><?= htmlspecialchars($r['nombre_area']) ?></div>
      <div class="display-6 fw-bold text-primary"><?= $r['pct_ocupacion'] ?? 0 ?>%</div>
      <div class="text-muted small">ocupación</div>
      <div class="d-flex justify-content-center gap-3 mt-2" style="font-size:.8rem">
        <span class="text-success"><i class="bi bi-circle-fill"></i> <?= $r['libres'] ?> libre(s)</span>
        <span class="text-danger"><i class="bi bi-circle-fill"></i> <?= $r['ocupadas'] ?> ocup.</span>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Filtros -->
<div class="card mb-3">
  <div class="card-body p-3">
    <form method="GET" class="d-flex gap-2 flex-wrap">
      <select name="area" class="form-select" style="max-width:200px">
        <option value="">Todas las áreas</option>
        <?php foreach ($areas as $a): ?>
        <option value="<?= $a['cod_area'] ?>" <?= $areaFiltro === $a['cod_area'] ? 'selected' : '' ?>><?= htmlspecialchars($a['nombre_area']) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="estado" class="form-select" style="max-width:160px">
        <option value="">Todos los estados</option>
        <option value="libre"   <?= $estadoFiltro === 'libre'    ? 'selected' : '' ?>>Libre</option>
        <option value="ocupada" <?= $estadoFiltro === 'ocupada'  ? 'selected' : '' ?>>Ocupada</option>
      </select>
      <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel"></i> Filtrar</button>
      <a href="<?= APP_URL ?>/camas" class="btn btn-outline-secondary">Limpiar</a>
    </form>
  </div>
</div>

<!-- Tabla de camas -->
<div class="card">
  <div class="table-wrapper">
    <table class="table table-hover">
      <thead><tr><th>Cama #</th><th>Área</th><th>Estado</th><th>Paciente</th><th>Asignado desde</th><th>Duración</th><th class="text-end">Acciones</th></tr></thead>
      <tbody>
        <?php if (empty($camas)): ?>
        <tr><td colspan="7" class="text-center py-4 text-muted">No se encontraron camas</td></tr>
        <?php else: foreach ($camas as $c): ?>
        <tr>
          <td><span class="badge bg-secondary fs-6"><?= $c['nro_cama'] ?></span></td>
          <td><?= htmlspecialchars($c['nombre_area']) ?></td>
          <td>
            <?php if ($c['estado'] === 'libre'): ?>
            <span class="badge bg-success">Libre</span>
            <?php else: ?>
            <span class="badge bg-danger">Ocupada</span>
            <?php endif; ?>
          </td>
          <td><?= $c['nombre_paciente'] ? htmlspecialchars($c['nombre_paciente']) : '<span class="text-muted">—</span>' ?></td>
          <td><?= $c['fecha_asignacion'] ? htmlspecialchars($c['fecha_asignacion']) : '—' ?></td>
          <td><?= $c['duracion_dias'] ? $c['duracion_dias'].' día(s)' : '—' ?></td>
          <td>
            <div class="d-flex gap-1 justify-content-end">
              <?php if ($c['estado'] === 'libre'): ?>
              <a href="<?= APP_URL ?>/camas/<?= $c['nro_cama'] ?>/<?= urlencode($c['cod_area']) ?>/asignar" class="btn btn-sm btn-outline-primary btn-action" title="Asignar paciente"><i class="bi bi-person-plus-fill"></i></a>
              <?php else: ?>
              <form method="POST" action="<?= APP_URL ?>/camas/<?= $c['nro_cama'] ?>/<?= urlencode($c['cod_area']) ?>/liberar" class="d-inline">
                <input type="hidden" name="_token" value="<?= $csrf ?>">
                <button class="btn btn-sm btn-outline-success btn-action" data-confirm="¿Liberar la cama #<?= $c['nro_cama'] ?>?" title="Liberar"><i class="bi bi-door-open-fill"></i></button>
              </form>
              <?php endif; ?>
              <?php if ($_SESSION['user']['rol'] === 'admin'): ?>
              <form method="POST" action="<?= APP_URL ?>/camas/<?= $c['nro_cama'] ?>/<?= urlencode($c['cod_area']) ?>/eliminar" class="d-inline">
                <input type="hidden" name="_token" value="<?= $csrf ?>">
                <button class="btn btn-sm btn-outline-danger btn-action" data-confirm="¿Eliminar la cama #<?= $c['nro_cama'] ?>?" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
              </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer text-muted small p-3">Total: <strong><?= count($camas) ?></strong> cama(s)</div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
