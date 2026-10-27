<?php
// Variables recibidas desde CarritoController: $items, $totalPagar
require_once __DIR__ . '/../layouts/header.php';
$usuarioNombre = $_SESSION['usuario_nombre'] ?? '';
?>

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
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #6b7280; padding: 30px;">
                            Tu carrito está vacío. <br><br>
                            <a href="/glow-fashion/index.php?url=home" class="btn-primary" style="text-decoration:none; display:inline-block;">Ir a Ver Productos</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <strong><?php echo e($item['nombre']); ?></strong>
                            <?php if ($item['cantidad'] > $item['stock']): ?>
                                <br><small style="color: #ef4444;">Solo quedan <?php echo (int)$item['stock']; ?> unidades. Ajusta la cantidad.</small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo formatear_precio($item['precio']); ?></td>
                        <td>
                            <form action="/glow-fashion/index.php?url=carrito/actualizar" method="POST" style="display:flex; gap:5px; align-items:center;">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo (int)$item['id']; ?>">
                                <input type="number" name="cantidad" value="<?php echo (int)$item['cantidad']; ?>" min="1" max="<?php echo max(1, (int)$item['stock']); ?>" style="width: 60px; padding: 5px; border-radius: 6px; border: 1px solid #ccc; text-align:center;">
                                <button type="submit" class="btn-sm btn-edit">Actualizar</button>
                            </form>
                        </td>
                        <td><strong><?php echo formatear_precio($item['subtotal']); ?></strong></td>
                        <td>
                            <form action="/glow-fashion/index.php?url=carrito/actualizar" method="POST" style="display:inline;">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo (int)$item['id']; ?>">
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

    <?php if (!empty($items)): ?>
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 20px; gap: 20px; flex-wrap: wrap;">
            <div>
                <form action="/glow-fashion/index.php?url=carrito/vaciar" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-secondary">Vaciar Carrito</button>
                </form>
            </div>

            <!-- Datos de entrega y pago -->
            <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); width: 100%; max-width: 450px;">
                <h3 style="margin-top: 0; color: #1f2937; margin-bottom: 15px;">Datos de Entrega y Pago</h3>

                <?php if (!esta_logueado()): ?>
                    <p style="color: #374151; font-size: 14px; line-height: 1.5;">
                        Para finalizar tu compra necesitas una cuenta. Tu carrito se conservará.
                    </p>
                    <div style="display: flex; gap: 10px; margin-top: 12px;">
                        <a href="/glow-fashion/index.php?url=login" class="btn-primary" style="text-decoration: none; padding: 10px 18px; border-radius: 8px; display: inline-block;">Iniciar sesión</a>
                        <a href="/glow-fashion/index.php?url=register" class="btn-secondary" style="text-decoration: none; padding: 10px 18px; border-radius: 8px; display: inline-block;">Crear cuenta</a>
                    </div>
                    <div style="border-top: 1px solid #e5e7eb; padding-top: 15px; margin-top: 20px; text-align: right;">
                        <h2 style="margin: 0; font-size: 22px; color: #1f2937;">Total: <span style="color: #7e46d6;"><?php echo formatear_precio($totalPagar); ?></span></h2>
                    </div>
                <?php else: ?>
                    <form action="/glow-fashion/index.php?url=procesar-compra" method="POST" class="crud-form">
                        <?php echo csrf_field(); ?>

                        <div class="form-group" style="margin-bottom: 12px;">
                            <label style="display:block; margin-bottom: 4px; font-weight:600;">Nombre Completo</label>
                            <input type="text" name="cliente" value="<?php echo e($usuarioNombre); ?>" required minlength="2" maxlength="150" placeholder="Ej: María López" style="width:100%; padding: 8px; border-radius:6px; border:1px solid #d1d5db; box-sizing: border-box;">
                        </div>

                        <div class="form-group" style="margin-bottom: 12px;">
                            <label style="display:block; margin-bottom: 4px; font-weight:600;">Dirección de Entrega</label>
                            <input type="text" name="direccion" required minlength="5" maxlength="255" placeholder="Ej: Calle 123 #45-67, Apto 201" style="width:100%; padding: 8px; border-radius:6px; border:1px solid #d1d5db; box-sizing: border-box;">
                        </div>

                        <div class="form-group" style="margin-bottom: 15px;">
                            <label style="display:block; margin-bottom: 4px; font-weight:600;">Forma de Pago</label>
                            <select name="forma_pago" required style="width:100%; padding: 8px; border-radius:6px; border:1px solid #d1d5db;">
                                <?php foreach (Venta::METODOS_PAGO as $metodo): ?>
                                    <option value="<?php echo e($metodo); ?>"><?php echo e($metodo); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div style="border-top: 1px solid #e5e7eb; padding-top: 15px; margin-top: 15px; text-align: right;">
                            <h2 style="margin: 0 0 15px 0; font-size: 22px; color: #1f2937;">Total: <span style="color: #7e46d6;"><?php echo formatear_precio($totalPagar); ?></span></h2>
                            <button type="submit" class="btn-primary" style="width: 100%; font-size: 16px; padding: 12px;">Confirmar y Finalizar Compra 🛍️</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
