<?php $pageTitle = $medico['nombre']; ?>
<div class="page-header">
    <div class="d-flex align-items-center gap-3">
        <div class="stat-icon stat-icon-purple" style="width:52px;height:52px;font-size:1.3rem;">
            <i class="bi bi-person-badge-fill"></i>
        </div>
        <div>
            <h1 class="mb-0"><?= htmlspecialchars($medico['nombre']) ?></h1>
            <span class="badge bg-primary"><?= htmlspecialchars($medico['especialidad']) ?></span>
        </div>
    </div>
    <div class="d-flex gap-2">
        <?php if ($_SESSION['user_rol'] === 'admin'): ?>
        <a href="<?= APP_URL ?>/medicos/<?= $medico['identificacion'] ?>/editar"
           class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-pencil me-1"></i>Editar
        </a>
        <?php endif; ?>
        <a href="<?= APP_URL ?>/medicos" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle text-primary me-2"></i>Información</div>
            <div class="card-body">
                <dl class="row small mb-0">
                    <dt class="col-5 text-muted">ID</dt>
                    <dd class="col-7"><?= $medico['identificacion'] ?></dd>
                    <dt class="col-5 text-muted">Licencia</dt>
                    <dd class="col-7"><?= htmlspecialchars($medico['nro_licencia']) ?></dd>
                    <dt class="col-5 text-muted">Cargo</dt>
                    <dd class="col-7"><?= htmlspecialchars($medico['cargo']) ?></dd>
                    <dt class="col-5 text-muted">Teléfono</dt>
                    <dd class="col-7"><?= htmlspecialchars($medico['telefono'] ?? '—') ?></dd>
                    <dt class="col-5 text-muted">Dirección</dt>
                    <dd class="col-7"><?= htmlspecialchars($medico['direccion'] ?? '—') ?></dd>
                    <dt class="col-5 text-muted">Ingreso</dt>
                    <dd class="col-7"><?= $medico['fecha_ingreso'] ?></dd>
                    <?php if ($_SESSION['user_rol'] === 'admin'): ?>
                    <dt class="col-5 text-muted">Salario</dt>
                    <dd class="col-7">$<?= number_format($medico['salario'], 0, ',', '.') ?></dd>
                    <?php endif; ?>
                    <dt class="col-5 text-muted">Universidad</dt>
                    <dd class="col-7"><?= htmlspecialchars($medico['universidad'] ?? '—') ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <!-- Próximas citas -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-calendar-event text-primary me-2"></i>Próximas Citas
            </div>
            <?php if ($citas): ?>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr><th>Fecha</th><th>Hora</th><th>Paciente</th><th>Estado</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($citas as $c): ?>
                        <tr>
                            <td class="small"><?= $c['fecha_cita'] ?></td>
                            <td class="small"><?= substr($c['hora_cita'],0,5) ?></td>
                            <td class="small"><?= htmlspecialchars($c['nombre_paciente']) ?></td>
                            <td><span class="badge-estado badge-<?= $c['estado'] ?>"><?= $c['estado'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="card-body text-muted text-center py-3">Sin citas próximas</div>
            <?php endif; ?>
        </div>

        <!-- Últimas consultas -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-journal-text text-primary me-2"></i>Últimas Consultas
            </div>
            <?php if ($consultas): ?>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr><th>Fecha</th><th>Paciente</th><th>Precio</th><th>Resumen</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($consultas as $c): ?>
                        <tr>
                            <td class="small"><?= $c['fecha_consulta'] ?></td>
                            <td class="small"><?= htmlspecialchars($c['nombre_paciente']) ?></td>
                            <td class="small">$<?= number_format($c['precio'],0,',','.') ?></td>
                            <td class="small text-muted text-truncate" style="max-width:200px">
                                <?= htmlspecialchars($c['resumen'] ?? '') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="card-body text-muted text-center py-3">Sin consultas registradas</div>
            <?php endif; ?>
        </div>
    </div>
</div>
