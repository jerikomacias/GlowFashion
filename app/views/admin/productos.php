<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Lista de ejemplo o datos provenientes de base de datos
$productos = $_SESSION['productos_demo'] ?? [
    ['id' => 1, 'nombre' => 'Labial Gloss Matte', 'categoria' => 'Maquillaje', 'precio' => 25000, 'stock' => 15],
    ['id' => 2, 'nombre' => 'Chaqueta Denim Oversize', 'categoria' => 'Ropa', 'precio' => 120000, 'stock' => 8],
];
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="admin-container">
    <div class="admin-header">
        <h1>Gestionar Productos</h1>
        <button class="btn-primary" onclick="toggleModal('modal-producto')">+ Nuevo Producto</button>
    </div>

    <!-- Tabla de productos -->
    <div class="table-card">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $p): ?>
                <tr>
                    <td>#<?php echo $p['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($p['nombre']); ?></strong></td>
                    <td><span class="badge"><?php echo htmlspecialchars($p['categoria']); ?></span></td>
                    <td>$<?php echo number_format($p['precio'], 0, ',', '.'); ?></td>
                    <td><?php echo $p['stock']; ?> uds.</td>
                    <td class="actions-cell">
                        <button class="btn-sm btn-edit" onclick="editarProducto(<?php echo htmlspecialchars(json_encode($p)); ?>)">Editar</button>
                        <button class="btn-sm btn-delete" onclick="eliminarRegistro('Producto', <?php echo $p['id']; ?>)">Eliminar</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Formulario Producto -->
    <div id="modal-producto" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="modal-title">Agregar Producto</h3>
                <span class="close-btn" onclick="toggleModal('modal-producto')">&times;</span>
            </div>
            <form action="/glow-fashion/index.php?url=admin/productos/guardar" method="POST" class="crud-form">
                <input type="hidden" name="id" id="prod-id">
                
                <div class="form-group">
                    <label>Nombre del Producto</label>
                    <input type="text" name="nombre" id="prod-nombre" required placeholder="Ej: Vestido Elegante">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Categoría</label>
                        <select name="categoria" id="prod-categoria" required>
                            <option value="Maquillaje">Maquillaje y Accesorios</option>
                            <option value="Ropa">Ropa Hombre/Mujer</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Precio ($)</label>
                        <input type="number" name="precio" id="prod-precio" required placeholder="50000">
                    </div>

                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" id="prod-stock" required placeholder="10">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="toggleModal('modal-producto')">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function toggleModal(id) {
    const modal = document.getElementById(id);
    modal.classList.toggle('active');
    if(!modal.classList.contains('active')) {
        document.getElementById('prod-id').value = '';
        document.getElementById('prod-nombre').value = '';
        document.getElementById('prod-precio').value = '';
        document.getElementById('prod-stock').value = '';
        document.getElementById('modal-title').innerText = 'Agregar Producto';
    }
}

function editarProducto(prod) {
    document.getElementById('prod-id').value = prod.id;
    document.getElementById('prod-nombre').value = prod.nombre;
    document.getElementById('prod-categoria').value = prod.categoria;
    document.getElementById('prod-precio').value = prod.precio;
    document.getElementById('prod-stock').value = prod.stock;
    document.getElementById('modal-title').innerText = 'Editar Producto #' + prod.id;
    toggleModal('modal-producto');
}

function eliminarRegistro(tipo, id) {
    if (confirm(`¿Estás seguro de que deseas eliminar este ${tipo}?`)) {
        alert(`${tipo} #${id} eliminado correctamente.`);
        // Aquí puedes redireccionar o enviar mediante AJAX a tu script de procesamiento
    }
}
</script>