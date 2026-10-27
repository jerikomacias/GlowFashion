# Glow Fashion

Tienda en línea de moda urbana, maquillaje y accesorios. Proyecto académico del SENA desarrollado en **PHP (MVC sencillo) + MySQL/MariaDB**.

## Requisitos
- XAMPP (Apache + MySQL/MariaDB + PHP 7.4 o superior con las extensiones `pdo_mysql` y `mbstring`, activas por defecto en XAMPP)
- El módulo `mod_rewrite` de Apache activo (viene activo en XAMPP)

## Instalación
1. Copia la carpeta del proyecto en `htdocs` con el nombre **`glow-fashion`** (la ruta debe quedar `htdocs/glow-fashion`).
2. Inicia **Apache** y **MySQL** desde el panel de XAMPP.
3. Abre phpMyAdmin → pestaña **Importar** → selecciona `config/Basededatos/glow_fashion_db.sql`. Crea la base `glow_fashion_db` con sus tablas y datos de prueba.
   - Si **ya tienes** la base creada con datos, NO uses ese archivo. Selecciona la base `glow_fashion_db`, haz copia de seguridad e importa, en este orden:
     1. `config/Basededatos/migracion_actualizar_bd_existente.sql` (solo si aún no la habías importado)
     2. `config/Basededatos/migracion_02_carrito_y_admin.sql` (carrito guardado por usuario y cuenta de administrador)
4. Revisa `config/config.php` (usuario, contraseña y nombre de la base). Es el único lugar donde se configura la conexión.
5. Abre: <http://localhost/glow-fashion/>

## Usuarios de prueba (datos ficticios)
| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | admin@glowfashion.com | `GlowAdmin#2026` |
| Cliente | camila@email.com | `Cliente123*` |

También puedes crear clientes nuevos desde "Regístrate". La contraseña exige 8+ caracteres con mayúscula, minúscula, número y carácter especial.

## Estructura
```
config/            config.php (constantes) · database.php (conexión PDO única) · Basededatos/ (SQL)
app/controllers/   Home, Productos, Carrito, Venta, Auth, Admin
app/models/        Product, User, Venta (todas las consultas SQL)
app/views/         plantillas (layouts, home, productos, carrito, ventas, auth, admin, errors)
app/helpers.php    escape HTML, mensajes flash, CSRF, permisos
public/            index.php (controlador frontal) · css · js · img
```

## Flujo de compra (todo queda en la base de datos)
1. El catálogo se lee de la tabla `productos` (solo los activos).
2. El carrito guarda en sesión únicamente `id` y `cantidad`; nombre, precio y stock se consultan siempre en la BD.
3. Al finalizar (requiere sesión iniciada) `Venta::crearVenta()` ejecuta **una transacción**: inserta en `ventas`, inserta cada línea en `detalle_ventas` y descuenta el stock. Si algo falla (por ejemplo, stock insuficiente) se deshace todo.
4. El administrador ve las compras en **Panel Admin → Ver Ventas**; el cliente, en **Mis Compras**.

## Rutas principales (`index.php?url=...`)
`home` · `sobre-nosotros` · `categoria/{maquillaje|ropa|accesorios}` · `carrito` · `procesar-compra` · `compra/confirmacion&id=N` · `mis-compras` · `login` · `register` · `logout` · `admin/productos` · `admin/ventas`

## Seguridad aplicada
- Contraseñas con `password_hash` / `password_verify` (bcrypt).
- Consultas preparadas (PDO) en todo el acceso a datos.
- Token CSRF en todos los formularios que modifican datos; el borrado de productos es por POST.
- Salida escapada con `e()` (previene XSS).
- Panel de administración protegido por rol (`usuarios.rol_id = 1`).
- "Retirar" un producto es un borrado lógico (`activo = 0`): el historial de ventas nunca se pierde.

## Depuración
Si algo falla, el detalle del error se guarda en `xampp/apache/logs/error.log`. Mientras desarrollas puedes poner `APP_DEBUG` en `true` dentro de `config/config.php` para verlo en pantalla (vuelve a `false` al entregar).

