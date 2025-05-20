<?php
include_once('../config/conexion.php');

// Crear conexión
$conn = conectarDB();

// Obtener datos del formulario
$nombre = $_POST['nombre_usuario'];  // <-- CORREGIDO
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Preparar la consulta
$sql = "INSERT INTO usuarios (nombre_usuario, email, contraseña) VALUES (?, ?, ?)";  // <-- nombres reales de la BD
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("sss", $nombre, $email, $password);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: ../../index.php");  // Redirige a la raíz si fue exitoso
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header("Location: ../../../frontend/html/iniciosesion.html");  // <-- Ruta corregida
        exit();
    }
} else {
    echo "Error en la preparación de la consulta.";
}
?>




