<?php
// Conexión al servidor
$servername = "localhost";
$username = "root";
$password = "";

// Conectar al servidor (ambas bases deben estar en el mismo servidor para esta conexión única)
$conn = new mysqli($servername, $username, $password);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
// Ejecutar la consulta SQL
$sql = "SELECT admin.nombre_usuario, usuario.estudiantes.nombre 
        FROM admin.admin
        INNER JOIN usuario.estudiantes ON admin.nombre_usuario = usuario.estudiantes.nombre_usuario";

// Realizar la consulta
$result = $conn->query($sql);

// Verificar si hay resultados
if ($result->num_rows > 0) {
    // Mostrar los datos de cada fila
    while ($row = $result->fetch_assoc()) {
        echo "Nombre de Usuario: " . $row["nombre_usuario"] . " - Nombre del Estudiante: " . $row["nombre"] . "<br>";
    }
} else {
    echo "No se encontraron resultados.";
}

// Cerrar la conexión
$conn->close();
?>
