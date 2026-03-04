<?php
$pageTitle  = 'Reporte de Citas';
$activeMenu = 'reportes';
$breadcrumb = 'Reporte Citas';
ob_start();
?>
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div><h1><i class="bi bi-calendar-week text-primary me-2"></i>Reporte de Citas</h1></div>
  <div class="d-flex gap-2">
    <a href="<?= APP_URL ?>/reportes/citas?inicio=<?= $inicio ?>&fin=<?= $fin ?>&export=csv" class="btn btn-success btn-sm"><i class="bi bi-download me-1"></i>Exportar CSV</a>
    <a href="<?= APP_URL ?>/reportes" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Volver</a>
  </div>
</div>
<div class="card mb-3">
  <div class="card-body p-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-sm-5"><label class="form-label mb-1 small fw-semibold">Desde</label><input type="date" name="inicio" class="form-control form-control-sm" value="<?= $inicio ?>"></div>
      <div class="col-sm-5"><label class="form-label mb-1 small fw-semibold">Hasta</label><input type="date" name="fin" class="form-control form-control-sm" value="<?= $fin ?>"></div>
      <div class="col-auto"><button type="submit" class="btn btn-primary btn-sm">Filtrar</button></div>
    </form>
  </div>
</div>
<div class="card">
  <div class="table-wrapper">
    <table class="table table-hover">
      <thead><tr><th>#</th><th>Fecha</th><th>Hora</th><th>Paciente</th><th>Medico</th><th>Especialidad</th><th>Estado</th><th>Motivo</th></tr></thead>
      <tbody>
        <?php if (empty($citas)): ?>
        <tr><td colspan="8" class="text-center py-4 text-muted">No hay datos en el rango seleccionado</td></tr>
        <?php else: foreach ($citas as $c): ?>
        <tr>
          <td><?= $c['id_cita'] ?></td>
          <td><?= date('d/m/Y',strtotime($c['fecha_cita'])) ?></td>
          <td><?= substr($c['hora_cita'],0,5) ?></td>
          <td><?= htmlspecialchars($c['nombre_paciente']) ?></td>
          <td><?= htmlspecialchars($c['nombre_medico']) ?></td>
          <td><?= htmlspecialchars($c['especialidad']) ?></td>
          <td><span class="badge-<?= $c['estado'] ?>"><?= ucfirst($c['estado']) ?></span></td>
          <td><?= htmlspecialchars($c['motivo']??'--') ?></td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer text-muted small p-3">Total: <strong><?= count($citas) ?></strong> registro(s)</div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
