<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir helpers
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/helpers/auth.php';
// Cargar rutas
$routes = require __DIR__ . '/../routes/web.php';

// Método y path actuales
$method = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 🚩 Detectar basePath automático
$basePath = str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'])));

// Quitar el basePath de la ruta solicitada (usamos stripos para ignorar mayúsculas/minúsculas)
if ($basePath !== '/' && stripos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// Limpiar "/public" si el navegador lo incluye en la URL
$path = str_ireplace(['/public/index.php', '/public'], '', $path);

// Quitar la barra al final si existe
$path = rtrim($path, '/');

if ($path === '' || $path === false) {
    $path = '/';
}

// Buscar coincidencia en rutas
$routeFound = false;
foreach ($routes as $r) {
    if ($r['method'] === $method && $r['path'] === $path) {
        $routeFound = true;
        [$controller, $action] = explode('@', $r['target']);
        
        $controllerPath = __DIR__ . '/../app/Controllers/' . $controller . '.php';
        
        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            $c = new $controller();
            $c->$action();
            exit;
        } else {
            http_response_code(500);
            include __DIR__ . '/500.php';
            exit;
        }
    }
}   

// Si no encuentra ruta
    if (!$routeFound) {
        http_response_code(404);
        include __DIR__ . '/404.php';
        exit;
    }