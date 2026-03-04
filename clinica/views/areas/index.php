<?php
$pageTitle  = 'Áreas';
$activeMenu = 'areas';
$breadcrumb = 'Áreas / Departamentos';
$successMsgs = ['1'=>'Área creada correctamente.','2'=>'Área actualizada.','3'=>'Área eliminada.'];
$flash = null;
if (isset($_GET['success']) && isset($successMsgs[$_GET['success']])) $flash = ['type'=>'success','msg'=>$successMsgs[$_GET['success']]];
ob_start();
?>
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div><h1><i class="bi bi-building text-primary me-2"></i>Áreas / Departamentos</h1><p>Gestión de áreas clínicas</p></div>
  <?php if ($_SESSION['user']['rol'] === 'admin'): ?>
  <a href="<?= APP_URL ?>/areas/crear" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nueva Área</a>
  <?php endif; ?>
</div>

<!-- Tarjetas de resumen -->
<div class="row g-3 mb-4">
  <?php foreach ($areas as $a): ?>
  <div class="col-md-4 col-lg-3">
    <div class="card h-100 border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <span class="badge bg-primary mb-1"><?= htmlspecialchars($a['cod_area']) ?></span>
            <h6 class="card-title mb-0"><?= htmlspecialchars($a['nombre_area']) ?></h6>
          </div>
          <?php if ($_SESSION['user']['rol'] === 'admin'): ?>
          <div class="dropdown">
            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="<?= APP_URL ?>/areas/<?= urlencode($a['cod_area']) ?>/editar"><i class="bi bi-pencil me-2"></i>Editar</a></li>
              <li>
                <form method="POST" action="<?= APP_URL ?>/areas/<?= urlencode($a['cod_area']) ?>/eliminar" class="d-inline">
                  <input type="hidden" name="_token" value="<?= $csrf ?>">
                  <button class="dropdown-item text-danger" data-confirm="¿Eliminar el área <?= htmlspecialchars($a['nombre_area']) ?>?"><i class="bi bi-trash me-2"></i>Eliminar</button>
                </form>
              </li>
            </ul>
          </div>
          <?php endif; ?>
        </div>
        <div class="row g-2 mt-2 text-center">
          <div class="col-4">
            <div class="p-2 rounded" style="background:#f0f9ff">
              <div class="fw-bold text-primary"><?= $a['total_camas'] ?></div>
              <div class="text-muted" style="font-size:.7rem">Camas</div>
            </div>
          </div>
          <div class="col-4">
            <div class="p-2 rounded" style="background:#f0fdf4">
              <div class="fw-bold text-success"><?= $a['camas_libres'] ?></div>
              <div class="text-muted" style="font-size:.7rem">Libres</div>
            </div>
          </div>
          <div class="col-4">
            <div class="p-2 rounded" style="background:#fff5f5">
              <div class="fw-bold text-danger"><?= $a['camas_ocupadas'] ?></div>
              <div class="text-muted" style="font-size:.7rem">Ocupadas</div>
            </div>
          </div>
        </div>
        <div class="mt-2 text-muted" style="font-size:.8rem">
          <i class="bi bi-person-heart me-1"></i><?= $a['total_enfermeras'] ?> enfermera(s)
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if (empty($areas)): ?>
  <div class="col-12"><div class="text-center py-5 text-muted"><i class="bi bi-building fs-1 d-block mb-2"></i>No hay áreas registradas</div></div>
  <?php endif; ?>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
