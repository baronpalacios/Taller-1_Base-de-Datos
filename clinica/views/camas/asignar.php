<?php
$pageTitle  = 'Asignar Cama';
$activeMenu = 'camas';
$breadcrumb = 'Camas / Asignar';
ob_start();
?>
<div class="page-header"><h1><i class="bi bi-person-plus text-primary me-2"></i>Asignar Cama #<?= $nro_cama ?></h1></div>
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <form method="POST" action="<?= APP_URL ?>/camas/<?= $nro_cama ?>/<?= urlencode($cod_area) ?>/asignar">
          <input type="hidden" name="_token" value="<?= $csrf ?>">
          <div class="mb-3">
            <label class="form-label fw-semibold">Paciente <span class="text-danger">*</span></label>
            <select name="id_paciente" class="form-select" required>
              <option value="">Seleccionar paciente...</option>
              <?php foreach ($pacientes as $p): ?>
              <option value="<?= $p['identificacion'] ?>"><?= htmlspecialchars($p['nombre']) ?> — <?= $p['identificacion'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Fecha de asignación <span class="text-danger">*</span></label>
            <input type="date" name="fecha_asignacion" class="form-control" required value="<?= date('Y-m-d') ?>">
          </div>
          <div class="mb-4">
            <label class="form-label fw-semibold">Duración estimada (días) <span class="text-danger">*</span></label>
            <input type="number" name="duracion_dias" class="form-control" required min="1" value="1">
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Asignar</button>
            <a href="<?= APP_URL ?>/camas" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
