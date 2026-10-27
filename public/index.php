<?php
// ============================================================
// Controlador frontal: todas las páginas entran por aquí.
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

$baseDir = dirname(__DIR__);

require_once $baseDir . '/config/config.php';
require_once $baseDir . '/config/database.php';
require_once $baseDir . '/app/helpers.php';

require_once $baseDir . '/app/models/Product.php';
require_once $baseDir . '/app/models/User.php';
require_once $baseDir . '/app/models/Venta.php';
require_once $baseDir . '/app/models/Carrito.php';

require_once $baseDir . '/app/controllers/HomeController.php';
require_once $baseDir . '/app/controllers/ProductosController.php';
require_once $baseDir . '/app/controllers/CarritoController.php';
require_once $baseDir . '/app/controllers/VentaController.php';
require_once $baseDir . '/app/controllers/AuthController.php';
require_once $baseDir . '/app/controllers/AdminController.php';

// Cualquier error no controlado (por ejemplo, la BD apagada) se registra y
// se muestra una página amable en lugar de mensajes técnicos.
set_exception_handler(function ($e) {
    error_log('[glow-fashion] ' . get_class($e) . ': ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Error - Glow Fashion</title></head>'
       . '<body style="text-align:center;padding:50px;font-family:sans-serif;">'
       . '<h1>Ups, algo salió mal</h1>'
       . '<p>No pudimos completar la operación. Inténtalo de nuevo en unos minutos.</p>';
    if (APP_DEBUG) {
        echo '<pre style="text-align:left;background:#f3f4f6;padding:15px;border-radius:8px;overflow:auto;">'
           . htmlspecialchars($e->getMessage() . "\n" . $e->getTraceAsString(), ENT_QUOTES, 'UTF-8') . '</pre>';
    }
    echo '<a href="' . URLROOT . '/index.php?url=home">Volver al inicio</a></body></html>';
});

$url = trim($_GET['url'] ?? '', '/');
if ($url === '') {
    $url = 'home';
}

switch ($url) {
    case 'home':
        (new HomeController())->index();
        break;

    case 'sobre-nosotros':
        (new HomeController())->sobreNosotros();
        break;

    case 'categoria/maquillaje':
        (new ProductosController())->categoria('maquillaje');
        break;

    case 'categoria/ropa':
        (new ProductosController())->categoria('ropa');
        break;

    case 'categoria/accesorios':
        (new ProductosController())->categoria('accesorios');
        break;

    // ---- Carrito y compra ----
    case 'carrito':
        (new CarritoController())->index();
        break;

    case 'carrito/agregar':
        (new CarritoController())->agregar();
        break;

    case 'carrito/actualizar':
        (new CarritoController())->actualizar();
        break;

    case 'carrito/vaciar':
        (new CarritoController())->vaciar();
        break;

    case 'procesar-compra':
        (new VentaController())->procesarCompra();
        break;

    case 'compra/confirmacion':
        (new VentaController())->confirmacion();
        break;

    case 'mis-compras':
        (new VentaController())->misCompras();
        break;

    // ---- Autenticación ----
    case 'login':
        (new AuthController())->login();
        break;

    case 'loginProcess':
        (new AuthController())->loginProcess();
        break;

    case 'register':
        (new AuthController())->register();
        break;

    case 'registerProcess':
        (new AuthController())->registerProcess();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    // ---- Panel de administración (solo rol Admin) ----
    case 'admin/productos':
        (new AdminController())->productos();
        break;

    case 'admin/guardar-producto':
        (new AdminController())->guardarProducto();
        break;

    case 'admin/editar-producto':
        (new AdminController())->editarProducto();
        break;

    case 'admin/actualizar-producto':
        (new AdminController())->actualizarProducto();
        break;

    case 'admin/eliminar-producto':
        (new AdminController())->eliminarProducto();
        break;

    case 'admin/ventas':
        (new AdminController())->ventas();
        break;

    case 'admin/exportar-ventas':
        (new AdminController())->exportarVentas();
        break;

    default:
        (new HomeController())->noEncontrado();
        break;
}
