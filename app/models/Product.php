<?php

class Product {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Obtener todos los productos
    public function getAllProducts() {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM productos p 
                JOIN categorias c ON p.categoria_id = c.id 
                ORDER BY p.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener productos filtrados por el slug/nombre de categoría
    public function getProductsByCategorySlug($slug) {
        // Mapeo de slugs de la URL a nombres exactos en la BD creados en la Fase 1
        $map = [
            'maquillaje'        => 'Maquillaje',
            'accesorios-hombre' => 'Accesorios',
            'ropa-hombre'       => 'Ropa Urbana',
            'ropa-mujer'        => 'Ropa Urbana'
        ];

        $nombreCategoria = isset($map[$slug]) ? $map[$slug] : $slug;

        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM productos p 
                JOIN categorias c ON p.categoria_id = c.id 
                WHERE LOWER(c.nombre) LIKE LOWER(:categoria)
                ORDER BY p.id DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':categoria', '%' . $nombreCategoria . '%', PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtener un único producto por ID para ver detalle
    public function getProductById($id) {
        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM productos p 
                JOIN categorias c ON p.categoria_id = c.id 
                WHERE p.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Crear un nuevo producto (Panel de Administrador)
    public function createProduct($data) {
        $sql = "INSERT INTO productos (categoria_id, nombre, descripcion, precio, stock, imagen, destacado) 
                VALUES (:categoria_id, :nombre, :descripcion, :precio, :stock, :imagen, :destacado)";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindValue(':categoria_id', $data['categoria_id'], PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $data['nombre'], PDO::PARAM_STR);
        $stmt->bindValue(':descripcion', $data['descripcion'], PDO::PARAM_STR);
        $stmt->bindValue(':precio', $data['precio']);
        $stmt->bindValue(':stock', $data['stock'], PDO::PARAM_INT);
        $stmt->bindValue(':imagen', $data['imagen'], PDO::PARAM_STR);
        $stmt->bindValue(':destacado', $data['destacado'], PDO::PARAM_INT);

        return $stmt->execute();
    }
}