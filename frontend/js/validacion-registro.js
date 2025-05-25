document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registro'); // ID de tu formulario de registro

    form.addEventListener('submit', function(event) {
        // Obtener valores de los campos 
        const nombre = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();

        let errores = []; // Array para almacenar errores

        // Validación nombre completo
        if (nombre === '') {
            errores.push("El campo 'Nombre completo' es obligatorio.");
        } else if (nombre.length < 3) {
            errores.push("El nombre debe tener al menos 3 caracteres.");
        }

        // Validación email (igual que en inicio de sesión)
        if (email === '') {
            errores.push("El campo 'Email' es obligatorio.");
        } else {
            const emailValido = email.includes('@') && email.includes('.');
            if (!emailValido) {
                errores.push("El formato del email no es válido.");
            }
        }

        // Validación contraseña (igual que en inicio de sesión)
        if (password === '') {
            errores.push("El campo 'Contraseña' es obligatorio.");
        } else if (password.length < 8) {
            errores.push("La contraseña debe tener al menos 8 caracteres.");
        }

        // Manejo de errores
        if (errores.length > 0) {
            event.preventDefault(); // Evita el envío del formulario
            alert(errores.join('\n')); // Muestra todos los errores
        } else {
            // Si no hay errores se envía el formulario
            form.submit();
        }
    });
});
