<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'finanzas_personales');

function conectarDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Función de seguridad básica
function sanitizar($dato) {
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}
?>
