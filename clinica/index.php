<?php
/**
 * index.php — Punto de entrada del sistema
 * Sistema Web Clínica | PHP 8+ MVC
 */
declare(strict_types=1);

define('ROOT', __DIR__);
require_once ROOT . '/config/app.php';

ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');
session_name(SESSION_NAME);
session_set_cookie_params(['lifetime' => SESSION_LIFE, 'path' => '/', 'samesite' => 'Strict']);
session_start();

spl_autoload_register(function(string $class): void {
    $dirs = [
        ROOT . '/app/Core/',
        ROOT . '/app/Controllers/',
        ROOT . '/app/Models/',
        ROOT . '/app/Middleware/'
    ];
    foreach ($dirs as $dir) {
        $file = $dir . $class . '.php';
        if (file_exists($file)) { require_once $file; return; }
    }
});

require_once ROOT . '/app/Core/Router.php';
require_once ROOT . '/app/Core/Controller.php';
require_once ROOT . '/app/Core/Database.php';

$router = new Router();

// ─── Auth ───────────────────────────────────────────────────
$router->get('/login',  ['AuthController', 'login']);
$router->post('/login', ['AuthController', 'doLogin']);
$router->get('/logout', ['AuthController', 'logout']);

// ─── Dashboard ──────────────────────────────────────────────
$router->get('/',          ['DashboardController', 'index']);
$router->get('/dashboard', ['DashboardController', 'index']);

// ─── Pacientes ──────────────────────────────────────────────
$router->get('/pacientes',                ['PacienteController', 'index']);
$router->get('/pacientes/crear',          ['PacienteController', 'create']);
$router->post('/pacientes/crear',         ['PacienteController', 'store']);
$router->get('/pacientes/{id}/editar',    ['PacienteController', 'edit']);
$router->post('/pacientes/{id}/editar',   ['PacienteController', 'update']);
$router->post('/pacientes/{id}/eliminar', ['PacienteController', 'delete']);
$router->get('/pacientes/{id}',           ['PacienteController', 'show']);
$router->get('/api/pacientes',            ['PacienteController', 'apiSearch']);

// ─── Médicos ────────────────────────────────────────────────
$router->get('/medicos',                ['MedicoController', 'index']);
$router->get('/medicos/crear',          ['MedicoController', 'create']);
$router->post('/medicos/crear',         ['MedicoController', 'store']);
$router->get('/medicos/{id}/editar',    ['MedicoController', 'edit']);
$router->post('/medicos/{id}/editar',   ['MedicoController', 'update']);
$router->post('/medicos/{id}/eliminar', ['MedicoController', 'delete']);
$router->get('/api/medicos',            ['MedicoController', 'apiList']);

// ─── Citas ──────────────────────────────────────────────────
$router->get('/citas',                   ['CitaController', 'index']);
$router->get('/citas/crear',             ['CitaController', 'create']);
$router->post('/citas/crear',            ['CitaController', 'store']);
$router->get('/citas/{id}/editar',       ['CitaController', 'edit']);
$router->post('/citas/{id}/editar',      ['CitaController', 'update']);
$router->post('/citas/{id}/cancelar',    ['CitaController', 'cancel']);
$router->post('/citas/{id}/completar',   ['CitaController', 'complete']);
$router->post('/citas/{id}/eliminar',    ['CitaController', 'delete']);

// ─── Historia clínica ───────────────────────────────────────
$router->get('/historia/{id}',           ['HistoriaController', 'show']);
$router->post('/historia/{id}/consulta', ['HistoriaController', 'addConsulta']);

// ─── Reportes ───────────────────────────────────────────────
$router->get('/reportes',           ['ReporteController', 'index']);
$router->get('/reportes/citas',     ['ReporteController', 'citas']);
$router->get('/reportes/pacientes', ['ReporteController', 'pacientes']);

// ─── Usuarios ───────────────────────────────────────────────
$router->get('/usuarios',                ['UsuarioController', 'index']);
$router->get('/usuarios/crear',          ['UsuarioController', 'create']);
$router->post('/usuarios/crear',         ['UsuarioController', 'store']);
$router->get('/usuarios/{id}/editar',    ['UsuarioController', 'edit']);
$router->post('/usuarios/{id}/editar',   ['UsuarioController', 'update']);
$router->post('/usuarios/{id}/eliminar', ['UsuarioController', 'delete']);

// ─── Áreas ──────────────────────────────────────────────────
$router->get('/areas',                ['AreaController', 'index']);
$router->get('/areas/crear',          ['AreaController', 'create']);
$router->post('/areas/crear',         ['AreaController', 'store']);
$router->get('/areas/{id}/editar',    ['AreaController', 'edit']);
$router->post('/areas/{id}/editar',   ['AreaController', 'update']);
$router->post('/areas/{id}/eliminar', ['AreaController', 'delete']);

// ─── Enfermeras ─────────────────────────────────────────────
$router->get('/enfermeras',                    ['EnfermeraController', 'index']);
$router->get('/enfermeras/crear',              ['EnfermeraController', 'create']);
$router->post('/enfermeras/crear',             ['EnfermeraController', 'store']);
$router->get('/enfermeras/{id}',               ['EnfermeraController', 'show']);
$router->get('/enfermeras/{id}/editar',        ['EnfermeraController', 'edit']);
$router->post('/enfermeras/{id}/editar',       ['EnfermeraController', 'update']);
$router->post('/enfermeras/{id}/eliminar',     ['EnfermeraController', 'delete']);

// ─── Camas ──────────────────────────────────────────────────
$router->get('/camas',                         ['CamaController', 'index']);
$router->get('/camas/crear',                   ['CamaController', 'create']);
$router->post('/camas/crear',                  ['CamaController', 'store']);
$router->get('/camas/{nro}/{area}/asignar',    ['CamaController', 'asignar']);
$router->post('/camas/{nro}/{area}/asignar',   ['CamaController', 'doAsignar']);
$router->post('/camas/{nro}/{area}/liberar',   ['CamaController', 'liberar']);
$router->post('/camas/{nro}/{area}/eliminar',  ['CamaController', 'delete']);

// ─── Campañas ───────────────────────────────────────────────
$router->get('/campanias',                                ['CampaniaController', 'index']);
$router->get('/campanias/crear',                          ['CampaniaController', 'create']);
$router->post('/campanias/crear',                         ['CampaniaController', 'store']);
$router->get('/campanias/{id}',                           ['CampaniaController', 'show']);
$router->get('/campanias/{id}/editar',                    ['CampaniaController', 'edit']);
$router->post('/campanias/{id}/editar',                   ['CampaniaController', 'update']);
$router->post('/campanias/{id}/eliminar',                 ['CampaniaController', 'delete']);
$router->post('/campanias/{id}/participante/agregar',     ['CampaniaController', 'addParticipante']);
$router->post('/campanias/{id}/participante/quitar',      ['CampaniaController', 'removeParticipante']);

// ─── Perfil ─────────────────────────────────────────────────
$router->get('/perfil',              ['PerfilController', 'index']);
$router->post('/perfil/actualizar',  ['PerfilController', 'update']);
$router->post('/perfil/password',    ['PerfilController', 'cambiarPassword']);

// ─── Dispatch ───────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'];
$uri    = $_SERVER['REQUEST_URI'];
$router->dispatch($method, $uri);
