document.addEventListener('DOMContentLoaded', function () {
    // Los mensajes flash desaparecen solos a los 6 segundos
    document.querySelectorAll('.flash').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity 0.4s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 400);
        }, 6000);
    });

    // Los formularios con data-confirm piden confirmación antes de enviarse
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (ev) {
            if (!confirm(form.getAttribute('data-confirm'))) {
                ev.preventDefault();
            }
        });
    });

    // Campos de dinero (data-moneda): muestran el punto de miles mientras se escribe (70.000)
    document.querySelectorAll('input[data-moneda]').forEach(function (campo) {
        campo.addEventListener('input', function () {
            // un punto seguido de 1-2 dígitos al final (pegado como 70000.50) es decimal
            var v = campo.value.replace(/\.(\d{1,2})$/, ',$1').replace(/[^\d,]/g, '');
            var partes = v.split(',');
            var entero = partes[0].replace(/^0+(?=\d)/, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            campo.value = partes.length > 1 ? entero + ',' + partes.slice(1).join('').slice(0, 2) : entero;
        });
    });

    // Botón "Mostrar / Ocultar" contraseña
    document.querySelectorAll('[data-toggle-password]').forEach(function (boton) {
        boton.addEventListener('click', function () {
            var campo = boton.parentElement.querySelector('input');
            if (!campo) { return; }
            var visible = (campo.type === 'text');
            campo.type = visible ? 'password' : 'text';
            boton.textContent = visible ? 'Mostrar' : 'Ocultar';
            boton.setAttribute('aria-pressed', visible ? 'false' : 'true');
            boton.setAttribute('aria-label', visible ? 'Mostrar contraseña' : 'Ocultar contraseña');
        });
    });
});

// Ventanas modales (por ejemplo "Tratamiento de datos")
(function () {
    var focoPrevio = null;

    function abrirModal(modal) {
        focoPrevio = document.activeElement;
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        var cerrar = modal.querySelector('.btn-primary') || modal.querySelector('[data-modal-close]');
        if (cerrar) { cerrar.focus(); }
    }

    function cerrarModal(modal) {
        // Si se abrió por el respaldo #hash, se limpia la URL para que no se reabra
        if (window.location.hash === '#' + modal.id && window.history && window.history.replaceState) {
            window.history.replaceState(null, '', window.location.pathname + window.location.search);
        }
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (focoPrevio && focoPrevio.focus) { focoPrevio.focus(); }
    }

    document.addEventListener('click', function (ev) {
        var abrir = ev.target.closest('[data-modal-open]');
        if (abrir) {
            ev.preventDefault();
            var modal = document.getElementById(abrir.getAttribute('data-modal-open'));
            if (modal) { abrirModal(modal); }
            return;
        }
        if (ev.target.closest('[data-modal-close]')) {
            ev.preventDefault();
            var contenedor = ev.target.closest('.modal-overlay');
            if (contenedor) { cerrarModal(contenedor); }
            return;
        }
        // Clic en el fondo oscuro, fuera de la ventana
        if (ev.target.classList && ev.target.classList.contains('modal-overlay')) {
            cerrarModal(ev.target);
        }
    });

    document.addEventListener('keydown', function (ev) {
        if (ev.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(cerrarModal);
        }
    });
})();
