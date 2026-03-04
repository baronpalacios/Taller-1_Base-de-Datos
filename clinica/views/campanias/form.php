<?php
$isEdit     = !empty($campania['cod_campania']);
$pageTitle  = $isEdit ? 'Editar Campaña' : 'Nueva Campaña';
$activeMenu = 'campanias';
$breadcrumb = 'Campañas / ' . ($isEdit ? 'Editar' : 'Nueva');
ob_start();
?>
<div class="page-header"><h1><i class="bi bi-megaphone-fill text-primary me-2"></i><?= $pageTitle ?></h1></div>
<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <form method="POST" action="<?= APP_URL ?>/campanias/<?= $isEdit ? urlencode($campania['cod_campania']).'/editar' : 'crear' ?>">
          <input type="hidden" name="_token" value="<?= $csrf ?>">
          <?php if (!$isEdit): ?>
          <div class="mb-3">
            <label class="form-label fw-semibold">Código <span class="text-danger">*</span></label>
            <input type="text" name="cod_campania" class="form-control text-uppercase" required maxlength="20" value="<?= htmlspecialchars($campania['cod_campania'] ?? '') ?>" placeholder="Ej: CAMP2024_01">
          </div>
          <?php else: ?>
          <div class="mb-3">
            <label class="form-label fw-semibold">Código</label>
            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($campania['cod_campania']) ?>" disabled>
          </div>
          <?php endif; ?>
          <div class="mb-3">
            <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($campania['nombre'] ?? '') ?>" placeholder="Nombre de la campaña">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Objetivo</label>
            <textarea name="objetivo" class="form-control" rows="3" placeholder="Descripción del objetivo..."><?= htmlspecialchars($campania['objetivo'] ?? '') ?></textarea>
          </div>
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Fecha de realización <span class="text-danger">*</span></label>
              <input type="date" name="fecha_realizacion" class="form-control" required value="<?= htmlspecialchars($campania['fecha_realizacion'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Médico responsable <span class="text-danger">*</span></label>
              <select name="id_medico_resp" class="form-select" required>
                <option value="">Seleccionar...</option>
                <?php foreach ($medicos as $m): ?>
                <option value="<?= $m['identificacion'] ?>" <?= ($campania['id_medico_resp'] ?? '') === $m['identificacion'] ? 'selected' : '' ?>><?= htmlspecialchars($m['nombre']) ?> — <?= htmlspecialchars($m['especialidad']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Actualizar' : 'Guardar' ?></button>
            <a href="<?= APP_URL ?>/campanias" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
