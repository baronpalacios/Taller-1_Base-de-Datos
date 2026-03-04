<?php
$pageTitle  = 'Pacientes';
$activeMenu = 'pacientes';
$breadcrumb = 'Pacientes';

// Flash messages
$successMsgs = ['1'=>'Paciente registrado correctamente.','2'=>'Paciente actualizado.','3'=>'Paciente eliminado.'];
$flash = null;
if (isset($_GET['success']) && isset($successMsgs[$_GET['success']])) {
    $flash = ['type' => 'success', 'msg' => $successMsgs[$_GET['success']]];
}

ob_start();
?>
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div><h1><i class="bi bi-people-fill text-primary me-2"></i>Pacientes</h1>
  <p>Gestión del registro de pacientes</p></div>
  <?php if (in_array($_SESSION['user']['rol'], ['admin','recepcionista'])): ?>
  <a href="<?= APP_URL ?>/pacientes/crear" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i>Nuevo Paciente
  </a>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-header p-3">
    <div class="search-box" style="max-width:360px">
      <i class="bi bi-search"></i>
      <input type="text" id="liveSearch" class="form-control" placeholder="Buscar por nombre, ID o NSS..."
             value="<?= htmlspecialchars($search) ?>">
    </div>
  </div>
  <div class="table-wrapper">
    <table class="table table-hover">
      <thead><tr>
        <th>ID</th><th>Nombre</th><th>Edad</th><th>EPS</th>
        <th>Teléfono</th><th>NSS</th><th class="text-end">Acciones</th>
      </tr></thead>
      <tbody>
        <?php if (empty($pacientes)): ?>
        <tr><td colspan="7" class="text-center py-4 text-muted">
          <i class="bi bi-search fs-3 d-block mb-2"></i>No se encontraron pacientes
        </td></tr>
        <?php else: ?>
        <?php foreach ($pacientes as $p): ?>
        <tr>
          <td><span class="badge bg-light text-secondary"><?= htmlspecialchars($p['identificacion']) ?></span></td>
          <td>
            <strong><?= htmlspecialchars($p['nombre']) ?></strong>
            <div class="text-muted" style="font-size:.75rem"><?= htmlspecialchars($p['labor'] ?? '') ?></div>
          </td>
          <td><?= $p['edad'] ?? '—' ?> años</td>
          <td><?= htmlspecialchars($p['eps'] ?? '—') ?></td>
          <td><?= htmlspecialchars($p['telefono'] ?? '—') ?></td>
          <td><code style="font-size:.8rem"><?= htmlspecialchars($p['nss']) ?></code></td>
          <td>
            <div class="d-flex gap-1 justify-content-end">
              <a href="<?= APP_URL ?>/pacientes/<?= urlencode($p['identificacion']) ?>" class="btn btn-sm btn-outline-info btn-action" title="Ver"><i class="bi bi-eye-fill"></i></a>
              <a href="<?= APP_URL ?>/historia/<?= urlencode($p['identificacion']) ?>" class="btn btn-sm btn-outline-success btn-action" title="Historia"><i class="bi bi-journal-medical"></i></a>
              <?php if (in_array($_SESSION['user']['rol'], ['admin','recepcionista'])): ?>
              <a href="<?= APP_URL ?>/pacientes/<?= urlencode($p['identificacion']) ?>/editar" class="btn btn-sm btn-outline-primary btn-action" title="Editar"><i class="bi bi-pencil-fill"></i></a>
              <?php endif; ?>
              <?php if ($_SESSION['user']['rol'] === 'admin'): ?>
              <form method="POST" action="<?= APP_URL ?>/pacientes/<?= urlencode($p['identificacion']) ?>/eliminar" class="d-inline">
                <input type="hidden" name="_token" value="<?= $csrf ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger btn-action" data-confirm="¿Eliminar a <?= htmlspecialchars($p['nombre']) ?>? Esta accion no se puede deshacer." title="Eliminar"><i class="bi bi-trash-fill"></i></button>
              </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer text-muted small p-3">
    Total: <strong><?= count($pacientes) ?></strong> paciente(s)
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
