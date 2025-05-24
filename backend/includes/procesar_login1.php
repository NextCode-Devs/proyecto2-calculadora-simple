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

<<<<<<< HEAD:includes/procesar_login1.php
    // Agrega id en la consulta
    $stmt = $conn->prepare("SELECT id, nombre_usuario, email, contraseña FROM usuarios WHERE LOWER(email) = ?");
=======
    $stmt = $conn->prepare("SELECT email, contraseña FROM usuarios WHERE LOWER(email) = ?");
>>>>>>> ec44bb649c9998f31ae0ae40996cf70b8ca162ea:backend/includes/procesar_login1.php
    if (!$stmt) {
        die('Error en la consulta.');
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();

        if (password_verify($password, $row['contraseña'])) {
<<<<<<< HEAD:includes/procesar_login1.php
            // Guarda el ID real del usuario (clave para filtrar después)
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['usuario_nombre'] = $row['nombre_usuario'];
            $_SESSION['usuario_email'] = $row['email'];

=======
            $_SESSION['usuario_email'] = $row['email'];
>>>>>>> ec44bb649c9998f31ae0ae40996cf70b8ca162ea:backend/includes/procesar_login1.php
            header("Location: ../../index.php");
            exit;
        } else {
            echo "Contraseña incorrecta.";
        }
    } else {
        echo "Usuario no encontrado.";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ../index.php");
    exit;
}

<<<<<<< HEAD:includes/procesar_login1.php

=======
>>>>>>> ec44bb649c9998f31ae0ae40996cf70b8ca162ea:backend/includes/procesar_login1.php
