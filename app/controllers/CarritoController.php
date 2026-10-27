<?php
// El carrito vive en la sesión, pero SOLO guarda id y cantidad.
// Nombre, precio y stock se consultan siempre en la base de datos.
class CarritoController {
    private $productos;

    public function __construct() {
        $this->productos = new Product();
    }

    // Carrito de invitado: vive en la sesión, normalizado a [producto_id => cantidad]
    private static function leerSesion(): array {
        $limpio = [];
        foreach (($_SESSION['carrito'] ?? []) as $item) {
            if (!is_array($item)) {
                continue;
            }
            $id       = (int)($item['id'] ?? 0);
            $cantidad = (int)($item['cantidad'] ?? 0);
            if ($id > 0 && $cantidad > 0) {
                $limpio[$id] = ($limpio[$id] ?? 0) + $cantidad;
            }
        }
        return $limpio;
    }

    // Carrito actual: [producto_id => cantidad].
    // Con sesión iniciada se lee de la base de datos; como invitado, de la sesión.
    public static function obtenerIds(): array {
        if (esta_logueado()) {
            return (new Carrito())->obtener((int)$_SESSION['usuario_id']);
        }
        return self::leerSesion();
    }

    private static function guardar(array $cantidades): void {
        if (esta_logueado()) {
            (new Carrito())->guardar((int)$_SESSION['usuario_id'], $cantidades);
            return;
        }
        $_SESSION['carrito'] = [];
        foreach ($cantidades as $id => $cantidad) {
            $_SESSION['carrito'][$id] = ['id' => (int)$id, 'cantidad' => (int)$cantidad];
        }
    }

    // Unidades en el carrito (contador del menú). Nunca rompe la página.
    public static function contarItems(): int {
        try {
            if (esta_logueado()) {
                return (new Carrito())->contar((int)$_SESSION['usuario_id']);
            }
            return array_sum(self::leerSesion());
        } catch (Throwable $e) {
            error_log('[glow-fashion] No se pudo contar el carrito: ' . $e->getMessage());
            return 0;
        }
    }

    // Al iniciar sesión: lo que el invitado puso en el carrito se une al carrito
    // que el usuario ya tenía guardado (sin superar el stock disponible).
    public static function fusionarAlIniciarSesion(int $usuarioId): void {
        $deSesion = self::leerSesion();
        if (empty($deSesion)) {
            return;
        }

        $modelo = new Carrito();
        $total  = $modelo->obtener($usuarioId);
        foreach ($deSesion as $id => $cantidad) {
            $total[$id] = ($total[$id] ?? 0) + $cantidad;
        }

        $final = [];
        foreach ((new Product())->getProductsByIds(array_keys($total)) as $p) {
            $id       = (int)$p['id'];
            $cantidad = min($total[$id], (int)$p['stock']);
            if ($cantidad > 0) {
                $final[$id] = $cantidad;
            }
        }

        $modelo->guardar($usuarioId, $final);
        unset($_SESSION['carrito']);
    }

    // Muestra el carrito
    public function index() {
        $cantidades = self::obtenerIds();
        $items      = [];
        $totalCent  = 0;

        if (!empty($cantidades)) {
            $filas = $this->productos->getProductsByIds(array_keys($cantidades));
            $vigentes = [];
            foreach ($filas as $p) {
                $id       = (int)$p['id'];
                $cantidad = $cantidades[$id];
                $precio   = (float)$p['precio'];
                $subCent  = (int)round($precio * 100) * $cantidad;
                $totalCent += $subCent;
                $vigentes[$id] = $cantidad;

                $items[] = [
                    'id'       => $id,
                    'nombre'   => $p['nombre'],
                    'precio'   => $precio,
                    'cantidad' => $cantidad,
                    'stock'    => (int)$p['stock'],
                    'subtotal' => $subCent / 100,
                ];
            }
            // Si algún producto fue retirado del catálogo, se quita del carrito
            if (count($vigentes) !== count($cantidades)) {
                flash('error', 'Algunos productos ya no están disponibles y se quitaron de tu carrito.');
                self::guardar($vigentes);
            }
        }

        $totalPagar = $totalCent / 100;
        require_once __DIR__ . '/../views/carrito/index.php';
    }

    public function agregar() {
        requerir_post();
        csrf_check();

        $id       = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $producto = $id ? $this->productos->getProductById($id) : false;

        if (!$producto || !(int)$producto['activo']) {
            flash('error', 'El producto no existe o ya no está disponible.');
            redirigir('carrito');
        }

        $cantidades = self::obtenerIds();
        $actual     = $cantidades[$id] ?? 0;
        $stock      = (int)$producto['stock'];

        if ($stock <= 0 || $actual + 1 > $stock) {
            flash('error', 'No hay más unidades disponibles de "' . $producto['nombre'] . '".');
        } else {
            $cantidades[$id] = $actual + 1;
            flash('exito', 'Producto agregado al carrito.');
        }

        self::guardar($cantidades);
        redirigir('carrito');
    }

    public function actualizar() {
        requerir_post();
        csrf_check();

        $id       = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $cantidad = filter_var($_POST['cantidad'] ?? null, FILTER_VALIDATE_INT);
        $cantidades = self::obtenerIds();

        if ($id && isset($cantidades[$id]) && $cantidad !== false && $cantidad !== null) {
            if ($cantidad <= 0) {
                unset($cantidades[$id]);
            } else {
                $producto = $this->productos->getProductById($id);
                $stock    = $producto ? (int)$producto['stock'] : 0;
                if ($cantidad > $stock) {
                    $cantidad = $stock;
                    flash('error', 'Solo hay ' . $stock . ' unidades disponibles de este producto.');
                }
                if ($cantidad <= 0) {
                    unset($cantidades[$id]);
                } else {
                    $cantidades[$id] = $cantidad;
                }
            }
            self::guardar($cantidades);
        }

        redirigir('carrito');
    }

    public function vaciar() {
        requerir_post();
        csrf_check();
        self::guardar([]);
        redirigir('carrito');
    }
}
