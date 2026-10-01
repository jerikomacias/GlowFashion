<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userRole = $_SESSION['user_role'] ?? 'vendedor';
$usuarioActual = $_SESSION['usuario'] ?? $_SESSION['user_email'] ?? 'invitado';

if (!isset($_SESSION['ventas_demo'])) {
    $_SESSION['ventas_demo'] = [
        ['orden' => 101, 'cliente' => 'Maria Lopez', 'producto' => 'Labial Gloss Matte', 'unidades' => 2, 'fecha' => '2026-09-10', 'total' => 50000, 'estado' => 'Completado', 'vendedor' => 'admin@glowfashion.com'],
        ['orden' => 102, 'cliente' => 'Carlos Perez', 'producto' => 'Chaqueta Denim Oversize', 'unidades' => 1, 'fecha' => '2026-09-09', 'total' => 120000, 'estado' => 'Pendiente', 'vendedor' => 'vendedor@glowfashion.com'],
        ['orden' => 103, 'cliente' => 'Laura Gómez', 'producto' => 'Labial Gloss Matte', 'unidades' => 1, 'fecha' => '2026-09-15', 'total' => 25000, 'estado' => 'Completado', 'vendedor' => 'admin@glowfashion.com'],
    ];
}

// PROCESAR ACCIONES (REGISTRAR VENTA / CAMBIAR ESTADO / CANCELAR)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'guardar') {
        $cliente  = trim($_POST['cliente']);
        $producto = trim($_POST['producto']);
        $unidades = (int)($_POST['unidades'] ?? 1);
        $fecha    = !empty($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d');
        $total    = (float)($_POST['total'] ?? 0);
        $estado   = $_POST['estado'] ?? 'Pendiente';

        $nuevaOrden = !empty($_SESSION['ventas_demo']) ? max(array_column($_SESSION['ventas_demo'], 'orden')) + 1 : 101;

        $_SESSION['ventas_demo'][] = [
            'orden'    => $nuevaOrden,
            'cliente'  => $cliente,
            'producto' => $producto,
            'unidades' => $unidades,
            'fecha'    => $fecha,
            'total'    => $total,
            'estado'   => $estado,
            'vendedor' => $usuarioActual
        ];

        header('Location: /glow-fashion/index.php?url=admin/ventas');
        exit();
    }

    if ($action === 'cambiar_estado') {
        $orden = (int)($_POST['orden'] ?? 0);
        foreach ($_SESSION['ventas_demo'] as &$v) {
            if ($v['orden'] === $orden && ($userRole === 'admin' || $v['vendedor'] === $usuarioActual)) {
                $v['estado'] = ($v['estado'] === 'Pendiente') ? 'Completado' : 'Pendiente';
                break;
            }
        }
        header('Location: /glow-fashion/index.php?url=admin/ventas');
        exit();
    }

    if ($action === 'cancelar') {
        $orden = (int)($_POST['orden'] ?? 0);
        foreach ($_SESSION['ventas_demo'] as &$v) {
            if ($v['orden'] === $orden && ($userRole === 'admin' || $v['vendedor'] === $usuarioActual)) {
                $v['estado'] = 'Cancelado';
                break;
            }
        }
        header('Location: /glow-fashion/index.php?url=admin/ventas');
        exit();
    }
}

// FILTRAR VENTAS SEGÚN EL ROL
$todasVentas = $_SESSION['ventas_demo'];
$ventas = ($userRole === 'admin') 
    ? $todasVentas 
    : array_filter($todasVentas, fn($v) => isset($v['vendedor']) && $v['vendedor'] === $usuarioActual);

