<?php
// Variable recibida desde VentaController: $ventas (cada una con 'detalles')
require_once __DIR__ . '/../layouts/header.php';
?>

<main class="admin-container">
    <div class="admin-header">
        <h1>Mis Compras</h1>
    </div>

    <?php if (empty($ventas)): ?>
        <div style="background: white; padding: 30px; border-radius: 12px; text-align: center; color: #6b7280;">
            Aún no has realizado ninguna compra en Glow Fashion.<br><br>
            <a href="/glow-fashion/index.php?url=home" class="btn-primary" style="text-decoration: none; display: inline-block; padding: 10px 20px; border-radius: 8px;">Ir a la tienda</a>
        </div>
    <?php else: ?>
        <?php foreach ($ventas as $v): ?>
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 20px;">
                <h3 style="margin: 0 0 8px 0; color: #1f2937;">Venta #<?php echo (int)$v['id']; ?></h3>
                <p style="margin: 0 0 4px 0; color: #6b7280; font-size: 14px;">
                    <?php echo e(date('d/m/Y H:i', strtotime($v['fecha']))); ?> · <?php echo e($v['metodo_pago']); ?> · Envío a: <?php echo e($v['direccion']); ?>
                </p>

                <table class="crud-table" style="margin-top: 12px;">
                    <thead>
                        <tr>
                            <th>PRODUCTO</th>
                            <th>CANTIDAD</th>
                            <th>PRECIO UNITARIO</th>
                            <th>SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($v['detalles'] as $d): ?>
                            <tr>
                                <td><?php echo e($d['nombre']); ?></td>
                                <td><?php echo (int)$d['cantidad']; ?></td>
                                <td><?php echo formatear_precio($d['precio_unitario']); ?></td>
                                <td><?php echo formatear_precio($d['subtotal']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <p style="text-align: right; font-size: 18px; margin: 12px 0 0 0;">
                    <strong>Total: <?php echo formatear_precio($v['total']); ?></strong>
                </p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
