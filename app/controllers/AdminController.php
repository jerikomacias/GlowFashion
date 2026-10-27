<?php
class AdminController {
    private $productos;
    private $ventas;

    // Todo el panel exige haber iniciado sesión como administrador (rol_id = 1)
    public function __construct() {
        requerir_admin();
        $this->productos = new Product();
        $this->ventas    = new Venta();
    }

    // Imágenes disponibles en public/img y public/img/productos (para elegir en el formulario)
    private function listarImagenes(): array {
        $imagenes = [];
        foreach (['', 'productos/'] as $subcarpeta) {
            $ruta = APPROOT . '/public/img/' . $subcarpeta;
            if (!is_dir($ruta)) {
                continue;
            }
            foreach (scandir($ruta) as $archivo) {
                if (preg_match('/\.(jpe?g|png|webp|gif)$/i', $archivo)) {
                    $imagenes[] = $subcarpeta . $archivo;
                }
            }
        }
        sort($imagenes);
        return $imagenes;
    }

    // Sube la imagen del producto (opcional). Devuelve ['ruta' => ..., 'error' => ...].
    // Solo acepta JPG, PNG o WEBP de hasta 3 MB; la extensión se decide por el contenido real.
    private function subirImagen(): array {
        $f = $_FILES['imagen_archivo'] ?? null;
        if (!$f || $f['error'] === UPLOAD_ERR_NO_FILE) {
            return ['ruta' => null, 'error' => null];
        }
        if ($f['error'] === UPLOAD_ERR_INI_SIZE || $f['error'] === UPLOAD_ERR_FORM_SIZE) {
            return ['ruta' => null, 'error' => 'La imagen es demasiado pesada (máximo 3 MB).'];
        }
        if ($f['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($f['tmp_name'])) {
            return ['ruta' => null, 'error' => 'No se pudo subir la imagen. Inténtalo de nuevo.'];
        }
        if ($f['size'] > 3 * 1024 * 1024) {
            return ['ruta' => null, 'error' => 'La imagen es demasiado pesada (máximo 3 MB).'];
        }

        $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($f['tmp_name']);
        if (!isset($permitidos[$mime]) || @getimagesize($f['tmp_name']) === false) {
            return ['ruta' => null, 'error' => 'El archivo debe ser una imagen JPG, PNG o WEBP.'];
        }

        $carpeta = APPROOT . '/public/img/productos';
        if (!is_dir($carpeta) && !mkdir($carpeta, 0755, true)) {
            return ['ruta' => null, 'error' => 'No se pudo crear la carpeta de imágenes.'];
        }

        $nombre = 'prod_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $permitidos[$mime];
        if (!move_uploaded_file($f['tmp_name'], $carpeta . '/' . $nombre)) {
            return ['ruta' => null, 'error' => 'No se pudo guardar la imagen en el servidor.'];
        }
        return ['ruta' => 'productos/' . $nombre, 'error' => null];
    }

    public function productos() {
        $productos  = $this->productos->getAllProducts();
        $categorias = $this->productos->getCategorias();
        $imagenes   = $this->listarImagenes();
        require_once __DIR__ . '/../views/admin/productos.php';
    }

    // Convierte el precio escrito por el administrador a número.
    // Acepta formato colombiano: 70.000 · 70000 · $70.000 COP · 1.250.000 · 70.000,50
    // (el punto separa miles y la coma separa decimales). Devuelve false si no es válido.
    private function parsearPrecio($texto) {
        $t = preg_replace('/[^\d.,]/u', '', trim((string)$texto)); // quita $, COP y espacios
        if ($t === '' || $t === null) {
            return false;
        }
        if (preg_match('/^\d{1,3}(,\d{3})+$/', $t)) {
            $t = str_replace(',', '', $t);                              // 70,000 -> 70000
        } elseif (strpos($t, ',') !== false) {
            $t = str_replace(',', '.', str_replace('.', '', $t));       // 1.250,50 -> 1250.50
        } elseif (preg_match('/^\d{1,3}(\.\d{3})+$/', $t)) {
            $t = str_replace('.', '', $t);                              // 70.000 -> 70000
        }
        return is_numeric($t) ? (float)$t : false;
    }

    // Lee y valida el formulario de producto (sirve para crear y para editar).
    // $imagenActual: imagen que ya tiene el producto al editar ('' al crear).
    // Devuelve [datos, errores, hayArchivo].
    private function leerFormularioProducto(string $imagenActual = ''): array {
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio      = $this->parsearPrecio($_POST['precio'] ?? '');
        $stock       = filter_var($_POST['stock'] ?? null, FILTER_VALIDATE_INT);
        $hayArchivo  = isset($_FILES['imagen_archivo']) && $_FILES['imagen_archivo']['error'] !== UPLOAD_ERR_NO_FILE;

        $datos = [
            'nombre'       => trim($_POST['nombre'] ?? ''),
            'descripcion'  => $descripcion !== '' ? $descripcion : null,
            'categoria_id' => filter_var($_POST['categoria_id'] ?? null, FILTER_VALIDATE_INT),
            'genero'       => $_POST['genero'] ?? '',
            'tipo'         => $_POST['tipo'] ?? '',
            'precio'       => $precio,
            'stock'        => $stock,
            'imagen'       => $_POST['imagen'] ?? ($imagenActual !== '' ? $imagenActual : 'logo.png'),
            'destacado'    => !empty($_POST['destacado']),
            'activo'       => !empty($_POST['activo']),
        ];

        $idsCategorias = array_map('intval', array_column($this->productos->getCategorias(), 'id'));

        $errores = [];
        if ($datos['nombre'] === '' || mb_strlen($datos['nombre']) > 150) {
            $errores[] = 'El nombre es obligatorio (máximo 150 caracteres).';
        }
        if (!$datos['categoria_id'] || !in_array($datos['categoria_id'], $idsCategorias, true)) {
            $errores[] = 'Selecciona una categoría válida.';
        }
        if (!in_array($datos['genero'], ['unisex', 'hombre', 'mujer'], true)) {
            $errores[] = 'Selecciona un género válido.';
        }
        if (!in_array($datos['tipo'], ['prendas', 'zapatos', 'accesorios', 'maquillaje'], true)) {
            $errores[] = 'Selecciona un tipo válido.';
        }
        if ($precio === false || $precio === null || $precio <= 0 || $precio > 99999999.99) {
            $errores[] = 'El precio debe ser un número mayor que 0.';
        }
        if ($stock === false || $stock === null || $stock < 0 || $stock > 1000000) {
            $errores[] = 'El stock debe ser un número entero igual o mayor que 0.';
        }
        $imagenValida = in_array($datos['imagen'], $this->listarImagenes(), true)
                        || ($imagenActual !== '' && $datos['imagen'] === $imagenActual);
        if (!$hayArchivo && !$imagenValida) {
            $errores[] = 'Selecciona una imagen válida.';
        }

        return [$datos, $errores, $hayArchivo];
    }

    public function guardarProducto() {
        requerir_post();
        csrf_check();

        list($datos, $errores, $hayArchivo) = $this->leerFormularioProducto();
        if (!empty($errores)) {
            flash('error', implode(' ', $errores));
            redirigir('admin/productos');
        }

        // Si se subió un archivo, tiene prioridad sobre la imagen elegida de la lista
        $rutaSubida = null;
        if ($hayArchivo) {
            $subida = $this->subirImagen();
            if ($subida['error'] !== null) {
                flash('error', $subida['error']);
                redirigir('admin/productos');
            }
            $rutaSubida      = $subida['ruta'];
            $datos['imagen'] = $rutaSubida;
        }

        try {
            $this->productos->createProduct($datos);
            flash('exito', 'Producto guardado correctamente.');
        } catch (PDOException $e) {
            if ($rutaSubida !== null) {
                @unlink(APPROOT . '/public/img/' . $rutaSubida); // no dejar la imagen huérfana
            }
            error_log('[glow-fashion] Error al guardar producto: ' . $e->getMessage());
            $detalle = APP_DEBUG ? ' (' . $e->getMessage() . ')' : '';
            flash('error', 'No se pudo guardar el producto.' . $detalle);
        }

        redirigir('admin/productos');
    }

    // Formulario para editar un producto (todos sus datos)
    public function editarProducto() {
        $id       = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $producto = $id ? $this->productos->getProductById($id) : false;
        if (!$producto) {
            flash('error', 'El producto que quieres editar no existe.');
            redirigir('admin/productos');
        }

        $categorias = $this->productos->getCategorias();
        $imagenes   = $this->listarImagenes();
        require_once __DIR__ . '/../views/admin/editar_producto.php';
    }

    // Guarda los cambios de un producto
    public function actualizarProducto() {
        requerir_post();
        csrf_check();

        $id       = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        $producto = $id ? $this->productos->getProductById($id) : false;
        if (!$producto) {
            flash('error', 'El producto que quieres editar no existe.');
            redirigir('admin/productos');
        }

        $imagenAnterior = (string)$producto['imagen'];
        list($datos, $errores, $hayArchivo) = $this->leerFormularioProducto($imagenAnterior);
        if (!empty($errores)) {
            flash('error', implode(' ', $errores));
            redirigir('admin/editar-producto&id=' . $id);
        }

        $rutaSubida = null;
        if ($hayArchivo) {
            $subida = $this->subirImagen();
            if ($subida['error'] !== null) {
                flash('error', $subida['error']);
                redirigir('admin/editar-producto&id=' . $id);
            }
            $rutaSubida      = $subida['ruta'];
            $datos['imagen'] = $rutaSubida;
        }

        try {
            $this->productos->updateProduct($id, $datos);

            // Si la imagen cambió y la anterior era una subida que nadie más usa, se borra el archivo
            if ($datos['imagen'] !== $imagenAnterior
                && strpos($imagenAnterior, 'productos/') === 0
                && !$this->productos->imagenEnUso($imagenAnterior, $id)) {
                @unlink(APPROOT . '/public/img/' . $imagenAnterior);
            }
            flash('exito', 'Producto actualizado correctamente.');
        } catch (PDOException $e) {
            if ($rutaSubida !== null) {
                @unlink(APPROOT . '/public/img/' . $rutaSubida);
            }
            error_log('[glow-fashion] Error al actualizar producto: ' . $e->getMessage());
            $detalle = APP_DEBUG ? ' (' . $e->getMessage() . ')' : '';
            flash('error', 'No se pudo actualizar el producto.' . $detalle);
            redirigir('admin/editar-producto&id=' . $id);
        }

        redirigir('admin/productos');
    }

    // Borrado lógico (por POST con token CSRF): el producto deja de mostrarse,
    // pero el historial de ventas se conserva intacto.
    public function eliminarProducto() {
        requerir_post();
        csrf_check();

        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id) {
            try {
                if ($this->productos->deactivate($id)) {
                    flash('exito', 'Producto retirado del catálogo.');
                } else {
                    flash('error', 'El producto no existe.');
                }
            } catch (PDOException $e) {
                error_log('[glow-fashion] Error al retirar producto: ' . $e->getMessage());
                flash('error', 'No se pudo retirar el producto.');
            }
        }

        redirigir('admin/productos');
    }

