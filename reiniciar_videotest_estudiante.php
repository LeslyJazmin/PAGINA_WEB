<?php
$mensaje = "";

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "usuario");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Verificar si se enviaron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar el DNI e id_curso del formulario
    $DNI = strtoupper($_POST['dni']);
    $id_curso = $_POST['id_curso'];

    // Iniciar transacción
    $conexion->begin_transaction();

    try {
        // Verificar si el estudiante está inscrito en el curso
        $sql_verificar = "SELECT * FROM inscripciones WHERE DNI = ? AND id_curso = ?";
        $stmt_verificar = $conexion->prepare($sql_verificar);
        $stmt_verificar->bind_param("si", $DNI, $id_curso);
        $stmt_verificar->execute();
        $resultado = $stmt_verificar->get_result();

        if ($resultado->num_rows > 0) {
            // Si el estudiante está inscrito, reiniciar intentos y nota del videotest
            $sql_reiniciar = "UPDATE inscripciones SET intento1 = NULL, intento2 = NULL, intento3 = NULL, nota_videotest = 0 WHERE DNI = ? AND id_curso = ?";
            $stmt_reiniciar = $conexion->prepare($sql_reiniciar);
            $stmt_reiniciar->bind_param("si", $DNI, $id_curso);

            if ($stmt_reiniciar->execute()) {
                $conexion->commit();
                $mensaje = "Los intentos han sido reiniciados correctamente. Puedes volver a tomar el videotest.";
            } else {
                throw new Exception("Error al reiniciar los intentos: " . $stmt_reiniciar->error);
            }

            $stmt_reiniciar->close();
        } else {
            // Si no está inscrito, mostrar un mensaje de error
            $conexion->rollback();
            $mensaje = "No estás inscrito en este curso.";
        }
    } catch (Exception $e) {
        $conexion->rollback();
        $mensaje = "Error: " . $e->getMessage();
    }

    // Cerrar los statements
    $stmt_verificar->close();
}

// Obtener los cursos de la base de datos
$cursos_query = "SELECT id_curso, nombre_curso FROM cursos";
$cursos_result = $conexion->query($cursos_query);

// Cerrar la conexión
$conexion->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario para reiniciar videotest del estudiante</title>
    <link rel="stylesheet" href="agregar_estudiante.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="container">
    <form action="" method="POST">
        <h2>FORMULARIO PARA REINICIAR VIDEOTEST DEL ESTUDIANTE</h2>
        <label for="dni">DNI del Estudiante:</label>
        <input type="text" id="dni" name="dni" required><br>

        <label for="id_curso">Nombre del Curso:</label>
        <select id="id_curso" name="id_curso" required>
            <?php
            // Verificar si hay cursos disponibles y generar opciones
            if ($cursos_result->num_rows > 0) {
                while ($curso = $cursos_result->fetch_assoc()) {
                    echo "<option value='" . $curso['id_curso'] . "'>" . $curso['nombre_curso'] . "</option>";
                }
            } else {
                echo "<option value=''>No hay cursos disponibles</option>";
            }
            ?>
        </select><br>
        <div class="form-buttons">
            <input type="submit" value="Reiniciar videotest">
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
</body>
</html>
