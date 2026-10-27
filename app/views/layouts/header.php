<?php
$usuario_logueado = esta_logueado();
$nombre_display   = $_SESSION['usuario_nombre'] ?? '';
$email_display    = $_SESSION['usuario_email'] ?? '';
$inicial          = $nombre_display !== '' ? mb_strtoupper(mb_substr($nombre_display, 0, 1)) : 'U';

$cantidad_carrito = CarritoController::contarItems();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glow Fashion</title>
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
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
        <a href="/glow-fashion/index.php?url=categoria/maquillaje">Maquillaje</a>
        <a href="/glow-fashion/index.php?url=categoria/ropa">Ropa y Zapatos</a>
        <a href="/glow-fashion/index.php?url=categoria/accesorios">Accesorios</a>
        <a href="#modal-tratamiento" data-modal-open="modal-tratamiento" role="button">Tratamiento de datos</a>

        <?php if ($usuario_logueado && !es_admin()): ?>
            <a href="/glow-fashion/index.php?url=mis-compras">Mis compras</a>
        <?php endif; ?>

        <!-- Botón del Carrito -->
        <a href="/glow-fashion/index.php?url=carrito" class="btn-cart-nav" style="position: relative; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; color: #1f2937; font-weight: 600;">
            <span>🛒</span>
            <span>Carrito</span>
            <?php if ($cantidad_carrito > 0): ?>
                <span class="cart-badge" style="background-color: #7e46d6; color: white; border-radius: 50%; padding: 2px 7px; font-size: 12px; font-weight: bold; display: inline-block;">
                    <?php echo $cantidad_carrito; ?>
                </span>
            <?php endif; ?>
        </a>

        <?php if ($usuario_logueado): ?>
            <div class="user-dropdown">
                <button type="button" class="avatar-btn">
                    <?php echo e($inicial); ?>
                </button>

                <div class="dropdown-content">
                    <div class="dropdown-header">
                        <span class="user-label">Conectado como</span>
                        <strong class="user-email"><?php echo e($email_display); ?></strong>
                    </div>

                    <div class="dropdown-menu-list">
                        <a href="/glow-fashion/index.php?url=mis-compras" class="dropdown-item">
                            <span class="item-icon">🛍️</span>
                            <span>Mis Compras</span>
                        </a>
                        <?php if (es_admin()): ?>
                            <a href="/glow-fashion/index.php?url=admin/productos" class="dropdown-item">
                                <span class="item-icon">📦</span>
                                <span>Gestionar Productos</span>
                            </a>
                            <a href="/glow-fashion/index.php?url=admin/ventas" class="dropdown-item">
                                <span class="item-icon">📊</span>
                                <span>Gestionar Ventas</span>
                            </a>
                        <?php endif; ?>
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
<?php render_flash(); ?>
