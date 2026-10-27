<?php
// Tarjeta de producto. Recibe $prod (fila de la tabla productos + categoria_nombre).
?>
<?php $agotado = ((int)$prod['stock'] <= 0); ?>
<div style="background: white; border-radius: 12px; padding: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between;">
    <div>
        <div style="height: 220px; display: flex; align-items: center; justify-content: center; margin-bottom: 12px; overflow: hidden; border-radius: 8px; background: #f9fafb;">
            <?php if (!empty($prod['imagen'])): ?>
                <img src="/glow-fashion/public/img/<?php echo e($prod['imagen']); ?>?v=2" alt="<?php echo e($prod['nombre']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
                <span style="font-size: 50px;">🛍️</span>
            <?php endif; ?>
        </div>

        <span class="badge" style="font-size: 11px; text-transform: uppercase; margin-bottom: 6px; display: inline-block;">
            <?php echo e($prod['categoria_nombre']); ?> - <?php echo e($prod['genero']); ?>
        </span>
        <h3 style="margin: 6px 0; font-size: 16px; color: #1f2937;"><?php echo e($prod['nombre']); ?></h3>
        <?php if (!empty($prod['descripcion'])): ?>
            <p style="font-size: 13px; color: #6b7280; line-height: 1.4; margin: 0 0 6px 0;"><?php echo e(mb_strimwidth($prod['descripcion'], 0, 90, '…')); ?></p>
        <?php endif; ?>
        <p style="font-size: 18px; font-weight: bold; color: #7e46d6; margin: 8px 0;"><?php echo formatear_precio($prod['precio']); ?></p>
        <?php if ($agotado): ?>
            <p style="font-size: 13px; color: #ef4444; font-weight: 600; margin: 0;">Agotado</p>
        <?php elseif ((int)$prod['stock'] <= 5): ?>
            <p style="font-size: 13px; color: #d97706; margin: 0;">¡Últimas <?php echo (int)$prod['stock']; ?> unidades!</p>
        <?php endif; ?>
    </div>

    <form action="/glow-fashion/index.php?url=carrito/agregar" method="POST" style="margin-top: 10px;">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="id" value="<?php echo (int)$prod['id']; ?>">

        <button type="submit" class="btn-primary" <?php echo $agotado ? 'disabled' : ''; ?> style="width: 100%; border: none; padding: 10px; border-radius: 8px; cursor: <?php echo $agotado ? 'not-allowed' : 'pointer'; ?>; font-size: 14px; <?php echo $agotado ? 'opacity: 0.5;' : ''; ?>">
            <?php echo $agotado ? 'Sin stock' : '🛒 Agregar al Carrito'; ?>
        </button>
    </form>
</div>
