<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Subir un nivel para salir de la carpeta 'public' y llegar a la raíz del proyecto
$baseDir = dirname(__DIR__);

// Capturar la ruta recibida por GET (por defecto 'home')
$url = $_GET['url'] ?? 'home';

switch ($url) {
    case 'home':
        require_once $baseDir . '/app/views/home/index.php';
        break;

    case 'admin/productos':
        require_once $baseDir . '/app/views/admin/productos.php';
        break;

    case 'admin/ventas':
        require_once $baseDir . '/app/views/admin/ventas.php';
        break;

    case 'login':
        require_once $baseDir . '/app/views/auth/login.php';
        break;

    case 'register':
        require_once $baseDir . '/app/views/auth/register.php';
        break;

    case 'logout':
        session_destroy();
        header('Location: /glow-fashion/index.php?url=home');
        exit();

    default:
        require_once $baseDir . '/app/views/home/index.php';
        break;
}