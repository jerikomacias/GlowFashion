<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="auth-container" style="max-width: 480px; margin: 40px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08);">
    <h2 style="text-align: center; margin-bottom: 20px; color: #1f2937; font-family: serif;">Crear Cuenta - Glow Fashion</h2>

    <form action="/glow-fashion/index.php?url=registerProcess" method="POST">
        <?php echo csrf_field(); ?>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: #374151;">Nombre</label>
            <input type="text" name="nombre" required maxlength="100" placeholder="Tu nombre" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: #374151;">Apellido</label>
            <input type="text" name="apellido" maxlength="100" placeholder="Tu apellido" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: #374151;">Correo Electrónico</label>
            <input type="email" name="email" required maxlength="150" placeholder="correo@ejemplo.com" style="width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; color: #374151;">Contraseña</label>
            <div class="password-field">
                <input type="password" name="password" required pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}" title="Debe contener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial." placeholder="••••••••" style="width: 100%; padding: 10px 80px 10px 10px; border: 1px solid #d1d5db; border-radius: 6px; box-sizing: border-box;">
                <button type="button" class="toggle-password" data-toggle-password aria-label="Mostrar contraseña" aria-pressed="false">Mostrar</button>
            </div>
            <small style="display: block; margin-top: 5px; color: #6b7280; font-size: 12px;">
                * La contraseña debe tener mínimo 8 caracteres, incluir una letra mayúscula, una minúscula, un número y un carácter especial.
            </small>
        </div>

        <!-- Tratamiento de datos y aviso académico -->
        <div style="background: #f3f4f6; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 12px; color: #4b5563; line-height: 1.4;">
            <strong>Aviso de Tratamiento de Datos (Fines Académicos):</strong> Este sitio web es una plataforma simulada desarrollada exclusivamente con fines académicos y educativos. Glow Fashion no está pensada para recopilar información personal real. Al registrarte, aceptas que los datos ingresados son ficticios.
        </div>

        <button type="submit" style="width: 100%; background: #7e46d6; color: white; border: none; padding: 12px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 15px;">
            Registrarse
        </button>
    </form>

    <p style="margin-top: 18px; text-align: center; font-size: 14px; color: #555;">
        ¿Ya tienes cuenta? <a href="/glow-fashion/index.php?url=login" style="color: #4b0082; font-weight: bold;">Inicia sesión</a>
    </p>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>
