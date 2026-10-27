<?php
// Variables recibidas desde AdminController::editarProducto(): $producto, $categorias, $imagenes
$navActivo   = 'productos';
$estiloInput = 'width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box;';
$imagenActual = (string)$producto['imagen'];

// Precio con formato colombiano: 70.000 (o 70.000,50 si tiene decimales)
$precioNumero = (float)$producto['precio'];
$precioTexto  = (floor($precioNumero) == $precioNumero)
    ? number_format($precioNumero, 0, ',', '.')
    : number_format($precioNumero, 2, ',', '.');

// Si la imagen actual no está en la lista (por ejemplo, el archivo ya no existe), se agrega para poder conservarla
$opcionesImagen = $imagenes;
if ($imagenActual !== '' && !in_array($imagenActual, $opcionesImagen, true)) {
    array_unshift($opcionesImagen, $imagenActual);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar producto - Glow Fashion</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f9fafb; margin: 0; padding: 0;">

<?php require __DIR__ . '/_nav.php'; ?>

<main style="max-width: 900px; margin: 40px auto; padding: 0 20px;">
    <h2 style="font-family: serif; color: #1f2937; margin-bottom: 6px;">Editar producto #<?php echo (int)$producto['id']; ?></h2>
    <p style="margin-top: 0; margin-bottom: 25px;">
        <a href="/glow-fashion/index.php?url=admin/productos" style="color: #7e46d6; text-decoration: none; font-weight: 600;">← Volver a Gestión de Productos</a>
    </p>

    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06);">
        <form action="/glow-fashion/index.php?url=admin/actualizar-producto" method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px;">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo (int)$producto['id']; ?>">

            <div>
                <label style="font-size: 13px; font-weight: 600;">Nombre del Producto</label>
                <input type="text" name="nombre" required maxlength="150" value="<?php echo e($producto['nombre']); ?>" style="<?php echo $estiloInput; ?>">
            </div>
            <div>
                <label style="font-size: 13px; font-weight: 600;">Categoría</label>
                <select name="categoria_id" required style="<?php echo $estiloInput; ?>">
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?php echo (int)$c['id']; ?>" <?php echo (int)$c['id'] === (int)$producto['categoria_id'] ? 'selected' : ''; ?>><?php echo e($c['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="font-size: 13px; font-weight: 600;">Género</label>
                <select name="genero" style="<?php echo $estiloInput; ?>">
                    <?php foreach (['unisex' => 'Unisex', 'hombre' => 'Hombre', 'mujer' => 'Mujer'] as $valor => $texto): ?>
                        <option value="<?php echo $valor; ?>" <?php echo $producto['genero'] === $valor ? 'selected' : ''; ?>><?php echo $texto; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="font-size: 13px; font-weight: 600;">Tipo</label>
                <select name="tipo" style="<?php echo $estiloInput; ?>">
                    <?php foreach (['prendas' => 'Prendas', 'zapatos' => 'Zapatos', 'accesorios' => 'Accesorios', 'maquillaje' => 'Maquillaje'] as $valor => $texto): ?>
                        <option value="<?php echo $valor; ?>" <?php echo $producto['tipo'] === $valor ? 'selected' : ''; ?>><?php echo $texto; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="font-size: 13px; font-weight: 600;">Precio ($)</label>
                <input type="text" inputmode="decimal" data-moneda name="precio" required value="<?php echo e($precioTexto); ?>" placeholder="Ej: 70.000" autocomplete="off" style="<?php echo $estiloInput; ?>">
            </div>
            <div>
                <label style="font-size: 13px; font-weight: 600;">Stock / Cantidad</label>
                <input type="number" min="0" step="1" name="stock" required value="<?php echo (int)$producto['stock']; ?>" style="<?php echo $estiloInput; ?>">
            </div>

            <!-- Imagen -->
            <div style="grid-column: 1 / -1; display: flex; gap: 20px; align-items: flex-start; flex-wrap: wrap;">
                <div>
                    <label style="font-size: 13px; font-weight: 600; display: block; margin-bottom: 4px;">Imagen actual</label>
                    <img src="/glow-fashion/public/img/<?php echo e($imagenActual); ?>" alt="Imagen actual de <?php echo e($producto['nombre']); ?>" style="width: 120px; height: 120px; object-fit: cover; border-radius: 10px; background: #f3f4f6;">
                </div>
                <div style="flex: 1; min-width: 220px;">
                    <label style="font-size: 13px; font-weight: 600;">Elegir otra imagen de la lista</label>
                    <select name="imagen" style="<?php echo $estiloInput; ?>">
                        <?php foreach ($opcionesImagen as $img): ?>
                            <option value="<?php echo e($img); ?>" <?php echo $img === $imagenActual ? 'selected' : ''; ?>><?php echo e($img); ?><?php echo $img === $imagenActual ? ' (actual)' : ''; ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label style="font-size: 13px; font-weight: 600; display: block; margin-top: 12px;">...o subir una imagen nueva</label>
                    <input type="file" name="imagen_archivo" accept="image/jpeg,image/png,image/webp" style="<?php echo $estiloInput; ?> background: #fff;">
                    <small style="color: #6b7280; font-size: 12px;">JPG, PNG o WEBP, máx. 3 MB. Si subes una imagen, se usa en lugar de la lista.</small>
                </div>
            </div>

            <div style="grid-column: 1 / -1;">
                <label style="font-size: 13px; font-weight: 600;">Descripción</label>
                <textarea name="descripcion" rows="3" style="<?php echo $estiloInput; ?>"><?php echo e($producto['descripcion']); ?></textarea>
            </div>

            <div style="grid-column: 1 / -1; display: flex; gap: 25px; flex-wrap: wrap;">
                <label style="font-size: 13px; font-weight: 600;">
                    <input type="checkbox" name="destacado" value="1" <?php echo (int)$producto['destacado'] ? 'checked' : ''; ?>> Producto destacado (sale primero en la portada)
                </label>
                <label style="font-size: 13px; font-weight: 600;">
                    <input type="checkbox" name="activo" value="1" <?php echo (int)$producto['activo'] ? 'checked' : ''; ?>> Visible en la tienda
                </label>
            </div>

            <div style="grid-column: 1 / -1; display: flex; gap: 12px;">
                <button type="submit" style="background: #7e46d6; color: white; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 600; cursor: pointer;">Guardar cambios</button>
                <a href="/glow-fashion/index.php?url=admin/productos" style="padding: 10px 20px; color: #6b7280; text-decoration: none;">Cancelar</a>
            </div>
        </form>
    </div>
</main>

<script src="<?php echo asset('js/main.js'); ?>"></script>
</body>
</html>
