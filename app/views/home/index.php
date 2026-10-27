<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="home-container" style="padding-bottom: 20px;">
    <!-- Hero Banner (Brilla con tu estilo con la imagen a un costado y fondo negro) -->
    <section class="hero-banner" style="background-color: #000000; background-image: linear-gradient(to right, #000000 50%, rgba(0,0,0,0.7) 75%, transparent 100%), url('/glow-fashion/public/img/hero-modelo.jpg?v=2'); background-size: contain; background-position: right center; background-repeat: no-repeat; min-height: 450px; display: flex; align-items: center; padding: 0 6%; color: white; border-radius: 16px; margin-bottom: 40px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
        <div style="max-width: 520px; z-index: 2;">
            <h1 style="font-size: 48px; font-family: serif; margin-bottom: 20px; font-weight: bold; line-height: 1.1; color: #ffffff;">Brilla con tu estilo</h1>
            <p style="font-size: 18px; line-height: 1.5; margin-bottom: 30px; color: #d1d5db;">Descubre moda, maquillaje y accesorios que reflejen tu esencia.</p>
            <a href="/glow-fashion/index.php?url=categoria/ropa" class="btn-primary" style="text-decoration: none; padding: 14px 32px; font-size: 16px; display: inline-block; font-weight: 600; border-radius: 30px; background: #7e46d6; color: white; box-shadow: 0 4px 15px rgba(126, 70, 214, 0.4);">Explorar colección</a>
        </div>
    </section>

    <!-- Grid de Categorías Principales con Enlaces -->
    <section class="categories-section" style="margin-bottom: 50px;">
        <h2 style="font-family: serif; font-size: 28px; text-align: center; margin-bottom: 30px; color: #1f2937;">Categorías Destacadas</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px;">
            <!-- 1. Prenda Hombre -->
            <a href="/glow-fashion/index.php?url=categoria/ropa&genero=hombre" style="text-decoration: none; color: inherit; display: block; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <div style="height: 280px; overflow: hidden;">
                    <img src="/glow-fashion/public/img/prenda-hombre.jpg?v=2" alt="Ropa Hombre" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="padding: 15px; text-align: center;">
                    <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #1f2937; letter-spacing: 0.5px;">PRENDA HOMBRE</h3>
                </div>
            </a>

            <!-- 2. Accesorios -->
            <a href="/glow-fashion/index.php?url=categoria/accesorios" style="text-decoration: none; color: inherit; display: block; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <div style="height: 280px; overflow: hidden;">
                    <img src="/glow-fashion/public/img/accesorio-hombre.jpg?v=2" alt="Accesorios" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="padding: 15px; text-align: center;">
                    <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #1f2937; letter-spacing: 0.5px;">ACCESORIOS</h3>
                </div>
            </a>

            <!-- 3. Prenda Mujer -->
            <a href="/glow-fashion/index.php?url=categoria/ropa&genero=mujer" style="text-decoration: none; color: inherit; display: block; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <div style="height: 280px; overflow: hidden;">
                    <img src="/glow-fashion/public/img/prenda-mujer.png?v=2" alt="Ropa Mujer" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="padding: 15px; text-align: center;">
                    <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #1f2937; letter-spacing: 0.5px;">PRENDA MUJER</h3>
                </div>
            </a>

            <!-- 4. Maquillaje -->
            <a href="/glow-fashion/index.php?url=categoria/maquillaje" style="text-decoration: none; color: inherit; display: block; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06); transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                <div style="height: 280px; overflow: hidden;">
                    <img src="/glow-fashion/public/img/maquillaje.png?v=2" alt="Maquillaje" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="padding: 15px; text-align: center;">
                    <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #1f2937; letter-spacing: 0.5px;">MAQUILLAJE</h3>
                </div>
            </a>
        </div>
    </section>

    <!-- Productos destacados (vienen de la base de datos: lo que el admin agrega aparece aquí) -->
    <?php if (!empty($destacados)): ?>
    <section style="margin-bottom: 50px;">
        <h2 style="font-family: serif; font-size: 28px; text-align: center; margin-bottom: 30px; color: #1f2937;">Productos Destacados</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 24px;">
            <?php foreach ($destacados as $prod): ?>
                <?php require __DIR__ . '/../productos/_tarjeta.php'; ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Sección Sobre Nosotros -->
    <section style="background: linear-gradient(135deg, #2563eb 0%, #7c3aed 50%, #9333ea 100%); padding: 50px 6%; border-radius: 16px; color: white; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 40px; margin-bottom: 20px; box-shadow: 0 10px 25px rgba(124, 58, 237, 0.2);">
        <div style="flex: 1; min-width: 280px;">
            <h2 style="font-family: serif; font-size: 42px; margin-bottom: 20px; font-weight: bold; line-height: 1.2;">Sobre<br>Nosotros</h2>
            <p style="font-size: 16px; line-height: 1.6; margin-bottom: 15px; color: #f3f4f6;">
                Glow Fashion nació de la unión de tres amigos con la pasión por la moda y la autenticidad.
            </p>
            <p style="font-size: 16px; line-height: 1.6; margin-bottom: 30px; color: #f3f4f6;">
                Creemos que vestir bien es una forma de expresión y queremos que brilles con tu propio estilo.
            </p>
            <a href="/glow-fashion/index.php?url=sobre-nosotros" style="background: #000000; color: white; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-block; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">Conócenos más</a>
        </div>

        <div style="flex: 1; min-width: 280px; display: flex; justify-content: center;">
            <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.15); width: 100%; max-width: 320px;">
                <div style="height: 260px; overflow: hidden; background: #e5e7eb; display: flex; align-items: center; justify-content: center;">
                    <img src="/glow-fashion/public/img/logo.png" alt="Glow Fashion Logo" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
