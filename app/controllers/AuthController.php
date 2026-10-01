<?php

class AuthController {

    // Muestra la vista de Inicio de Sesión
    public function login() {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    // Procesa el formulario de Login (POST)
    public function loginProcess() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // --- AQUÍ VA TU VALIDACIÓN CON BASE DE DATOS / MODELO ---
            // Ejemplo:
            // $usuario = $this->userModel->getByEmail($email);
            // if ($usuario && password_verify($password, $usuario['password'])) {

            if (!empty($email)) { // Reemplazar con la verificación real de tu BD
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                // Guardar la variable de sesión
                $_SESSION['usuario'] = $email;
                $_SESSION['user_email'] = $email;

                header('Location: /glow-fashion/index.php?url=home');
                exit();
            } else {
                $error = "Credenciales incorrectas";
                require_once __DIR__ . '/../views/auth/login.php';
            }
        } else {
            header('Location: /glow-fashion/index.php?url=login');
            exit();
        }
    }

    // Muestra la vista de Registro
    public function register() {
        require_once __DIR__ . '/../views/auth/register.php';
    }

    // Procesa el formulario de Registro (POST)
    public function registerProcess() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre   = $_POST['nombre'] ?? '';
            $email    = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Cifrar la contraseña
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            // --- AQUÍ GUARDAS EL USUARIO EN TU BASE DE DATOS ---
            // Ejemplo:
            // $this->userModel->create($nombre, $email, $passwordHash);

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Iniciar sesión automáticamente
            $_SESSION['usuario'] = $email;
            $_SESSION['user_email'] = $email;

            // Redirigir al inicio
            header('Location: /glow-fashion/index.php?url=home');
            exit();
        } else {
            header('Location: /glow-fashion/index.php?url=register');
            exit();
        }
    }

    // Cierra la sesión activa
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        header('Location: /glow-fashion/index.php?url=home');
        exit();
    }
}