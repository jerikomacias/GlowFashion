<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$ventas = $_SESSION['ventas_demo'] ?? [
    ['id' => 101, 'cliente' => 'Maria Lopez', 'fecha' => '2026-09-10', 'total' => 145000, 'estado' => 'Completado'],
    ['id' => 102, 'cliente' => 'Carlos Perez', 'fecha' => '2026-09-09', 'total' => 25000, 'estado' => 'Pendiente'],
];
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="admin-container">
    <div class="admin-header">
        <h1>Gestionar Ventas</h1>
        <button class="btn-primary" onclick="toggleModal('modal-venta')">+ Registrar Venta</button>
    </div>

    <div class="table-card">
        <table class="crud-table">
            <thead>
                <tr>
                    <th>Nº Orden</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $v): ?>
                <tr>
                    <td>#<?php echo $v['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($v['cliente']); ?></strong></td>
                    <td><?php echo $v['fecha']; ?></td>
                    <td>$<?php echo number_format($v['total'], 0, ',', '.'); ?></td>
                    <td>
                        <span class="status-badge <?php echo strtolower($v['estado']); ?>">
                            <?php echo $v['estado']; ?>
                        </span>
                    </td>
                    <td class="actions-cell">
                        <button class="btn-sm btn-edit" onclick="editarVenta(<?php echo htmlspecialchars(json_encode($v)); ?>)">Cambiar Estado</button>
                        <button class="btn-sm btn-delete" onclick="eliminarRegistro('Venta', <?php echo $v['id']; ?>)">Cancelar</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Formulario Venta -->
    <div id="modal-venta" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="modal-venta-title">Registrar Venta</h3>
                <span class="close-btn" onclick="toggleModal('modal-venta')">&times;</span>
            </div>
            <form action="/glow-fashion/index.php?url=admin/ventas/guardar" method="POST" class="crud-form">
                <input type="hidden" name="id" id="venta-id">
                
                <div class="form-group">
                    <label>Nombre del Cliente</label>
                    <input type="text" name="cliente" id="venta-cliente" required placeholder="Nombre completo">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Total ($)</label>
                        <input type="number" name="total" id="venta-total" required placeholder="0000">
                    </div>

                    <div class="form-group">
                        <label>Estado</label>
                        <select name="estado" id="venta-estado" required>
                            <option value="Completado">Completado</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="toggleModal('modal-venta')">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
function toggleModal(id) {
    const modal = document.getElementById(id);
    modal.classList.toggle('active');
}

function editarVenta(venta) {
    document.getElementById('venta-id').value = venta.id;
    document.getElementById('venta-cliente').value = venta.cliente;
    document.getElementById('venta-total').value = venta.total;
    document.getElementById('venta-estado').value = venta.estado;
    document.getElementById('modal-venta-title').innerText = 'Editar Venta #' + venta.id;
    toggleModal('modal-venta');
}

function eliminarRegistro(tipo, id) {
    if (confirm(`¿Estás seguro de eliminar la ${tipo} #${id}?`)) {
        alert(`${tipo} #${id} eliminada.`);
    }
}
</script>