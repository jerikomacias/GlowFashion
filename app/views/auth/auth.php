<?php
session_start();

// 1. CONEXIÓN A LA BASE DE DATOS (Ajusta los datos si tu config varía)
$host = "localhost";
$user = "root";
$pass = "";
$db = "glow_fashion_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}

$mensaje_error = "";
$mensaje_exito = "";
$tab_activa = "login"; // Pestaña por defecto

// 2. PROCESAMIENTO DE REGISTRO
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == 'registro') {
    $tab_activa = "registro";
    $nombre   = trim($_POST['nombre']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($nombre) && !empty($email) && !empty($password)) {
        // Verificar si el correo ya existe
        $check = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $mensaje_error = "El correo electrónico ya está registrado.";
        } else {
            // Encriptar la contraseña por seguridad
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insertar en MySQL
            $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nombre, $email, $passwordHash);

            if ($stmt->execute()) {
                $mensaje_exito = "¡Registro exitoso! Ya puedes iniciar sesión.";
                $tab_activa = "login";
            } else {
                $mensaje_error = "Error al registrar en la base de datos: " . $conn->error;
            }
            $stmt->close();
        }
        $check->close();
    } else {
        $mensaje_error = "Por favor completa todos los campos del registro.";
    }
}

// 3. PROCESAMIENTO DE INICIO DE SESIÓN
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == 'login') {
    $tab_activa = "login";
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, nombre, password FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $usuario = $res->fetch_assoc();
            if (password_verify($password, $usuario['password'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                header("Location: /glow-fashion/");
                exit;
            } else {
                $mensaje_error = "Contraseña incorrecta.";
            }
        } else {
            $mensaje_error = "No existe una cuenta con ese correo.";
        }
        $stmt->close();
    } else {
        $mensaje_error = "Por favor ingresa tu correo y contraseña.";
    }
}

include_once __DIR__ . '/../layouts/header.php'; 
?>

<section class="auth-section">
    <div class="auth-container">
        <!-- Pestañas de selección -->
        <div class="auth-tabs">
            <button class="tab-btn <?php echo $tab_activa === 'login' ? 'active' : ''; ?>" onclick="mostrarFormulario('login')">Iniciar Sesión</button>
            <button class="tab-btn <?php echo $tab_activa === 'registro' ? 'active' : ''; ?>" onclick="mostrarFormulario('registro')">Registrarse</button>
        </div>

        <!-- Mensajes de Estado/Error -->
        <?php if (!empty($mensaje_error)): ?>
            <div style="background-color: #ffe6e6; color: #d93025; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-family: sans-serif; font-size: 14px; text-align: center;">
                <?php echo $mensaje_error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($mensaje_exito)): ?>
            <div style="background-color: #e6fffa; color: #0d9488; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-family: sans-serif; font-size: 14px; text-align: center;">
                <?php echo $mensaje_exito; ?>
            </div>
        <?php endif; ?>

        <!-- FORMULARIO DE INICIO DE SESIÓN -->
        <form id="form-login" class="auth-form <?php echo $tab_activa === 'login' ? 'active' : ''; ?>" action="" method="POST">
            <input type="hidden" name="accion" value="login">
            <h2>Bienvenido de nuevo</h2>
            <p class="auth-subtitulo">Ingresa tus datos para acceder a tu cuenta</p>

            <div class="form-group">
                <label for="login-email">Correo Electrónico</label>
                <input type="email" id="login-email" name="email" placeholder="tuemail@ejemplo.com" required>
            </div>

            <div class="form-group">
                <label for="login-password">Contraseña</label>
                <input type="password" id="login-password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-auth">Ingresar</button>
        </form>

        <!-- FORMULARIO DE REGISTRO -->
        <form id="form-registro" class="auth-form <?php echo $tab_activa === 'registro' ? 'active' : ''; ?>" action="" method="POST">
            <input type="hidden" name="accion" value="registro">
            <h2>Crea tu cuenta</h2>
            <p class="auth-subtitulo">Únete a la comunidad de Glow Fashion</p>

            <div class="form-group">
                <label for="reg-nombre">Nombre Completo</label>
                <input type="text" id="reg-nombre" name="nombre" placeholder="Tu nombre" required>
            </div>

            <div class="form-group">
                <label for="reg-email">Correo Electrónico</label>
                <input type="email" id="reg-email" name="email" placeholder="tuemail@ejemplo.com" required>
            </div>

            <div class="form-group">
                <label for="reg-password">Contraseña</label>
                <input type="password" id="reg-password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-auth">Crear Cuenta</button>
        </form>
    </div>
</section>

<!-- Alternar visibilidad de pestañas -->
<script>
function mostrarFormulario(tipo) {
    const formLogin = document.getElementById('form-login');
    const formRegistro = document.getElementById('form-registro');
    const btns = document.querySelectorAll('.tab-btn');

    if (tipo === 'login') {
        formLogin.classList.add('active');
        formRegistro.classList.remove('active');
        btns[0].classList.add('active');
        btns[1].classList.remove('active');
    } else {
        formRegistro.classList.add('active');
        formLogin.classList.remove('active');
        btns[1].classList.add('active');
        btns[0].classList.remove('active');
    }
}
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>