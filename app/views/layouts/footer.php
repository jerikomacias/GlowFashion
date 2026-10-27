<footer style="background-color: #000000; color: #9ca3af; text-align: center; padding: 25px 20px; font-size: 14px; margin-top: 40px; border-top: 1px solid #1f2937;">
    <p style="margin: 0;">© 2026 Glow Fashion - Brilla con tu estilo. Proyecto desarrollado con fines académicos.
        · <a href="#modal-tratamiento" data-modal-open="modal-tratamiento" role="button" style="color: #c4b5fd; text-decoration: underline;">Tratamiento de datos</a>
    </p>
</footer>

<!-- Ventana "Tratamiento de datos" (se abre desde el menú o desde el pie de página) -->
<div class="modal-overlay" id="modal-tratamiento" role="dialog" aria-modal="true" aria-labelledby="modal-tratamiento-titulo" aria-hidden="true">
    <div class="modal-box modal-texto">
        <div class="modal-header">
            <h2 id="modal-tratamiento-titulo">Tratamiento de datos</h2>
            <a href="#" class="close-btn" data-modal-close role="button" aria-label="Cerrar">&times;</a>
        </div>
        <p>
            El presente proyecto tiene fines exclusivamente académicos. Los datos utilizados durante el desarrollo,
            las pruebas y la demostración del sistema son ficticios y fueron creados únicamente con fines de
            simulación. El sistema no recolecta, almacena ni procesa datos personales reales de estudiantes,
            docentes o terceros.
        </p>
        <div class="form-actions">
            <a href="#" class="btn-primary btn-modal" data-modal-close role="button">Entendido</a>
        </div>
    </div>
</div>

<script src="<?php echo asset('js/main.js'); ?>"></script>
</body>
</html>
