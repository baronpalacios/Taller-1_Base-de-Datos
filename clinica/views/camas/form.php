<?php
$pageTitle  = 'Nueva Cama';
$activeMenu = 'camas';
$breadcrumb = 'Camas / Nueva';
ob_start();
?>
<div class="page-header"><h1><i class="bi bi-hospital text-primary me-2"></i>Nueva Cama</h1></div>
<div class="row justify-content-center">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <form method="POST" action="<?= APP_URL ?>/camas/crear">
          <input type="hidden" name="_token" value="<?= $csrf ?>">
          <div class="mb-3">
            <label class="form-label fw-semibold">Número de cama <span class="text-danger">*</span></label>
            <input type="number" name="nro_cama" class="form-control" required min="1" value="<?= $cama['nro_cama'] ?? '' ?>">
          </div>
          <div class="mb-4">
            <label class="form-label fw-semibold">Área <span class="text-danger">*</span></label>
            <select name="cod_area" class="form-select" required>
              <option value="">Seleccionar...</option>
              <?php foreach ($areas as $a): ?>
              <option value="<?= $a['cod_area'] ?>" <?= ($cama['cod_area'] ?? '') === $a['cod_area'] ? 'selected' : '' ?>><?= htmlspecialchars($a['nombre_area']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Guardar</button>
            <a href="<?= APP_URL ?>/camas" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
