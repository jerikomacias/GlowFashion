<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Subir un nivel para salir de 'public' y situarse en la raíz del proyecto
$baseDir = dirname(__DIR__);

// Cargar el controlador de autenticación si existe
if (file_exists($baseDir . '/app/controllers/AuthController.php')) {
    require_once $baseDir . '/app/controllers/AuthController.php';
}

// Capturar la URL (por defecto 'home')
$url = $_GET['url'] ?? 'home';

switch ($url) {
    case 'home':
        require_once $baseDir . '/app/views/home/index.php';
        break;

    case 'categoria/maquillaje':
        $_GET['cat'] = 'maquillaje';
        require_once $baseDir . '/app/views/productos/index.php';
        break;

    case 'categoria/ropa':
        $_GET['cat'] = 'ropa';
        require_once $baseDir . '/app/views/productos/index.php';
        break;

    case 'login':
        $auth = new AuthController();
        $auth->login();
        break;

    case 'loginProcess':
        $auth = new AuthController();
        $auth->loginProcess();
        break;

    case 'register':
        $auth = new AuthController();
        $auth->register();
        break;

    case 'registerProcess':
        $auth = new AuthController();
        $auth->registerProcess();
        break;

    case 'logout':
        $auth = new AuthController();
        $auth->logout();
        break;

    case 'admin/productos':
        require_once $baseDir . '/app/views/admin/productos.php';
        break;

    case 'admin/ventas':
        require_once $baseDir . '/app/views/admin/ventas.php';
        break;

    case 'carrito':
        require_once $baseDir . '/app/views/carrito/index.php';
        break;

    default:
        require_once $baseDir . '/app/views/home/index.php';
        break;
}