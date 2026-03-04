<?php
$pageTitle  = '404';
$activeMenu = '';
ob_start();
?>
<div class="text-center py-5">
  <div style="font-size:5rem;color:#cbd5e1">404</div>
  <h2 class="fw-bold">Página no encontrada</h2>
  <p class="text-muted">La ruta solicitada no existe.</p>
  <a href="<?= APP_URL ?>/dashboard" class="btn btn-primary"><i class="bi bi-house me-1"></i>Ir al inicio</a>
</div>
<?php
$content = ob_get_clean();
if (isset($_SESSION['user'])) require __DIR__ . '/layouts/main.php';
else echo $content;
