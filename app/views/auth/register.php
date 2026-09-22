<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<section class="auth-section" style="padding: 60px 20px; display: flex; justify-content: center; align-items: center; min-height: 70vh; background-color: #f7f7f7;">
    <div class="auth-box" style="background: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 420px;">
        <h2 style="text-align: center; color: #4b0082; margin-bottom: 25px; font-family: sans-serif;">Crear Cuenta</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div style="background-color: #ffe6e6; color: #d9534f; padding: 10px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-size: 14px;">
                <?php 
                    echo $_SESSION['error']; 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <form action="/glow-fashion/index.php?url=registerProcess" method="POST">
            <div style="margin-bottom: 20px;">
                <label for="nombre" style="display: block; font-weight: bold; margin-bottom: 6px; color: #333;">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" required placeholder="Tu Nombre" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 15px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; font-weight: bold; margin-bottom: 6px; color: #333;">Correo Electrónico:</label>
                <input type="email" id="email" name="email" required placeholder="tu@email.com" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 15px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="password" style="display: block; font-weight: bold; margin-bottom: 6px; color: #333;">Contraseña de Usuario:</label>
                <input type="password" id="password" name="password" required placeholder="••••••••" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 15px;">
            </div>

            <!-- SELECCIÓN DE ROL -->
            <div style="margin-bottom: 20px;">
                <label for="rol" style="display: block; font-weight: bold; margin-bottom: 6px; color: #333;">Tipo de Cuenta:</label>
                <select id="rol" name="rol" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 15px; background-color: #fff; cursor: pointer;">
                    <option value="usuario">Usuario / Cliente</option>
                    <option value="admin">Administrador</option>
                </select>
            </div>

            <!-- CAMPO CLAVE SECRETA ADMIN (OCULTO POR DEFECTO) -->
            <div id="adminKeyGroup" style="margin-bottom: 25px; display: none;">
                <label for="admin_key" style="display: block; font-weight: bold; margin-bottom: 6px; color: #d9534f;">Clave Secreta de Administrador:</label>
                <input type="password" id="admin_key" name="admin_key" placeholder="Código de acceso administrador" style="width: 100%; padding: 12px; border: 1px solid #d9534f; border-radius: 6px; box-sizing: border-box; font-size: 15px; background-color: #fff8f8;">
            </div>

            <button type="submit" class="btn-hero" style="width: 100%; border: none; padding: 14px; font-size: 16px; font-weight: bold; cursor: pointer; text-align: center; display: block; border-radius: 6px;">Registrarse</button>
        </form>

        <p style="margin-top: 20px; text-align: center; font-size: 14px; color: #555;">
            ¿Ya tienes cuenta? <a href="/glow-fashion/index.php?url=login" style="color: #4b0082; font-weight: bold; text-decoration: underline;">Inicia sesión aquí</a>
        </p>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rolSelect = document.getElementById('rol');
    const adminKeyGroup = document.getElementById('adminKeyGroup');
    const adminKeyInput = document.getElementById('admin_key');

    rolSelect.addEventListener('change', function() {
        if (this.value === 'admin') {
            adminKeyGroup.style.display = 'block';
            adminKeyInput.setAttribute('required', 'required');
        } else {
            adminKeyGroup.style.display = 'none';
            adminKeyInput.removeAttribute('required');
            adminKeyInput.value = '';
        }
    });
});
</script>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>