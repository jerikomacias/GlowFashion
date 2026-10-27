<?php
// ============================================================
// Configuración general de Glow Fashion
// Única fuente de verdad para la conexión a la base de datos.
// ============================================================

// Base de datos (XAMPP por defecto: usuario root, sin contraseña)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'glow_fashion_db');
define('DB_CHARSET', 'utf8mb4');

// Ruta raíz de la aplicación en disco
define('APPROOT', dirname(__DIR__));

// Ruta base en el navegador (nombre de la carpeta dentro de htdocs)
define('URLROOT', '/glow-fashion');

// Nombre del sitio
define('SITENAME', 'Glow Fashion');

// Poner en true SOLO mientras desarrollas: muestra el detalle de los errores.
// En false, el detalle se guarda en el log de Apache (xampp/apache/logs/error.log).
define('APP_DEBUG', false);

// ------------------------------------------------------------
// Cuenta de ADMINISTRADOR (rol_id = 1)
// Al iniciar sesión (o registrarse) con este correo y su contraseña,
// el sistema crea o repara la cuenta admin automáticamente.
// Aquí solo se guarda el HASH de la contraseña, nunca el texto.
// Para cambiarla: genera otro hash con password_hash('NuevaClave', PASSWORD_BCRYPT).
// ------------------------------------------------------------
define('ADMIN_EMAIL', 'admin@glowfashion.com');
define('ADMIN_PASSWORD_HASH', '$2y$10$SIQ9zreffo.5GdOrVd.KCuJY8oFRbl2j8FccG4Xxfqs2WO.T6J81e');
