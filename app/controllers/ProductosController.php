<?php
require_once APPROOT . '/app/models/Product.php';

class ProductosController {
    private $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    // Muestra todos los productos o lista general
    public function index() {
        $productos = $this->productModel->getAllProducts();
        $titulo = "Todas las Colecciones";
        $subtitulo = "Explora todo nuestro catálogo urbano y de tendencia.";

        require_once APPROOT . '/app/views/layouts/header.php';
        require_once APPROOT . '/app/views/productos/index.php';
        require_once APPROOT . '/app/views/layouts/footer.php';
    }

    // Filtra productos por categoría (/productos/categoria/{slug})
    public function categoria($slug = '') {
        if (empty($slug)) {
            header('Location: ' . URLROOT . '/productos');
            exit();
        }

        $productos = $this->productModel->getProductsByCategorySlug($slug);

        // Títulos dinámicos según el módulo seleccionado
        $titulos = [
            'maquillaje'        => ['titulo' => 'Colección de Maquillaje', 'sub' => 'Resalta tu belleza única con nuestra gama de productos seleccionados.'],
            'accesorios-hombre' => ['titulo' => 'Accesorios para Hombre', 'sub' => 'El toque distintivo que complementa tu presencia diaria.'],
            'ropa-hombre'       => ['titulo' => 'Ropa para Hombre', 'sub' => 'Estilo urbano y de vanguardia pensado para destacar.'],
            'ropa-mujer'        => ['titulo' => 'Ropa para Mujer', 'sub' => 'Prendas auténticas y versátiles para marcar tendencia.']
        ];

        $info = isset($titulos[$slug]) 
            ? $titulos[$slug] 
            : ['titulo' => 'Categoría: ' . ucfirst($slug), 'sub' => 'Productos seleccionados para tu estilo.'];

        $titulo = $info['titulo'];
        $subtitulo = $info['sub'];

        require_once APPROOT . '/app/views/layouts/header.php';
        require_once APPROOT . '/app/views/productos/index.php';
        require_once APPROOT . '/app/views/layouts/footer.php';
    }
}