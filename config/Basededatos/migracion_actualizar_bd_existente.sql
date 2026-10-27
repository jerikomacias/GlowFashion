-- ============================================================
-- Glow Fashion - MIGRACIÓN para una base de datos YA EXISTENTE
-- Diseñada para MariaDB 10.4 (XAMPP). Se puede ejecutar más de una vez.
--
-- USO: en phpMyAdmin selecciona la base `glow_fashion_db`, pestaña
--      "Importar" (o "SQL") y ejecuta este archivo. Haz una copia de
--      seguridad antes (pestaña "Exportar").
--
-- Qué hace:
--   1. ventas: agrega cliente_nombre, direccion y metodo_pago (el código los usa).
--   2. productos: agrega genero, tipo y activo; usa imágenes que sí existen.
--   3. usuarios: deja el rol SOLO en rol_id (elimina es_admin y rol).
--   4. Llaves foráneas: borrar un producto o usuario ya NO borra ventas.
--   5. Restricciones: stock y precio no pueden ser negativos.
--   6. Datos de prueba: correos ficticios y contraseñas de prueba nuevas.
--
-- Contraseñas de prueba después de migrar:
--   camila@email.com / Cliente123*   (el administrador se deja en migracion_02_carrito_y_admin.sql)
-- ============================================================

USE `glow_fashion_db`;
SET NAMES utf8mb4;

-- ------------------------------------------------------------
-- 1. ventas
-- ------------------------------------------------------------
ALTER TABLE `ventas`
  ADD COLUMN IF NOT EXISTS `cliente_nombre` varchar(150) NOT NULL DEFAULT '' AFTER `usuario_id`,
  ADD COLUMN IF NOT EXISTS `direccion` varchar(255) NOT NULL DEFAULT '' AFTER `cliente_nombre`,
  ADD COLUMN IF NOT EXISTS `metodo_pago` varchar(60) NOT NULL DEFAULT 'Efectivo' AFTER `direccion`;

-- ------------------------------------------------------------
-- 2. productos
-- ------------------------------------------------------------
ALTER TABLE `productos`
  ADD COLUMN IF NOT EXISTS `genero` enum('unisex','hombre','mujer') NOT NULL DEFAULT 'unisex' AFTER `imagen`,
  ADD COLUMN IF NOT EXISTS `tipo` enum('prendas','zapatos','accesorios','maquillaje') NOT NULL DEFAULT 'prendas' AFTER `genero`,
  ADD COLUMN IF NOT EXISTS `activo` tinyint(1) NOT NULL DEFAULT 1 AFTER `destacado`,
  MODIFY `imagen` varchar(255) DEFAULT 'logo.png';

-- Clasificación e imágenes de los 4 productos originales
UPDATE `productos` SET `genero`='unisex', `tipo`='prendas',    `imagen`='prenda-hombre.jpg'   WHERE `nombre`='Hoodie Oversized Cyber';
UPDATE `productos` SET `genero`='unisex', `tipo`='prendas',    `imagen`='prenda-mujer.png'    WHERE `nombre`='Pantalón Cargo Black Street';
UPDATE `productos` SET `genero`='mujer',  `tipo`='maquillaje', `imagen`='maquillaje.png'      WHERE `nombre`='Labial Matte Neon Glow';
UPDATE `productos` SET `genero`='unisex', `tipo`='accesorios', `imagen`='accesorio-hombre.jpg' WHERE `nombre`='Gafas Futurglow Silver';

-- Productos que antes solo existían "de mentira" en la sesión del navegador
INSERT INTO `productos` (`categoria_id`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `genero`, `tipo`, `destacado`)
SELECT 1, 'Chaqueta Denim Oversize', 'Chaqueta de denim de corte oversize', 120000.00, 8, 'prenda-hombre.jpg', 'hombre', 'prendas', 0
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `productos` WHERE `nombre`='Chaqueta Denim Oversize');
INSERT INTO `productos` (`categoria_id`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `genero`, `tipo`, `destacado`)
SELECT 1, 'Top Urbano Red Supreme', 'Top urbano de corte ajustado', 85000.00, 10, 'prenda-mujer.png', 'mujer', 'prendas', 0
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `productos` WHERE `nombre`='Top Urbano Red Supreme');
INSERT INTO `productos` (`categoria_id`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `genero`, `tipo`, `destacado`)
SELECT 3, 'Reloj Lujo Audemars Piguet', 'Reloj de estilo lujo para hombre', 350000.00, 5, 'accesorio-hombre.jpg', 'hombre', 'accesorios', 0
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `productos` WHERE `nombre`='Reloj Lujo Audemars Piguet');
INSERT INTO `productos` (`categoria_id`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `genero`, `tipo`, `destacado`)
SELECT 1, 'Zapatillas LV Skate Limitada', 'Zapatillas skate de edición limitada', 220000.00, 6, 'hero-modelo.jpg', 'hombre', 'zapatos', 0
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `productos` WHERE `nombre`='Zapatillas LV Skate Limitada');
INSERT INTO `productos` (`categoria_id`, `nombre`, `descripcion`, `precio`, `stock`, `imagen`, `genero`, `tipo`, `destacado`)
SELECT 3, 'Bolso & Accesorios Glam', 'Bolso y accesorios de estilo glam para mujer', 110000.00, 7, 'maquillaje.png', 'mujer', 'accesorios', 0
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `productos` WHERE `nombre`='Bolso & Accesorios Glam');

