<?php
$pageTitle  = 'Citas';
$activeMenu = 'citas';
$breadcrumb = 'Citas';
$successMsgs = ['1'=>'Cita agendada.','2'=>'Cita actualizada.','3'=>'Cita cancelada.','4'=>'Cita completada.','5'=>'Cita eliminada.'];
$flash = null;
if (isset($_GET['success']) && isset($successMsgs[$_GET['success']])) $flash = ['type'=>'success','msg'=>$successMsgs[$_GET['success']]];
ob_start();
?>
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div><h1><i class="bi bi-calendar-check-fill text-primary me-2"></i>Citas</h1><p>Agenda y gestión de citas médicas</p></div>
  <?php if (in_array($_SESSION['user']['rol'],['admin','recepcionista'])): ?>
  <a href="<?= APP_URL ?>/citas/crear" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nueva Cita</a>
  <?php endif; ?>
</div>

<!-- Filters -->
<div class="card mb-3">
  <div class="card-body p-3">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-sm-4 col-md-3">
        <label class="form-label mb-1 small fw-semibold">Fecha</label>
        <input type="date" name="fecha" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['fecha']) ?>">
      </div>
      <div class="col-sm-4 col-md-3">
        <label class="form-label mb-1 small fw-semibold">Estado</label>
        <select name="estado" class="form-select form-select-sm">
          <option value="">Todos</option>
          <?php foreach (['pendiente','confirmada','completada','cancelada'] as $e): ?>
          <option value="<?= $e ?>" <?= $filters['estado']===$e?'selected':'' ?>><?= ucfirst($e) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-sm-4 col-md-3">
        <label class="form-label mb-1 small fw-semibold">Médico</label>
        <select name="medico" class="form-select form-select-sm">
          <option value="">Todos</option>
          <?php foreach ($medicos as $m): ?>
          <option value="<?= $m['identificacion'] ?>" <?= $filters['medico']===$m['identificacion']?'selected':'' ?>><?= htmlspecialchars($m['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel-fill me-1"></i>Filtrar</button>
        <a href="<?= APP_URL ?>/citas" class="btn btn-outline-secondary btn-sm ms-1"><i class="bi bi-x"></i></a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="table-wrapper">
    <table class="table table-hover">
      <thead><tr><th>#</th><th>Fecha y Hora</th><th>Paciente</th><th>Médico</th><th>Motivo</th><th>Estado</th><th class="text-end">Acciones</th></tr></thead>
      <tbody>
        <?php if (empty($citas)): ?>
        <tr><td colspan="7" class="text-center py-4 text-muted"><i class="bi bi-calendar-x fs-3 d-block mb-2"></i>No hay citas que mostrar</td></tr>
        <?php else: foreach ($citas as $c): ?>
        <tr>
          <td><small class="text-muted"><?= $c['id_cita'] ?></small></td>
          <td>
            <strong><?= date('d/m/Y', strtotime($c['fecha_cita'])) ?></strong>
            <div class="text-primary fw-bold"><?= substr($c['hora_cita'],0,5) ?></div>
          </td>
          <td>
            <a href="<?= APP_URL ?>/pacientes/<?= urlencode($c['id_paciente']) ?>" class="text-decoration-none">
              <?= htmlspecialchars($c['nombre_paciente']) ?>
            </a>
          </td>
          <td>
            <div><?= htmlspecialchars($c['nombre_medico']) ?></div>
            <small class="text-muted"><?= htmlspecialchars($c['especialidad']) ?></small>
          </td>
          <td><span style="max-width:140px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($c['motivo']??'—') ?></span></td>
          <td><span class="badge-<?= $c['estado'] ?>"><?= ucfirst($c['estado']) ?></span></td>
          <td>
            <div class="d-flex gap-1 justify-content-end flex-wrap">
              <?php if (in_array($_SESSION['user']['rol'],['admin','recepcionista'])): ?>
              <a href="<?= APP_URL ?>/citas/<?= $c['id_cita'] ?>/editar" class="btn btn-sm btn-outline-primary btn-action" title="Editar"><i class="bi bi-pencil-fill"></i></a>
              <?php endif; ?>
              <?php if ($c['estado'] === 'pendiente'): ?>
              <form method="POST" action="<?= APP_URL ?>/citas/<?= $c['id_cita'] ?>/completar" class="d-inline">
                <input type="hidden" name="_token" value="<?= $csrf ?>">
                <button class="btn btn-sm btn-outline-success btn-action" title="Completar"><i class="bi bi-check-lg"></i></button>
              </form>
              <form method="POST" action="<?= APP_URL ?>/citas/<?= $c['id_cita'] ?>/cancelar" class="d-inline">
                <input type="hidden" name="_token" value="<?= $csrf ?>">
                <button class="btn btn-sm btn-outline-warning btn-action" data-confirm="¿Cancelar esta cita?" title="Cancelar"><i class="bi bi-x-lg"></i></button>
              </form>
              <?php endif; ?>
              <?php if ($_SESSION['user']['rol'] === 'admin'): ?>
              <form method="POST" action="<?= APP_URL ?>/citas/<?= $c['id_cita'] ?>/eliminar" class="d-inline">
                <input type="hidden" name="_token" value="<?= $csrf ?>">
                <button class="btn btn-sm btn-outline-danger btn-action" data-confirm="¿Eliminar esta cita?" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
              </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer text-muted small p-3">Total: <strong><?= count($citas) ?></strong> cita(s)</div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
