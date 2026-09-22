<section class="module-section active" style="padding-top: 40px;">
  <div class="module-header">
    <h2><?php echo htmlspecialchars($titulo); ?></h2>
    <p><?php echo htmlspecialchars($subtitulo); ?></p>
  </div>

  <div class="products-grid">
    <?php if (!empty($productos)): ?>
      <?php foreach ($productos as $producto): ?>
        <div class="product-card">
          <!-- Si la imagen viene de BD usa assets, o la imagen por defecto -->
          <img src="<?php echo URLROOT; ?>/public/assets/<?php echo htmlspecialchars($producto['imagen']); ?>" 
               alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
               onerror="this.src='<?php echo URLROOT; ?>/public/assets/default_product.jpg';">
          
          <div class="product-info">
            <h4><?php echo htmlspecialchars($producto['nombre']); ?></h4>
            <span class="price">$<?php echo number_format($producto['precio'], 0, ',', '.'); ?></span>
            
            <form action="<?php echo URLROOT; ?>/carrito/agregar" method="POST" style="margin-top: 10px;">
              <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
              <button type="submit" class="btn-primary-sm">Añadir al Carrito</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
        <p>No se encontraron productos disponibles en esta categoría por el momento.</p>
        <a href="<?php echo URLROOT; ?>" class="btn-purple-action" style="display: inline-block; margin-top: 15px; text-decoration: none;">Volver al Inicio</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- HEADER (layouts/header.php) -->
<header>
    <div class="logo-header">GF</div>
    <nav>
        <a href="#">Inicio</a>
        <a href="#">Maquillaje y Accesorios</a>
        <a href="#">Ropa Hombre/Mujer</a>
        <a href="login.php" class="btn-registro-nav">INICIO/REGIS</a>
    </nav>
</header>

<!-- HERO -->
<section class="hero-section">
    <div class="hero-texto">
        <h1>Brilla con<br>tu estilo</h1>
        <p>Descubre moda, maquillaje y accesorios que reflejen tu esencia.</p>
        <a href="#" class="btn-hero">Explorar colección</a>
    </div>
    <div class="hero-imagen">
        <img src="ruta-de-tu-imagen-principal.jpg" alt="Hero Image">
    </div>
</section>

<!-- CATEGORÍAS -->
<section class="categorias-section">
    <div class="grid-categorias">
        <div class="categoria-item">
            <img src="ruta-hombre.jpg" alt="Ropa Hombre">
            <span>PRENDA HOMBRE</span>
        </div>
        <div class="categoria-item">
            <img src="ruta-reloj.jpg" alt="Accesorios">
            <span>ACCESORIO HOMBRE</span>
        </div>
        <div class="categoria-item">
            <img src="ruta-mujer.jpg" alt="Ropa Mujer">
            <span>PRENDA MUJER</span>
        </div>
        <div class="categoria-item">
            <img src="ruta-maquillaje.jpg" alt="Maquillaje">
            <span>MAQUILLAJE</span>
        </div>
    </div>
</section>

<!-- SOBRE NOSOTROS -->
<section class="sobre-nosotros-section">
    <div class="nosotros-contenido">
        <h2>Sobre<br>Nosotros</h2>
        <p>Glow Fashion nació de la unión de tres amigos con la pasión por la moda y la autenticidad.</p>
        <p>Creemos que vestir bien es una forma de expresión y queremos que brilles con tu propio estilo.</p>
        <a href="#" class="btn-nosotros">Conócenos más</a>
    </div>
    <div class="nosotros-logo-box">
        <img src="ruta-logo-gf.png" alt="GF Logo">
    </div>
</section>