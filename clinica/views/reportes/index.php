<?php
$pageTitle  = 'Reportes';
$activeMenu = 'reportes';
$breadcrumb = 'Reportes';
ob_start();
?>
<div class="page-header"><h1><i class="bi bi-file-earmark-bar-graph-fill text-primary me-2"></i>Reportes</h1><p>Informes y exportaciones del sistema</p></div>
<div class="row g-3">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body p-4 text-center">
        <div class="mb-3" style="font-size:3rem;color:#1a6fc4"><i class="bi bi-calendar-week"></i></div>
        <h5 class="fw-bold">Reporte de Citas</h5>
        <p class="text-muted small">Ver citas por rango de fechas y exportar a CSV</p>
        <a href="<?= APP_URL ?>/reportes/citas" class="btn btn-primary"><i class="bi bi-arrow-right me-1"></i>Ver Reporte</a>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card">
      <div class="card-body p-4 text-center">
        <div class="mb-3" style="font-size:3rem;color:#059669"><i class="bi bi-people"></i></div>
        <h5 class="fw-bold">Nuevos Pacientes</h5>
        <p class="text-muted small">Ver pacientes registrados por periodo</p>
        <a href="<?= APP_URL ?>/reportes/pacientes" class="btn btn-success"><i class="bi bi-arrow-right me-1"></i>Ver Reporte</a>
      </div>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
