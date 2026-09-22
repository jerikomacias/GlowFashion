<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<section class="auth-section" style="padding: 60px 20px; display: flex; justify-content: center; align-items: center; min-height: 70vh; background-color: #f7f7f7;">
    <div class="auth-box" style="background: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 420px;">
        <h2 style="text-align: center; color: #4b0082; margin-bottom: 25px; font-family: sans-serif;">Iniciar Sesión</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div style="background-color: #ffe6e6; color: #d9534f; padding: 10px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-size: 14px;">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div style="background-color: #e6ffe6; color: #2e7d32; padding: 10px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-size: 14px;">
                <?php 
                    echo $_SESSION['mensaje']; 
                    unset($_SESSION['mensaje']);
                ?>
            </div>
        <?php endif; ?>

        <form action="/glow-fashion/index.php?url=loginProcess" method="POST">
            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; font-weight: bold; margin-bottom: 6px; color: #333;">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required placeholder="tu@email.com" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 15px;">
            </div>

            <div style="margin-bottom: 25px;">
                <label for="password" style="display: block; font-weight: bold; margin-bottom: 6px; color: #333;">Contraseña:</label>
                <input type="password" id="password" name="password" required placeholder="••••••••" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 15px;">
            </div>

            <button type="submit" class="btn-hero" style="width: 100%; border: none; padding: 14px; font-size: 16px; font-weight: bold; cursor: pointer; text-align: center; display: block; border-radius: 6px;">Ingresar</button>
        </form>

        <p style="margin-top: 20px; text-align: center; font-size: 14px; color: #555;">
            ¿Aún no tienes cuenta? <a href="/glow-fashion/index.php?url=register" style="color: #4b0082; font-weight: bold; text-decoration: underline;">Regístrate aquí</a>
        </p>
    </div>
</section>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>