// CALCULAR REPORTE GENERAL DE VENTAS POR PRODUCTO Y VENDEDOR (SOLO ADMIN)
$reporteProductos = [];
if ($userRole === 'admin') {
    foreach ($_SESSION['ventas_demo'] as $v) {
        if ($v['estado'] !== 'Cancelado') {
            $prod = $v['producto'] ?? 'Producto General';
            if (!isset($reporteProductos[$prod])) {
                $reporteProductos[$prod] = [
                    'producto' => $prod,
                    'vendedor' => $v['vendedor'] ?? 'Sistema',
                    'unidades_vendidas' => 0,
                    'ingresos_totales' => 0
                ];
            }
            $reporteProductos[$prod]['unidades_vendidas'] += ($v['unidades'] ?? 1);
            $reporteProductos[$prod]['ingresos_totales'] += $v['total'];
        }
    }
}
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="admin-container">
    <div class="admin-header">
        <div>
            <h1>Gestionar Ventas</h1>
            <span style="font-size: 13px; color: #6b7280;">Rol activo: <strong><?php echo strtoupper($userRole); ?></strong> (<?php echo htmlspecialchars($usuarioActual); ?>)</span>
        </div>
        <button class="btn-primary" onclick="toggleModal('modal-venta')">+ Registrar Venta</button>
    </div>

    <!-- SECCIÓN DE REPORTE GENERAL (SOLO VISIBLE PARA ADMIN) -->
    <?php if ($userRole === 'admin'): ?>
        <div style="background: white; border-radius: 12px; padding: 20px; margin-bottom: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-left: 5px solid #7e46d6;">
            <h2 style="margin-top: 0; font-size: 18px; color: #1f2937; margin-bottom: 15px;">📊 Reporte General de Ventas por Producto</h2>
            <table class="crud-table" style="font-size: 14px;">
                <thead>
                    <tr style="background: #f9fafb;">
                        <th>PRODUCTO</th>
                        <th>SUBIDO POR (VENDEDOR)</th>
                        <th>UNIDADES VENDIDAS</th>
                        <th>TOTAL GENERADO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reporteProductos)): ?>
                        <tr><td colspan="4" style="text-align: center; color: #6b7280;">No hay ventas confirmadas aún.</td></tr>
                    <?php else: ?>
                        <?php foreach ($reporteProductos as $rep): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($rep['producto']); ?></strong></td>
                                <td><span style="font-size: 12px; background: #e0e7ff; color: #3730a3; padding: 3px 8px; border-radius: 4px; font-weight: 600;"><?php echo htmlspecialchars($rep['vendedor']); ?></span></td>
                                <td><?php echo $rep['unidades_vendidas']; ?> uds.</td>
                                <td><strong style="color: #7e46d6;">$<?php echo number_format($rep['ingresos_totales'], 0, ',', '.'); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- Tabla Historial de Ventas -->
    <div class="table-card">
        <h3 style="padding: 15px 20px 0 20px; margin: 0; font-size: 16px; color: #374151;">Historial de Pedidos / Transacciones</h3>
        <table class="crud-table">
            <thead>
                <tr>
                    <th>Nº ORDEN</th>
                    <th>CLIENTE</th>
                    <th>PRODUCTO</th>
                    <th>FECHA</th>
                    <th>TOTAL</th>
                    <th>ESTADO</th>
                    <?php if ($userRole === 'admin'): ?><th>VENDEDOR</th><?php endif; ?>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ventas)): ?>
                    <tr>
                        <td colspan="<?php echo ($userRole === 'admin') ? '8' : '7'; ?>" style="text-align: center; color: #6b7280; padding: 20px;">
                            No hay ventas registradas.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($ventas as $v): ?>
                    <tr>
                        <td>#<?php echo $v['orden']; ?></td>
                        <td><strong><?php echo htmlspecialchars($v['cliente']); ?></strong></td>
                        <td><?php echo htmlspecialchars($v['producto'] ?? 'Varios'); ?></td>
                        <td><?php echo htmlspecialchars($v['fecha']); ?></td>
                        <td>$<?php echo number_format($v['total'], 0, ',', '.'); ?></td>
                        <td>
                            <?php 
                                $badgeClass = 'badge-pendiente';
                                if ($v['estado'] === 'Completado') $badgeClass = 'badge-completado';
                                if ($v['estado'] === 'Cancelado') $badgeClass = 'badge-cancelado';
                            ?>
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($v['estado']); ?></span>
                        </td>
                        <?php if ($userRole === 'admin'): ?>
                            <td><span style="font-size: 12px; color: #4b5563; font-weight: 500;"><?php echo htmlspecialchars($v['vendedor'] ?? 'Admin'); ?></span></td>
                        <?php endif; ?>
                        <td class="actions-cell">
                            <form action="/glow-fashion/index.php?url=admin/ventas" method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="cambiar_estado">
                                <input type="hidden" name="orden" value="<?php echo $v['orden']; ?>">
                                <button type="submit" class="btn-sm btn-edit">Cambiar Estado</button>
                            </form>
                            
                            <?php if ($v['estado'] !== 'Cancelado'): ?>
                            <form action="/glow-fashion/index.php?url=admin/ventas" method="POST" style="display:inline;" onsubmit="return confirm('¿Seguro que deseas cancelar esta orden?');">
                                <input type="hidden" name="action" value="cancelar">
                                <input type="hidden" name="orden" value="<?php echo $v['orden']; ?>">
                                <button type="submit" class="btn-sm btn-delete">Cancelar</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Formulario Registrar Venta -->
    <div id="modal-venta" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Registrar Nueva Venta</h3>
                <span class="close-btn" onclick="toggleModal('modal-venta')">&times;</span>
            </div>
            <form action="/glow-fashion/index.php?url=admin/ventas" method="POST" class="crud-form">
                <input type="hidden" name="action" value="guardar">
                
                <div class="form-group">
                    <label>Nombre del Cliente</label>
                    <input type="text" name="cliente" required placeholder="Ej: Laura Gómez">
                </div>

                <div class="form-group">
                    <label>Producto Vendido</label>
                    <input type="text" name="producto" required placeholder="Ej: Labial Gloss Matte">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Unidades</label>
                        <input type="number" name="unidades" value="1" min="1" required>
                    </div>

                    <div class="form-group">
                        <label>Total ($)</label>
                        <input type="number" name="total" required placeholder="25000">
                    </div>

                    <div class="form-group">
                        <label>Estado Inicial</label>
                        <select name="estado" required>
                            <option value="Completado">Completado</option>
                            <option value="Pendiente" selected>Pendiente</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="toggleModal('modal-venta')">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar Venta</button>
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
</script>