## Cómo cargar productos
1. Inicia sesión como administrador → **Gestionar Productos**.
2. Llena el formulario: nombre, categoría, género, tipo, precio, stock, descripción, "producto destacado" e imagen.
3. Para la imagen puedes elegir una de la lista o **subir una nueva** (JPG, PNG o WEBP, máx. 3 MB). Las imágenes subidas se guardan en `public/img/productos/`.
4. El producto aparece de inmediato en la tienda, con su descripción, en la categoría y filtros que elegiste.
5. Para **editar** un producto pulsa **Editar** en el inventario: puedes cambiar nombre, categoría, género, tipo, precio, stock, imagen (de la lista o subiendo una nueva), descripción, destacado y si está visible en la tienda. Los productos retirados siguen en el inventario (en gris, con la etiqueta "Retirado") y se reactivan marcando "Visible en la tienda".

## Integrantes (Sobre nosotros)
Edita el arreglo `$integrantes` al inicio de `app/views/home/sobre_nosotros.php` (nombre, rol y nombre del archivo de foto) y guarda las fotos en `public/img/integrantes/`. Si una foto no existe, se muestra un círculo con la inicial.

## Tratamiento de datos
El enlace "Tratamiento de datos" (menú superior y pie de página) abre una ventana con el aviso académico. Se cierra con la X, con "Entendido", con la tecla Esc o haciendo clic fuera. El texto está en `app/views/layouts/footer.php`.

## Cuenta de administrador
- Correo: `admin@glowfashion.com` · Contraseña: `GlowAdmin#2026`
- Se define en `config/config.php` (`ADMIN_EMAIL` y `ADMIN_PASSWORD_HASH`; ahí solo vive el hash, no la contraseña). Si la cuenta no existe en la base o perdió su rol, el sistema la crea/repara sola al iniciar sesión o al registrarse con ese correo y esa contraseña.
- Ese correo está **reservado**: nadie puede registrarse con él sin conocer la contraseña de administrador.
- Para cambiar la contraseña: genera un hash nuevo (`php -r "echo password_hash('NuevaClave#1', PASSWORD_BCRYPT);"`) y pégalo en `ADMIN_PASSWORD_HASH`. Actualiza también el campo `password` del usuario admin en la tabla `usuarios` si quieres que coincidan.
- Por seguridad la contraseña NO se muestra en las páginas del sitio. Antes de publicar el proyecto, cambia esta contraseña.
- Al iniciar sesión, el administrador entra directo a **Ventas y Reportes**: indicadores, productos más vendidos, ventas por método de pago y por día, mejores clientes, alerta de stock bajo, historial completo, filtro por fechas y exportación a Excel (CSV).

## Qué se guarda y dónde (todo en la base de datos)
| Dato | Tabla | Quién lo ve |
|---|---|---|
| Productos que agrega el administrador | `productos` | Invitados, clientes y admin, en cualquier dispositivo (portada y categorías) |
| Compras de cada cliente | `ventas` + `detalle_ventas` | El cliente en **Mis compras**; el admin en **Ventas y Reportes** |
| Carrito de cada cliente | `carrito_items` | El cliente, al volver a iniciar sesión en cualquier dispositivo |

El carrito de un invitado vive en la sesión del navegador; al iniciar sesión se une al carrito guardado de la cuenta.

## Ver la página desde otros dispositivos (celular, otro PC)
1. El PC con XAMPP y los dispositivos deben estar en la misma red Wi-Fi.
2. En el PC con XAMPP abre una consola (`cmd`) y ejecuta `ipconfig`; anota la "Dirección IPv4" (por ejemplo `192.168.1.20`).
3. En el otro dispositivo abre `http://192.168.1.20/glow-fashion/` (con tu IP).
4. Si no abre, permite Apache (puerto 80) en el Firewall de Windows. Para verla desde cualquier lugar (fuera de tu red) el proyecto debe publicarse en un hosting con PHP y MySQL.
