<?php
$pageTitle  = 'Historia Clinica';
$activeMenu = 'pacientes';
$breadcrumb = 'Historia Clinica';
$flash = null;
if (isset($_GET['success'])) $flash = ['type'=>'success','msg'=>'Consulta registrada correctamente.'];
ob_start();
?>
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <h1><i class="bi bi-journal-medical text-success me-2"></i>Historia Clinica</h1>
    <p><strong><?= htmlspecialchars($paciente['nombre']) ?></strong> | ID: <?= htmlspecialchars($paciente['identificacion']) ?> | HC #<?= $historia['id_historia'] ?></p>
  </div>
  <a href="<?= APP_URL ?>/pacientes/<?= urlencode($paciente['identificacion']) ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
</div>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card mb-3">
      <div class="card-header p-3"><h6 class="mb-0 fw-bold">Paciente</h6></div>
      <div class="card-body p-3">
        <div class="d-flex justify-content-between py-1"><span class="text-muted small">Nombre</span><strong class="small"><?= htmlspecialchars($paciente['nombre']) ?></strong></div>
        <div class="d-flex justify-content-between py-1"><span class="text-muted small">Edad</span><strong class="small"><?= ($paciente['edad']??'--') ?> anos</strong></div>
        <div class="d-flex justify-content-between py-1"><span class="text-muted small">EPS</span><strong class="small"><?= htmlspecialchars($paciente['eps']??'--') ?></strong></div>
      </div>
    </div>
    <?php if (in_array($_SESSION['user']['rol'],['admin','medico'])): ?>
    <div class="card">
      <div class="card-header p-3"><h6 class="mb-0 fw-bold">Nueva Consulta</h6></div>
      <div class="card-body p-3">
        <form method="POST" action="<?= APP_URL ?>/historia/<?= urlencode($paciente['identificacion']) ?>/consulta">
          <input type="hidden" name="_token" value="<?= $csrf ?>">
          <div class="mb-2"><label class="form-label small">Medico *</label>
            <select name="id_medico" class="form-select form-select-sm" required>
              <option value="">-- Seleccionar --</option>
              <?php foreach ($medicos as $m): ?>
              <option value="<?= $m['identificacion'] ?>"><?= htmlspecialchars($m['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-2"><label class="form-label small">Fecha *</label><input type="date" name="fecha_consulta" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required></div>
          <div class="mb-2"><label class="form-label small">Precio</label><input type="number" name="precio" class="form-control form-control-sm" value="0" min="0"></div>
          <div class="mb-2"><label class="form-label small">Resumen</label><textarea name="resumen" class="form-control form-control-sm" rows="2"></textarea></div>
          <div class="mb-2"><label class="form-label small">Diagnostico</label><textarea name="diagnostico" class="form-control form-control-sm" rows="2"></textarea></div>
          <div class="mb-3"><label class="form-label small">Tratamiento</label><textarea name="tratamiento" class="form-control form-control-sm" rows="2"></textarea></div>
          <button type="submit" class="btn btn-success btn-sm w-100">Registrar Consulta</button>
        </form>
      </div>
    </div>
    <?php endif; ?>
  </div>
  <div class="col-md-8">
    <div class="card">
      <div class="card-header p-3"><h6 class="mb-0 fw-bold">Historial de Consultas</h6></div>
      <div class="card-body p-3">
        <?php if (empty($consultas)): ?>
        <div class="text-center py-4 text-muted">Sin consultas registradas</div>
        <?php else: foreach ($consultas as $c): ?>
        <div class="border rounded p-3 mb-3">
          <div class="d-flex justify-content-between mb-2">
            <strong><?= date('d/m/Y', strtotime($c['fecha_consulta'])) ?></strong>
            <span class="text-success fw-bold">$<?= number_format($c['precio'],0,',','.') ?></span>
          </div>
          <p class="text-muted small mb-1"><?= htmlspecialchars($c['nombre_medico']) ?> — <?= htmlspecialchars($c['especialidad']) ?></p>
          <?php if (!empty($c['resumen'])): ?><div class="mb-1"><small class="fw-semibold">Motivo:</small> <small><?= htmlspecialchars($c['resumen']) ?></small></div><?php endif; ?>
          <?php if (!empty($c['diagnostico'])): ?><div class="mb-1"><small class="fw-semibold text-danger">Diagnostico:</small> <small><?= htmlspecialchars($c['diagnostico']) ?></small></div><?php endif; ?>
          <?php if (!empty($c['tratamiento'])): ?><div><small class="fw-semibold text-primary">Tratamiento:</small> <small><?= htmlspecialchars($c['tratamiento']) ?></small></div><?php endif; ?>
        </div>
        <?php endforeach; endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
