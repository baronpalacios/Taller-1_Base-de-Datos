<?php
$isEdit = !empty($cita['id_cita']);
$pageTitle  = $isEdit ? 'Editar Cita' : 'Nueva Cita';
$activeMenu = 'citas'; $breadcrumb = $pageTitle;
ob_start();
?>
<div class="page-header"><h1><i class="bi bi-calendar-plus text-primary me-2"></i><?= $isEdit ? 'Editar' : 'Agendar' ?> Cita</h1></div>
<div class="card" style="max-width:700px">
  <div class="card-body p-4">
    <?php if (!empty($error)): ?><div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?></div><?php endif; ?>
    <form method="POST" action="<?= $isEdit ? APP_URL.'/citas/'.$cita['id_cita'].'/editar' : APP_URL.'/citas/crear' ?>">
      <input type="hidden" name="_token" value="<?= $csrf ?>">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Paciente *</label>
          <select name="id_paciente" class="form-select" required>
            <option value="">-- Seleccionar --</option>
            <?php foreach ($pacientes as $p): ?>
            <option value="<?= $p['identificacion'] ?>" <?= ($cita['id_paciente']??'')===$p['identificacion']?'selected':'' ?>>
              <?= htmlspecialchars($p['nombre']) ?> (<?= $p['identificacion'] ?>)
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Médico *</label>
          <select name="id_medico" class="form-select" required>
            <option value="">-- Seleccionar --</option>
            <?php foreach ($medicos as $m): ?>
            <option value="<?= $m['identificacion'] ?>" <?= ($cita['id_medico']??'')===$m['identificacion']?'selected':'' ?>>
              <?= htmlspecialchars($m['nombre']) ?> — <?= htmlspecialchars($m['especialidad']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Fecha *</label>
          <input type="date" name="fecha_cita" class="form-control" value="<?= htmlspecialchars($cita['fecha_cita']??date('Y-m-d')) ?>" min="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Hora *</label>
          <input type="time" name="hora_cita" class="form-control" value="<?= htmlspecialchars($cita['hora_cita']??'08:00') ?>" required>
        </div>
        <div class="col-12">
          <label class="form-label">Motivo de consulta</label>
          <input type="text" name="motivo" class="form-control" value="<?= htmlspecialchars($cita['motivo']??'') ?>" placeholder="Ej: Control rutinario, dolor abdominal...">
        </div>
        <div class="col-md-6">
          <label class="form-label">Estado</label>
          <select name="estado" class="form-select">
            <?php foreach (['pendiente','confirmada','completada','cancelada'] as $e): ?>
            <option value="<?= $e ?>" <?= ($cita['estado']??'pendiente')===$e?'selected':'' ?>><?= ucfirst($e) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Notas adicionales</label>
          <textarea name="notas" class="form-control" rows="2" placeholder="Observaciones..."><?= htmlspecialchars($cita['notas']??'') ?></textarea>
        </div>
      </div>
      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save-fill me-1"></i>Guardar</button>
        <a href="<?= APP_URL ?>/citas" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>Cancelar</a>
      </div>
    </form>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
