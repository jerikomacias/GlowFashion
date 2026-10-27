-- ============================================================
-- Glow Fashion - Base de datos completa (instalación nueva)
-- Motor: MariaDB 10.4 / MySQL 8 (XAMPP). Juego de caracteres: utf8mb4.
--
-- USO: importar este archivo en phpMyAdmin (pestaña "Importar") en un
--      servidor SIN la base glow_fashion_db. Si ya tienes la base creada
--      con datos, usa en su lugar: migracion_actualizar_bd_existente.sql
--
-- Usuarios de prueba (datos ficticios):
--   Administrador: admin@glowfashion.com / GlowAdmin#2026
--   Cliente:       camila@email.com      / Cliente123*
-- ============================================================

CREATE DATABASE IF NOT EXISTS `glow_fashion_db`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `glow_fashion_db`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ------------------------------------------------------------
-- roles
-- ------------------------------------------------------------
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Admin', 'Administrador total de la plataforma'),
(2, 'Cliente', 'Usuario comprador de la tienda');

-- ------------------------------------------------------------
-- categorias
-- ------------------------------------------------------------
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categorias` (`id`, `nombre`, `descripcion`) VALUES
(1, 'Ropa Urbana', 'Chaquetas, hoodies, pantalones cargo y prendas oversized'),
(2, 'Maquillaje', 'Bases, labiales y cosméticos de tendencia'),
(3, 'Accesorios', 'Gafas, cadenas, gorras y bolsos urbanos');

-- ------------------------------------------------------------
-- usuarios (el rol vive SOLO en rol_id)
-- ------------------------------------------------------------
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rol_id` int(11) NOT NULL DEFAULT 2,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `rol_id` (`rol_id`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuarios` (`id`, `rol_id`, `nombre`, `apellido`, `email`, `password`, `telefono`, `direccion`) VALUES
(1, 1, 'Admin',  'Glow', 'admin@glowfashion.com', '$2y$10$SIQ9zreffo.5GdOrVd.KCuJY8oFRbl2j8FccG4Xxfqs2WO.T6J81e', '3001234567', 'Oficina Central Glow'),
(2, 2, 'Camila', 'Ríos', 'camila@email.com',      '$2y$10$..13.kb.xlppDNyLtr1j4u27h2otmCEhAB5y9Lb3mDQq9SKz4IzZ6',   '3009876543', 'Calle 45 #12-34');

-- ------------------------------------------------------------
-- productos (activo = 0 significa "retirado del catálogo")
-- ------------------------------------------------------------
CREATE TABLE `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria_id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `imagen` varchar(255) DEFAULT 'logo.png',
  `genero` enum('unisex','hombre','mujer') NOT NULL DEFAULT 'unisex',
  `tipo` enum('prendas','zapatos','accesorios','maquillaje') NOT NULL DEFAULT 'prendas',
  `destacado` tinyint(1) DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `chk_productos_stock` CHECK (`stock` >= 0),
  CONSTRAINT `chk_productos_precio` CHECK (`precio` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `productos` (`categoria_id`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `genero`, `tipo`, `destacado`) VALUES
(1, 'Hoodie Oversized Cyber',        'Hoodie de algodón perchado estilo urbano con estampado reflectivo', 149900.00, 15, 'prenda-hombre.jpg',    'unisex', 'prendas',    1),
(1, 'Pantalón Cargo Black Street',   'Pantalón cargo con múltiples bolsillos y ajuste en tobillos',       129900.00, 20, 'prenda-mujer.png',      'unisex', 'prendas',    0),
(2, 'Labial Matte Neon Glow',        'Labial de larga duración con acabado mate de alta pigmentación',     35000.00, 50, 'maquillaje.png',        'mujer',  'maquillaje', 1),
(3, 'Gafas Futurglow Silver',        'Gafas de sol estilo futurista con protección UV400',                 55000.00, 30, 'accesorio-hombre.jpg',  'unisex', 'accesorios', 1),
(1, 'Chaqueta Denim Oversize',       'Chaqueta de denim de corte oversize',                               120000.00,  8, 'prenda-hombre.jpg',    'hombre', 'prendas',    0),
(1, 'Top Urbano Red Supreme',        'Top urbano de corte ajustado',                                       85000.00, 10, 'prenda-mujer.png',      'mujer',  'prendas',    0),
(3, 'Reloj Lujo Audemars Piguet',    'Reloj de estilo lujo para hombre',                                  350000.00,  5, 'accesorio-hombre.jpg',  'hombre', 'accesorios', 0),
(1, 'Zapatillas LV Skate Limitada',  'Zapatillas skate de edición limitada',                              220000.00,  6, 'hero-modelo.jpg',       'hombre', 'zapatos',    0),
(3, 'Bolso & Accesorios Glam',       'Bolso y accesorios de estilo glam para mujer',                      110000.00,  7, 'maquillaje.png',        'mujer',  'accesorios', 0);

-- ------------------------------------------------------------
-- ventas y detalle_ventas (las compras reales de la tienda)
-- ------------------------------------------------------------
CREATE TABLE `ventas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `cliente_nombre` varchar(150) NOT NULL DEFAULT '',
  `direccion` varchar(255) NOT NULL DEFAULT '',
  `metodo_pago` varchar(60) NOT NULL DEFAULT 'Efectivo',
  `total` decimal(10,2) NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `fk_ventas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `detalle_ventas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `venta_id` (`venta_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `detalle_ventas_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `chk_detalle_cantidad` CHECK (`cantidad` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- carrito_items: carrito guardado por usuario (sobrevive al cerrar sesión)
-- ------------------------------------------------------------
CREATE TABLE `carrito_items` (
  `usuario_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`usuario_id`,`producto_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `fk_carrito_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_carrito_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_carrito_cantidad` CHECK (`cantidad` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
