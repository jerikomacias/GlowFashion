<?php
require_once __DIR__ . '/../config/database.php';

class AdminController {

    private function verificarAdmin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Verifica si la sesión es de rol admin
        if (!isset($_SESSION['rol']) || strtolower($_SESSION['rol']) !== 'admin') {
            header('Location: /glow-fashion/index.php?url=home');
            exit();
        }
    }

    // --- CRUD PRODUCTOS ---
    public function productos() {
        $this->verificarAdmin();
        $db = Database::getConnection();

        // Obtener la lista de productos real
        $stmt = $db->query("SELECT * FROM productos ORDER BY id DESC");
        $productos = $stmt->fetchAll();

        require_once __DIR__ . '/../views/admin/productos.php';
    }

    public function guardarProducto() {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $precio = $_POST['precio'] ?? 0;
            $stock = $_POST['stock'] ?? 0;

            if (!empty($nombre)) {
                $db = Database::getConnection();
                $stmt = $db->prepare("INSERT INTO productos (nombre, precio, stock) VALUES (:nombre, :precio, :stock)");
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':precio' => $precio,
                    ':stock' => $stock
                ]);
                $_SESSION['mensaje'] = "Producto guardado con éxito en la base de datos.";
            }

            header('Location: /glow-fashion/index.php?url=admin/productos');
            exit();
        }
    }

    public function eliminarProducto() {
        $this->verificarAdmin();
        $id = $_GET['id'] ?? null;

        if ($id) {
            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM productos WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $_SESSION['mensaje'] = "Producto eliminado de la base de datos.";
        }

        header('Location: /glow-fashion/index.php?url=admin/productos');
        exit();
    }

    // --- CRUD VENTAS ---
    public function ventas() {
        $this->verificarAdmin();
        $db = Database::getConnection();

        // Consulta de ventas (puedes ajustar los nombres de columnas según tu tabla 'ventas')
        $stmt = $db->query("SELECT * FROM ventas ORDER BY id DESC");
        $ventas = $stmt->fetchAll();

        require_once __DIR__ . '/../views/admin/ventas.php';
    }

    public function registrarVenta() {
        $this->verificarAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $total = $_POST['total'] ?? 0;
            $id_usuario = $_SESSION['usuario_id'] ?? 1; // ID del usuario/cliente o admin

            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO ventas (total, fecha) VALUES (:total, NOW())");
            $stmt->execute([
                ':total' => $total
            ]);

            $_SESSION['mensaje'] = "Venta registrada con éxito en la base de datos.";
            header('Location: /glow-fashion/index.php?url=admin/ventas');
            exit();
        }
    }
}