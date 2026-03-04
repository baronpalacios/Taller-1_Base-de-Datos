<?php
$isEdit = !empty($usuario['id']);
$pageTitle  = $isEdit ? 'Editar Usuario' : 'Nuevo Usuario';
$activeMenu = 'usuarios'; $breadcrumb = $pageTitle;
ob_start();
?>
<div class="page-header"><h1><i class="bi bi-person-gear text-primary me-2"></i><?= $isEdit ? 'Editar' : 'Nuevo' ?> Usuario</h1></div>
<div class="card" style="max-width:550px">
  <div class="card-body p-4">
    <?php if (!empty($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <form method="POST" action="<?= $isEdit ? APP_URL.'/usuarios/'.$usuario['id'].'/editar' : APP_URL.'/usuarios/crear' ?>">
      <input type="hidden" name="_token" value="<?= $csrf ?>">
      <div class="mb-3"><label class="form-label">Usuario *</label><input type="text" name="username" class="form-control" value="<?= htmlspecialchars($usuario['username']??'') ?>" required></div>
      <div class="mb-3"><label class="form-label">Email *</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']??'') ?>" required></div>
      <div class="mb-3"><label class="form-label">Contrasena <?= $isEdit ? '(dejar vacio para mantener)' : '*' ?></label><input type="password" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?> minlength="6"></div>
      <div class="mb-3"><label class="form-label">Rol *</label>
        <select name="rol" class="form-select">
          <?php foreach (['admin','medico','recepcionista'] as $r): ?>
          <option value="<?= $r ?>" <?= ($usuario['rol']??'recepcionista')===$r?'selected':'' ?>><?= ucfirst($r) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php if ($isEdit): ?>
      <div class="mb-3 form-check"><input type="checkbox" name="activo" id="activo" class="form-check-input" value="1" <?= ($usuario['activo']??1)?'checked':'' ?>><label class="form-check-label" for="activo">Usuario activo</label></div>
      <?php endif; ?>
      <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save-fill me-1"></i>Guardar</button>
        <a href="<?= APP_URL ?>/usuarios" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i>Cancelar</a>
      </div>
    </form>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
