<?php
$mensaje = "";

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "usuario");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener todos los cursos
$cursos = [];
$resultado = $conexion->query("SELECT id_curso, nombre_curso FROM cursos");
if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $cursos[] = $fila;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $DNI = strtoupper($_POST['dni']);
    $id_curso = $_POST['id_curso'];

    // Verificar si el estudiante está inscrito en ese curso
    $stmt_check = $conexion->prepare("SELECT * FROM inscripciones WHERE DNI = ? AND id_curso = ?");
    $stmt_check->bind_param("si", $DNI, $id_curso);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows == 0) {
        $mensaje = "El estudiante no está inscrito en ese curso.";
    } else {
        $stmt_delete_inscripcion = $conexion->prepare("DELETE FROM inscripciones WHERE DNI = ? AND id_curso = ?");
        $stmt_delete_inscripcion->bind_param("si", $DNI, $id_curso);
        if ($stmt_delete_inscripcion->execute()) {
            $mensaje = "Inscripción eliminada correctamente.";
        } else {
            $mensaje = "Error al eliminar inscripción: " . $stmt_delete_inscripcion->error;
        }
        $stmt_delete_inscripcion->close();
    }

    $stmt_check->close();
}
?>
<!DOCTYPE html>
<html lang="es">
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
    <h2>FORMULARIO PARA ELIMINAR INSCRIPCIÓN DE CURSO</h2>

    <div class="form-group">
        <label for="dni">DNI del Estudiante:</label>
        <input type="text" id="dni" name="dni" required>
    </div>

    <div class="form-group">
        <label for="id_curso">Curso a eliminar:</label>
        <select id="id_curso" name="id_curso" required>
            <option value="" disabled selected>Seleccione un curso</option>
            <?php foreach ($cursos as $curso): ?>
                <option value="<?php echo $curso['id_curso']; ?>">
                    <?php echo htmlspecialchars($curso['nombre_curso']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-buttons">
        <input type="submit" value="Eliminar Inscripción">
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
</div>
</body>
</html>

<?php
// Cerrar la conexión
$conexion->close();
?>
