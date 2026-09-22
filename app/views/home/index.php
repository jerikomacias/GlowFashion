<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="main-content">
    <!-- 1. HERO BANNER -->
    <section class="hero-section">
        <div class="hero-text-container">
            <h1 class="hero-title">Brilla con<br>tu estilo</h1>
            <p class="hero-subtitle">Descubre moda, maquillaje y<br>accesorios que reflejen tu esencia.</p>
            <a href="/glow-fashion/index.php?url=carrito" class="btn-hero">Explorar colección</a>
        </div>
        <div class="hero-img-container">
            <img src="/glow-fashion/public/img/hero-modelo.jpg" alt="Modelo Glow Fashion">
        </div>
    </section>

    <!-- 2. SECCIÓN DE CATEGORÍAS -->
    <section class="categories-section">
        <div class="categories-grid">
            <div class="category-card">
                <div class="card-img-holder">
                    <img src="/glow-fashion/public/img/prenda-hombre.jpg" alt="Prenda Hombre">
                </div>
                <h3>PRENDA HOMBRE</h3>
            </div>

            <div class="category-card">
                <div class="card-img-holder">
                    <img src="/glow-fashion/public/img/accesorio-hombre.jpg" alt="Accesorio Hombre">
                </div>
                <h3>ACCESORIO HOMBRE</h3>
            </div>

            <div class="category-card">
                <div class="card-img-holder">
                    <img src="/glow-fashion/public/img/prenda-mujer.jpg" alt="Prenda Mujer">
                </div>
                <h3>PRENDA MUJER</h3>
            </div>

            <div class="category-card">
                <div class="card-img-holder">
                    <img src="/glow-fashion/public/img/maquillaje.jpg" alt="Maquillaje">
                </div>
                <h3>MAQUILLAJE</h3>
            </div>
        </div>
    </section>

    <!-- 3. SECCIÓN SOBRE NOSOTROS -->
    <section class="about-glow-section">
        <div class="about-glow-content">
            <h2>Sobre<br>Nosotros</h2>
            <p>Glow Fashion nacio de la union de tres amigos con la pasion por la moda y la autenticidad.</p>
            <p>Creemos que vestir bien es una forma de expresion y queremos que brilles con tu propio estilo</p>
            <a href="/glow-fashion/index.php?url=nosotros" class="btn-about">Conócenos más</a>
        </div>
        <div class="about-glow-card">
            <img src="/glow-fashion/public/img/logo.png" alt="Glow Fashion Logo" class="about-logo-img">
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>