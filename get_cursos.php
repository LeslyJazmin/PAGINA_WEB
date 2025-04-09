<?php
$conexion = new mysqli("localhost", "root", "", "usuario");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

$resultado = $conexion->query("SELECT nombre_curso FROM cursos");

while ($fila = $resultado->fetch_assoc()) {
    echo "<option value='" . htmlspecialchars($fila['nombre_curso']) . "'>" . htmlspecialchars($fila['nombre_curso']) . "</option>";
}

$conexion->close();
?>
