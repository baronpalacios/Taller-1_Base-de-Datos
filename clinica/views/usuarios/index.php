<?php
$pageTitle  = 'Usuarios';
$activeMenu = 'usuarios';
$breadcrumb = 'Usuarios';
$successMsgs = ['1'=>'Usuario creado.','2'=>'Usuario actualizado.','3'=>'Usuario eliminado.'];
$flash = null;
if (isset($_GET['success']) && isset($successMsgs[$_GET['success']])) $flash = ['type'=>'success','msg'=>$successMsgs[$_GET['success']]];
if (isset($_GET['error']) && $_GET['error']==='self') $flash = ['type'=>'danger','msg'=>'No puedes eliminar tu propio usuario.'];
ob_start();
?>
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div><h1><i class="bi bi-person-lock text-primary me-2"></i>Usuarios</h1><p>Gestion de acceso al sistema</p></div>
  <a href="<?= APP_URL ?>/usuarios/crear" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Nuevo Usuario</a>
</div>
<div class="card">
  <div class="table-wrapper">
    <table class="table table-hover">
      <thead><tr><th>#</th><th>Usuario</th><th>Email</th><th>Rol</th><th>Estado</th><th>Ultimo Login</th><th class="text-end">Acciones</th></tr></thead>
      <tbody>
        <?php if (empty($usuarios)): ?>
        <tr><td colspan="7" class="text-center py-4 text-muted">No hay usuarios</td></tr>
        <?php else: foreach ($usuarios as $u): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><span class="badge-<?= $u['rol'] ?>"><?= ucfirst($u['rol']) ?></span></td>
          <td><?= $u['activo'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?></td>
          <td><small class="text-muted"><?= $u['ultimo_login']??'Nunca' ?></small></td>
          <td>
            <div class="d-flex gap-1 justify-content-end">
              <a href="<?= APP_URL ?>/usuarios/<?= $u['id'] ?>/editar" class="btn btn-sm btn-outline-primary btn-action" title="Editar"><i class="bi bi-pencil-fill"></i></a>
              <?php if ($u['id'] !== (int)$_SESSION['user']['id']): ?>
              <form method="POST" action="<?= APP_URL ?>/usuarios/<?= $u['id'] ?>/eliminar" class="d-inline">
                <input type="hidden" name="_token" value="<?= $csrf ?>">
                <button class="btn btn-sm btn-outline-danger btn-action" data-confirm="Eliminar usuario <?= htmlspecialchars($u['username']) ?>?" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
              </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
