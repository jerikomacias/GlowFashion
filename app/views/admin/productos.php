<?php
// Variables recibidas desde AdminController: $productos, $categorias, $imagenes
$navActivo = 'productos';
$estiloInput = 'width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box;';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Glow Fashion</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f9fafb; margin: 0; padding: 0;">

<?php require __DIR__ . '/_nav.php'; ?>

<main style="max-width: 1100px; margin: 40px auto; padding: 0 20px;">
    <h2 style="font-family: serif; color: #1f2937; margin-bottom: 25px;">Gestión de Productos</h2>

    <!-- Formulario para agregar producto -->
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); margin-bottom: 30px;">
        <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 18px; color: #374151;">Agregar Nuevo Producto</h3>
        <form action="/glow-fashion/index.php?url=admin/guardar-producto" method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <?php echo csrf_field(); ?>

            <div>
                <label style="font-size: 13px; font-weight: 600;">Nombre del Producto</label>
                <input type="text" name="nombre" required maxlength="150" style="<?php echo $estiloInput; ?>">
            </div>
            <div>
                <label style="font-size: 13px; font-weight: 600;">Categoría</label>
                <select name="categoria_id" required style="<?php echo $estiloInput; ?>">
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?php echo (int)$c['id']; ?>"><?php echo e($c['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="font-size: 13px; font-weight: 600;">Género</label>
                <select name="genero" style="<?php echo $estiloInput; ?>">
                    <option value="unisex">Unisex</option>
                    <option value="hombre">Hombre</option>
                    <option value="mujer">Mujer</option>
                </select>
            </div>
            <div>
                <label style="font-size: 13px; font-weight: 600;">Tipo</label>
                <select name="tipo" style="<?php echo $estiloInput; ?>">
                    <option value="prendas">Prendas</option>
                    <option value="zapatos">Zapatos</option>
                    <option value="accesorios">Accesorios</option>
                    <option value="maquillaje">Maquillaje</option>
                </select>
            </div>
            <div>
                <label style="font-size: 13px; font-weight: 600;">Precio ($)</label>
                <input type="text" inputmode="decimal" data-moneda name="precio" required placeholder="Ej: 70.000" autocomplete="off" style="<?php echo $estiloInput; ?>">
            </div>
            <div>
                <label style="font-size: 13px; font-weight: 600;">Stock / Cantidad</label>
                <input type="number" min="0" step="1" name="stock" required style="<?php echo $estiloInput; ?>">
            </div>
            <div>
                <label style="font-size: 13px; font-weight: 600;">Imagen</label>
                <select name="imagen" style="<?php echo $estiloInput; ?>">
                    <?php foreach ($imagenes as $img): ?>
                        <option value="<?php echo e($img); ?>" <?php echo $img === 'logo.png' ? 'selected' : ''; ?>><?php echo e($img); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="font-size: 13px; font-weight: 600;">...o subir una imagen nueva</label>
                <input type="file" name="imagen_archivo" accept="image/jpeg,image/png,image/webp" style="<?php echo $estiloInput; ?> background: #fff;">
                <small style="color: #6b7280; font-size: 12px;">JPG, PNG o WEBP, máx. 3 MB. Si subes una imagen, se usa en lugar de la lista.</small>
            </div>
            <div style="display: flex; align-items: flex-end;">
                <label style="font-size: 13px; font-weight: 600;">
                    <input type="checkbox" name="destacado" value="1"> Producto destacado
                </label>
            </div>
            <div style="grid-column: 1 / -1;">
                <label style="font-size: 13px; font-weight: 600;">Descripción (opcional)</label>
                <textarea name="descripcion" rows="2" style="<?php echo $estiloInput; ?>"></textarea>
            </div>
            <div style="grid-column: 1 / -1;">
                <button type="submit" style="background: #7e46d6; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer;">Guardar Producto</button>
            </div>
        </form>
    </div>

    <!-- Tabla de productos existentes -->
    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); overflow-x: auto;">
        <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 18px; color: #374151;">Inventario Actual</h3>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #f3f4f6; color: #6b7280; font-size: 14px;">
                    <th style="padding: 10px;">ID</th>
                    <th style="padding: 10px;">Imagen</th>
                    <th style="padding: 10px;">Nombre</th>
                    <th style="padding: 10px;">Categoría</th>
                    <th style="padding: 10px;">Género</th>
                    <th style="padding: 10px;">Tipo</th>
                    <th style="padding: 10px;">Precio</th>
                    <th style="padding: 10px;">Stock</th>
                    <th style="padding: 10px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($productos)): ?>
                    <?php foreach ($productos as $p): ?>
                        <tr style="border-bottom: 1px solid #f3f4f6; font-size: 14px;<?php echo !(int)$p['activo'] ? ' opacity: 0.55;' : ''; ?>">
                            <td style="padding: 12px;"><?php echo (int)$p['id']; ?></td>
                            <td style="padding: 12px;"><img src="/glow-fashion/public/img/<?php echo e($p['imagen']); ?>" alt="" style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px; background: #f3f4f6;"></td>
                            <td style="padding: 12px; font-weight: 600;"><?php echo e($p['nombre']); ?><?php if (!(int)$p['activo']): ?> <span style="font-size: 11px; font-weight: 600; background: #f3f4f6; color: #6b7280; padding: 2px 8px; border-radius: 10px;">Retirado</span><?php endif; ?></td>
                            <td style="padding: 12px;"><?php echo e($p['categoria_nombre']); ?></td>
                            <td style="padding: 12px; text-transform: capitalize;"><?php echo e($p['genero']); ?></td>
                            <td style="padding: 12px; text-transform: capitalize;"><?php echo e($p['tipo']); ?></td>
                            <td style="padding: 12px;"><?php echo formatear_precio($p['precio']); ?></td>
                            <td style="padding: 12px;<?php echo (int)$p['stock'] <= 0 ? ' color: #ef4444; font-weight: 600;' : ''; ?>"><?php echo (int)$p['stock']; ?></td>
                            <td style="padding: 12px; white-space: nowrap;">
                                <a href="/glow-fashion/index.php?url=admin/editar-producto&id=<?php echo (int)$p['id']; ?>" style="color: #7e46d6; font-weight: 600; text-decoration: none; margin-right: 12px;">Editar</a>
                                <?php if ((int)$p['activo']): ?>
                                <form action="/glow-fashion/index.php?url=admin/eliminar-producto" method="POST" data-confirm="¿Retirar este producto del catálogo? El historial de ventas se conserva. Puedes volver a activarlo con Editar." style="margin: 0; display: inline;">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="id" value="<?php echo (int)$p['id']; ?>">
                                    <button type="submit" style="background: none; border: none; padding: 0; color: #ef4444; font-weight: 600; cursor: pointer; font-size: 14px;">Retirar</button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="padding: 20px; text-align: center; color: #9ca3af;">No hay productos registrados en la base de datos.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<script src="<?php echo asset('js/main.js'); ?>"></script>
</body>
</html>
