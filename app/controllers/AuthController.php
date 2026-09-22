<?php
require_once __DIR__ . '/../../config/database.php';

class AuthController {

    private $ADMIN_SECRET_KEY = "GlowAdmin2026";

    public function login() {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function register() {
        require_once __DIR__ . '/../views/auth/register.php';
    }

    public function loginProcess() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';

            if (empty($email) || empty($password)) {
                $_SESSION['error'] = 'Por favor completa todos los campos.';
                header('Location: /glow-fashion/index.php?url=login');
                exit();
            }

            try {
                $db = Database::getConnection();
                $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = :email");
                $stmt->execute([':email' => $email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['usuario_id'] = $user['id'];
                    $_SESSION['usuario'] = $user['email'];
                    $_SESSION['rol'] = $user['rol'];

                    header('Location: /glow-fashion/index.php?url=home');
                    exit();
                } else {
                    $_SESSION['error'] = 'Correo o contraseña incorrectos.';
                    header('Location: /glow-fashion/index.php?url=login');
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = 'Error en la base de datos: ' . $e->getMessage();
                header('Location: /glow-fashion/index.php?url=login');
                exit();
            }
        } else {
            header('Location: /glow-fashion/index.php?url=login');
            exit();
        }
    }

    public function registerProcess() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? trim($_POST['password']) : '';
            $rol = isset($_POST['rol']) ? trim($_POST['rol']) : 'usuario';
            $adminKey = isset($_POST['admin_key']) ? trim($_POST['admin_key']) : '';

            if (empty($nombre) || empty($email) || empty($password)) {
                $_SESSION['error'] = 'Por favor completa todos los campos obligatorios.';
                header('Location: /glow-fashion/index.php?url=register');
                exit();
            }

            if ($rol === 'admin' && $adminKey !== $this->ADMIN_SECRET_KEY) {
                $_SESSION['error'] = 'La clave secreta de Administrador es incorrecta.';
                header('Location: /glow-fashion/index.php?url=register');
                exit();
            }

            try {
                $db = Database::getConnection();

                $stmtCheck = $db->prepare("SELECT id FROM usuarios WHERE email = :email");
                $stmtCheck->execute([':email' => $email]);
                
                if ($stmtCheck->fetch()) {
                    $_SESSION['error'] = 'El correo ya está registrado.';
                    header('Location: /glow-fashion/index.php?url=register');
                    exit();
                }

                $stmt = $db->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, :rol)");
                $stmt->execute([
                    ':nombre'   => $nombre,
                    ':email'    => $email,
                    ':password' => password_hash($password, PASSWORD_DEFAULT),
                    ':rol'      => $rol
                ]);

                $_SESSION['mensaje'] = 'Registro exitoso como ' . htmlspecialchars($rol) . '. ¡Ahora puedes iniciar sesión!';
                header('Location: /glow-fashion/index.php?url=login');
                exit();

            } catch (PDOException $e) {
                $_SESSION['error'] = 'Error al registrar: ' . $e->getMessage();
                header('Location: /glow-fashion/index.php?url=register');
                exit();
            }
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: /glow-fashion/index.php');
        exit();
    }
}