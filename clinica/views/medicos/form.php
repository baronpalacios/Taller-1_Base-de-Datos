<?php
$isEdit = !empty($medico['identificacion']);
$pageTitle  = $isEdit ? 'Editar Médico' : 'Nuevo Médico';
$activeMenu = 'medicos'; $breadcrumb = $pageTitle;
ob_start();
?>
<div class="page-header"><h1><i class="bi bi-person-badge-fill text-primary me-2"></i><?= $isEdit ? 'Editar' : 'Nuevo' ?> Médico</h1></div>
<div class="card" style="max-width:800px">
  <div class="card-body p-4">
    <?php if (!empty($error)): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?></div><?php endif; ?>
    <form method="POST" action="<?= $isEdit ? APP_URL.'/medicos/'.urlencode($medico['identificacion']).'/editar' : APP_URL.'/medicos/crear' ?>">
      <input type="hidden" name="_token" value="<?= $csrf ?>">
      <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Datos Personales</h6>
      <div class="row g-3 mb-4">
        <?php if (!$isEdit): ?>
        <div class="col-md-6"><label class="form-label">Identificación *</label><input type="text" name="identificacion" class="form-control" value="<?= htmlspecialchars($medico['identificacion']??'') ?>" required></div>
        <?php else: ?>
        <div class="col-md-6"><label class="form-label">Identificación</label><input type="text" class="form-control bg-light" value="<?= htmlspecialchars($medico['identificacion']) ?>" readonly></div>
        <?php endif; ?>
        <div class="col-md-6"><label class="form-label">Nombre Completo *</label><input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($medico['nombre']??'') ?>" required></div>
        <div class="col-md-6"><label class="form-label">Teléfono</label><input type="tel" name="telefono" class="form-control" value="<?= htmlspecialchars($medico['telefono']??'') ?>"></div>
        <div class="col-md-6"><label class="form-label">Cargo</label><input type="text" name="cargo" class="form-control" value="<?= htmlspecialchars($medico['cargo']??'Médico') ?>"></div>
        <div class="col-12"><label class="form-label">Dirección</label><input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($medico['direccion']??'') ?>"></div>
      </div>
      <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Datos Profesionales</h6>
      <div class="row g-3">
        <div class="col-md-6"><label class="form-label">Especialidad *</label><input type="text" name="especialidad" class="form-control" value="<?= htmlspecialchars($medico['especialidad']??'') ?>" required></div>
        <div class="col-md-6"><label class="form-label">Nro. Licencia *</label><input type="text" name="nro_licencia" class="form-control" value="<?= htmlspecialchars($medico['nro_licencia']??'') ?>" required></div>
        <div class="col-md-6"><label class="form-label">Universidad</label><input type="text" name="universidad" class="form-control" value="<?= htmlspecialchars($medico['universidad']??'') ?>"></div>
        <div class="col-md-6"><label class="form-label">Fecha Ingreso *</label><input type="date" name="fecha_ingreso" class="form-control" value="<?= htmlspecialchars($medico['fecha_ingreso']??'') ?>" required></div>
        <div class="col-md-6"><label class="form-label">Salario</label><input type="number" name="salario" class="form-control" value="<?= htmlspecialchars($medico['salario']??'0') ?>" min="0" step="100"></div>
        <div class="col-md-6 d-flex align-items-end">
          <div class="form-check">
            <input type="checkbox" name="disponible" id="disponible" class="form-check-input" <?= ($medico['disponible']??1) ? 'checked' : '' ?> value="1">
            <label class="form-check-label fw-semibold" for="disponible">Médico disponible</label>
          </div>
        </div>
      </div>
      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save-fill me-1"></i>Guardar</button>
        <a href="<?= APP_URL ?>/medicos" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>Cancelar</a>
      </div>
    </form>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
