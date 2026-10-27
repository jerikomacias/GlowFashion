<?php
// Variable recibida desde VentaController: $venta (con $venta['detalles'])
require_once __DIR__ . '/../layouts/header.php';
?>

<main class="admin-container">
    <div class="admin-header">
        <h1>¡Gracias por tu compra en Glow Fashion!</h1>
    </div>

    <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 20px;">
        <p style="margin: 0 0 8px 0;">Número de venta: <strong>#<?php echo (int)$venta['id']; ?></strong></p>
        <p style="margin: 0 0 8px 0;">Fecha: <?php echo e($venta['fecha']); ?></p>
        <p style="margin: 0 0 8px 0;">Cliente: <?php echo e($venta['cliente_nombre']); ?></p>
        <p style="margin: 0 0 8px 0;">Dirección de entrega: <?php echo e($venta['direccion']); ?></p>
        <p style="margin: 0;">Forma de pago: <?php echo e($venta['metodo_pago']); ?></p>
    </div>

    <div class="table-card">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>PRODUCTO</th>
                    <th>CANTIDAD</th>
                    <th>PRECIO UNITARIO</th>
                    <th>SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($venta['detalles'] as $d): ?>
                    <tr>
                        <td><strong><?php echo e($d['nombre']); ?></strong></td>
                        <td><?php echo (int)$d['cantidad']; ?></td>
                        <td><?php echo formatear_precio($d['precio_unitario']); ?></td>
                        <td><?php echo formatear_precio($d['subtotal']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h2 style="text-align: right; margin-top: 20px; color: #1f2937;">Total pagado: <span style="color: #7e46d6;"><?php echo formatear_precio($venta['total']); ?></span></h2>

    <div style="margin-top: 20px; display: flex; gap: 10px;">
        <a href="/glow-fashion/index.php?url=home" class="btn-primary" style="text-decoration: none; padding: 10px 20px; border-radius: 8px; display: inline-block;">Volver a la tienda</a>
        <a href="/glow-fashion/index.php?url=mis-compras" class="btn-secondary" style="text-decoration: none; padding: 10px 20px; border-radius: 8px; display: inline-block;">Ver mis compras</a>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
