<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Detectar sesión guardada
$usuario_logueado = false;
$email_display = '';

if (!empty($_SESSION['usuario'])) {
    $usuario_logueado = true;
    $email_display = $_SESSION['usuario'];
} elseif (!empty($_SESSION['user_email'])) {
    $usuario_logueado = true;
    $email_display = $_SESSION['user_email'];
}

$inicial = !empty($email_display) ? strtoupper(substr($email_display, 0, 1)) : 'U';

// Calcular total de productos en el carrito
$cantidad_carrito = 0;
if (!empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $cantidad_carrito += $item['cantidad'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glow Fashion</title>
    <link rel="stylesheet" href="/glow-fashion/public/css/style.css?v=6.0">
</head>
<body>

<header class="navbar">
    <div class="logo-container">
        <a href="/glow-fashion/index.php?url=home" class="logo-link">
            <img src="/glow-fashion/public/img/logo.png" alt="Glow Fashion Logo" class="logo-img">
        </a>
    </div>

    <nav class="nav-links">
        <a href="/glow-fashion/index.php?url=home">Inicio</a>
        <a href="/glow-fashion/index.php?url=categoria/maquillaje">Maquillaje y Accesorios</a>
        <a href="/glow-fashion/index.php?url=categoria/ropa">Ropa Hombre/Mujer</a>

        <!-- Botón del Carrito con Contador -->
        <a href="/glow-fashion/index.php?url=carrito" class="btn-cart-nav" style="position: relative; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; color: #1f2937; font-weight: 600;">
            <span>🛒</span>
            <span>Carrito</span>
            <?php if ($cantidad_carrito > 0): ?>
                <span class="cart-badge" style="background-color: #7e46d6; color: white; border-radius: 50%; padding: 2px 7px; font-size: 12px; font-weight: bold; font-family: sans-serif; display: inline-block; text-align: center;">
                    <?php echo $cantidad_carrito; ?>
                </span>
            <?php endif; ?>
        </a>

        <?php if ($usuario_logueado): ?>
            <!-- MENÚ DESPLEGABLE DE USUARIO -->
            <div class="user-dropdown">
                <button type="button" class="avatar-btn">
                    <?php echo htmlspecialchars($inicial); ?>
                </button>
                
                <div class="dropdown-content">
                    <div class="dropdown-header">
                        <span class="user-label">Conectado como</span>
                        <strong class="user-email"><?php echo htmlspecialchars($email_display); ?></strong>
                    </div>
                    
                    <div class="dropdown-menu-list">
                        <a href="/glow-fashion/index.php?url=admin/productos" class="dropdown-item">
                            <span class="item-icon">📦</span>
                            <span>Gestionar Productos</span>
                        </a>
                        <a href="/glow-fashion/index.php?url=admin/ventas" class="dropdown-item">
                            <span class="item-icon">📊</span>
                            <span>Gestionar Ventas</span>
                        </a>
                    </div>

                    <div class="dropdown-footer">
                        <a href="/glow-fashion/index.php?url=logout" class="dropdown-item logout-item">
                            <span class="item-icon">🚪</span>
                            <span>Cerrar Sesión</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <a href="/glow-fashion/index.php?url=login" class="btn-login-nav">Iniciar Sesión</a>
        <?php endif; ?>
    </nav>
</header>