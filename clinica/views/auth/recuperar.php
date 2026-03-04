<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/clinica.css">
</head>
<body>
<div class="login-page">
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="login-logo"><i class="bi bi-key-fill"></i></div>
            <h1 class="h5 fw-bold">Recuperar Contraseña</h1>
            <p class="text-muted small">Ingresa tu correo y recibirás las instrucciones</p>
        </div>

        <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> small alert-permanent"><?= $flash['msg'] ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= APP_URL ?>/recuperar-password">
            <input type="hidden" name="_csrf" value="<?= $csrf ?>">
            <div class="mb-3">
                <label class="form-label">Correo electrónico</label>
                <input type="email" name="email" class="form-control" required autofocus
                       placeholder="tu@correo.com">
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-send me-2"></i>Enviar instrucciones
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="<?= APP_URL ?>/login" class="text-muted small">
                <i class="bi bi-arrow-left me-1"></i>Volver al login
            </a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
