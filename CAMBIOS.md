# Registro de correcciones (revisión del 15-10-2026)

## A. Base de datos y flujo de compra
1. El checkout ya registra la compra en la BD (antes solo guardaba en `$_SESSION['ventas_demo']`). Formulario → `procesar-compra`.
2. `ventas` ahora tiene `cliente_nombre`, `direccion`, `metodo_pago`; el INSERT incluye `usuario_id` y `subtotal`.
3. Nombres de campos del formulario y del controlador unificados (`cliente`, `direccion`, `forma_pago`).
4. El catálogo se lee de la tabla `productos`. Precio y nombre se toman de la BD en el servidor; el carrito guarda solo id y cantidad.
5. Stock validado (`stock >= cantidad`, con bloqueo `FOR UPDATE`) y `CHECK (stock >= 0)`.
6. Un solo nombre de BD (`glow_fashion_db`) y una sola clase `Database` (PDO) que usa `config/config.php`.
7. Se eliminaron los `catch` vacíos: los errores se registran en el log y se informa al usuario.
8. Tras comprar, el cliente va a `compra/confirmacion`, no al panel admin.
9. "Retirar" producto = borrado lógico (`activo = 0`), por POST + CSRF. Llaves foráneas con `RESTRICT`.
10. Guardar producto: usa `categoria_id`, `genero`, `tipo`, imagen y descripción reales; la vista muestra la categoría correcta.
11. Se unificó en `ventas`/`detalle_ventas`. `pedidos`/`detalle_pedidos` quedan sin uso (DROP opcional en la migración).

## B. Autenticación y seguridad
12. Login real: `password_verify` contra la BD.
13. El registro guarda el usuario en la BD (campos nombre y apellido) e inicia sesión.
14. Panel admin protegido por rol; el menú admin solo lo ve el administrador.
15. El rol vive solo en `usuarios.rol_id` (se eliminan `es_admin` y `rol`).
16. XSS: toda salida pasa por `e()`; se quitó el `alert()` con datos del usuario. CSRF en todos los formularios.
17. Datos de prueba ficticios y contraseñas de prueba distintas para admin y cliente.

## C. Código roto, muerto o incoherente
18. Eliminados `carrito.php`, `procesar_pedido.php`, `confirmacion.php`, `historial_pedidos.php` (raíz) y `config/conexion.php`. Se reemplazaron por `CarritoController`, `VentaController` (confirmación) y la ruta `mis-compras`.
19. Modelos y controladores ahora se usan de verdad (MVC); se eliminó `auth.php`.
20. Ruta desconocida → página 404 real.
21. `public/.htacces` renombrado a `.htaccess`.
22. "Nombre Completo" del checkout ya no se rellena con el correo.
23. Se eliminó el `foreach` por referencia sin `unset`.
24. Imágenes del SQL apuntan a archivos que existen en `public/img`.
25. `README.md` completo, `main.js` con funciones útiles, SQL con `CREATE DATABASE`.

## Hallazgos adicionales
26. Varias páginas no cerraban `</body></html>` ni usaban el pie de página común: ahora todas usan `layouts/footer.php`.
27. Mensajes de éxito/error unificados (flash) en toda la tienda.

## Segunda tanda (20-10-2026)
28. Nuevo apartado "Tratamiento de datos": ventana desplegable (menú y pie de página) con el texto indicado, con la paleta y tipografía del sitio.
29. Botón "Mostrar/Ocultar" contraseña en inicio de sesión y registro.
30. Panel admin: subida de imagen del producto (valida tipo real, tamaño y bloquea scripts en la carpeta), miniatura en el inventario.
31. El catálogo ahora muestra la descripción de cada producto.
32. Sobre nosotros: sección "Nuestro equipo" con foto e iniciales de respaldo.
33. Se actualizó la versión del CSS (`?v=7.0`) para que el navegador no use estilos viejos.

## Tercera tanda (20-10-2026)
34. "Tratamiento de datos": los CSS y JS ahora llevan versión automática (`asset()`), así el navegador no usa un `main.js` viejo guardado en caché (causa más probable de que el clic no respondiera). Además la ventana funciona aunque JavaScript falle (respaldo `:target`).
35. Cuenta de administrador definida en `config/config.php`; inicio de sesión o registro con ese correo y su contraseña crean/reparan la cuenta con rol admin. El correo está reservado.
36. El administrador, al iniciar sesión, entra a Ventas y Reportes (indicadores, rankings, ventas por día y método de pago, stock bajo, filtro por fechas, exportación CSV).
37. Carrito guardado por usuario en la tabla `carrito_items`: sobrevive al cerrar sesión y se une con el carrito de invitado al iniciar sesión. Se vacía dentro de la misma transacción de la compra.
38. La portada muestra productos reales de la base de datos (destacados y más recientes), para que lo que agrega el admin se vea de inmediato en cualquier dispositivo.
39. Enlace "Mis compras" en el menú principal para clientes.
40. Nuevo archivo `migracion_02_carrito_y_admin.sql` para bases existentes; el SQL completo ya incluye todo.

## Cuarta tanda (20-10-2026)
41. Panel admin: botón **Editar** en cada producto con formulario completo (nombre, categoría, género, tipo, precio, stock, imagen, descripción, destacado y visible en la tienda). Validación igual que al crear, imagen nueva por lista o subida, y se borra la imagen subida anterior si nadie más la usa.
42. El inventario muestra también los productos retirados (en gris) para poder reactivarlos.

## Quinta tanda (22-10-2026)
43. Precios: el campo acepta formato colombiano (70.000, $70.000 COP, 1.250.000, 70.000,50). Antes "70.000" se leía como 70 con decimales. Ahora el campo muestra el punto de miles mientras se escribe y el formulario de edición muestra el precio con ese formato.
