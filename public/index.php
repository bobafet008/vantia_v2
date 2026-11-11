<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../app/helpers.php';
$rutas = require __DIR__ . '/../routes/web.php';

$pagina = $_GET['pagina'] ?? 'inicio';

if (!isset($rutas[$pagina])) {
    http_response_code(404);
    exit('La página solicitada no existe.');
}

[$controlador, $accion] = $rutas[$pagina];
require_once __DIR__ . '/../app/Controllers/' . $controlador . '.php';

$objeto = new $controlador();
$objeto->$accion();
