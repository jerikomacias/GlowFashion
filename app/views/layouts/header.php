<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Detectar cualquier variable de sesión donde se haya guardado el email o usuario
$usuario_logueado = false;
$email_display = '';

if (!empty($_SESSION['user_email'])) {
    $usuario_logueado = true;
    $email_display = $_SESSION['user_email'];
} elseif (!empty($_SESSION['user'])) {
    $usuario_logueado = true;
    $email_display = is_array($_SESSION['user']) ? ($_SESSION['user']['email'] ?? 'Usuario') : $_SESSION['user'];
} elseif (!empty($_SESSION['email'])) {
    $usuario_logueado = true;
    $email_display = $_SESSION['email'];
} elseif (!empty($_SESSION['usuario'])) {
    $usuario_logueado = true;
    $email_display = $_SESSION['usuario'];
}

$inicial = !empty($email_display) ? strtoupper(substr($email_display, 0, 1)) : 'U';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Glow Fashion</title>
    <link rel="stylesheet" href="/glow-fashion/public/css/style.css?v=3.0">
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