<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Finanzas Personales</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link href="../../frontend/css/header.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700&display=swap" rel="stylesheet"/>

  <style>
    .titulo-finanzas {
      font-family: 'Poppins', sans-serif;
      font-size: 1.5rem;
      font-weight: 900;
      color: #2c3e50;
      letter-spacing: 0.5px;
    }

    .boton-flotante {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 1000;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      font-size: 28px;
      background-color: #0d6efd;
      color: white;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.25);
      transition: all 0.3s ease;
    }

    .boton-flotante:hover {
      background-color:rgb(166, 178, 197);
      transform: scale(1.1);
    }

    #calculadoraFlotante {
      position: fixed;
      bottom: 100px;
      right: 20px;
      z-index: 1000;
      width: 270px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.3);
      display: none;
    }

    .calculadora .botones {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 5px;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
      <a class="navbar-brand custom-brand titulo-finanzas" href="index.php">🧾 FINANZAS PERSONALES</a>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link nav-link-custom" href="index.php">
              <i class="fas fa-home me-1"></i> Inicio
            </a>
          </li>
          <li class="nav-item">
            <form action="frontend/html/logout.html" method="post">
              <button type="submit" class="nav-link nav-link-custom logout-btn">
                <i class="fas fa-sign-out-alt me-1"></i> Cerrar sesión
              </button>
            </form>
          </li>
        </ul>
      </div>
    </div>

    <button id="toggleThemeBtn" class="btn btn-secondary">
      <i id="iconoTema" class="fas fa-sun"></i>
    </button>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <i class="fas fa-bars"></i>
    </button>
  </nav>

  <div id="calculadoraFlotante">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Calculadora</h5>
        <button type="button" class="btn-close" aria-label="Cerrar" onclick="document.getElementById('calculadoraFlotante').style.display='none'"></button>
      </div>
      <div class="card-body">
        <div class="calculadora">
          <input type="text" id="pantalla" class="form-control mb-2" disabled>
          <div class="botones">
            <button class="btn btn-secondary" value="7">7</button>
            <button class="btn btn-secondary" value="8">8</button>
            <button class="btn btn-secondary" value="9">9</button>
            <button class="btn btn-warning operador" value="/">/</button>
            <button class="btn btn-secondary" value="4">4</button>
            <button class="btn btn-secondary" value="5">5</button>
            <button class="btn btn-secondary" value="6">6</button>
            <button class="btn btn-warning operador" value="*">*</button>
            <button class="btn btn-secondary" value="1">1</button>
            <button class="btn btn-secondary" value="2">2</button>
            <button class="btn btn-secondary" value="3">3</button>
            <button class="btn btn-warning operador" value="-">-</button>
            <button class="btn btn-secondary" value="0">0</button>
            <button class="btn btn-secondary" value=".">.</button>
            <button class="btn btn-success igual" value="=">=</button>
            <button class="btn btn-warning operador" value="+">+</button>
            <button type="button" class="btn btn-danger limpiar" value="C" style="grid-column: span 4;">C</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <button id="btnMostrarCalculadora" class="boton-flotante">
    <span style="font-size: 2.1rem;">🧮</span>
  </button>

  <script>
    document.getElementById('toggleThemeBtn').addEventListener('click', () => {
      const html = document.documentElement;
      const icono = document.getElementById('iconoTema');
      const currentTheme = html.getAttribute('data-bs-theme');
      
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
      html.setAttribute('data-bs-theme', newTheme);
      localStorage.setItem('theme', newTheme);

      icono.classList.toggle('fa-moon', newTheme === 'dark');
      icono.classList.toggle('fa-sun', newTheme === 'light');
    });

    document.getElementById("btnMostrarCalculadora").addEventListener("click", function () {
      const calc = document.getElementById("calculadoraFlotante");
      calc.style.display = (calc.style.display === "none" || calc.style.display === "") ? "block" : "none";
    });

    window.addEventListener('DOMContentLoaded', () => {
      const savedTheme = localStorage.getItem('theme');
      const html = document.documentElement;
      const icono = document.getElementById('iconoTema');

      if (savedTheme) {
        html.setAttribute('data-bs-theme', savedTheme);
        icono.classList.toggle('fa-moon', savedTheme === 'dark');
        icono.classList.toggle('fa-sun', savedTheme === 'light');
      }
    });
  </script>
</body>
</html>
