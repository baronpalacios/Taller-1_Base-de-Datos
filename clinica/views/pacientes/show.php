<?php
$pageTitle  = 'Ficha Paciente';
$activeMenu = 'pacientes';
$breadcrumb = 'Ficha Paciente';
ob_start();
?>
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
  <div><h1><i class="bi bi-person-vcard text-primary me-2"></i><?= htmlspecialchars($paciente['nombre']) ?></h1>
  <p>ID: <?= htmlspecialchars($paciente['identificacion']) ?> | NSS: <?= htmlspecialchars($paciente['nss']) ?></p></div>
  <div class="d-flex gap-2">
    <a href="<?= APP_URL ?>/historia/<?= urlencode($paciente['identificacion']) ?>" class="btn btn-success"><i class="bi bi-journal-medical me-1"></i>Historia Clínica</a>
    <?php if (in_array($_SESSION['user']['rol'],['admin','recepcionista'])): ?>
    <a href="<?= APP_URL ?>/pacientes/<?= urlencode($paciente['identificacion']) ?>/editar" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Editar</a>
    <?php endif; ?>
    <a href="<?= APP_URL ?>/pacientes" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Volver</a>
  </div>
</div>
<div class="row g-3">
  <div class="col-md-5">
    <div class="card h-100">
      <div class="card-header p-3"><h6 class="mb-0 fw-bold"><i class="bi bi-person-fill text-primary me-2"></i>Datos Personales</h6></div>
      <div class="card-body">
        <?php $fields = ['Nombre'=>$paciente['nombre'],'Identificación'=>$paciente['identificacion'],'Teléfono'=>$paciente['telefono']??'—','Dirección'=>$paciente['direccion']??'—','Fecha Nacimiento'=>$paciente['fecha_nacimiento']??'—','Edad'=>($paciente['edad']??'—').' años','Labor'=>$paciente['labor']??'—','EPS'=>$paciente['eps']??'—','NSS'=>$paciente['nss'],'Fecha Afiliación'=>$paciente['fecha_afiliacion']??'—']; ?>
        <?php foreach ($fields as $k=>$v): ?>
        <div class="d-flex justify-content-between py-2 border-bottom">
          <span class="text-muted small"><?= $k ?></span>
          <strong class="small text-end" style="max-width:60%"><?= htmlspecialchars($v) ?></strong>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <div class="col-md-7">
    <div class="card">
      <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="bi bi-journal-medical text-success me-2"></i>Últimas Consultas</h6>
        <a href="<?= APP_URL ?>/historia/<?= urlencode($paciente['identificacion']) ?>" class="btn btn-sm btn-outline-success">Ver historia completa</a>
      </div>
      <div class="table-wrapper">
        <?php if (empty($consultas)): ?>
        <div class="text-center py-4 text-muted"><i class="bi bi-journal-x fs-3 d-block mb-2"></i>Sin consultas registradas</div>
        <?php else: ?>
        <table class="table table-hover mb-0">
          <thead><tr><th>Fecha</th><th>Médico</th><th>Diagnóstico</th><th>Precio</th></tr></thead>
          <tbody>
            <?php foreach (array_slice($consultas,0,5) as $c): ?>
            <tr>
              <td><?= $c['fecha_consulta'] ?></td>
              <td><?= htmlspecialchars($c['nombre_medico']) ?></td>
              <td><span title="<?= htmlspecialchars($c['diagnostico']??'') ?>" style="max-width:150px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($c['diagnostico'] ?? $c['resumen'] ?? '—') ?></span></td>
              <td>$<?= number_format($c['precio'],0,',','.') ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../layouts/main.php'; ?>
