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
    // Capturar el DNI del estudiante
    $DNI = strtoupper($_POST['dni']);

    // Verificar si el estudiante existe
    $stmt_check = $conexion->prepare("SELECT DNI FROM estudiantes WHERE DNI = ?");
    $stmt_check->bind_param("s", $DNI);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows == 0) {
        $mensaje = "Este estudiante no está registrado.";
    } else {
        // Iniciar transacción
        $conexion->begin_transaction();

        try {
            // 1. Eliminar los exámenes relacionados con la inscripción del estudiante
            $stmt_delete_examen = $conexion->prepare("DELETE FROM examenes WHERE id_curso IN (SELECT id_curso FROM inscripciones WHERE DNI = ?)");
            $stmt_delete_examen->bind_param("s", $DNI);

            // Ejecutar la consulta y verificar si se eliminó correctamente
            if (!$stmt_delete_examen->execute()) {
                throw new Exception("Error al eliminar exámenes: " . $stmt_delete_examen->error);
            }

            // 2. Eliminar las inscripciones del estudiante
            $stmt_delete_inscripcion = $conexion->prepare("DELETE FROM inscripciones WHERE DNI = ?");
            $stmt_delete_inscripcion->bind_param("s", $DNI);

            // Ejecutar la consulta y verificar si se eliminó correctamente
            if (!$stmt_delete_inscripcion->execute()) {
                throw new Exception("Error al eliminar inscripción: " . $stmt_delete_inscripcion->error);
            }

            // 3. Eliminar al estudiante de la tabla estudiantes
            $stmt_delete_estudiante = $conexion->prepare("DELETE FROM estudiantes WHERE DNI = ?");
            $stmt_delete_estudiante->bind_param("s", $DNI);

            // Ejecutar la consulta y verificar si se eliminó correctamente
            if (!$stmt_delete_estudiante->execute()) {
                throw new Exception("Error al eliminar estudiante: " . $stmt_delete_estudiante->error);
            }

            // Si todo fue bien, confirmamos la transacción
            $conexion->commit();

            $mensaje = "Estudiante eliminado correctamente.";
        } catch (Exception $e) {
            // En caso de error, revertir la transacción
            $conexion->rollback();
            $mensaje = "Error: " . $e->getMessage();
        }
    }

    // Cerrar la conexión y los statements
    $stmt_check->close();
    if (isset($stmt_delete_estudiante)) {
        $stmt_delete_estudiante->close();
    }
    if (isset($stmt_delete_inscripcion)) {
        $stmt_delete_inscripcion->close();
    }
    if (isset($stmt_delete_examen)) {
        $stmt_delete_examen->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Estudiante</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="agregar_estudiante.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="container">
        <form action="" method="POST">
        <h2>FORMULARIO PARA ELIMINAR ESTUDIANTE</h2>
        <div class="form-group">
            <label for="dni">DNI del Estudiante:</label>
            <input type="text" id="dni" name="dni" required>
        </div>

        <div class="form-buttons">
            <input type="submit" value="Eliminar Estudiante">
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
            window.location.href = "inicio_admin.php";  // Redirige a la página de administración
        }

        // Función para cerrar el formulario
        function cerrarFormulario() {
            window.location.href = "inicio_admin.php";  // Redirige a la página de administración
        }
    </script>
</body>
</html>

<?php
// Cerrar la conexión
$conexion->close();
?>
