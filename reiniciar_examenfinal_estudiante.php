<?php
$mensaje = "";
// Verificar si se enviaron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar el DNI e id_curso del formulario
    $DNI = strtoupper($_POST['dni']);
    $id_curso = $_POST['id_curso'];

    // Conexión a la base de datos
    $conexion = new mysqli("localhost", "root", "", "usuario");

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }

    // Iniciar transacción
    $conexion->begin_transaction();

    try {
        // Actualizar el campo de examen final y los intentos
        $sql_reiniciar = "UPDATE inscripciones SET examen_final = NULL, `intentose1` = NULL, `intentose2` = NULL, `intentose3` = NULL WHERE DNI = ? AND id_curso = ?";
        $stmt_reiniciar = $conexion->prepare($sql_reiniciar);
        $stmt_reiniciar->bind_param("si", $DNI, $id_curso);

        if ($stmt_reiniciar->execute()) {
            $conexion->commit();
            $mensaje = "El examen y todos los intentos han sido reiniciados correctamente.";
        } else {
            throw new Exception("Error al reiniciar el examen y los intentos: " . $stmt_reiniciar->error);
        }
    } catch (Exception $e) {
        $conexion->rollback();
        $mensaje = "Error: " . $e->getMessage();
    }

    // Cerrar la conexión y los statements
    $stmt_reiniciar->close();
    $conexion->close();
} // Cierre del if inicial
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario para reiniciar examen final del estudiante</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="agregar_estudiante.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="container">
    <form action="" method="POST">
        <h2>FORMULARIO PARA REINICIAR EXAMEN FINAL DEL ESTUDIANTE</h2>
        <label for="dni">DNI del Estudiante:</label>
        <input type="text" id="dni" name="dni" required><br>

        <label for="id_curso">Nombre del Curso:</label>
        <select id="id_curso" name="id_curso" required>
            <option value="1">Soldadura con arco eléctrico electrodo revestido</option>
            <option value="2">Primeros Auxilios</option>
            <option value="3">Uso y manejo de Extintores</option>
            <option value="4">IPERC</option>
        </select><br>

        <div class="form-buttons">
            <input type="submit" value="Reiniciar examen final">
            <!-- Botón para cerrar el formulario -->
            <button type="button" onclick="cerrarFormulario()">Cerrar</button>
        </div>
    </form>

    <!-- Modal de confirmación -->
    <div id="modal" class="modal" style="display: <?php echo !empty($mensaje) ? 'flex' : 'none'; ?>;">
        <div class="modal-content">
            <p id="modal-message"><?php echo $mensaje; ?></p>
            <div class="modal-buttons">
                <button onclick="cerrarModal()">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
        // Función para cerrar el modal
        function cerrarModal() {
            window.location.href = "auladmi.php";
        }
        
        // Función para cerrar el formulario
        function cerrarFormulario() {
            window.location.href = "auladmi.php";
        }
    </script>
</div>
</body>
</html>