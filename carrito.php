<?php
session_start();
require_once 'conexion.php'; // Incluye tu conexión PDO ($pdo)

// Inicializar la estructura del carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Procesar acciones del carrito via POST o GET
$accion = $_REQUEST['accion'] ?? null;

if ($accion) {
    switch ($accion) {
        case 'agregar':
            $producto_id = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
            $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT) ?? 1;

            if ($producto_id && $cantidad > 0) {
                // Obtener datos actualizados del producto
                $stmt = $pdo->prepare("SELECT id, nombre, precio, stock FROM productos WHERE id = ?");
                $stmt->execute([$producto_id]);
                $producto = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($producto) {
                    if (isset($_SESSION['carrito'][$producto_id])) {
                        $_SESSION['carrito'][$producto_id]['cantidad'] += $cantidad;
                    } else {
                        $_SESSION['carrito'][$producto_id] = [
                            'id'       => $producto['id'],
                            'nombre'   => $producto['nombre'],
                            'precio'   => $producto['precio'],
                            'cantidad' => $cantidad
                        ];
                    }
                }
            }
            header('Location: carrito.php');
            exit;

        case 'actualizar':
            $producto_id = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
            $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);

            if ($producto_id && isset($_SESSION['carrito'][$producto_id])) {
                if ($cantidad > 0) {
                    $_SESSION['carrito'][$producto_id]['cantidad'] = $cantidad;
                } else {
                    unset($_SESSION['carrito'][$producto_id]);
                }
            }
            header('Location: carrito.php');
            exit;

        case 'eliminar':
            $producto_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if ($producto_id && isset($_SESSION['carrito'][$producto_id])) {
                unset($_SESSION['carrito'][$producto_id]);
            }
            header('Location: carrito.php');
            exit;

        case 'vaciar':
            $_SESSION['carrito'] = [];
            header('Location: carrito.php');
            exit;
    }
}

// Calcular total del carrito
$total_compra = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total_compra += $item['precio'] * $item['cantidad'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Glow Fashion - Carrito de Compras</title>
</head>
<body>
    <h1>Tu Carrito de Compras</h1>

    <?php if (empty($_SESSION['carrito'])): ?>
        <p>El carrito está vacío. <a href="productos.php">Ver catálogo</a></p>
    <?php else: ?>
        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio Unitario</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['carrito'] as $id => $item): 
                    $subtotal = $item['precio'] * $item['cantidad'];
                ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nombre']) ?></td>
                        <td>$<?= number_format($item['precio'], 2) ?></td>
                        <td>
                            <form action="carrito.php" method="POST" style="display:inline;">
                                <input type="hidden" name="accion" value="actualizar">
                                <input type="hidden" name="producto_id" value="<?= $id ?>">
                                <input type="number" name="cantidad" value="<?= $item['cantidad'] ?>" min="1" style="width: 50px;">
                                <button type="submit">Actualizar</button>
                            </form>
                        </td>
                        <td>$<?= number_format($subtotal, 2) ?></td>
                        <td>
                            <a href="carrito.php?accion=eliminar&id=<?= $id ?>">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Total: $<?= number_format($total_compra, 2) ?></h3>

        <a href="carrito.php?accion=vaciar">Vaciar Carrito</a> | 
        <a href="procesar_pedido.php">Finalizar Compra</a>
    <?php endif; ?>
</body>
</html>