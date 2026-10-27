<?php
// Barra superior compartida por las páginas del panel de administración.
// Define $navActivo = 'productos' | 'ventas' antes de incluirla.
$navActivo = $navActivo ?? '';
$estiloLink = 'color: white; margin-right: 15px; text-decoration: none;';
?>
<div style="background: #1f2937; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
    <h1 style="margin: 0; font-size: 20px;">Glow Fashion - Panel Admin</h1>
    <div>
        <a href="/glow-fashion/index.php?url=home" style="<?php echo $estiloLink; ?>">Ver Tienda</a>
        <a href="/glow-fashion/index.php?url=admin/ventas" style="<?php echo $estiloLink; ?><?php echo $navActivo === 'ventas' ? ' font-weight: bold;' : ''; ?>">Ventas y Reportes</a>
        <a href="/glow-fashion/index.php?url=admin/productos" style="<?php echo $estiloLink; ?><?php echo $navActivo === 'productos' ? ' font-weight: bold;' : ''; ?>">Productos</a>
        <a href="/glow-fashion/index.php?url=logout" style="color: #fca5a5; text-decoration: none;">Cerrar sesión</a>
    </div>
</div>
<?php render_flash(); ?>
