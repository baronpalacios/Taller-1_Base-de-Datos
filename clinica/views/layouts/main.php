<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Panel' ?> — <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="brand">
            <i class="bi bi-hospital-fill me-2"></i>
            <span class="brand-text"><?= APP_NAME ?></span>
        </div>
        <button class="btn btn-link text-white d-lg-none p-0" id="sidebarClose">
            <i class="bi bi-x-lg fs-5"></i>
        </button>
    </div>
    <ul class="sidebar-nav">
        <li>
            <a href="<?= APP_URL ?>/dashboard" class="nav-link <?= $activeMenu === 'dashboard' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i><span>Dashboard</span>
            </a>
        </li>
        <li class="nav-section">Gestión Clínica</li>
        <li>
            <a href="<?= APP_URL ?>/pacientes" class="nav-link <?= $activeMenu === 'pacientes' ? 'active' : '' ?>">
                <i class="bi bi-people-fill"></i><span>Pacientes</span>
            </a>
        </li>
        <li>
            <a href="<?= APP_URL ?>/citas" class="nav-link <?= $activeMenu === 'citas' ? 'active' : '' ?>">
                <i class="bi bi-calendar-check-fill"></i><span>Citas</span>
            </a>
        </li>
        <li>
            <a href="<?= APP_URL ?>/medicos" class="nav-link <?= $activeMenu === 'medicos' ? 'active' : '' ?>">
                <i class="bi bi-person-badge-fill"></i><span>Médicos</span>
            </a>
        </li>
        <?php if ($_SESSION['user']['rol'] !== 'recepcionista'): ?>
        <li class="nav-section">Hospitalización</li>
        <li>
            <a href="<?= APP_URL ?>/areas" class="nav-link <?= $activeMenu === 'areas' ? 'active' : '' ?>">
                <i class="bi bi-building"></i><span>Áreas</span>
            </a>
        </li>
        <li>
            <a href="<?= APP_URL ?>/enfermeras" class="nav-link <?= $activeMenu === 'enfermeras' ? 'active' : '' ?>">
                <i class="bi bi-person-heart"></i><span>Enfermeras</span>
            </a>
        </li>
        <li>
            <a href="<?= APP_URL ?>/camas" class="nav-link <?= $activeMenu === 'camas' ? 'active' : '' ?>">
                <i class="bi bi-hospital"></i><span>Camas</span>
            </a>
        </li>
        <li>
            <a href="<?= APP_URL ?>/campanias" class="nav-link <?= $activeMenu === 'campanias' ? 'active' : '' ?>">
                <i class="bi bi-megaphone-fill"></i><span>Campañas</span>
            </a>
        </li>
        <li class="nav-section">Reportes</li>
        <li>
            <a href="<?= APP_URL ?>/reportes" class="nav-link <?= $activeMenu === 'reportes' ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-bar-graph-fill"></i><span>Reportes</span>
            </a>
        </li>
        <?php endif; ?>
        <?php if ($_SESSION['user']['rol'] === 'admin'): ?>
        <li class="nav-section">Administración</li>
        <li>
            <a href="<?= APP_URL ?>/usuarios" class="nav-link <?= $activeMenu === 'usuarios' ? 'active' : '' ?>">
                <i class="bi bi-person-lock"></i><span>Usuarios</span>
            </a>
        </li>
        <li>
            <a href="<?= APP_URL ?>/perfil" class="nav-link <?= $activeMenu === 'perfil' ? 'active' : '' ?>">
                <i class="bi bi-person-circle"></i><span>Mi Perfil</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                <i class="bi bi-person-circle fs-4"></i>
            </div>
            <div class="user-details">
                <span class="user-name"><?= htmlspecialchars($_SESSION['user']['username']) ?></span>
                <span class="user-role badge bg-light text-primary"><?= $_SESSION['user']['rol'] ?></span>
            </div>
        </div>
        <a href="<?= APP_URL ?>/logout" class="btn btn-logout">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</div>

<!-- Overlay para mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Main Content -->
<div class="main-content" id="mainContent">
    <!-- Top Navbar -->
    <nav class="topbar">
        <button class="btn btn-link text-dark p-0" id="sidebarToggle">
            <i class="bi bi-list fs-4"></i>
        </button>
        <ol class="breadcrumb mb-0 ms-3 d-none d-sm-flex">
            <li class="breadcrumb-item"><a href="<?= APP_URL ?>/dashboard">Inicio</a></li>
            <?php if (isset($breadcrumb)): ?>
            <li class="breadcrumb-item active"><?= $breadcrumb ?></li>
            <?php endif; ?>
        </ol>
        <div class="ms-auto d-flex align-items-center gap-2">
            <span class="d-none d-md-inline text-muted small">
                <?= date('d/m/Y H:i') ?>
            </span>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="page-content">
        <?php if (isset($flash)): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show" role="alert">
            <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill me-2"></i>
            <?= $flash['msg'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
<?= $scripts ?? '' ?>
</body>
</html>
