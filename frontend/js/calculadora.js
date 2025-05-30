class Calculadora {
    constructor() {
        this.pantalla = document.getElementById('pantalla');
        this.botones = document.querySelectorAll('.calculadora .btn');
        this.operacionActual = '';
        this.init();
    }

    init() {
        this.botones.forEach(boton => {
            boton.addEventListener('click', () => this.manejarClick(boton.value));
        });
    }

    manejarClick(valor) {
        if (valor === 'C') {
            this.limpiarPantalla();
        } else if (valor === '=') {
            this.calcularResultado();
        } else {
            this.agregarValor(valor);
        }
    }

    limpiarPantalla() {
        this.operacionActual = '';
        this.actualizarPantalla();
    }

    calcularResultado() {
        try {
            // Reemplazar símbolos para evaluación segura
            const expresion = this.operacionActual
                .replace(/×/g, '*')
                .replace(/÷/g, '/');
            
            // Validación básica de seguridad
            if (!/^[\d+\-*/. ()]+$/.test(expresion)) {
                throw new Error('Expresión inválida');
            }
            
            const resultado = eval(expresion);
            this.operacionActual = resultado.toString();
            this.actualizarPantalla();
        } catch (error) {
            this.operacionActual = 'Error';
            this.actualizarPantalla();
            setTimeout(() => this.limpiarPantalla(), 1000);
        }
    }

    agregarValor(valor) {
        // Reemplazar símbolos para mejor visualización
        if (valor === '*') valor = '×';
        if (valor === '/') valor = '÷';
        
        this.operacionActual += valor;
        this.actualizarPantalla();
    }

    actualizarPantalla() {
        this.pantalla.value = this.operacionActual;
    }
}

// Iniciar calculadora cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    new Calculadora();
});