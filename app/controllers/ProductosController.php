<?php
class ProductosController {
    private $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    // Catálogo por categoría: categoria/maquillaje | categoria/ropa | categoria/accesorios
    public function categoria($slug) {
        $titulos = [
            'maquillaje' => 'Maquillaje & Belleza',
            'ropa'       => 'Colección de Ropa & Calzado',
            'accesorios' => 'Colección de Accesorios',
        ];
        if (!isset($titulos[$slug])) {
            (new HomeController())->noEncontrado();
            return;
        }

        // Filtros opcionales de la URL, validados contra listas permitidas
        $generosValidos = ['hombre', 'mujer', 'unisex'];
        $tiposValidos   = ['prendas', 'zapatos', 'accesorios', 'maquillaje'];

        $generoFiltro = $_GET['genero'] ?? 'todos';
        $tipoFiltro   = $_GET['tipo'] ?? 'todos';
        if (!in_array($generoFiltro, $generosValidos, true)) {
            $generoFiltro = 'todos';
        }
        if (!in_array($tipoFiltro, $tiposValidos, true)) {
            $tipoFiltro = 'todos';
        }

        $catFiltro        = $slug;
        $tituloCatalogo   = $titulos[$slug];
        $productosMostrar = $this->productModel->getProductsByCategory($slug, $generoFiltro, $tipoFiltro);

        require_once __DIR__ . '/../views/productos/index.php';
    }
}
