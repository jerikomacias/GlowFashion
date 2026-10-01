<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userRole = $_SESSION['user_role'] ?? 'vendedor';
$usuarioActual = $_SESSION['usuario'] ?? $_SESSION['user_email'] ?? 'invitado';

// Inicializar la lista de productos con autor/vendedor
if (!isset($_SESSION['productos_demo'])) {
    $_SESSION['productos_demo'] = [
        ['id' => 1, 'nombre' => 'Labial Gloss Matte', 'categoria' => 'Maquillaje', 'precio' => 25000, 'stock' => 15, 'vendedor' => 'admin@glowfashion.com'],
        ['id' => 2, 'nombre' => 'Chaqueta Denim Oversize', 'categoria' => 'Ropa', 'precio' => 120000, 'stock' => 8, 'vendedor' => 'vendedor@glowfashion.com'],
        ['id' => 3, 'nombre' => 'Paleta de Sombras Nude', 'categoria' => 'Maquillaje', 'precio' => 45000, 'stock' => 10, 'vendedor' => 'vendedor2@glowfashion.com'],
    ];
}

// PROCESAR ACCIONES (AGREGAR / EDITAR / ELIMINAR)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'guardar') {
        $id = $_POST['id'] ?? '';
        $nombre = trim($_POST['nombre']);
        $categoria = $_POST['categoria'];
        $precio = (float)($_POST['precio'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);

        if (!empty($id)) {
            // EDITAR PRODUCTO (Solo si es Admin o el dueño del producto)
            foreach ($_SESSION['productos_demo'] as &$p) {
                if ($p['id'] == $id && ($userRole === 'admin' || $p['vendedor'] === $usuarioActual)) {
                    $p['nombre'] = $nombre;
                    $p['categoria'] = $categoria;
                    $p['precio'] = $precio;
                    $p['stock'] = $stock;
                    break;
                }
            }
        } else {
            // AGREGAR NUEVO PRODUCTO
            $nuevoId = !empty($_SESSION['productos_demo']) ? max(array_column($_SESSION['productos_demo'], 'id')) + 1 : 1;
            $_SESSION['productos_demo'][] = [
                'id' => $nuevoId,
                'nombre' => $nombre,
                'categoria' => $categoria,
                'precio' => $precio,
                'stock' => $stock,
                'vendedor' => $usuarioActual
            ];
        }
        header('Location: /glow-fashion/index.php?url=admin/productos');
        exit();
    }

    if ($action === 'eliminar') {
        $id = (int)($_POST['id'] ?? 0);
        $_SESSION['productos_demo'] = array_values(array_filter($_SESSION['productos_demo'], function ($p) use ($id, $userRole, $usuarioActual) {
            if ($p['id'] === $id) {
                return !($userRole === 'admin' || $p['vendedor'] === $usuarioActual);
            }
            return true;
        }));
        header('Location: /glow-fashion/index.php?url=admin/productos');
        exit();
    }
}

// FILTRAR PRODUCTOS SEGÚN EL ROL
$todosProductos = $_SESSION['productos_demo'];
$productos = ($userRole === 'admin') 
    ? $todosProductos 
    : array_filter($todosProductos, fn($p) => isset($p['vendedor']) && $p['vendedor'] === $usuarioActual);
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="admin-container">
    <div class="admin-header">
        <div>
            <h1>Gestionar Productos</h1>
            <span style="font-size: 13px; color: #6b7280;">Rol activo: <strong><?php echo strtoupper($userRole); ?></strong> (<?php echo htmlspecialchars($usuarioActual); ?>)</span>
        </div>
        <button class="btn-primary" onclick="toggleModal('modal-producto')">+ Nuevo Producto</button>
    </div>

    <!-- Tabla de productos -->
    <div class="table-card">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>PRODUCTO</th>
                    <th>CATEGORÍA</th>
                    <th>PRECIO</th>
                    <th>STOCK</th>
                    <?php if ($userRole === 'admin'): ?>
                        <th>CREADO POR</th>
                    <?php endif; ?>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($productos)): ?>
                    <tr>
                        <td colspan="<?php echo ($userRole === 'admin') ? '7' : '6'; ?>" style="text-align: center; color: #6b7280; padding: 20px;">
                            No hay productos registrados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($productos as $p): ?>
                    <tr>
                        <td>#<?php echo $p['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($p['nombre']); ?></strong></td>
                        <td><span class="badge"><?php echo htmlspecialchars($p['categoria']); ?></span></td>
                        <td>$<?php echo number_format($p['precio'], 0, ',', '.'); ?></td>
                        <td><?php echo $p['stock']; ?> uds.</td>
                        <?php if ($userRole === 'admin'): ?>
                            <td><span style="font-size: 12px; background: #f3f4f6; padding: 4px 8px; border-radius: 6px; font-weight: 600; color: #4b5563;"><?php echo htmlspecialchars($p['vendedor'] ?? 'Sistema'); ?></span></td>
                        <?php endif; ?>
                        <td class="actions-cell">
                            <button class="btn-sm btn-edit" onclick='editarProducto(<?php echo json_encode($p); ?>)'>Editar</button>
                            <form action="/glow-fashion/index.php?url=admin/productos" method="POST" style="display:inline;" onsubmit="return confirm('¿Deseas eliminar este producto?');">
                                <input type="hidden" name="action" value="eliminar">
                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                <button type="submit" class="btn-sm btn-delete">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
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
            <form action="/glow-fashion/index.php?url=admin/productos" method="POST" class="crud-form">
                <input type="hidden" name="action" value="guardar">
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
</script>