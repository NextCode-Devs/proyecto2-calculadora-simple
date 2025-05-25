<?php
include_once('config/conexion.php');

$tipo = $_POST['tipo'];
$monto = $_POST['monto'];
$descripcion = $_POST['descripcion'];
$nota = !empty($_POST['nota']) ? $_POST['nota'] : null;

$stmt = $conexion->prepare("INSERT INTO transaccion (tipo, monto, descripcion, nota) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sdss", $tipo, $monto, $descripcion, $nota);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Transacción guardada con éxito.";
} else {
    echo "Error al guardar.";
}

$stmt->close();
$conexion->close();
?>
