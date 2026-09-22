<?php
session_start();
require_once 'conexion.php';

// 1. Validar autenticación de usuario
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php?redirect=procesar_pedido.php');
    exit;
}

// 2. Validar que el carrito no esté vacío
if (empty($_SESSION['carrito'])) {
    header('Location: carrito.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$carrito = $_SESSION['carrito'];

// Calcular el total final
$total_pedido = 0;
foreach ($carrito as $item) {
    $total_pedido += $item['precio'] * $item['cantidad'];
}

try {
    // Iniciar transacción de base de datos
    $pdo->beginTransaction();

    // Insertar en la tabla 'pedidos'
    $stmt_pedido = $pdo->prepare("INSERT INTO pedidos (usuario_id, total, estado) VALUES (?, ?, 'completado')");
    $stmt_pedido->execute([$usuario_id, $total_pedido]);
    $pedido_id = $pdo->lastInsertId();

    // Preparar inserción de detalles y actualización de stock
    $stmt_detalle = $pdo->prepare("INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt_stock   = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ? AND stock >= ?");

    foreach ($carrito as $producto_id => $item) {
        $subtotal = $item['precio'] * $item['cantidad'];

        // Verificar y descontar stock
        $stmt_stock->execute([$item['cantidad'], $producto_id, $item['cantidad']]);
        if ($stmt_stock->rowCount() === 0) {
            throw new Exception("Stock insuficiente para el producto: " . $item['nombre']);
        }

        // Insertar registro de detalle
        $stmt_detalle->execute([
            $pedido_id,
            $producto_id,
            $item['cantidad'],
            $item['precio'],
            $subtotal
        ]);
    }

    // Confirmar la transacción
    $pdo->commit();

    // Limpiar carrito de compras
    unset($_SESSION['carrito']);

    // Redirigir a confirmación
    header("Location: confirmacion.php?id=" . $pedido_id);
    exit;

} catch (Exception $e) {
    // Revertir cambios en caso de error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Error al procesar el pedido: " . $e->getMessage());
}