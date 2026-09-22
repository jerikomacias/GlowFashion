<?php
session_start();
require_once 'conexion.php';

// Validar inicio de sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php?redirect=historial_pedidos.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Consultar los pedidos del usuario activo
$stmt_pedidos = $pdo->prepare("
    SELECT id, total, estado, fecha_creacion 
    FROM pedidos 
    WHERE usuario_id = ? 
    ORDER BY fecha_creacion DESC
");
$stmt_pedidos->execute([$usuario_id]);
$pedidos = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Glow Fashion - Mi Historial de Pedidos</title>
    <style>
        .pedido-card {
            border: 1px solid #ccc;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
        }
        .estado-completado { color: green; font-weight: bold; }
        .estado-pendiente { color: orange; font-weight: bold; }
        .estado-cancelado { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Mis Pedidos Realizados</h1>
    <p><a href="productos.php">← Volver a la tienda</a> | <a href="carrito.php">Ver Carrito</a></p>

    <?php if (empty($pedidos)): ?>
        <p>Aún no has realizado ninguna compra en Glow Fashion.</p>
    <?php else: ?>
        <?php foreach ($pedidos as $pedido): ?>
            <div class="pedido-card">
                <h3>Pedido #<?= $pedido['id'] ?></h3>
                <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pedido['fecha_creacion'])) ?></p>
                <p>
                    <strong>Estado:</strong> 
                    <span class="estado-<?= strtolower($pedido['estado']) ?>">
                        <?= ucfirst($pedido['estado']) ?>
                    </span>
                </p>

                <h4>Detalle de Compra:</h4>
                <?php
                // Consultar prendas del pedido en detalle_pedidos
                $stmt_detalle = $pdo->prepare("
                    SELECT d.cantidad, d.precio_unitario, d.subtotal, p.nombre 
                    FROM detalle_pedidos d
                    JOIN productos p ON d.producto_id = p.id
                    WHERE d.pedido_id = ?
                ");
                $stmt_detalle->execute([$pedido['id']]);
                $detalles = $stmt_detalle->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f2f2f2;">
                            <th>Prenda</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['nombre']) ?></td>
                                <td align="center"><?= $item['cantidad'] ?></td>
                                <td>$<?= number_format($item['precio_unitario'], 2) ?></td>
                                <td>$<?= number_format($item['subtotal'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p style="text-align: right; font-size: 1.1em;">
                    <strong>Total Cancelado: $<?= number_format($pedido['total'], 2) ?></strong>
                </p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>