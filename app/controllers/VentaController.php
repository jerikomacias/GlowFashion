<?php
class VentaController {

    // Finalizar compra: valida datos, registra la venta en la BD y descuenta el stock.
    public function procesarCompra() {
        requerir_post();
        csrf_check();
        requerir_login();

        $cantidades = CarritoController::obtenerIds();
        if (empty($cantidades)) {
            flash('error', 'Tu carrito está vacío.');
            redirigir('carrito');
        }

        $cliente   = trim($_POST['cliente'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $metodo    = trim($_POST['forma_pago'] ?? '');

        $errores = [];
        if (mb_strlen($cliente) < 2 || mb_strlen($cliente) > 150) {
            $errores[] = 'Escribe tu nombre completo (entre 2 y 150 caracteres).';
        }
        if (mb_strlen($direccion) < 5 || mb_strlen($direccion) > 255) {
            $errores[] = 'Escribe una dirección de entrega válida (entre 5 y 255 caracteres).';
        }
        if (!in_array($metodo, Venta::METODOS_PAGO, true)) {
            $errores[] = 'Selecciona una forma de pago válida.';
        }
        if (!empty($errores)) {
            flash('error', implode(' ', $errores));
            redirigir('carrito');
        }

        try {
            $ventaId = (new Venta())->crearVenta((int)$_SESSION['usuario_id'], $cliente, $direccion, $metodo, $cantidades);
        } catch (RuntimeException $e) {
            // Errores de negocio (por ejemplo, stock insuficiente): se muestran al cliente
            flash('error', $e->getMessage());
            redirigir('carrito');
        } catch (PDOException $e) {
            error_log('[glow-fashion] Error al registrar la venta: ' . $e->getMessage());
            $detalle = APP_DEBUG ? ' (' . $e->getMessage() . ')' : '';
            flash('error', 'No pudimos registrar tu compra. Inténtalo de nuevo.' . $detalle);
            redirigir('carrito');
        }

        // El carrito guardado ya se vació dentro de la transacción de la compra
        unset($_SESSION['carrito']);
        flash('exito', '¡Gracias por tu compra!');
        redirigir('compra/confirmacion&id=' . $ventaId);
    }

    // Página de confirmación de una compra
    public function confirmacion() {
        requerir_login();

        $id    = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $venta = $id ? (new Venta())->getById($id, es_admin() ? null : (int)$_SESSION['usuario_id']) : false;

        if (!$venta) {
            (new HomeController())->noEncontrado();
            return;
        }

        require_once __DIR__ . '/../views/ventas/confirmacion.php';
    }

    // Historial de compras del cliente
    public function misCompras() {
        requerir_login();
        $ventas = (new Venta())->getByUsuario((int)$_SESSION['usuario_id']);
        require_once __DIR__ . '/../views/ventas/mis_compras.php';
    }
}
