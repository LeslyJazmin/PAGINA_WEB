<?php
$mensaje = "";
$dni_existente = false;
$nombre_estudiante = "";

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "usuario");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener los cursos desde la base de datos
$query_cursos = "SELECT id_curso, nombre_curso FROM cursos";
$resultado_cursos = $conexion->query($query_cursos);

// Verificar si se ha ingresado un DNI para la verificación inicial
if (isset($_POST['verificar_dni'])) {
    $DNI_verificar = strtoupper($_POST['dni_verificar']);
    $stmt_check = $conexion->prepare("SELECT DNI, nombre FROM estudiantes WHERE DNI = ?");
    $stmt_check->bind_param("s", $DNI_verificar);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $dni_existente = true;
        $stmt_check->bind_result($dni_existente_bd, $nombre_estudiante);
        $stmt_check->fetch();
    }
    $stmt_check->close();
}

// Verificar si se enviaron los datos del formulario principal
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['verificar_dni'])) {
    // Capturar los datos enviados por el formulario y convertir el DNI a mayúsculas
    $id_curso = $_POST['id_curso'];
    if (isset($_POST['dni_no_editable'])) {
        $DNI = strtoupper($_POST['dni_no_editable']);
    } else {
        $DNI = strtoupper($_POST['dni']); // Solo si es nuevo estudiante
    }

    // Si el DNI ya existía, solo se procesa la inscripción al nuevo curso
    if (isset($_POST['dni_no_editable'])) {
        $DNI = strtoupper($_POST['dni_no_editable']);
        // Verificar si ya está inscrito en el curso
        $stmt_verifica_inscripcion = $conexion->prepare("SELECT * FROM inscripciones WHERE DNI = ? AND id_curso = ?");
        $stmt_verifica_inscripcion->bind_param("si", $DNI, $id_curso);
        $stmt_verifica_inscripcion->execute();
        $stmt_verifica_inscripcion->store_result();

        if ($stmt_verifica_inscripcion->num_rows > 0) {
            $mensaje = "El estudiante ya está inscrito en este curso.";
        } else {
            // Registrar la inscripción
            $stmt_inscripcion = $conexion->prepare("INSERT INTO inscripciones (DNI, id_curso) VALUES (?, ?)");
            $stmt_inscripcion->bind_param("si", $DNI, $id_curso);

            if ($stmt_inscripcion->execute()) {
                $mensaje = "Inscripción en el nuevo curso realizada correctamente.";
            } else {
                $mensaje = "Error al registrar la inscripción: " . $stmt_inscripcion->error;
            }
        }
        $stmt_verifica_inscripcion->close();
    } else {
        // Si el DNI no existía, proceder a registrar todo el estudiante e inscribirlo
        $contrasena = $_POST['contrasena'];
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $telefono = $_POST['telefono'];
        $sexo = $_POST['sexo'];

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
        if (isset($stmt)) $stmt->close();
        if (isset($stmt_inscripcion)) $stmt_inscripcion->close();
    }
}