    // Lee y valida el rango de fechas (YYYY-MM-DD) de la URL. Una fecha inválida se ignora.
    private function leerFechas(): array {
        $fechas = [];
        foreach (['desde', 'hasta'] as $campo) {
            $valor = trim($_GET[$campo] ?? '');
            $fecha = DateTime::createFromFormat('Y-m-d', $valor);
            $fechas[$campo] = ($fecha && $fecha->format('Y-m-d') === $valor) ? $valor : null;
        }
        // Si vienen al revés, se intercambian
        if ($fechas['desde'] && $fechas['hasta'] && $fechas['desde'] > $fechas['hasta']) {
            list($fechas['desde'], $fechas['hasta']) = [$fechas['hasta'], $fechas['desde']];
        }
        return $fechas;
    }

    // Panel de ventas y reportes (se puede filtrar por rango de fechas)
    public function ventas() {
        $f     = $this->leerFechas();
        $desde = $f['desde'];
        $hasta = $f['hasta'];

        $resumen       = $this->ventas->resumen($desde, $hasta);
        $topProductos  = $this->ventas->topProductos($desde, $hasta, 5);
        $porMetodo     = $this->ventas->porMetodoPago($desde, $hasta);
        $porDia        = $this->ventas->porDia($desde, $hasta, 14);
        $topClientes   = $this->ventas->topClientes($desde, $hasta, 5);
        $stockBajo     = $this->productos->getStockBajo(5);
        $ventas        = $this->ventas->getAll($desde, $hasta);

        require_once __DIR__ . '/../views/admin/ventas.php';
    }

