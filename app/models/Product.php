<?php

class Product {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Categorías para el formulario del administrador
    public function getCategorias() {
        return $this->db->query("SELECT id, nombre FROM categorias ORDER BY id")->fetchAll();
    }

    // Todos los productos, también los retirados (panel de administración)
    public function getAllProducts() {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre
                FROM productos p
                JOIN categorias c ON p.categoria_id = c.id
                ORDER BY p.activo DESC, p.id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    // Catálogo de la tienda filtrado por categoría, género y tipo.
    // Los productos "unisex" aparecen también al filtrar por hombre o mujer.
    public function getProductsByCategory($slug, $genero = 'todos', $tipo = 'todos') {
        $map = [
            'maquillaje' => 'Maquillaje',
            'ropa'       => 'Ropa Urbana',
            'accesorios' => 'Accesorios',
        ];

        $sql = "SELECT p.*, c.nombre AS categoria_nombre
                FROM productos p
                JOIN categorias c ON p.categoria_id = c.id
                WHERE p.activo = 1";
        $params = [];

        if (isset($map[$slug])) {
            $sql .= " AND c.nombre = :categoria";
            $params[':categoria'] = $map[$slug];
        }
        if ($genero !== 'todos') {
            $sql .= " AND p.genero IN (:genero, 'unisex')";
            $params[':genero'] = $genero;
        }
        if ($tipo !== 'todos') {
            $sql .= " AND p.tipo = :tipo";
            $params[':tipo'] = $tipo;
        }
        $sql .= " ORDER BY p.destacado DESC, p.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Productos para la portada: primero los destacados, luego los más recientes
    public function getFeatured($limite = 8) {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre
                FROM productos p
                JOIN categorias c ON p.categoria_id = c.id
                WHERE p.activo = 1
                ORDER BY p.destacado DESC, p.id DESC
                LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Productos activos con poco stock (alerta en el panel admin)
    public function getStockBajo($umbral = 5) {
        $stmt = $this->db->prepare(
            "SELECT id, nombre, stock FROM productos WHERE activo = 1 AND stock <= :umbral ORDER BY stock ASC, nombre ASC"
        );
        $stmt->bindValue(':umbral', (int)$umbral, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Un producto por ID (activo o no)
    public function getProductById($id) {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre
                FROM productos p
                JOIN categorias c ON p.categoria_id = c.id
                WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Varios productos activos a la vez (para armar el carrito con precios reales)
    public function getProductsByIds(array $ids) {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (empty($ids)) {
            return [];
        }
        $marcadores = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare("SELECT id, nombre, precio, stock, imagen FROM productos WHERE activo = 1 AND id IN ($marcadores)");
        $stmt->execute($ids);
        return $stmt->fetchAll();
    }

    // Crear un producto (panel de administración)
    public function createProduct($data) {
        $sql = "INSERT INTO productos (categoria_id, nombre, descripcion, precio, stock, imagen, genero, tipo, destacado)
                VALUES (:categoria_id, :nombre, :descripcion, :precio, :stock, :imagen, :genero, :tipo, :destacado)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':categoria_id' => (int)$data['categoria_id'],
            ':nombre'       => $data['nombre'],
            ':descripcion'  => $data['descripcion'],
            ':precio'       => number_format((float)$data['precio'], 2, '.', ''),
            ':stock'        => (int)$data['stock'],
            ':imagen'       => $data['imagen'],
            ':genero'       => $data['genero'],
            ':tipo'         => $data['tipo'],
            ':destacado'    => !empty($data['destacado']) ? 1 : 0,
        ]);
    }

    // Editar un producto (panel de administración)
    public function updateProduct($id, $data) {
        $sql = "UPDATE productos SET
                    categoria_id = :categoria_id,
                    nombre       = :nombre,
                    descripcion  = :descripcion,
                    precio       = :precio,
                    stock        = :stock,
                    imagen       = :imagen,
                    genero       = :genero,
                    tipo         = :tipo,
                    destacado    = :destacado,
                    activo       = :activo
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':categoria_id' => (int)$data['categoria_id'],
            ':nombre'       => $data['nombre'],
            ':descripcion'  => $data['descripcion'],
            ':precio'       => number_format((float)$data['precio'], 2, '.', ''),
            ':stock'        => (int)$data['stock'],
            ':imagen'       => $data['imagen'],
            ':genero'       => $data['genero'],
            ':tipo'         => $data['tipo'],
            ':destacado'    => !empty($data['destacado']) ? 1 : 0,
            ':activo'       => !empty($data['activo']) ? 1 : 0,
            ':id'           => (int)$id,
        ]);
    }

    // ¿Algún otro producto usa esta imagen? (para no borrar un archivo compartido)
    public function imagenEnUso($imagen, $excluirId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM productos WHERE imagen = :imagen AND id <> :id");
        $stmt->execute([':imagen' => $imagen, ':id' => (int)$excluirId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    // "Eliminar" = borrado lógico. Así el historial de ventas nunca se pierde.
    public function deactivate($id) {
        $stmt = $this->db->prepare("UPDATE productos SET activo = 0 WHERE id = :id");
        $stmt->execute([':id' => (int)$id]);
        return $stmt->rowCount() > 0;
    }
}
