document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');

    form.addEventListener('submit', function(event) {
        // Obtener valores de los campos
        const nombre = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        
        let errores = []; // Array para almacenar errores

        // Validación nombre completo
        if (nombre === '') {
            errores.push("El campo 'Nombre completo' es obligatorio.");
        } else if (nombre.length < 3) {
            errores.push("El nombre debe tener al menos 3 caracteres.");
        }

        // Validación email
        if (email === '') {
            errores.push("El campo 'Email' es obligatorio.");
        } else {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                errores.push("El formato del email no es válido.");
            }
        }

        // Validación contraseña segura
        if (password === '') {
            errores.push("El campo 'Contraseña' es obligatorio.");
        } else {
            if (password.length < 8) {
                errores.push("La contraseña debe tener al menos 8 caracteres.");
            }
            if (!/[A-Z]/.test(password)) {
                errores.push("La contraseña debe contener al menos una letra mayúscula.");
            }
            if (!/[a-z]/.test(password)) {
                errores.push("La contraseña debe contener al menos una letra minúscula.");
            }
            if (!/[0-9]/.test(password)) {
                errores.push("La contraseña debe contener al menos un número.");
            }
            // CORRECCIÓN: Usar una expresión regular que realmente detecte caracteres especiales
            if (!/[^A-Za-z0-9]/.test(password)) {
                errores.push("La contraseña debe contener al menos un carácter especial (ej: !@#$%^&*).");
            }
        }

        // Manejo de errores
        if (errores.length > 0) {
            event.preventDefault(); // Evita el envío del formulario
            alert(errores.join('\n')); // Muestra todos los errores
        }
    });
});