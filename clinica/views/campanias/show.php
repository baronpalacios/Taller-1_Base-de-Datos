<?php
$pageTitle  = 'Detalle Campaña';
$activeMenu = 'campanias';
$breadcrumb = 'Campañas / ' . htmlspecialchars($campania['nombre']);
ob_start();
?>
<div class="page-header d-flex align-items-center justify-content-between">
  <div>
    <h1><i class="bi bi-megaphone-fill text-primary me-2"></i><?= htmlspecialchars($campania['nombre']) ?></h1>
    <span class="badge bg-light text-secondary"><?= htmlspecialchars($campania['cod_campania']) ?></span>
  </div>
  <?php if ($_SESSION['user']['rol'] === 'admin'): ?>
  <a href="<?= APP_URL ?>/campanias/<?= urlencode($campania['cod_campania']) ?>/editar" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Editar</a>
  <?php endif; ?>
</div>

<div class="row g-3">
  <div class="col-md-5">
    <div class="card shadow-sm h-100">
      <div class="card-header"><h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información</h6></div>
      <div class="card-body">
        <table class="table table-sm table-borderless mb-0">
          <tr><td class="text-muted" style="width:40%">Fecha</td><td><?= htmlspecialchars($campania['fecha_realizacion']) ?></td></tr>
          <tr><td class="text-muted">Médico resp.</td><td><?= htmlspecialchars($campania['nombre_medico']) ?></td></tr>
          <tr><td class="text-muted">Objetivo</td><td><?= nl2br(htmlspecialchars($campania['objetivo'] ?? '—')) ?></td></tr>
        </table>
      </div>
    </div>
  </div>
  <div class="col-md-7">
    <div class="card shadow-sm h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-people me-2"></i>Participantes (<?= count($participantes) ?>)</h6>
      </div>
      <div class="card-body p-0">
        <!-- Agregar participante -->
        <?php if (in_array($_SESSION['user']['rol'], ['admin','recepcionista'])): ?>
        <div class="p-3 border-bottom">
          <form method="POST" action="<?= APP_URL ?>/campanias/<?= urlencode($campania['cod_campania']) ?>/participante/agregar" class="d-flex gap-2">
            <input type="hidden" name="_token" value="<?= $csrf ?>">
            <select name="id_paciente" class="form-select form-select-sm" required>
              <option value="">Agregar paciente...</option>
              <?php foreach ($pacientes as $p):
                    $yaParticipa = in_array($p['identificacion'], array_column($participantes, 'id_paciente')); ?>
              <?php if (!$yaParticipa): ?>
              <option value="<?= $p['identificacion'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
              <?php endif; ?>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i></button>
          </form>
        </div>
        <?php endif; ?>
        <!-- Lista -->
        <div style="max-height:300px;overflow-y:auto">
          <?php if (empty($participantes)): ?>
          <p class="text-center text-muted py-4">Sin participantes aún.</p>
          <?php else: foreach ($participantes as $p): ?>
          <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
            <div>
              <strong><?= htmlspecialchars($p['nombre']) ?></strong>
              <div class="text-muted" style="font-size:.75rem">EPS: <?= htmlspecialchars($p['eps'] ?? '—') ?> · NSS: <?= htmlspecialchars($p['nss']) ?></div>
            </div>
            <?php if (in_array($_SESSION['user']['rol'], ['admin','recepcionista'])): ?>
            <form method="POST" action="<?= APP_URL ?>/campanias/<?= urlencode($campania['cod_campania']) ?>/participante/quitar" class="d-inline">
              <input type="hidden" name="_token" value="<?= $csrf ?>">
              <input type="hidden" name="id_paciente" value="<?= $p['id_paciente'] ?>">
              <button class="btn btn-sm btn-outline-danger" data-confirm="¿Quitar a <?= htmlspecialchars($p['nombre']) ?> de esta campaña?"><i class="bi bi-x-lg"></i></button>
            </form>
            <?php endif; ?>
          </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="mt-3"><a href="<?= APP_URL ?>/campanias" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a></div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
