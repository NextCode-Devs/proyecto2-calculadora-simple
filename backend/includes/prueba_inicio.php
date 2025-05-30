<?php
require_once('../config/conexion.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? strtolower(trim($_POST['email'])) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if ($email === '' || $password === '') {
        die('Email y contraseña son obligatorios.');
    }

    $conn = conectarDB();
    if (!$conn) {
        die('Error de conexión a la base de datos.');
    }

    // Consulta preparada correctamente
    $stmt = $conn->prepare("SELECT id, nombre_usuario, email, contraseña FROM usuarios WHERE LOWER(email) = ?");
    if (!$stmt) {
        die('Error en la consulta.');
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();

        if (password_verify($password, $row['contraseña'])) {
            // Login exitoso
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['usuario_nombre'] = $row['nombre_usuario'];
            $_SESSION['usuario_email'] = $row['email'];

            header("Location: ../../index.php");
            exit;
        } else {
            echo "Contraseña incorrecta. <br>";
            echo "<button onclick=\"window.location.href='../../index.php'\">Volver</button>";
        }
    } else {
        echo "Usuario no encontrado. <br>";
        echo "<button onclick=\"window.location.href='../../index.php'\">Volver</button>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ../index.php");
    exit;
}