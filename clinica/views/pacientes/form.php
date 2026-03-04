<?php
$isEdit = !empty($paciente['identificacion']);
$pageTitle  = $isEdit ? 'Editar Paciente' : 'Nuevo Paciente';
$activeMenu = 'pacientes';
$breadcrumb = $isEdit ? 'Editar Paciente' : 'Nuevo Paciente';
ob_start();
?>
<div class="page-header">
  <h1><?= $isEdit ? '<i class="bi bi-pencil-fill text-primary me-2"></i>Editar' : '<i class="bi bi-person-plus-fill text-primary me-2"></i>Nuevo' ?> Paciente</h1>
  <p><?= $isEdit ? 'Actualizar datos del paciente' : 'Registrar nuevo paciente en el sistema' ?></p>
</div>

<div class="card" style="max-width:800px">
  <div class="card-body p-4">
    <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="<?= $isEdit ? APP_URL.'/pacientes/'.urlencode($paciente['identificacion']).'/editar' : APP_URL.'/pacientes/crear' ?>">
      <input type="hidden" name="_token" value="<?= $csrf ?>">

      <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Datos Personales</h6>
      <div class="row g-3 mb-4">
        <?php if (!$isEdit): ?>
        <div class="col-md-6">
          <label class="form-label">Número de Identificación *</label>
          <input type="text" name="identificacion" class="form-control" value="<?= htmlspecialchars($paciente['identificacion'] ?? '') ?>" required>
        </div>
        <?php else: ?>
        <div class="col-md-6">
          <label class="form-label">Número de Identificación</label>
          <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($paciente['identificacion']) ?>" readonly>
        </div>
        <?php endif; ?>
        <div class="col-md-6">
          <label class="form-label">Nombre Completo *</label>
          <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($paciente['nombre'] ?? '') ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Teléfono</label>
          <input type="tel" name="telefono" class="form-control" value="<?= htmlspecialchars($paciente['telefono'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Fecha de Nacimiento *</label>
          <input type="date" name="fecha_nacimiento" class="form-control" value="<?= htmlspecialchars($paciente['fecha_nacimiento'] ?? '') ?>" required>
        </div>
        <div class="col-12">
          <label class="form-label">Dirección</label>
          <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($paciente['direccion'] ?? '') ?>">
        </div>
      </div>

      <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">Datos de Afiliación</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">NSS (Número Seguridad Social) *</label>
          <input type="text" name="nss" class="form-control" value="<?= htmlspecialchars($paciente['nss'] ?? '') ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">EPS</label>
          <input type="text" name="eps" class="form-control" value="<?= htmlspecialchars($paciente['eps'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Labor / Ocupación</label>
          <input type="text" name="labor" class="form-control" value="<?= htmlspecialchars($paciente['labor'] ?? '') ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Fecha de Afiliación</label>
          <input type="date" name="fecha_afiliacion" class="form-control" value="<?= htmlspecialchars($paciente['fecha_afiliacion'] ?? '') ?>">
        </div>
      </div>

      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary px-4">
          <i class="bi bi-save-fill me-1"></i>Guardar
        </button>
        <a href="<?= APP_URL ?>/pacientes" class="btn btn-secondary">
          <i class="bi bi-arrow-left me-1"></i>Cancelar
        </a>
      </div>
    </form>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
