<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<section class="auth-section" style="padding: 60px 20px; display: flex; justify-content: center; align-items: center; min-height: 70vh; background-color: #f7f7f7;">
    <div class="auth-box" style="background: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 420px;">
        <h2 style="text-align: center; color: #4b0082; margin-bottom: 25px; font-family: sans-serif;">Iniciar Sesión</h2>

        <form action="/glow-fashion/index.php?url=loginProcess" method="POST">
            <?php echo csrf_field(); ?>

            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; font-weight: bold; margin-bottom: 6px; color: #333;">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required placeholder="tu@email.com" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 15px;">
            </div>

            <div style="margin-bottom: 25px;">
                <label for="password" style="display: block; font-weight: bold; margin-bottom: 6px; color: #333;">Contraseña:</label>
                <div class="password-field">
                    <input type="password" id="password" name="password" required placeholder="••••••••" style="width: 100%; padding: 12px 80px 12px 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 15px;">
                    <button type="button" class="toggle-password" data-toggle-password aria-label="Mostrar contraseña" aria-pressed="false">Mostrar</button>
                </div>
            </div>

            <button type="submit" class="btn-hero" style="width: 100%; border: none; padding: 14px; font-size: 16px; font-weight: bold; cursor: pointer; text-align: center; display: block; border-radius: 6px;">Ingresar</button>
        </form>

        <p style="margin-top: 20px; text-align: center; font-size: 14px; color: #555;">
            ¿Aún no tienes cuenta? <a href="/glow-fashion/index.php?url=register" style="color: #4b0082; font-weight: bold; text-decoration: underline;">Regístrate aquí</a>
        </p>
    </div>
</section>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
