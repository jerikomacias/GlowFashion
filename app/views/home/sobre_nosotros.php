<?php
// ============================================================
// INTEGRANTES DEL EQUIPO: edita aquí nombre, rol y foto.
// Guarda las fotos en public/img/integrantes/ (JPG o PNG, cuadradas
// o de retrato) y escribe el nombre del archivo en 'foto'.
// Si la foto no existe, se muestra un círculo con las iniciales.
// ============================================================
$integrantes = [
    ['nombre' => 'Jeriko Macías', 'rol' => 'Integrante y Desarrollador del Proyecto Glow Fashion', 'foto' => 'integrante-1.jpg'],
    ['nombre' => 'Laura Estrada', 'rol' => 'Integrante y Desarrollador del Proyecto Glow Fashion', 'foto' => 'integrante-2.jpg'],
    ['nombre' => 'Samuel García', 'rol' => 'Integrante y Desarrollador del Proyecto Glow Fashion', 'foto' => 'integrante-3.jpg'],
];
?>
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="home-container" style="max-width: 900px; margin: 40px auto; padding: 0 20px;">
    <article style="background: #ffffff; padding: 50px; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
        
        <header style="text-align: center; margin-bottom: 40px; border-bottom: 2px solid #f3f4f6; padding-bottom: 25px;">
            <h1 style="font-family: serif; font-size: 38px; color: #1f2937; margin-bottom: 12px; font-weight: bold;">SOBRE NOSOTROS — GLOW FASHION</h1>
            <p style="font-size: 18px; color: #7e46d6; font-weight: 600; font-style: italic;">Una idea que nació de la pasión por la moda y las ganas de brillar.</p>
        </header>

        <div style="color: #4b5563; font-size: 16px; line-height: 1.8; display: flex; flex-direction: column; gap: 20px;">
            <p>
                <strong>Glow Fashion</strong> nació de la iniciativa de tres amigos, dos hombres y una mujer, que compartían una misma visión: crear una marca capaz de conectar la moda con la personalidad, la creatividad y la autenticidad de cada persona. Unidos por su interés en la ropa urbana, el maquillaje y los accesorios modernos, comenzamos a imaginar un espacio donde las personas pudieran encontrar productos que representaran su estilo y su manera de ver el mundo.
            </p>

            <p>
                La idea surgió al reconocer que la moda es mucho más que vestirse; es una forma de expresión que permite transmitir seguridad, identidad y confianza sin necesidad de decir una sola palabra. A partir de esta idea, decidimos convertir nuestra pasión en un proyecto emprendedor, creando una página de ventas en la que cada persona pudiera descubrir nuevas tendencias, explorar diferentes estilos y encontrar prendas y accesorios que se adaptaran a sus gustos.
            </p>

            <p>
                Así nació <strong>Glow Fashion</strong>, una marca que busca combinar las tendencias actuales con la esencia de la moda urbana, ofreciendo una experiencia de compra moderna, accesible y cercana. Nuestro propósito es reunir en un solo lugar opciones de ropa, maquillaje y accesorios que permitan complementar cada look y expresar la personalidad de quienes confían en nosotros.
            </p>

            <p>
                Desde el comienzo, hemos querido construir algo más que una página de ventas. Queremos crear una comunidad en la que la diversidad de estilos sea bienvenida, donde cada persona pueda sentirse libre de experimentar, innovar y destacar a su manera. Creemos que no existe una única forma de vestir bien, porque cada persona tiene una esencia diferente que merece ser representada.
            </p>

            <p>
                En <strong>Glow Fashion</strong>, nuestro nombre refleja lo que queremos transmitir: <em>Glow</em>, porque creemos que todos tenemos una luz propia, y <em>Fashion</em>, porque la moda es el medio que nos permite expresarla. Cada detalle de nuestra marca representa el compromiso de crecer, innovar y ofrecer alternativas que conecten con las necesidades y preferencias de nuestros clientes.
            </p>

            <p>
                Hoy seguimos construyendo este sueño con entusiasmo, creatividad y la convicción de que las grandes ideas comienzan con una pasión compartida. Nuestra meta es crecer junto a nuestra comunidad, adaptarnos a las nuevas tendencias y convertirnos en una marca reconocida por su estilo, originalidad y dedicación.
            </p>

            <div style="text-align: center; margin-top: 30px; padding: 25px; background: #f9fafb; border-radius: 12px; border-left: 4px solid #7e46d6;">
                <p style="font-size: 20px; font-family: serif; font-weight: bold; color: #1f2937; margin: 0;">
                    Glow Fashion: tu estilo, tu esencia, tu brillo.
                </p>
            </div>
        </div>

        <!-- Integrantes -->
        <section style="margin-top: 50px; border-top: 2px solid #f3f4f6; padding-top: 35px;">
            <h2 style="font-family: serif; font-size: 28px; text-align: center; color: #1f2937; margin-bottom: 8px;">Nuestro equipo</h2>
            <p style="text-align: center; color: #7e46d6; font-style: italic; font-weight: 600; margin-bottom: 30px;">Las personas detrás de Glow Fashion</p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px;">
                <?php foreach ($integrantes as $i): ?>
                    <?php
                    $tieneFoto = is_file(APPROOT . '/public/img/integrantes/' . $i['foto']);
                    $iniciales = mb_strtoupper(mb_substr($i['nombre'], 0, 1));
                    ?>
                    <div style="background: #f9fafb; border-radius: 12px; padding: 24px 16px; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                        <?php if ($tieneFoto): ?>
                            <img src="/glow-fashion/public/img/integrantes/<?php echo e($i['foto']); ?>" alt="Foto de <?php echo e($i['nombre']); ?>" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 4px solid #f3e8ff; box-shadow: 0 4px 12px rgba(126, 70, 214, 0.25);">
                        <?php else: ?>
                            <div style="width: 150px; height: 150px; border-radius: 50%; margin: 0 auto; background: #f3e8ff; color: #7e46d6; font-family: serif; font-size: 56px; font-weight: bold; display: flex; align-items: center; justify-content: center; border: 4px solid #ffffff; box-shadow: 0 4px 12px rgba(126, 70, 214, 0.25);">
                                <?php echo e($iniciales); ?>
                            </div>
                        <?php endif; ?>
                        <h3 style="font-family: serif; font-size: 20px; color: #1f2937; margin: 16px 0 4px 0;"><?php echo e($i['nombre']); ?></h3>
                        <p style="color: #7e46d6; font-weight: 600; font-size: 14px; margin: 0;"><?php echo e($i['rol']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <div style="margin-top: 40px; text-align: center;">
            <a href="/glow-fashion/index.php?url=home" class="btn-primary" style="text-decoration: none; padding: 12px 30px; font-size: 15px; border-radius: 8px; font-weight: 600; display: inline-block;">
                ← Volver al Inicio
            </a>
        </div>

    </article>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
