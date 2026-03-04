<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

echo "<h2>PHP " . PHP_VERSION . "</h2>";
echo "<p>ROOT: " . __DIR__ . "</p>";

echo "<h3>1. Config</h3>";
try {
    define('ROOT', __DIR__);
    require_once ROOT . '/config/app.php';
    echo "✓ config/app.php OK — APP_URL: " . APP_URL . "<br>";
} catch (Throwable $e) {
    echo "<b style='color:red'>✗ config/app.php ERROR: " . $e->getMessage() . " línea:" . $e->getLine() . "</b><br>";
    die();
}

echo "<h3>2. Database</h3>";
try {
    require_once ROOT . '/app/Core/Database.php';
    $db = Database::getInstance();
    echo "✓ BD conectada<br>";
    echo "✓ Usuarios: " . $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn() . "<br>";
} catch (Throwable $e) {
    echo "<b style='color:red'>✗ BD ERROR: " . $e->getMessage() . "</b><br>";
}

echo "<h3>3. Core files</h3>";
foreach (['Router','Controller'] as $c) {
    try {
        require_once ROOT . '/app/Core/' . $c . '.php';
        echo "✓ $c OK<br>";
    } catch (Throwable $e) {
        echo "<b style='color:red'>✗ $c: " . $e->getMessage() . " L:" . $e->getLine() . "</b><br>";
    }
}

echo "<h3>4. Controllers</h3>";
foreach (['Auth','Dashboard','Paciente','Medico','Cita','Historia','Reporte','Usuario','Area','Enfermera','Cama','Campania','Perfil'] as $c) {
    $f = ROOT . '/app/Controllers/' . $c . 'Controller.php';
    if (!file_exists($f)) { echo "<b style='color:orange'>⚠ {$c}Controller — NO EXISTE</b><br>"; continue; }
    try {
        require_once $f;
        echo "✓ {$c}Controller OK<br>";
    } catch (Throwable $e) {
        echo "<b style='color:red'>✗ {$c}Controller: " . $e->getMessage() . " L:" . $e->getLine() . " en " . $e->getFile() . "</b><br>";
    }
}

echo "<h3>5. Models</h3>";
foreach (['Paciente','Medico','Cita','Historia','Usuario','Area','Enfermera','Cama','Campania'] as $m) {
    $f = ROOT . '/app/Models/' . $m . 'Model.php';
    if (!file_exists($f)) { echo "<b style='color:orange'>⚠ {$m}Model — NO EXISTE</b><br>"; continue; }
    try {
        require_once $f;
        echo "✓ {$m}Model OK<br>";
    } catch (Throwable $e) {
        echo "<b style='color:red'>✗ {$m}Model: " . $e->getMessage() . " L:" . $e->getLine() . "</b><br>";
    }
}

echo "<h3>6. index.php completo</h3>";
try {
    // Simular carga del index sin ejecutar dispatch
    $content = file_get_contents(ROOT . '/index.php');
    // Verificar sintaxis buscando el dispatch
    if (strpos($content, 'dispatch') !== false) {
        $pos = strrpos($content, 'dispatch');
        $totalLen = strlen($content);
        echo "✓ dispatch encontrado en posición $pos de $totalLen chars<br>";
        $linesTotal = substr_count($content, "\n");
        echo "✓ Total líneas en index.php: $linesTotal<br>";
    }
} catch (Throwable $e) {
    echo "<b style='color:red'>✗ index.php: " . $e->getMessage() . "</b><br>";
}

echo "<h3>7. Error log</h3>";
$logs = ['/home/clinicaclasespit/logs/error_log', '/home/clinicaclasespit/public_html/error_log', __DIR__ . '/error_log'];
foreach ($logs as $log) {
    if (file_exists($log) && is_readable($log)) {
        $lines = array_slice(file($log), -30);
        echo "<b>$log:</b><pre style='background:#111;color:#f66;padding:8px;font-size:.7rem'>" . htmlspecialchars(implode('', $lines)) . "</pre>";
    }
}
echo "<p style='color:green'>✓ Diagnóstico completo</p>";
