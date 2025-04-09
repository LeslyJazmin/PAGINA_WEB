<?php
$mensaje = "";

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "usuario");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener los cursos desde la base de datos
$query_cursos = "SELECT id_curso, nombre_curso FROM cursos";
$resultado_cursos = $conexion->query($query_cursos);

// Verificar si se enviaron los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar los datos enviados por el formulario y convertir el DNI a mayúsculas
    $DNI = strtoupper($_POST['dni']);
    $contrasena = $_POST['contrasena'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $sexo = $_POST['sexo'];
    $id_curso = $_POST['id_curso'];  // Capturar el ID del curso

    // Verificar si el estudiante ya está registrado
    $stmt_check = $conexion->prepare("SELECT DNI FROM estudiantes WHERE DNI = ?");
    $stmt_check->bind_param("s", $DNI);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        // Si el estudiante ya está registrado, solo registrar la inscripción
        $stmt_inscripcion = $conexion->prepare("INSERT INTO inscripciones (DNI, id_curso) VALUES (?, ?)");
        $stmt_inscripcion->bind_param("si", $DNI, $id_curso);

        if ($stmt_inscripcion->execute()) {
            $mensaje = "Estudiante ya registrado. Inscripción en el nuevo curso realizada correctamente.";
        } else {
            $mensaje = "Error al registrar la inscripción: " . $stmt_inscripcion->error;
        }
    } else {
        // Si el estudiante no está registrado, proceder a registrar todo
        $conexion->begin_transaction();
        try {
            // Insertar un nuevo estudiante
            $stmt = $conexion->prepare("INSERT INTO estudiantes (DNI, contrasena, nombre, correo, telefono, sexo) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $DNI, $contrasena, $nombre, $correo, $telefono, $sexo);

            if (!$stmt->execute()) {
                throw new Exception("Error al insertar estudiante: " . $stmt->error);
            }

            // Insertar la inscripción
            $stmt_inscripcion = $conexion->prepare("INSERT INTO inscripciones (DNI, id_curso) VALUES (?, ?)");
            $stmt_inscripcion->bind_param("si", $DNI, $id_curso);

            if (!$stmt_inscripcion->execute()) {
                throw new Exception("Error al insertar inscripción: " . $stmt_inscripcion->error);
            }

            $conexion->commit();
            $mensaje = "Estudiante agregado e inscrito correctamente.";
        } catch (Exception $e) {
            $conexion->rollback();
            $mensaje = "Error: " . $e->getMessage();
        }
    }

    // Cerrar los statements y la conexión
    $stmt_check->close();
    if (isset($stmt)) $stmt->close();
    if (isset($stmt_inscripcion)) $stmt_inscripcion->close();
    $conexion->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar nuevo estudiante</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="agregar_estudiante.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.body.style.zoom = "95%";
        if (window.matchMedia("(max-width: 480px)").matches) {
            document.body.style.zoom = "55%";
        }
    });

    window.addEventListener('resize', function() {
        if (window.matchMedia("(max-width: 480px)").matches) {
            document.body.style.zoom = "55%";
        } else {
            document.body.style.zoom = "95%";
        }
    });
</script>
<div class="container">
        <form action="" method="POST">
        <h2>FORMULARIO PARA INSERTAR ESTUDIANTE</h2>
        <label for="dni">DNI:</label>
        <input type="text" id="dni" name="dni" required><br>

        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required><br>

        <label for="nombre">Nombre completo del Estudiante:</label>
        <input type="text" id="nombre" name="nombre" required><br>

        <label for="correo">Correo Electrónico:</label>
        <input type="email" id="correo" name="correo" required><br>

        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" required><br>

        <label for="sexo">Sexo:</label>
        <select id="sexo" name="sexo" required>
            <option value="Femenino">Femenino</option>
            <option value="Masculino">Masculino</option>
        </select><br>

        <label for="id_curso">Nombre del Curso:</label>
        <select id="id_curso" name="id_curso" required>
            <?php
                // Mostrar los cursos obtenidos de la base de datos
                if ($resultado_cursos->num_rows > 0) {
                    while ($row = $resultado_cursos->fetch_assoc()) {
                        echo "<option value='" . $row['id_curso'] . "'>" . $row['nombre_curso'] . "</option>";
                    }
                }
            ?>
        </select><br>
        <div class="form-buttons">
        <input type="submit" value="Agregar Estudiante">
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
                <button onclick="agregarNuevo()">Agregar de nuevo</button>
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
            window.location.href = "auladmi.php";  // Redirige a la página de administración
        }

        // Función para recargar la página y agregar un nuevo estudiante
        function agregarNuevo() {
            window.location.href = "";
        }
    </script>
</body>
</html>
