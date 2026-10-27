<?php
class HomeController {
    public function index() {
        // Productos reales de la base de datos para la portada
        try {
            $destacados = (new Product())->getFeatured(8);
        } catch (Throwable $e) {
            error_log('[glow-fashion] No se pudieron cargar los productos de la portada: ' . $e->getMessage());
            $destacados = [];
        }
        require_once __DIR__ . '/../views/home/index.php';
    }

    public function sobreNosotros() {
        require_once __DIR__ . '/../views/home/sobre_nosotros.php';
    }

    public function noEncontrado() {
        http_response_code(404);
        require_once __DIR__ . '/../views/errors/404.php';
    }
}
