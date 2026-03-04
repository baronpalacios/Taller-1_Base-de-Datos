<?php
$isEdit     = !empty($enfermera['identificacion']);
$pageTitle  = $isEdit ? 'Editar Enfermera' : 'Nueva Enfermera';
$activeMenu = 'enfermeras';
$breadcrumb = 'Enfermeras / ' . ($isEdit ? 'Editar' : 'Nueva');
ob_start();
?>
<div class="page-header">
  <h1><i class="bi bi-person-heart text-primary me-2"></i><?= $pageTitle ?></h1>
</div>
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <?php if ($error): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= $error ?></div><?php endif; ?>
        <form method="POST" action="<?= APP_URL ?>/enfermeras/<?= $isEdit ? urlencode($enfermera['identificacion']).'/editar' : 'crear' ?>">
          <input type="hidden" name="_token" value="<?= $csrf ?>">

          <h6 class="fw-bold mb-3 text-muted text-uppercase" style="font-size:.75rem;letter-spacing:1px">Datos Personales</h6>
          <div class="row g-3 mb-4">
            <?php if (!$isEdit): ?>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Identificación <span class="text-danger">*</span></label>
              <input type="text" name="identificacion" class="form-control" required value="<?= htmlspecialchars($enfermera['identificacion'] ?? '') ?>">
            </div>
            <?php endif; ?>
            <div class="col-md-<?= $isEdit ? '6' : '8' ?>">
              <label class="form-label fw-semibold">Nombre completo <span class="text-danger">*</span></label>
              <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($enfermera['nombre'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Teléfono</label>
              <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($enfermera['telefono'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Dirección</label>
              <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($enfermera['direccion'] ?? '') ?>">
            </div>
          </div>

          <h6 class="fw-bold mb-3 text-muted text-uppercase" style="font-size:.75rem;letter-spacing:1px">Datos Laborales</h6>
          <div class="row g-3 mb-4">
            <div class="col-md-4">
              <label class="form-label fw-semibold">Tipo <span class="text-danger">*</span></label>
              <select name="tipo" class="form-select" required>
                <?php foreach (['auxiliar'=>'Auxiliar','asistente'=>'Asistente','jefe'=>'Jefe de Enfermería'] as $v=>$l): ?>
                <option value="<?= $v ?>" <?= ($enfermera['tipo'] ?? '') === $v ? 'selected' : '' ?>><?= $l ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Área <span class="text-danger">*</span></label>
              <select name="cod_area" class="form-select" required>
                <option value="">Seleccionar...</option>
                <?php foreach ($areas as $a): ?>
                <option value="<?= $a['cod_area'] ?>" <?= ($enfermera['cod_area'] ?? '') === $a['cod_area'] ? 'selected' : '' ?>><?= htmlspecialchars($a['nombre_area']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Años de experiencia</label>
              <input type="number" name="anios_experiencia" class="form-control" min="0" value="<?= htmlspecialchars($enfermera['anios_experiencia'] ?? '0') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Fecha de ingreso <span class="text-danger">*</span></label>
              <input type="date" name="fecha_ingreso" class="form-control" required value="<?= htmlspecialchars($enfermera['fecha_ingreso'] ?? '') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Salario</label>
              <input type="number" name="salario" class="form-control" min="0" step="0.01" value="<?= htmlspecialchars($enfermera['salario'] ?? '0') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Cargo</label>
              <input type="text" name="cargo" class="form-control" value="<?= htmlspecialchars($enfermera['cargo'] ?? 'Enfermera') ?>">
            </div>
          </div>

          <h6 class="fw-bold mb-3 text-muted text-uppercase" style="font-size:.75rem;letter-spacing:1px">Habilidades</h6>
          <div class="mb-4">
            <label class="form-label fw-semibold">Habilidades (separadas por coma)</label>
            <input type="text" name="habilidades" class="form-control" placeholder="Ej: Venopunción, Cateterismo, RCP, Triaje"
                   value="<?= htmlspecialchars($enfermera['habilidades'] ?? '') ?>">
            <div class="form-text">Ingrese las habilidades separadas por coma.</div>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Actualizar' : 'Guardar' ?></button>
            <a href="<?= APP_URL ?>/enfermeras" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
