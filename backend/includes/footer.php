<!DOCTYPE html>
<html lang="es" >
<head>
    <meta charset="UTF-8">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<footer class="footer-minimal pt-5 pb-4">
  <div class="container">
    <div class="row">
      <!-- Columna 1 -->
      <div class="col-md-3">
        <h6 class="footer-title">EMPRESA</h6>
        <ul class="list-unstyled">
          <li><a href="#" class="footer-link">Sobre nosotros</a></li>
        </ul>
      </div>
      <!-- Columna 2 -->
      <div class="col-md-3">
        <h6 class="footer-title">AYUDA Y SOPORTE</h6>
        <ul class="list-unstyled">
          <li><a href="#" class="footer-link">Contactanos</a></li>
          <li><a href="#" class="footer-link">Soporte</a></li>
          <li><a href="#" class="footer-link">Desarrolladores</a></li>
        </ul>
      </div>
      <!-- Redes Sociales -->
      <div class="col-md-3 d-flex align-items-start justify-content-md-end justify-content-start mt-4 mt-md-0">
        <div>
          <a href="#" class="footer-link me-3"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="footer-link me-3"><i class="fab fa-linkedin-in"></i></a>
          <a href="#" class="footer-link me-3"><i class="fab fa-github"></i></a>
          <a href="#" class="footer-link me-3"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
    </div>
    <div class="text-center pt-3 mt-4 border-top">
      <small class="footer-copy">&copy; <?= date('Y') ?> Finanzas Personales. Todos los derechos reservados.</small>
    </div>
  </div>
</footer>
</body>
<style>
/* Estilos para modo claro (default) */
.footer-minimal {
  background-color: #fff;
  color: #333;
  border-top: 1px solid #ddd;
}

/* Estilos para modo oscuro */
html[data-bs-theme="dark"] .footer-minimal {
  background-color: #212529; /* fondo oscuro */
  color: #ccc;               /* texto claro */
  border-top: 1px solid #444;
}

html[data-bs-theme="dark"] .footer-link {
  color: #aaa;
}

html[data-bs-theme="dark"] .footer-link:hover {
  color: #fff;
}

html[data-bs-theme="dark"] .footer-title {
  color: #eee;
}

html[data-bs-theme="dark"] .footer-copy {
  color: #999;
}

</style>
