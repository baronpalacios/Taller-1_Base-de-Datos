<?php
$pageTitle  = 'Perfil Enfermera';
$activeMenu = 'enfermeras';
$breadcrumb = 'Enfermeras / ' . htmlspecialchars($enfermera['nombre']);
ob_start();
$tipos = ['auxiliar'=>['label'=>'Auxiliar','color'=>'secondary'],'asistente'=>['label'=>'Asistente','color'=>'info'],'jefe'=>['label'=>'Jefe de Enfermería','color'=>'warning']];
$tipo = $tipos[$enfermera['tipo']] ?? ['label'=>$enfermera['tipo'],'color'=>'secondary'];
?>
<div class="page-header d-flex align-items-center justify-content-between">
  <div>
    <h1><i class="bi bi-person-heart text-primary me-2"></i><?= htmlspecialchars($enfermera['nombre']) ?></h1>
    <span class="badge bg-<?= $tipo['color'] ?>"><?= $tipo['label'] ?></span>
  </div>
  <?php if ($_SESSION['user']['rol'] === 'admin'): ?>
  <a href="<?= APP_URL ?>/enfermeras/<?= urlencode($enfermera['identificacion']) ?>/editar" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Editar</a>
  <?php endif; ?>
</div>

<div class="row g-3">
  <div class="col-md-6">
    <div class="card shadow-sm h-100">
      <div class="card-header"><h6 class="mb-0"><i class="bi bi-person me-2"></i>Información Personal</h6></div>
      <div class="card-body">
        <table class="table table-sm table-borderless mb-0">
          <tr><td class="text-muted" style="width:40%">Identificación</td><td><strong><?= htmlspecialchars($enfermera['identificacion']) ?></strong></td></tr>
          <tr><td class="text-muted">Nombre</td><td><?= htmlspecialchars($enfermera['nombre']) ?></td></tr>
          <tr><td class="text-muted">Teléfono</td><td><?= htmlspecialchars($enfermera['telefono'] ?? '—') ?></td></tr>
          <tr><td class="text-muted">Dirección</td><td><?= htmlspecialchars($enfermera['direccion'] ?? '—') ?></td></tr>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card shadow-sm h-100">
      <div class="card-header"><h6 class="mb-0"><i class="bi bi-briefcase me-2"></i>Información Laboral</h6></div>
      <div class="card-body">
        <table class="table table-sm table-borderless mb-0">
          <tr><td class="text-muted" style="width:40%">Cargo</td><td><?= htmlspecialchars($enfermera['cargo']) ?></td></tr>
          <tr><td class="text-muted">Tipo</td><td><span class="badge bg-<?= $tipo['color'] ?>"><?= $tipo['label'] ?></span></td></tr>
          <tr><td class="text-muted">Área</td><td><?= htmlspecialchars($enfermera['nombre_area'] ?? '—') ?></td></tr>
          <tr><td class="text-muted">Experiencia</td><td><?= $enfermera['anios_experiencia'] ?> año(s)</td></tr>
          <tr><td class="text-muted">Fecha ingreso</td><td><?= htmlspecialchars($enfermera['fecha_ingreso']) ?></td></tr>
          <tr><td class="text-muted">Salario</td><td>$ <?= number_format($enfermera['salario'], 2) ?></td></tr>
        </table>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-header"><h6 class="mb-0"><i class="bi bi-star me-2"></i>Habilidades</h6></div>
      <div class="card-body">
        <?php if (empty($habilidades)): ?>
        <p class="text-muted mb-0">No hay habilidades registradas.</p>
        <?php else: ?>
        <div class="d-flex flex-wrap gap-2">
          <?php foreach ($habilidades as $h): ?>
          <span class="badge" style="background:#e8f0fa;color:#1a6fc4;font-size:.85rem;padding:.4em .8em"><?= htmlspecialchars($h) ?></span>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<div class="mt-3"><a href="<?= APP_URL ?>/enfermeras" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a></div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
