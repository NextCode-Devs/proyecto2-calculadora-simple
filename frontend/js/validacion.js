document.addEventListener('DOMContentLoaded', () => { //Carga los datos del formulario antes de ejecutar el JS  

        const form = document.getElementById('iniciosesion');

    form.addEventListener('submit', function(event) { //Ejecuta la función en el boton 
        
        
        //const usuario = document.getElementById('usuario').value; 
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        let errores = []; //Para guardar los mensajes de error 

       /* if (usuario === '') { //Si esta vacio se carga al array errores 
            errores.push("El campo 'Usuario' es obligatorio.");
        }*/ 

        if (email === '') { //Lanza error si no se carga 
            errores.push("El campo 'Email' es obligatorio.");
        } else {
            const emailValido = email.includes('@') && email.includes('.');
        if (!emailValido) { // Si escribimos mas el email, es invalido 
                errores.push("El formato del email no es válido.");
            }
        }

        if (password === '') {
            errores.push("El campo 'Contraseña' es obligatorio.");
        } else if (password.length < 8) { 
            errores.push("La contraseña debe tener al menos 8 caracteres."); //Error si no cumple o es identico
        }

        if (errores.length > 0) {
            event.preventDefault(); //No se envia el formulario 
            alert(errores.join('\n'));
            
        } else { 
            form.submit();
        }
    });
});