    // Descarga las ventas (con el mismo filtro de fechas) en CSV para abrir en Excel
    public function exportarVentas() {
        $f      = $this->leerFechas();
        $ventas = $this->ventas->getAll($f['desde'], $f['hasta']);

        // Evita que Excel interprete una celda como fórmula
        $limpio = function ($v) {
            $v = (string)$v;
            return ($v !== '' && strpos('=+-@', $v[0]) !== false) ? "'" . $v : $v;
        };

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="ventas_glow_fashion_' . date('Ymd_His') . '.csv"');

        $salida = fopen('php://output', 'w');
        fwrite($salida, "\xEF\xBB\xBF"); // BOM UTF-8 para que Excel muestre bien las tildes
        fputcsv($salida, ['ID', 'Fecha', 'Cliente', 'Correo', 'Direccion', 'Metodo de pago', 'Productos', 'Total'], ';');
        foreach ($ventas as $v) {
            fputcsv($salida, [
                $v['id'],
                $v['fecha'],
                $limpio($v['cliente_nombre']),
                $limpio($v['usuario_email']),
                $limpio($v['direccion']),
                $limpio($v['metodo_pago']),
                $limpio($v['detalle']),
                number_format((float)$v['total'], 2, ',', ''),
            ], ';');
        }
        fclose($salida);
        exit();
    }
}
