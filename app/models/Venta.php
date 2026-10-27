<?php

class Venta {
    // Formas de pago permitidas (deben coincidir con el <select> del carrito)
    const METODOS_PAGO = [
        'Efectivo (Contra entrega)',
        'Transferencia / Nequi / Daviplata',
        'Tarjeta de Crédito / Débito',
    ];

    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Registra la venta completa en una sola transacción:
    //   ventas + detalle_ventas + descuento de stock.
    // Si algo falla se deshace todo (rollback) y no queda nada a medias.
    // $cantidades = [producto_id => cantidad]
    // Los precios y el stock se leen SIEMPRE de la base de datos, nunca del navegador.
    public function crearVenta(int $usuarioId, string $cliente, string $direccion, string $metodoPago, array $cantidades): int {
        if (empty($cantidades)) {
            throw new RuntimeException('El carrito está vacío.');
        }
        ksort($cantidades); // orden fijo para evitar bloqueos cruzados entre compras simultáneas

        $this->db->beginTransaction();
        try {
            $selProducto = $this->db->prepare(
                "SELECT id, nombre, precio, stock FROM productos WHERE id = :id AND activo = 1 FOR UPDATE"
            );

            $lineas    = [];
            $totalCent = 0; // se calcula en centavos (enteros) para evitar errores de decimales

            foreach ($cantidades as $productoId => $cantidad) {
                $selProducto->execute([':id' => $productoId]);
                $p = $selProducto->fetch();
                $selProducto->closeCursor();

                if (!$p) {
                    throw new RuntimeException('Uno de los productos del carrito ya no está disponible.');
                }
                if ((int)$p['stock'] < $cantidad) {
                    throw new RuntimeException('Stock insuficiente para "' . $p['nombre'] . '". Disponibles: ' . (int)$p['stock'] . '.');
                }

                $precioCent = (int)round(((float)$p['precio']) * 100);
                $subCent    = $precioCent * $cantidad;
                $totalCent += $subCent;

                $lineas[] = [
                    'producto_id' => (int)$p['id'],
                    'cantidad'    => (int)$cantidad,
                    'precio'      => number_format($precioCent / 100, 2, '.', ''),
                    'subtotal'    => number_format($subCent / 100, 2, '.', ''),
                    'nombre'      => $p['nombre'],
                ];
            }

            // 1. Venta principal
            $insVenta = $this->db->prepare(
                "INSERT INTO ventas (usuario_id, cliente_nombre, direccion, metodo_pago, total)
                 VALUES (:usuario_id, :cliente, :direccion, :metodo, :total)"
            );
            $insVenta->execute([
                ':usuario_id' => $usuarioId,
                ':cliente'    => $cliente,
                ':direccion'  => $direccion,
                ':metodo'     => $metodoPago,
                ':total'      => number_format($totalCent / 100, 2, '.', ''),
            ]);
            $ventaId = (int)$this->db->lastInsertId();

            // 2. Detalle + descuento de stock
            $insDetalle = $this->db->prepare(
                "INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal)
                 VALUES (:venta_id, :producto_id, :cantidad, :precio, :subtotal)"
            );
            $updStock = $this->db->prepare(
                "UPDATE productos SET stock = stock - :cantidad WHERE id = :id AND stock >= :minimo"
            );

            foreach ($lineas as $l) {
                $insDetalle->execute([
                    ':venta_id'    => $ventaId,
                    ':producto_id' => $l['producto_id'],
                    ':cantidad'    => $l['cantidad'],
                    ':precio'      => $l['precio'],
                    ':subtotal'    => $l['subtotal'],
                ]);

                $updStock->execute([
                    ':cantidad' => $l['cantidad'],
                    ':id'       => $l['producto_id'],
                    ':minimo'   => $l['cantidad'],
                ]);
                if ($updStock->rowCount() !== 1) {
                    throw new RuntimeException('No se pudo descontar el stock de "' . $l['nombre'] . '".');
                }
            }

            // 3. La compra se completó: el carrito guardado del usuario queda vacío
            $vaciar = $this->db->prepare("DELETE FROM carrito_items WHERE usuario_id = :u");
            $vaciar->execute([':u' => $usuarioId]);

            $this->db->commit();
            return $ventaId;

        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    // Construye el filtro de fechas (YYYY-MM-DD). Devuelve [fragmentoSQL, parámetros].
    private function filtroFechas($desde, $hasta, $alias = 'v') {
        $sql = '';
        $params = [];
        if ($desde) {
            $sql .= " AND $alias.fecha >= :desde";
            $params[':desde'] = $desde . ' 00:00:00';
        }
        if ($hasta) {
            $sql .= " AND $alias.fecha <= :hasta";
            $params[':hasta'] = $hasta . ' 23:59:59';
        }
        return [$sql, $params];
    }

    private function consultar($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // Todas las ventas con un resumen de productos (panel de administración)
    public function getAll($desde = null, $hasta = null) {
        list($filtro, $params) = $this->filtroFechas($desde, $hasta);
        $sql = "SELECT v.id, v.cliente_nombre, v.direccion, v.metodo_pago, v.total, v.fecha,
                       MAX(u.email) AS usuario_email,
                       GROUP_CONCAT(CONCAT(d.cantidad, ' x ', p.nombre) ORDER BY d.id SEPARATOR ', ') AS detalle
                FROM ventas v
                LEFT JOIN usuarios u       ON u.id = v.usuario_id
                LEFT JOIN detalle_ventas d ON d.venta_id = v.id
                LEFT JOIN productos p      ON p.id = d.producto_id
                WHERE 1 = 1 $filtro
                GROUP BY v.id, v.cliente_nombre, v.direccion, v.metodo_pago, v.total, v.fecha
                ORDER BY v.fecha DESC, v.id DESC";
        return $this->consultar($sql, $params)->fetchAll();
    }

    // ---------- Reportes del administrador ----------

    // Totales: número de ventas, ingresos, unidades vendidas y ticket promedio
    public function resumen($desde = null, $hasta = null) {
        list($filtro, $params) = $this->filtroFechas($desde, $hasta);
        $r = $this->consultar(
            "SELECT COUNT(*) AS num_ventas, COALESCE(SUM(v.total), 0) AS ingresos
             FROM ventas v WHERE 1 = 1 $filtro", $params
        )->fetch();
        $u = $this->consultar(
            "SELECT COALESCE(SUM(d.cantidad), 0) AS unidades
             FROM detalle_ventas d JOIN ventas v ON v.id = d.venta_id WHERE 1 = 1 $filtro", $params
        )->fetch();

        $numVentas = (int)$r['num_ventas'];
        $ingresos  = (float)$r['ingresos'];
        return [
            'num_ventas' => $numVentas,
            'ingresos'   => $ingresos,
            'unidades'   => (int)$u['unidades'],
            'ticket'     => $numVentas > 0 ? $ingresos / $numVentas : 0,
        ];
    }

    public function topProductos($desde = null, $hasta = null, $limite = 5) {
        list($filtro, $params) = $this->filtroFechas($desde, $hasta);
        $limite = (int)$limite;
        return $this->consultar(
            "SELECT p.nombre, SUM(d.cantidad) AS unidades, SUM(d.subtotal) AS ingresos
             FROM detalle_ventas d
             JOIN ventas v    ON v.id = d.venta_id
             JOIN productos p ON p.id = d.producto_id
             WHERE 1 = 1 $filtro
             GROUP BY p.id, p.nombre
             ORDER BY unidades DESC, ingresos DESC
             LIMIT $limite", $params
        )->fetchAll();
    }

    public function porMetodoPago($desde = null, $hasta = null) {
        list($filtro, $params) = $this->filtroFechas($desde, $hasta);
        return $this->consultar(
            "SELECT v.metodo_pago, COUNT(*) AS num_ventas, SUM(v.total) AS ingresos
             FROM ventas v WHERE 1 = 1 $filtro
             GROUP BY v.metodo_pago
             ORDER BY ingresos DESC", $params
        )->fetchAll();
    }

    // Ventas por día (los días más recientes primero)
    public function porDia($desde = null, $hasta = null, $limite = 14) {
        list($filtro, $params) = $this->filtroFechas($desde, $hasta);
        $limite = (int)$limite;
        return $this->consultar(
            "SELECT DATE(v.fecha) AS dia, COUNT(*) AS num_ventas, SUM(v.total) AS ingresos
             FROM ventas v WHERE 1 = 1 $filtro
             GROUP BY DATE(v.fecha)
             ORDER BY dia DESC
             LIMIT $limite", $params
        )->fetchAll();
    }

    public function topClientes($desde = null, $hasta = null, $limite = 5) {
        list($filtro, $params) = $this->filtroFechas($desde, $hasta);
        $limite = (int)$limite;
        return $this->consultar(
            "SELECT v.usuario_id, MAX(v.cliente_nombre) AS nombre, MAX(u.email) AS email,
                    COUNT(*) AS compras, SUM(v.total) AS total
             FROM ventas v
             LEFT JOIN usuarios u ON u.id = v.usuario_id
             WHERE 1 = 1 $filtro
             GROUP BY v.usuario_id
             ORDER BY total DESC
             LIMIT $limite", $params
        )->fetchAll();
    }

    // Compras de un cliente, cada una con su detalle
    public function getByUsuario(int $usuarioId) {
        $stmt = $this->db->prepare("SELECT * FROM ventas WHERE usuario_id = :u ORDER BY fecha DESC, id DESC");
        $stmt->execute([':u' => $usuarioId]);
        $ventas = $stmt->fetchAll();
        foreach ($ventas as &$v) {
            $v['detalles'] = $this->getDetalles((int)$v['id']);
        }
        unset($v);
        return $ventas;
    }

    // Una venta con su detalle. Si se pasa $usuarioId, solo la devuelve si le pertenece.
    public function getById(int $id, $usuarioId = null) {
        $sql = "SELECT * FROM ventas WHERE id = :id";
        $params = [':id' => $id];
        if ($usuarioId !== null) {
            $sql .= " AND usuario_id = :u";
            $params[':u'] = (int)$usuarioId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $venta = $stmt->fetch();
        if (!$venta) {
            return false;
        }
        $venta['detalles'] = $this->getDetalles($id);
        return $venta;
    }

    private function getDetalles(int $ventaId) {
        $stmt = $this->db->prepare(
            "SELECT d.cantidad, d.precio_unitario, d.subtotal, p.nombre
             FROM detalle_ventas d
             JOIN productos p ON p.id = d.producto_id
             WHERE d.venta_id = :id
             ORDER BY d.id"
        );
        $stmt->execute([':id' => $ventaId]);
        return $stmt->fetchAll();
    }
}
