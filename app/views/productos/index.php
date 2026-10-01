<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inicializar lista de productos en la sesión si no existe
if (!isset($_SESSION['productos_demo'])) {
    $_SESSION['productos_demo'] = [
        ['id' => 1, 'nombre' => 'Labial Gloss Matte', 'categoria' => 'Maquillaje', 'precio' => 25000, 'stock' => 15, 'imagen' => '💄'],
        ['id' => 2, 'nombre' => 'Chaqueta Denim Oversize', 'categoria' => 'Ropa', 'precio' => 120000, 'stock' => 8, 'imagen' => '🧥'],
        ['id' => 3, 'nombre' => 'Paleta de Sombras Nude', 'categoria' => 'Maquillaje', 'precio' => 45000, 'stock' => 10, 'imagen' => '🎨'],
        ['id' => 4, 'nombre' => 'Jeans High Waist', 'categoria' => 'Ropa', 'precio' => 95000, 'stock' => 12, 'imagen' => '👖'],
    ];
}

// Obtener la categoría seleccionada desde la URL (o 'todos')
$categoriaFiltro = $_GET['cat'] ?? 'todos';

// Filtrar productos según la categoría
$productosMostrar = $_SESSION['productos_demo'];
if ($categoriaFiltro === 'maquillaje') {
    $productosMostrar = array_filter($_SESSION['productos_demo'], function($p) {
        return strtolower($p['categoria']) === 'maquillaje';
    });
} elseif ($categoriaFiltro === 'ropa') {
    $productosMostrar = array_filter($_SESSION['productos_demo'], function($p) {
        return strtolower($p['categoria']) === 'ropa';
    });
}

// Título dinámico
$tituloCatalogo = "Catálogo de Productos";
if ($categoriaFiltro === 'maquillaje') $tituloCatalogo = "Maquillaje y Accesorios";
if ($categoriaFiltro === 'ropa') $tituloCatalogo = "Ropa Hombre/Mujer";
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="admin-container">
    <div class="admin-header" style="margin-bottom: 30px;">
        <h1><?php echo htmlspecialchars($tituloCatalogo); ?></h1>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 24px;">
        <?php if (empty($productosMostrar)): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 12px; color: #6b7280;">
                No hay productos disponibles en esta categoría por el momento.
            </div>
        <?php else: ?>
            <?php foreach ($productosMostrar as $prod): ?>
                <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); display: flex; flex-direction: column; justify-content: space-between; text-align: center;">
                    <div>
                        <div style="font-size: 50px; margin-bottom: 15px;"><?php echo $prod['imagen'] ?? '🛍️'; ?></div>
                        <span class="badge" style="font-size: 12px; display: inline-block; margin-bottom: 8px;"><?php echo htmlspecialchars($prod['categoria']); ?></span>
                        <h3 style="margin: 8px 0; font-size: 18px; color: #1f2937;"><?php echo htmlspecialchars($prod['nombre']); ?></h3>
                        <p style="font-size: 20px; font-weight: bold; color: #7e46d6; margin: 10px 0;">$<?php echo number_format($prod['precio'], 0, ',', '.'); ?></p>
                    </div>

                    <form action="/glow-fashion/index.php?url=carrito" method="POST" style="margin-top: 15px;">
                        <input type="hidden" name="action" value="agregar">
                        <input type="hidden" name="id" value="<?php echo $prod['id']; ?>">
                        <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($prod['nombre']); ?>">
                        <input type="hidden" name="precio" value="<?php echo $prod['precio']; ?>">
                        
                        <button type="submit" class="btn-primary" style="width: 100%; border: none; padding: 10px; border-radius: 8px; cursor: pointer;">
                            🛒 Agregar al Carrito
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>