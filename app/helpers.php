<?php
// Funciones de apoyo compartidas por controladores y vistas.

// Escapa texto para imprimirlo de forma segura en HTML (previene XSS)
function e($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

// Redirige a una ruta interna: redirigir('carrito') -> /glow-fashion/index.php?url=carrito
function redirigir(string $ruta): void {
    header('Location: ' . URLROOT . '/index.php?url=' . $ruta);
    exit();
}

// URL de un archivo de public/ con versión automática (cambia cuando editas el archivo,
// así el navegador nunca usa un CSS/JS viejo guardado en caché)
function asset(string $ruta): string {
    $archivo = APPROOT . '/public/' . $ruta;
    $version = is_file($archivo) ? filemtime($archivo) : 1;
    return URLROOT . '/public/' . $ruta . '?v=' . $version;
}

function formatear_precio($valor): string {
    return '$' . number_format((float)$valor, 0, ',', '.');
}

// ---------- Mensajes flash (se muestran una sola vez) ----------
function flash(string $tipo, string $mensaje): void {
    $_SESSION['flash'][] = ['tipo' => $tipo, 'mensaje' => $mensaje];
}

function render_flash(): void {
    if (empty($_SESSION['flash'])) {
        return;
    }
    foreach ($_SESSION['flash'] as $f) {
        $ok    = ($f['tipo'] === 'exito');
        $fondo = $ok ? '#e6ffe6' : '#ffe6e6';
        $color = $ok ? '#2e7d32' : '#d9534f';
        echo '<div class="flash" role="alert" style="max-width: 1000px; margin: 16px auto; padding: 12px 16px; border-radius: 8px; font-size: 14px; text-align: center; background: ' . $fondo . '; color: ' . $color . ';">'
            . e($f['mensaje']) . '</div>';
    }
    unset($_SESSION['flash']);
}

// ---------- Protección CSRF ----------
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void {
    $enviado = $_POST['csrf_token'] ?? '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], (string)$enviado)) {
        http_response_code(419);
        die('La sesión del formulario expiró. Vuelve atrás, recarga la página e inténtalo de nuevo.');
    }
}

// ---------- Sesión y permisos ----------
function esta_logueado(): bool {
    return !empty($_SESSION['usuario_id']);
}

// rol_id 1 = Admin, 2 = Cliente (tabla roles)
function es_admin(): bool {
    return esta_logueado() && (int)($_SESSION['usuario_rol'] ?? 0) === 1;
}

function requerir_login(): void {
    if (!esta_logueado()) {
        flash('error', 'Inicia sesión para continuar.');
        redirigir('login');
    }
}

function requerir_admin(): void {
    requerir_login();
    if (!es_admin()) {
        http_response_code(403);
        require APPROOT . '/app/views/errors/403.php';
        exit();
    }
}

function requerir_post(): void {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        redirigir('home');
    }
}
