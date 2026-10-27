<?php
// Variables recibidas desde AdminController::ventas():
// $desde, $hasta, $resumen, $topProductos, $porMetodo, $porDia, $topClientes, $stockBajo, $ventas
$navActivo = 'ventas';

$filtrosUrl = http_build_query(array_filter(['desde' => $desde, 'hasta' => $hasta]));
$urlExportar = '/glow-fashion/index.php?url=admin/exportar-ventas' . ($filtrosUrl ? '&' . $filtrosUrl : '');

$maxDia = 0;
foreach ($porDia as $d) {
    $maxDia = max($maxDia, (float)$d['ingresos']);
}

$tarjeta = 'background: white; padding: 22px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.06);';
$th      = 'padding: 10px; font-size: 13px; color: #6b7280;';
$td      = 'padding: 10px; font-size: 14px; border-bottom: 1px solid #f3f4f6;';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas y Reportes - Glow Fashion</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f9fafb; margin: 0; padding: 0;">

<?php require __DIR__ . '/_nav.php'; ?>

<main style="max-width: 1150px; margin: 40px auto; padding: 0 20px;">
    <h2 style="font-family: serif; color: #1f2937; margin-bottom: 6px;">Ventas y Reportes</h2>
    <p style="color: #6b7280; margin-top: 0; margin-bottom: 25px;">
        <?php if ($desde || $hasta): ?>
            Mostrando <?php echo $desde ? 'desde ' . e($desde) : ''; ?> <?php echo $hasta ? 'hasta ' . e($hasta) : ''; ?>
        <?php else: ?>
            Mostrando todo el historial de ventas.
        <?php endif; ?>
    </p>

    <!-- Filtro por fechas -->
    <form action="/glow-fashion/index.php" method="GET" style="<?php echo $tarjeta; ?> display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; margin-bottom: 25px;">
        <input type="hidden" name="url" value="admin/ventas">
        <div>
            <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Desde</label>
            <input type="date" name="desde" value="<?php echo e($desde); ?>" style="padding: 8px; border: 1px solid #d1d5db; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 4px;">Hasta</label>
            <input type="date" name="hasta" value="<?php echo e($hasta); ?>" style="padding: 8px; border: 1px solid #d1d5db; border-radius: 6px;">
        </div>
        <button type="submit" style="background: #7e46d6; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer;">Filtrar</button>
        <a href="/glow-fashion/index.php?url=admin/ventas" style="color: #6b7280; font-size: 14px; padding: 10px 4px;">Limpiar</a>
        <a href="<?php echo e($urlExportar); ?>" style="margin-left: auto; background: #1f2937; color: white; padding: 10px 20px; border-radius: 6px; font-weight: 600; text-decoration: none; font-size: 14px;">⬇ Exportar a Excel (CSV)</a>
    </form>

    <!-- Indicadores -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div style="<?php echo $tarjeta; ?> border-left: 4px solid #7e46d6;">
            <div style="color: #6b7280; font-size: 13px;">Ventas realizadas</div>
            <div style="font-size: 30px; font-weight: bold; color: #1f2937;"><?php echo (int)$resumen['num_ventas']; ?></div>
        </div>
        <div style="<?php echo $tarjeta; ?> border-left: 4px solid #7e46d6;">
            <div style="color: #6b7280; font-size: 13px;">Ingresos totales</div>
            <div style="font-size: 30px; font-weight: bold; color: #7e46d6;"><?php echo formatear_precio($resumen['ingresos']); ?></div>
        </div>
        <div style="<?php echo $tarjeta; ?> border-left: 4px solid #7e46d6;">
            <div style="color: #6b7280; font-size: 13px;">Unidades vendidas</div>
            <div style="font-size: 30px; font-weight: bold; color: #1f2937;"><?php echo (int)$resumen['unidades']; ?></div>
        </div>
        <div style="<?php echo $tarjeta; ?> border-left: 4px solid #7e46d6;">
            <div style="color: #6b7280; font-size: 13px;">Ticket promedio</div>
            <div style="font-size: 30px; font-weight: bold; color: #1f2937;"><?php echo formatear_precio($resumen['ticket']); ?></div>
        </div>
    </div>

    <!-- Alerta de stock bajo -->
    <?php if (!empty($stockBajo)): ?>
        <div style="background: #fffbeb; border: 1px solid #fcd34d; color: #92400e; padding: 16px 20px; border-radius: 12px; margin-bottom: 25px; font-size: 14px;">
            <strong>⚠ Stock bajo (5 unidades o menos):</strong>
            <?php
            $partes = [];
            foreach ($stockBajo as $p) {
                $partes[] = e($p['nombre']) . ' (' . (int)$p['stock'] . ')';
            }
            echo implode(' · ', $partes);
            ?>
        </div>
    <?php endif; ?>

    <!-- Rankings -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div style="<?php echo $tarjeta; ?>">
            <h3 style="margin-top: 0; font-size: 17px; color: #374151;">Productos más vendidos</h3>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead><tr><th style="<?php echo $th; ?>">Producto</th><th style="<?php echo $th; ?>">Unidades</th><th style="<?php echo $th; ?>">Ingresos</th></tr></thead>
                <tbody>
                <?php if (empty($topProductos)): ?>
                    <tr><td colspan="3" style="<?php echo $td; ?> color: #9ca3af; text-align: center;">Sin ventas en este periodo.</td></tr>
                <?php else: foreach ($topProductos as $p): ?>
                    <tr>
                        <td style="<?php echo $td; ?>"><?php echo e($p['nombre']); ?></td>
                        <td style="<?php echo $td; ?>"><?php echo (int)$p['unidades']; ?></td>
                        <td style="<?php echo $td; ?>"><?php echo formatear_precio($p['ingresos']); ?></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <div style="<?php echo $tarjeta; ?>">
            <h3 style="margin-top: 0; font-size: 17px; color: #374151;">Ventas por método de pago</h3>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead><tr><th style="<?php echo $th; ?>">Método</th><th style="<?php echo $th; ?>">Ventas</th><th style="<?php echo $th; ?>">Ingresos</th></tr></thead>
                <tbody>
                <?php if (empty($porMetodo)): ?>
                    <tr><td colspan="3" style="<?php echo $td; ?> color: #9ca3af; text-align: center;">Sin ventas en este periodo.</td></tr>
                <?php else: foreach ($porMetodo as $m): ?>
                    <tr>
                        <td style="<?php echo $td; ?>"><?php echo e($m['metodo_pago']); ?></td>
                        <td style="<?php echo $td; ?>"><?php echo (int)$m['num_ventas']; ?></td>
                        <td style="<?php echo $td; ?>"><?php echo formatear_precio($m['ingresos']); ?></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <div style="<?php echo $tarjeta; ?>">
            <h3 style="margin-top: 0; font-size: 17px; color: #374151;">Mejores clientes</h3>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead><tr><th style="<?php echo $th; ?>">Cliente</th><th style="<?php echo $th; ?>">Compras</th><th style="<?php echo $th; ?>">Total</th></tr></thead>
                <tbody>
                <?php if (empty($topClientes)): ?>
                    <tr><td colspan="3" style="<?php echo $td; ?> color: #9ca3af; text-align: center;">Sin ventas en este periodo.</td></tr>
                <?php else: foreach ($topClientes as $c): ?>
                    <tr>
                        <td style="<?php echo $td; ?>"><?php echo e($c['nombre']); ?><?php if (!empty($c['email'])): ?><br><small style="color: #6b7280;"><?php echo e($c['email']); ?></small><?php endif; ?></td>
                        <td style="<?php echo $td; ?>"><?php echo (int)$c['compras']; ?></td>
                        <td style="<?php echo $td; ?>"><?php echo formatear_precio($c['total']); ?></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Ventas por día -->
    <div style="<?php echo $tarjeta; ?> margin-bottom: 25px;">
        <h3 style="margin-top: 0; font-size: 17px; color: #374151;">Ventas por día (últimos 14 días con ventas)</h3>
        <?php if (empty($porDia)): ?>
            <p style="color: #9ca3af; margin: 0;">Sin ventas en este periodo.</p>
        <?php else: foreach ($porDia as $d): ?>
            <?php $ancho = $maxDia > 0 ? max(2, round(((float)$d['ingresos'] / $maxDia) * 100)) : 0; ?>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px; font-size: 13px;">
                <span style="width: 90px; color: #6b7280;"><?php echo e($d['dia']); ?></span>
                <div style="flex: 1; background: #f3e8ff; border-radius: 6px; overflow: hidden;">
                    <div style="width: <?php echo (int)$ancho; ?>%; background: #7e46d6; height: 18px;"></div>
                </div>
                <span style="width: 190px; text-align: right;"><?php echo formatear_precio($d['ingresos']); ?> · <?php echo (int)$d['num_ventas']; ?> venta(s)</span>
            </div>
        <?php endforeach; endif; ?>
    </div>

    <!-- Historial completo -->
    <div style="<?php echo $tarjeta; ?> overflow-x: auto;">
        <h3 style="margin-top: 0; font-size: 17px; color: #374151;">Historial de todas las ventas</h3>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 2px solid #f3f4f6;">
                    <th style="<?php echo $th; ?>">ID Venta</th>
                    <th style="<?php echo $th; ?>">Cliente</th>
                    <th style="<?php echo $th; ?>">Productos</th>
                    <th style="<?php echo $th; ?>">Dirección</th>
                    <th style="<?php echo $th; ?>">Método de Pago</th>
                    <th style="<?php echo $th; ?>">Total Pagado</th>
                    <th style="<?php echo $th; ?>">Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ventas)): ?>
                    <?php foreach ($ventas as $v): ?>
                        <tr>
                            <td style="<?php echo $td; ?> font-weight: bold;">#<?php echo (int)$v['id']; ?></td>
                            <td style="<?php echo $td; ?>">
                                <?php echo e($v['cliente_nombre']); ?>
                                <?php if (!empty($v['usuario_email'])): ?>
                                    <br><small style="color: #6b7280;"><?php echo e($v['usuario_email']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td style="<?php echo $td; ?>"><?php echo e($v['detalle']); ?></td>
                            <td style="<?php echo $td; ?>"><?php echo e($v['direccion']); ?></td>
                            <td style="<?php echo $td; ?>"><?php echo e($v['metodo_pago']); ?></td>
                            <td style="<?php echo $td; ?> font-weight: bold; color: #7e46d6;"><?php echo formatear_precio($v['total']); ?></td>
                            <td style="<?php echo $td; ?> color: #6b7280;"><?php echo e($v['fecha']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="padding: 30px; text-align: center; color: #9ca3af;">No se han registrado ventas<?php echo ($desde || $hasta) ? ' en este periodo' : ' todavía'; ?>.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<script src="<?php echo asset('js/main.js'); ?>"></script>
</body>
</html>
