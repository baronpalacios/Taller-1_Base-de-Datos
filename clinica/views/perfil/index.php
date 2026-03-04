<?php
$pageTitle  = 'Mi Perfil';
$activeMenu = 'perfil';
$breadcrumb = 'Mi Perfil';
$successMsgs = ['1'=>'Email actualizado correctamente.','2'=>'Contraseña cambiada correctamente.'];
$flash = null;
if (isset($_GET['success']) && isset($successMsgs[$_GET['success']])) $flash = ['type'=>'success','msg'=>$successMsgs[$_GET['success']]];
ob_start();
$roles = ['admin'=>['label'=>'Administrador','color'=>'danger'],'medico'=>['label'=>'Médico','color'=>'primary'],'recepcionista'=>['label'=>'Recepcionista','color'=>'info']];
$rol = $roles[$user['rol']] ?? ['label'=>$user['rol'],'color'=>'secondary'];
?>
<div class="page-header">
  <h1><i class="bi bi-person-circle text-primary me-2"></i>Mi Perfil</h1>
  <p>Administra tu cuenta y contraseña</p>
</div>

<div class="row g-3">
  <!-- Info básica -->
  <div class="col-md-4">
    <div class="card shadow-sm text-center">
      <div class="card-body py-4">
        <div class="mb-3" style="font-size:4rem;color:#1a6fc4"><i class="bi bi-person-circle"></i></div>
        <h5 class="mb-1"><?= htmlspecialchars($user['username']) ?></h5>
        <span class="badge bg-<?= $rol['color'] ?> mb-2"><?= $rol['label'] ?></span>
        <div class="text-muted small"><?= htmlspecialchars($user['email'] ?? '') ?></div>
        <div class="text-muted small mt-1">Registrado: <?= htmlspecialchars(substr($user['created_at'] ?? '',0,10)) ?></div>
      </div>
    </div>
  </div>

  <div class="col-md-8">
    <!-- Cambiar email -->
    <div class="card shadow-sm mb-3">
      <div class="card-header"><h6 class="mb-0"><i class="bi bi-envelope me-2"></i>Actualizar Email</h6></div>
      <div class="card-body p-4">
        <?php if ($error && !str_contains($error,'contraseña')): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <form method="POST" action="<?= APP_URL ?>/perfil/actualizar">
          <input type="hidden" name="_token" value="<?= $csrf ?>">
          <div class="mb-3">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($user['email'] ?? '') ?>">
          </div>
          <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Guardar email</button>
        </form>
      </div>
    </div>

    <!-- Cambiar contraseña -->
    <div class="card shadow-sm">
      <div class="card-header"><h6 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Cambiar Contraseña</h6></div>
      <div class="card-body p-4">
        <?php if ($error && str_contains($error,'contraseña') || str_contains($error,'coinc') || str_contains($error,'actual')): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <form method="POST" action="<?= APP_URL ?>/perfil/password">
          <input type="hidden" name="_token" value="<?= $csrf ?>">
          <div class="mb-3">
            <label class="form-label fw-semibold">Contraseña actual</label>
            <input type="password" name="password_actual" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Nueva contraseña</label>
            <input type="password" name="password_nueva" class="form-control" required minlength="6">
            <div class="form-text">Mínimo 6 caracteres.</div>
          </div>
          <div class="mb-4">
            <label class="form-label fw-semibold">Confirmar nueva contraseña</label>
            <input type="password" name="password_confirmacion" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-warning text-dark"><i class="bi bi-shield-check me-1"></i>Cambiar contraseña</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
