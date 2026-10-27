-- ============================================================
-- Glow Fashion - MIGRACIÓN 2: carrito por usuario + cuenta de administrador
-- Para una base YA EXISTENTE (después de migracion_actualizar_bd_existente.sql).
-- Se puede ejecutar más de una vez. Diseñada para MariaDB 10.4 (XAMPP).
--
-- USO: phpMyAdmin -> selecciona `glow_fashion_db` -> pestaña "Importar".
--
-- Qué hace:
--   1. Crea la tabla carrito_items (el carrito de cada cliente queda guardado).
--   2. Deja la cuenta de administrador lista:
--        admin@glowfashion.com / GlowAdmin#2026
--      (la aplicación también la crea/repara sola al iniciar sesión).
-- ============================================================

USE `glow_fashion_db`;
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `carrito_items` (
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

-- Cuenta de administrador (rol_id 1 = Admin)
INSERT INTO `usuarios` (`rol_id`, `nombre`, `apellido`, `email`, `password`)
SELECT 1, 'Admin', 'Glow', 'admin@glowfashion.com', '$2y$10$SIQ9zreffo.5GdOrVd.KCuJY8oFRbl2j8FccG4Xxfqs2WO.T6J81e'
FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `usuarios` WHERE `email` = 'admin@glowfashion.com');

UPDATE `usuarios` SET `rol_id` = 1, `password` = '$2y$10$SIQ9zreffo.5GdOrVd.KCuJY8oFRbl2j8FccG4Xxfqs2WO.T6J81e' WHERE `email` = 'admin@glowfashion.com';
