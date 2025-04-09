<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni = $_POST['dni'];
    $field = $_POST['field']; 
    $value = $_POST['value']; 

    // Verifica que el campo que se va a actualizar es 'contrasena' o 'telefono'
    if ($field !== 'contrasena' && $field !== 'telefono') {
        echo "El campo no es válido para la actualización.";
        exit;
    }

    // Conexión a la base de datos
    $mysqli = new mysqli("localhost", "root", "", "usuario");
    if ($mysqli->connect_error) {
        die("Conexión fallida: " . $mysqli->connect_error);
    }

    // Actualiza el campo correspondiente en la tabla estudiantes
    $sql = "UPDATE estudiantes SET $field = ? WHERE DNI = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ss', $value, $dni);

    if ($stmt->execute()) {
        echo "Actualización exitosa.";
    } else {
        echo "Error al actualizar los datos: " . $stmt->error;
    }

    // Cierra la conexión
    $stmt->close();
    $mysqli->close();
}
?>
