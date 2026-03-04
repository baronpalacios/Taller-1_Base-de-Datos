<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión — <?= APP_NAME ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body{background:linear-gradient(135deg,#0d3b6e 0%,#1a6fc4 100%);min-height:100vh;display:flex;align-items:center;justify-content:center;}
    .login-card{background:#fff;border-radius:20px;padding:2.5rem;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.25);}
    .login-logo{width:70px;height:70px;background:linear-gradient(135deg,#1a6fc4,#0fa8a8);border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;font-size:2rem;color:#fff;}
    .form-control{border-radius:10px;padding:.65rem 1rem;}
    .btn-login{background:linear-gradient(135deg,#1a6fc4,#0d3b6e);border:none;border-radius:10px;padding:.7rem;font-size:1rem;font-weight:600;}
    .input-group-text{border-radius:10px 0 0 10px;background:#f8fafc;}
  </style>
</head>
<body>
<div class="login-card">
  <div class="login-logo"><i class="bi bi-hospital-fill"></i></div>
  <h2 class="text-center fw-bold mb-1" style="color:#0d3b6e"><?= APP_NAME ?></h2>
  <p class="text-center text-muted mb-3 small">Sistema de Gestión Clínica</p>

  <?php if (!empty($error)): ?>
  <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-triangle-fill me-1"></i><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="<?= APP_URL ?>/login">
    <input type="hidden" name="_token" value="<?= $csrf ?>">
    <div class="mb-3">
      <label class="form-label fw-semibold small">Usuario</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-person-fill text-muted"></i></span>
        <input type="text" name="username" class="form-control" placeholder="Ingrese su usuario" required autofocus>
      </div>
    </div>
    <div class="mb-4">
      <label class="form-label fw-semibold small">Contraseña</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-lock-fill text-muted"></i></span>
        <input type="password" name="password" id="pwdInput" class="form-control" placeholder="Ingrese su contraseña" required>
        <button type="button" class="btn btn-outline-secondary" onclick="togglePwd()"><i class="bi bi-eye" id="eyeIcon"></i></button>
      </div>
    </div>
    <button type="submit" class="btn btn-login btn-primary w-100 text-white">
      <i class="bi bi-box-arrow-in-right me-1"></i>Iniciar Sesión
    </button>
  </form>
  <p class="text-center text-muted mt-3 mb-0 small">
    <i class="bi bi-shield-lock me-1"></i>Acceso restringido. Solo personal autorizado.
  </p>
</div>
<script>
function togglePwd(){
  const i=document.getElementById('pwdInput'),e=document.getElementById('eyeIcon');
  i.type=i.type==='password'?'text':'password';
  e.className=i.type==='password'?'bi bi-eye':'bi bi-eye-slash';
}
</script>
</body>
</html>