$conexion->close();
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
    <style>
         .form-oculto {
            display: none;
        
        }
        .form-container {
            display: flex;
            justify-content: center;
        }

        .container-inicial label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .container-inicial input[type="text"] {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 3px;
            box-sizing: border-box;
        }

        .container-inicial button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 16px;
        }

        .container-inicial button:hover {
            background-color: #0056b3;
        }

        .form-oculto {
            display: none;
        }

        input:read-only {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
        .botones-centrados {
            display: flex;
            justify-content: center;
            gap: 10px; /* Espacio entre botones */
            margin-top: 10px;
        }

    </style>
</head>
<body>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.body.style.zoom = "95%";
        if (window.matchMedia("(max-width: 480px)").matches) {
            document.body.style.zoom = "55%";
        }

        // Mostrar el formulario correcto si ya se verificó DNI (desde PHP)
        <?php if (isset($_POST['verificar_dni'])): ?>
            <?php if ($dni_existente): ?>
                document.getElementById('formulario-curso').style.display = 'block';
                document.getElementById('nombre-curso').value = "<?php echo $nombre_estudiante; ?>";
                document.getElementById('dni-curso').value = "<?php echo strtoupper($_POST['dni_verificar']); ?>";
            <?php else: ?>
                document.getElementById('formulario-completo').style.display = 'block';
                document.getElementById('dni-completo').value = "<?php echo strtoupper($_POST['dni_verificar']); ?>";
            <?php endif; ?>
            // Ocultar formulario inicial (verificación DNI)
            document.querySelector('.container-inicial').style.display = 'none';
        <?php else: ?>
            // Por defecto mostrar solo el formulario de verificación DNI
            document.querySelector('.container-inicial').style.display = 'block';
        <?php endif; ?>
    });

    window.addEventListener('resize', function() {
        if (window.matchMedia("(max-width: 480px)").matches) {
            document.body.style.zoom = "55%";
        } else {
            document.body.style.zoom = "95%";
        }
    });

    function cerrarFormulario() {
        window.location.href = "inicio_admin.php";
    }

    function cerrarModal() {
        window.location.href = "inicio_admin.php";
    }

    function agregarNuevo() {
        window.location.href = ""; // recarga para volver a iniciar el flujo
    }
</script>

<div class="container-inicial">
    <h2>Verificar DNI del Estudiante</h2>
    <form method="POST">
        <label for="dni_verificar">DNI:</label>
        <input type="text" id="dni_verificar" name="dni_verificar" required>

        <!-- Contenedor para centrar los botones -->
        <div class="botones-centrados">
            <button type="submit" name="verificar_dni">Verificar</button>
            <button type="button" onclick="cerrarFormulario()">Cerrar</button>
        </div>
    </form>
</div>


<div class="container form-oculto" id="formulario-completo">
  <form action="" method="POST">
        <h2>FORMULARIO PARA INSERTAR ESTUDIANTE</h2>
        <label for="dni-completo">DNI:</label>
        <input type="text" id="dni-completo" name="dni" required readonly><br>

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
            if ($resultado_cursos->num_rows > 0) {
                while ($row = $resultado_cursos->fetch_assoc()) {
                    echo "<option value='" . $row['id_curso'] . "'>" . $row['nombre_curso'] . "</option>";
                }
            }
            ?>
        </select><br>
        <div class="form-buttons">
            <input type="submit" value="Agregar Estudiante">
            <button type="button" onclick="cerrarFormulario()">Cerrar</button>
        </div>
    </form>
</div>

<div class="container form-oculto" id="formulario-curso">
    <form action="" method="POST">
        <h2>Inscribir a Curso Existente</h2>
        <label for="dni-curso">DNI:</label>
        <input type="text" id="dni-curso" name="dni_no_editable" readonly><br>

        <label for="nombre-curso">Nombre del Estudiante:</label>
        <input type="text" id="nombre-curso" name="nombre_no_editable" readonly><br>

        <label for="id_curso">Seleccionar Curso:</label>
        <select id="id_curso" name="id_curso" required>
            <?php
            $resultado_cursos->data_seek(0);
            if ($resultado_cursos->num_rows > 0) {
                while ($row = $resultado_cursos->fetch_assoc()) {
                    echo "<option value='" . $row['id_curso'] . "'>" . $row['nombre_curso'] . "</option>";
                }
            }
            ?>
        </select><br>
        <div class="form-buttons">
            <input type="submit" value="Inscribir al Curso">
            <button type="button" onclick="cerrarFormulario()">Cerrar</button>
        </div>
    </form>
</div>

<div id="modal" class="modal" style="display: <?php echo !empty($mensaje) ? 'flex' : 'none'; ?>;">
    <div class="modal-content">
        <p id="modal-message"><?php echo $mensaje; ?></p>
        <div class="modal-buttons">
            <button onclick="cerrarModal()">Cerrar</button>
            <button onclick="agregarNuevo()">Agregar de nuevo</button>
        </div>
    </div>
</div>

</body>
</html>