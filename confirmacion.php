<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$pedido_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$pedido_id) {
    header('Location: index.php');
    exit;
}

// Consultar el pedido verificando que pertenezca al usuario activo
$stmt = $pdo->prepare("SELECT * FROM pedidos WHERE id = ? AND usuario_id = ?");
$stmt->execute([$pedido_id, $_SESSION['usuario_id']]);
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    die("Pedido no encontrado.");
}

// Consultar el detalle de los productos comprados
$stmt_detalles = $pdo->prepare("
    SELECT d.*, p.nombre 
    FROM detalle_pedidos d
    JOIN productos p ON d.producto_id = p.id
    WHERE d.pedido_id = ?
");
$stmt_detalles->execute([$pedido_id]);
$detalles = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Glow Fashion - Pedido Confirmado</title>
</head>
<body>
    <h1>¡Gracias por tu compra en Glow Fashion!</h1>
    <p>Número de Pedido: <strong>#<?= $pedido['id'] ?></strong></p>
    <p>Fecha: <?= $pedido['fecha_creacion'] ?></p>
    <p>Estado: <?= ucfirst($pedido['estado']) ?></p>

    <h2>Resumen de Productos</h2>
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalles as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['nombre']) ?></td>
                    <td><?= $item['cantidad'] ?></td>
                    <td>$<?= number_format($item['precio_unitario'], 2) ?></td>
                    <td>$<?= number_format($item['subtotal'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Total Pagado: $<?= number_format($pedido['total'], 2) ?></h3>
    <a href="productos.php">Volver a la tienda</a>
</body>
</html>