-- ------------------------------------------------------------
-- 3. usuarios: el rol vive solo en rol_id (1 = Admin, 2 = Cliente)
-- ------------------------------------------------------------
ALTER TABLE `usuarios`
  MODIFY `apellido` varchar(100) NOT NULL DEFAULT '',
  DROP COLUMN IF EXISTS `es_admin`,
  DROP COLUMN IF EXISTS `rol`;

-- ------------------------------------------------------------
-- 4. Llaves foráneas seguras (RESTRICT: no se borra historial por accidente)
-- ------------------------------------------------------------
ALTER TABLE `ventas`
  DROP FOREIGN KEY IF EXISTS `ventas_ibfk_1`,
  DROP FOREIGN KEY IF EXISTS `fk_ventas_usuario`;
ALTER TABLE `ventas`
  ADD CONSTRAINT `fk_ventas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `detalle_ventas`
  DROP FOREIGN KEY IF EXISTS `detalle_ventas_ibfk_2`,
  DROP FOREIGN KEY IF EXISTS `fk_detalle_producto`;
ALTER TABLE `detalle_ventas`
  ADD CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- ------------------------------------------------------------
-- 5. Restricciones de datos
-- ------------------------------------------------------------
UPDATE `productos` SET `stock` = 0 WHERE `stock` < 0;

ALTER TABLE `productos` DROP CONSTRAINT IF EXISTS `chk_productos_stock`;
ALTER TABLE `productos` ADD CONSTRAINT `chk_productos_stock` CHECK (`stock` >= 0);
ALTER TABLE `productos` DROP CONSTRAINT IF EXISTS `chk_productos_precio`;
ALTER TABLE `productos` ADD CONSTRAINT `chk_productos_precio` CHECK (`precio` >= 0);
ALTER TABLE `detalle_ventas` DROP CONSTRAINT IF EXISTS `chk_detalle_cantidad`;
ALTER TABLE `detalle_ventas` ADD CONSTRAINT `chk_detalle_cantidad` CHECK (`cantidad` > 0);

-- ------------------------------------------------------------
-- 6. Datos de prueba ficticios
--    Los usuarios de prueba 3 en adelante pasan a correos de ejemplo.
--    Admin (id 1) y Camila (id 2) reciben contraseñas de prueba distintas.
-- ------------------------------------------------------------
UPDATE `usuarios` SET `email` = CONCAT('usuario', `id`, '@example.com') WHERE `id` > 2;
UPDATE `usuarios` SET `password` = '$2y$10$BvdR7LnIEIgHBe25BC.IDex7Aq0dvfDE0jqGOyUfUzDZ51gugizGa' WHERE `id` = 1 AND `email` = 'admin@glowfashion.com';
UPDATE `usuarios` SET `password` = '$2y$10$..13.kb.xlppDNyLtr1j4u27h2otmCEhAB5y9Lb3mDQq9SKz4IzZ6'   WHERE `id` = 2 AND `email` = 'camila@email.com';

-- ------------------------------------------------------------
-- Opcional: las tablas `pedidos` y `detalle_pedidos` ya no se usan
-- (la tienda registra todo en `ventas` y `detalle_ventas`).
-- Si NO contienen datos que quieras conservar, puedes eliminarlas
-- quitando los dos guiones del inicio de las siguientes líneas:
-- ------------------------------------------------------------
-- DROP TABLE IF EXISTS `detalle_pedidos`;
-- DROP TABLE IF EXISTS `pedidos`;
