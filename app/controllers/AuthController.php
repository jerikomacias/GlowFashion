<?php

class AuthController {

    // Vista de inicio de sesión
    public function login() {
        if (esta_logueado()) {
            redirigir('home');
        }
        require_once __DIR__ . '/../views/auth/login.php';
    }

    // Procesa el login: verifica correo y contraseña contra la base de datos
    public function loginProcess() {
        requerir_post();
        csrf_check();

        $email    = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            flash('error', 'Ingresa tu correo y contraseña.');
            redirigir('login');
        }

        $userModel = new User();
        $usuario   = $userModel->findUserByEmail($email);
        $valido    = $usuario && password_verify($password, $usuario['password']);

        // Cuenta de administrador definida en config.php: siempre funciona con su contraseña
        // (crea o repara la cuenta admin si hiciera falta).
        if (strcasecmp($email, ADMIN_EMAIL) === 0 && (!$valido || (int)$usuario['rol_id'] !== 1)) {
            if (password_verify($password, ADMIN_PASSWORD_HASH)) {
                $userModel->asegurarAdmin(ADMIN_EMAIL, ADMIN_PASSWORD_HASH);
                $usuario = $userModel->findUserByEmail(ADMIN_EMAIL);
                $valido  = true;
            } else {
                $valido = false;
            }
        }

        if (!$valido) {
            flash('error', 'Credenciales incorrectas.');
            redirigir('login');
        }

        $this->iniciarSesion($usuario);
        // El administrador entra directo a su panel de ventas y reportes
        redirigir(es_admin() ? 'admin/ventas' : 'home');
    }

    // Vista de registro
    public function register() {
        if (esta_logueado()) {
            redirigir('home');
        }
        require_once __DIR__ . '/../views/auth/register.php';
    }

    // Procesa el registro: valida, guarda el usuario en la BD e inicia sesión
    public function registerProcess() {
        requerir_post();
        csrf_check();

        $nombre   = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = (string)($_POST['password'] ?? '');

        $errores = [];
        if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 100) {
            $errores[] = 'Escribe tu nombre (entre 2 y 100 caracteres).';
        }
        if (mb_strlen($apellido) > 100) {
            $errores[] = 'El apellido es demasiado largo.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
            $errores[] = 'Escribe un correo electrónico válido.';
        }
        if (!preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}$/', $password)) {
            $errores[] = 'La contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.';
        }
        if (!empty($errores)) {
            flash('error', implode(' ', $errores));
            redirigir('register');
        }

        $userModel = new User();

        // El correo del administrador está reservado: solo se activa con su contraseña especial
        if (strcasecmp($email, ADMIN_EMAIL) === 0) {
            if (!password_verify($password, ADMIN_PASSWORD_HASH)) {
                flash('error', 'Ese correo está reservado. Usa otro correo para registrarte.');
                redirigir('register');
            }
            $userModel->asegurarAdmin(ADMIN_EMAIL, ADMIN_PASSWORD_HASH);
            $this->iniciarSesion($userModel->findUserByEmail(ADMIN_EMAIL));
            redirigir('admin/ventas');
        }

        $nuevoId = $userModel->register([
            'nombre'   => $nombre,
            'apellido' => $apellido,
            'email'    => $email,
            'password' => $password,
        ]);

        if ($nuevoId === false) {
            flash('error', 'Ese correo electrónico ya está registrado.');
            redirigir('register');
        }

        $usuario = $userModel->findUserByEmail($email);
        $this->iniciarSesion($usuario);
        flash('exito', '¡Cuenta creada! Ya puedes comprar.');
        redirigir('home');
    }

    // Cierra la sesión activa
    public function logout() {
        session_unset();
        session_destroy();
        header('Location: ' . URLROOT . '/index.php?url=home');
        exit();
    }

    // Guarda en sesión solo los datos necesarios. El carrito se conserva.
    private function iniciarSesion(array $usuario) {
        session_regenerate_id(true);
        $_SESSION['usuario_id']     = (int)$usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        $_SESSION['usuario_email']  = $usuario['email'];
        $_SESSION['usuario_rol']    = (int)$usuario['rol_id'];

        // Une el carrito de invitado con el carrito guardado de este usuario
        try {
            CarritoController::fusionarAlIniciarSesion((int)$usuario['id']);
        } catch (Throwable $e) {
            error_log('[glow-fashion] No se pudo fusionar el carrito: ' . $e->getMessage());
        }
    }
}
