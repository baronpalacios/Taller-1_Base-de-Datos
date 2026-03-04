<?php
$pageTitle  = 'Reporte Pacientes';
$activeMenu = 'reportes';
$breadcrumb = 'Reporte Pacientes';
ob_start();
?>
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div><h1><i class="bi bi-people text-success me-2"></i>Nuevos Pacientes</h1></div>
  <div class="d-flex gap-2">
    <a href="<?= APP_URL ?>/reportes/pacientes?inicio=<?= $inicio ?>&fin=<?= $fin ?>&export=csv" class="btn btn-success btn-sm"><i class="bi bi-download me-1"></i>Exportar CSV</a>
    <a href="<?= APP_URL ?>/reportes" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Volver</a>
  </div>
</div>
<div class="card mb-3">
  <div class="card-body p-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-sm-5"><label class="form-label mb-1 small fw-semibold">Desde</label><input type="date" name="inicio" class="form-control form-control-sm" value="<?= $inicio ?>"></div>
      <div class="col-sm-5"><label class="form-label mb-1 small fw-semibold">Hasta</label><input type="date" name="fin" class="form-control form-control-sm" value="<?= $fin ?>"></div>
      <div class="col-auto"><button type="submit" class="btn btn-success btn-sm">Filtrar</button></div>
    </form>
  </div>
</div>
<div class="card">
  <div class="table-wrapper">
    <table class="table table-hover">
      <thead><tr><th>ID</th><th>Nombre</th><th>Edad</th><th>EPS</th><th>Labor</th><th>Fecha Afiliacion</th><th>Telefono</th></tr></thead>
      <tbody>
        <?php if (empty($pacientes)): ?>
        <tr><td colspan="7" class="text-center py-4 text-muted">No hay datos en el rango seleccionado</td></tr>
        <?php else: foreach ($pacientes as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['identificacion']) ?></td>
          <td><?= htmlspecialchars($p['nombre']) ?></td>
          <td><?= $p['edad']??'--' ?> anos</td>
          <td><?= htmlspecialchars($p['eps']??'--') ?></td>
          <td><?= htmlspecialchars($p['labor']??'--') ?></td>
          <td><?= $p['fecha_afiliacion']??'--' ?></td>
          <td><?= htmlspecialchars($p['telefono']??'--') ?></td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer text-muted small p-3">Total: <strong><?= count($pacientes) ?></strong> registro(s)</div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
