<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inicializar el carrito en la sesión si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// PROCESAR ACCIONES DEL CARRITO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. Agregar producto al carrito desde los catálogos
    if ($action === 'agregar') {
        $id = (int)$_POST['id'];
        $nombre = $_POST['nombre'];
        $precio = (float)$_POST['precio'];

        $encontrado = false;
        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['id'] === $id) {
                $item['cantidad']++;
                $encontrado = true;
                break;
            }
        }

        if (!$encontrado) {
            $_SESSION['carrito'][] = [
                'id' => $id,
                'nombre' => $nombre,
                'precio' => $precio,
                'cantidad' => 1
            ];
        }

        header('Location: /glow-fashion/index.php?url=carrito');
        exit();
    }

    // 2. Modificar cantidad o eliminar item
    if ($action === 'actualizar') {
        $id = (int)$_POST['id'];
        $cantidad = (int)$_POST['cantidad'];

        foreach ($_SESSION['carrito'] as $key => &$item) {
            if ($item['id'] === $id) {
                if ($cantidad > 0) {
                    $item['cantidad'] = $cantidad;
                } else {
                    unset($_SESSION['carrito'][$key]);
                }
                break;
            }
        }
        $_SESSION['carrito'] = array_values($_SESSION['carrito']);
        header('Location: /glow-fashion/index.php?url=carrito');
        exit();
    }

    // 3. Vaciar carrito
    if ($action === 'vaciar') {
        $_SESSION['carrito'] = [];
        header('Location: /glow-fashion/index.php?url=carrito');
        exit();
    }

    // 4. Finalizar Compra con Nombre, Dirección y Forma de Pago
    if ($action === 'finalizar' && !empty($_SESSION['carrito'])) {
        $nombreCliente  = trim($_POST['cliente'] ?? '');
        $direccion      = trim($_POST['direccion'] ?? '');
        $formaPago      = trim($_POST['forma_pago'] ?? 'Efectivo');

        if (!isset($_SESSION['ventas_demo'])) {
            $_SESSION['ventas_demo'] = [];
        }

        $nuevaOrden = !empty($_SESSION['ventas_demo']) ? max(array_column($_SESSION['ventas_demo'], 'orden')) + 1 : 101;
        
        $totalVenta = 0;
        foreach ($_SESSION['carrito'] as $c) {
            $totalVenta += ($c['precio'] * $c['cantidad']);
        }

        // Registrar la orden con los datos de entrega
        $_SESSION['ventas_demo'][] = [
            'orden'      => $nuevaOrden,
            'cliente'    => $nombreCliente,
            'direccion'  => $direccion,
            'pago'       => $formaPago,
            'fecha'      => date('Y-m-d'),
            'total'      => $totalVenta,
            'estado'     => 'Pendiente'
        ];

        $_SESSION['carrito'] = [];
        echo "<script>
            alert('¡Gracias por tu compra, {$nombreCliente}! Tu orden #{$nuevaOrden} ha sido registrada. Pago: {$formaPago}. Envío a: {$direccion}');
            window.location.href='/glow-fashion/index.php?url=home';
        </script>";
        exit();
    }
}

$carrito = $_SESSION['carrito'];
$totalPagar = 0;
foreach ($carrito as $item) {
    $totalPagar += $item['precio'] * $item['cantidad'];
}

// Precompletar el nombre si el usuario está autenticado
$usuarioActual = $_SESSION['usuario'] ?? $_SESSION['user_email'] ?? '';
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="admin-container">
    <div class="admin-header">
        <h1>Mi Carrito de Compras</h1>
    </div>

    <div class="table-card">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>PRODUCTO</th>
                    <th>PRECIO UNITARIO</th>
                    <th>CANTIDAD</th>
                    <th>SUBTOTAL</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($carrito)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #6b7280; padding: 30px;">
                            Tu carrito está vacío. <br><br>
                            <a href="/glow-fashion/index.php?url=home" class="btn-primary" style="text-decoration:none; display:inline-block;">Ir a Ver Productos</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($carrito as $item): 
                        $subtotal = $item['precio'] * $item['cantidad'];
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($item['nombre']); ?></strong></td>
                        <td>$<?php echo number_format($item['precio'], 0, ',', '.'); ?></td>
                        <td>
                            <form action="/glow-fashion/index.php?url=carrito" method="POST" style="display:flex; gap:5px; align-items:center;">
                                <input type="hidden" name="action" value="actualizar">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="cantidad" value="<?php echo $item['cantidad']; ?>" min="1" style="width: 60px; padding: 5px; border-radius: 6px; border: 1px solid #ccc; text-align:center;">
                                <button type="submit" class="btn-sm btn-edit">Actualizar</button>
                            </form>
                        </td>
                        <td><strong>$<?php echo number_format($subtotal, 0, ',', '.'); ?></strong></td>
                        <td>
                            <form action="/glow-fashion/index.php?url=carrito" method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="actualizar">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="cantidad" value="0">
                                <button type="submit" class="btn-sm btn-delete">Quitar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($carrito)): ?>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 20px; gap: 20px;">
            <div>
                <form action="/glow-fashion/index.php?url=carrito" method="POST">
                    <input type="hidden" name="action" value="vaciar">
                    <button type="submit" class="btn-secondary">Vaciar Carrito</button>
                </form>
            </div>

            <!-- Formulario de Checkout / Datos de Envío y Pago -->
            <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); width: 100%; max-width: 450px;">
                <h3 style="margin-top: 0; color: #1f2937; margin-bottom: 15px;">Datos de Entrega y Pago</h3>
                
                <form action="/glow-fashion/index.php?url=carrito" method="POST" class="crud-form">
                    <input type="hidden" name="action" value="finalizar">

                    <div class="form-group" style="margin-bottom: 12px;">
                        <label style="display:block; margin-bottom: 4px; font-weight:600;">Nombre Completo</label>
                        <input type="text" name="cliente" value="<?php echo htmlspecialchars($usuarioActual); ?>" required placeholder="Ej: María López" style="width:100%; padding: 8px; border-radius:6px; border:1px solid #d1d5db;">
                    </div>

                    <div class="form-group" style="margin-bottom: 12px;">
                        <label style="display:block; margin-bottom: 4px; font-weight:600;">Dirección de Entrega</label>
                        <input type="text" name="direccion" required placeholder="Ej: Calle 123 #45-67, Apto 201" style="width:100%; padding: 8px; border-radius:6px; border:1px solid #d1d5db;">
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom: 4px; font-weight:600;">Forma de Pago</label>
                        <select name="forma_pago" required style="width:100%; padding: 8px; border-radius:6px; border:1px solid #d1d5db;">
                            <option value="Efectivo (Contra entrega)">Efectivo (Contra entrega)</option>
                            <option value="Transferencia / Nequi / Daviplata">Transferencia / Nequi / Daviplata</option>
                            <option value="Tarjeta de Crédito / Débito">Tarjeta de Crédito / Débito</option>
                        </select>
                    </div>

                    <div style="border-top: 1px solid #e5e7eb; padding-top: 15px; margin-top: 15px; text-align: right;">
                        <h2 style="margin: 0 0 15px 0; font-size: 22px; color: #1f2937;">Total: <span style="color: #7e46d6;">$<?php echo number_format($totalPagar, 0, ',', '.'); ?></span></h2>
                        <button type="submit" class="btn-primary" style="width: 100%; font-size: 16px; padding: 12px;">Confirmar y Finalizar Compra 🛍️</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</main>