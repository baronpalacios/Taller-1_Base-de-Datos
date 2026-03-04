<?php
$pageTitle  = 'Dashboard';
$activeMenu = 'dashboard';
$breadcrumb = 'Dashboard';

// Mensajes de bienvenida
$hora  = (int)date('H');
$saludo = $hora < 12 ? 'Buenos días' : ($hora < 18 ? 'Buenas tardes' : 'Buenas noches');

ob_start();
?>
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div>
    <h1><?= $saludo ?>, <?= htmlspecialchars($_SESSION['user']['username']) ?> 👋</h1>
    <p>Resumen del sistema — <?= date('d \d\e F Y') ?></p>
  </div>
  <a href="<?= APP_URL ?>/citas/crear" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i>Nueva Cita
  </a>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
      <div>
        <div class="stat-number"><?= $stats['total_pacientes'] ?></div>
        <div class="stat-label">Total Pacientes</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon green"><i class="bi bi-calendar-check-fill"></i></div>
      <div>
        <div class="stat-number"><?= $stats['citas_hoy'] ?></div>
        <div class="stat-label">Citas Hoy</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon orange"><i class="bi bi-person-badge-fill"></i></div>
      <div>
        <div class="stat-number"><?= $stats['medicos_activos'] ?></div>
        <div class="stat-label">Médicos Activos</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon teal"><i class="bi bi-person-plus-fill"></i></div>
      <div>
        <div class="stat-number"><?= $stats['pacientes_mes'] ?></div>
        <div class="stat-label">Nuevos este mes</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <!-- Citas de hoy -->
  <div class="col-12 col-xl-8">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between p-3">
        <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-day text-primary me-2"></i>Citas de Hoy</h6>
        <a href="<?= APP_URL ?>/citas" class="btn btn-sm btn-outline-primary">Ver todas</a>
      </div>
      <div class="table-wrapper">
        <?php if (empty($citasHoy)): ?>
        <div class="text-center py-4 text-muted">
          <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>No hay citas programadas para hoy
        </div>
        <?php else: ?>
        <table class="table table-hover mb-0">
          <thead><tr>
            <th>Hora</th><th>Paciente</th><th>Médico</th><th>Estado</th>
          </tr></thead>
          <tbody>
            <?php foreach ($citasHoy as $c): ?>
            <tr>
              <td><strong><?= substr($c['hora_cita'],0,5) ?></strong></td>
              <td><?= htmlspecialchars($c['nombre_paciente']) ?></td>
              <td>
                <div class="small"><?= htmlspecialchars($c['nombre_medico']) ?></div>
                <div class="text-muted" style="font-size:.75rem"><?= htmlspecialchars($c['especialidad']) ?></div>
              </td>
              <td><span class="badge-<?= $c['estado'] ?>"><?= ucfirst($c['estado']) ?></span></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Accesos rápidos -->
  <div class="col-12 col-xl-4">
    <div class="card h-100">
      <div class="card-header p-3">
        <h6 class="mb-0 fw-bold"><i class="bi bi-lightning-fill text-warning me-2"></i>Acceso Rápido</h6>
      </div>
      <div class="card-body">
        <div class="d-grid gap-2">
          <a href="<?= APP_URL ?>/pacientes/crear" class="btn btn-outline-primary text-start">
            <i class="bi bi-person-plus-fill me-2"></i>Registrar Paciente
          </a>
          <a href="<?= APP_URL ?>/citas/crear" class="btn btn-outline-success text-start">
            <i class="bi bi-calendar-plus me-2"></i>Agendar Cita
          </a>
          <a href="<?= APP_URL ?>/pacientes" class="btn btn-outline-info text-start">
            <i class="bi bi-search me-2"></i>Buscar Paciente
          </a>
          <?php if (in_array($_SESSION['user']['rol'], ['admin','medico'])): ?>
          <a href="<?= APP_URL ?>/reportes" class="btn btn-outline-warning text-start">
            <i class="bi bi-file-earmark-bar-graph me-2"></i>Ver Reportes
          </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
