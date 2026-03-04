<?php
$isEdit     = !empty($area['cod_area']);
$pageTitle  = $isEdit ? 'Editar Área' : 'Nueva Área';
$activeMenu = 'areas';
$breadcrumb = 'Áreas / ' . ($isEdit ? 'Editar' : 'Nueva');
ob_start();
?>
<div class="page-header">
  <h1><i class="bi bi-building text-primary me-2"></i><?= $pageTitle ?></h1>
  <p><?= $isEdit ? 'Actualizar información del área' : 'Registrar nuevo departamento' ?></p>
</div>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <?php if ($error): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= $error ?></div><?php endif; ?>
        <form method="POST" action="<?= APP_URL ?>/areas/<?= $isEdit ? urlencode($area['cod_area']).'/editar' : 'crear' ?>">
          <input type="hidden" name="_token" value="<?= $csrf ?>">

          <?php if (!$isEdit): ?>
          <div class="mb-3">
            <label class="form-label fw-semibold">Código <span class="text-danger">*</span></label>
            <input type="text" name="cod_area" class="form-control text-uppercase" required maxlength="20"
                   value="<?= htmlspecialchars($area['cod_area'] ?? '') ?>"
                   placeholder="Ej: URG, PED, CAR">
            <div class="form-text">Solo letras mayúsculas, números y guión bajo. Máx 20 caracteres.</div>
          </div>
          <?php else: ?>
          <div class="mb-3">
            <label class="form-label fw-semibold">Código</label>
            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($area['cod_area']) ?>" disabled>
          </div>
          <?php endif; ?>

          <div class="mb-4">
            <label class="form-label fw-semibold">Nombre del Área <span class="text-danger">*</span></label>
            <input type="text" name="nombre_area" class="form-control" required maxlength="100"
                   value="<?= htmlspecialchars($area['nombre_area'] ?? '') ?>"
                   placeholder="Ej: Urgencias, Pediatría, Cardiología">
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Actualizar' : 'Guardar' ?></button>
            <a href="<?= APP_URL ?>/areas" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
