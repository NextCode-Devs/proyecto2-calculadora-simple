// Manejar cierre de alertas automático
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar alertas automáticamente después de 5 segundos
    const alertas = document.querySelectorAll('.alert');
    alertas.forEach(alerta => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alerta);
            bsAlert.close();
        }, 5000);
    });

    // Validación de formulario adicional
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const monto = parseFloat(this.monto.value);
            if (isNaN(monto)) {
                e.preventDefault();
                alert('El monto debe ser un número válido');
                return false;
            }

            if (!this.descripcion.value.trim()) {
                e.preventDefault();
                alert('La descripción es requerida');
                return false;
            }
            return true;
        });
    }
});

