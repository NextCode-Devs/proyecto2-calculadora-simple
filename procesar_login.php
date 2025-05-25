<?php
session_start();
require_once 'config/conexion.php';
$conn = conectarDB();

if (!isset($_POST['email']) || !isset($_POST['password'])) {
    echo "Por favor, completa todos los campos.";
    exit();
}

$email = trim($_POST['email']);
$password = trim($_POST['password']);

if ($email === '' || $password === '') {
    echo "Por favor, completa todos los campos.";
    exit();
}

$query = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo "Error en la consulta: " . $conn->error;
    exit();
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $usuario = $result->fetch_assoc();

    if ($usuario['password'] === $password) {
        $_SESSION['nombre_usuario'] = $usuario['Nombre'];
        $_SESSION['email_usuario'] = $usuario['email'];
        header('Location: index.php');
        exit();
    } else {
        echo "Contraseña incorrecta";
    }
} else {
    echo "Email no encontrado";
}
?>

