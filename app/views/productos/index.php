<?php
// Variables recibidas desde ProductosController:
// $catFiltro, $generoFiltro, $tipoFiltro, $tituloCatalogo, $productosMostrar

$base = '/glow-fashion/index.php?url=categoria/' . $catFiltro;

// Botones de filtro según la categoría: [texto, enlace, ¿activo?]
$filtros = [['Ver Todo', $base, ($generoFiltro === 'todos' && $tipoFiltro === 'todos')]];

if ($catFiltro === 'accesorios') {
    $filtros[] = ['Accesorios Hombre', $base . '&genero=hombre', $generoFiltro === 'hombre'];
    $filtros[] = ['Accesorios Mujer',  $base . '&genero=mujer',  $generoFiltro === 'mujer'];
}
if ($catFiltro === 'ropa') {
    $filtros[] = ['Ropa Hombre', $base . '&genero=hombre', $generoFiltro === 'hombre'];
    $filtros[] = ['Ropa Mujer',  $base . '&genero=mujer',  $generoFiltro === 'mujer'];
    $filtros[] = ['Zapatos / Sneakers 👟', $base . '&tipo=zapatos', $tipoFiltro === 'zapatos'];
}
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="admin-container">
    <div class="admin-header" style="margin-bottom: 20px;">
        <h1><?php echo e($tituloCatalogo); ?></h1>
    </div>

    <!-- Filtros -->
    <div style="display: flex; gap: 10px; margin-bottom: 30px; flex-wrap: wrap;">
        <?php foreach ($filtros as [$texto, $enlace, $activo]): ?>
            <a href="<?php echo e($enlace); ?>"
               style="padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 14px; background: <?php echo $activo ? '#7e46d6; color: white;' : '#e5e7eb; color: #374151;'; ?>">
               <?php echo e($texto); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Grid de productos (datos reales de la base de datos) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 24px;">
        <?php if (empty($productosMostrar)): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 12px; color: #6b7280;">
                No hay productos disponibles en este apartado.
            </div>
        <?php else: ?>
            <?php foreach ($productosMostrar as $prod): ?>
                <?php require __DIR__ . '/_tarjeta.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
