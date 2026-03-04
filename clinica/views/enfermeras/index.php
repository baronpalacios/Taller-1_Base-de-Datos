<?php
$pageTitle  = 'Enfermeras';
$activeMenu = 'enfermeras';
$breadcrumb = 'Enfermeras';
$successMsgs = ['1'=>'Enfermera registrada correctamente.','2'=>'Enfermera actualizada.','3'=>'Enfermera eliminada.'];
$flash = null;
if (isset($_GET['success']) && isset($successMsgs[$_GET['success']])) $flash = ['type'=>'success','msg'=>$successMsgs[$_GET['success']]];
ob_start();
?>
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div><h1><i class="bi bi-person-heart text-primary me-2"></i>Enfermeras</h1><p>Gestión del personal de enfermería</p></div>
  <?php if ($_SESSION['user']['rol'] === 'admin'): ?>
  <a href="<?= APP_URL ?>/enfermeras/crear" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nueva Enfermera</a>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-header p-3">
    <form method="GET" class="d-flex gap-2 flex-wrap">
      <div class="search-box flex-grow-1" style="max-width:300px">
        <i class="bi bi-search"></i>
        <input type="text" name="q" class="form-control" placeholder="Buscar por nombre o ID..." value="<?= htmlspecialchars($search) ?>">
      </div>
      <select name="area" class="form-select" style="max-width:200px">
        <option value="">Todas las áreas</option>
        <?php foreach ($areas as $a): ?>
        <option value="<?= $a['cod_area'] ?>" <?= $areaFiltro === $a['cod_area'] ? 'selected' : '' ?>><?= htmlspecialchars($a['nombre_area']) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel"></i></button>
      <a href="<?= APP_URL ?>/enfermeras" class="btn btn-outline-secondary">Limpiar</a>
    </form>
  </div>
  <div class="table-wrapper">
    <table class="table table-hover">
      <thead><tr><th>ID</th><th>Nombre</th><th>Tipo</th><th>Área</th><th>Experiencia</th><th>Teléfono</th><th class="text-end">Acciones</th></tr></thead>
      <tbody>
        <?php if (empty($enfermeras)): ?>
        <tr><td colspan="7" class="text-center py-4 text-muted">No se encontraron enfermeras</td></tr>
        <?php else: foreach ($enfermeras as $e): ?>
        <tr>
          <td><span class="badge bg-light text-secondary"><?= htmlspecialchars($e['identificacion']) ?></span></td>
          <td><strong><?= htmlspecialchars($e['nombre']) ?></strong></td>
          <td>
            <?php $tipos = ['auxiliar'=>'secondary','asistente'=>'info','jefe'=>'warning']; ?>
            <span class="badge bg-<?= $tipos[$e['tipo']] ?? 'secondary' ?>"><?= ucfirst($e['tipo']) ?></span>
          </td>
          <td><?= htmlspecialchars($e['nombre_area'] ?? '—') ?></td>
          <td><?= $e['anios_experiencia'] ?> año(s)</td>
          <td><?= htmlspecialchars($e['telefono'] ?? '—') ?></td>
          <td>
            <div class="d-flex gap-1 justify-content-end">
              <a href="<?= APP_URL ?>/enfermeras/<?= urlencode($e['identificacion']) ?>" class="btn btn-sm btn-outline-info btn-action" title="Ver"><i class="bi bi-eye-fill"></i></a>
              <?php if ($_SESSION['user']['rol'] === 'admin'): ?>
              <a href="<?= APP_URL ?>/enfermeras/<?= urlencode($e['identificacion']) ?>/editar" class="btn btn-sm btn-outline-primary btn-action" title="Editar"><i class="bi bi-pencil-fill"></i></a>
              <form method="POST" action="<?= APP_URL ?>/enfermeras/<?= urlencode($e['identificacion']) ?>/eliminar" class="d-inline">
                <input type="hidden" name="_token" value="<?= $csrf ?>">
                <button class="btn btn-sm btn-outline-danger btn-action" data-confirm="¿Eliminar a <?= htmlspecialchars($e['nombre']) ?>?" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
              </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer text-muted small p-3">Total: <strong><?= count($enfermeras) ?></strong> enfermera(s)</div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
