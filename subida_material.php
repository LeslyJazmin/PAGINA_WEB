<?php
session_start(); // Iniciar sesión

// Variable para mensajes
$mensaje = null;
$tipo_mensaje = ''; // Para indicar si el mensaje es de éxito o error

// Conexión a la base de datos (ajusta tus credenciales si es necesario)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuario";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// Si se hace un POST con el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $link_material = $_POST['link_material'];
    $link_video = $_POST['link_video'];
    $id_curso = $_POST['id_curso'];

    // Verificar si ya existe un registro para el curso
    $sql_check = "SELECT * FROM material WHERE id_curso = :id_curso";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->bindParam(':id_curso', $id_curso);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        // Si ya existe, actualizar los enlaces en la base de datos
        $sql_update = "UPDATE material
                       SET link_curso_material = :link_material, link_curso_video = :link_video
                       WHERE id_curso = :id_curso";
        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->bindParam(':id_curso', $id_curso);
        $stmt_update->bindParam(':link_material', $link_material);
        $stmt_update->bindParam(':link_video', $link_video);
        if ($stmt_update->execute()) {
            $mensaje = "Datos actualizados correctamente.";
            $tipo_mensaje = 'success';
        } else {
            $mensaje = "Error al actualizar los datos.";
            $tipo_mensaje = 'error';
        }
    } else {
        // Si no existe, guardar el nuevo enlace en la base de datos
        $sql_insert = "INSERT INTO material (id_curso, link_curso_material, link_curso_video)
                       VALUES (:id_curso, :link_material, :link_video)";
        $stmt_insert = $pdo->prepare($sql_insert);
        $stmt_insert->bindParam(':id_curso', $id_curso);
        $stmt_insert->bindParam(':link_material', $link_material);
        $stmt_insert->bindParam(':link_video', $link_video);
        if ($stmt_insert->execute()) {
            $mensaje = "Datos guardados correctamente.";
            $tipo_mensaje = 'success';
        } else {
            $mensaje = "Error al guardar los datos.";
            $tipo_mensaje = 'error';
        }
    }
}

// Obtener los datos existentes si es necesario (para mostrar al cargar la página)
$current_data = [];
$id_curso_seleccionado = isset($_POST['id_curso']) ? $_POST['id_curso'] : 2; // Valor por defecto o el seleccionado
$sql_select = "SELECT link_curso_material, link_curso_video FROM material WHERE id_curso = :id_curso";
$stmt_select = $pdo->prepare($sql_select);
$stmt_select->bindParam(':id_curso', $id_curso_seleccionado);
$stmt_select->execute();
$current_data = $stmt_select->fetch(PDO::FETCH_ASSOC);

// Obtener todos los cursos disponibles para el campo 'id_curso'
$cursos = [];
$sql_cursos = "SELECT id_curso, nombre_curso FROM cursos"; // Asegúrate de tener una tabla 'cursos' con id_curso y nombre_curso
$stmt_cursos = $pdo->query($sql_cursos);
$cursos = $stmt_cursos->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir material</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="agregar_estudiante.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <form action="" method="POST">
            <h2>FORMULARIO PARA SUBIR MATERIAL</h2>
            <div class="form-group">
                <label for="id_curso">Selecciona el curso:</label>
                <select name="id_curso" id="id_curso" required>
                    <?php foreach ($cursos as $curso): ?>
                        <option value="<?php echo $curso['id_curso']; ?>" <?php echo (isset($current_data['id_curso']) && $current_data['id_curso'] == $curso['id_curso']) ? 'selected' : ''; ?>>
                            <?php echo $curso['nombre_curso']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="link_material">Link del material:</label>
                <input type="url" id="link_material" name="link_material" required
                       style="width: 660px; padding: 10px; margin: 10px 0; border: 2px solid #ffffff; border-radius: 5px; background-color: #e6f2ff; font-size: 14px; color: #333;">
            </div>

            <div class="form-group">
                <label for="link_video">Link del video:</label>
                <input type="url" id="link_video" name="link_video" required
                       style="width: 660px; padding: 10px; margin: 10px 0; border: 2px solid #ffffff; border-radius: 5px; background-color: #e6f2ff; font-size: 14px; color: #333;">
            </div>

            <div class="form-buttons">
                <input type="submit" name="subir" value="Subir">
                <button type="button" onclick="cerrarFormulario()">Cerrar</button>
            </div>
        </form>

        <div id="modal" class="modal" style="display: <?php echo !empty($mensaje) ? 'flex' : 'none'; ?>;">
            <div class="modal-content">
                <p id="modal-message"><?php echo $mensaje; ?></p>
                <div class="modal-buttons">
                    <button onclick="cerrarFormulario()">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para cerrar el modal (ahora también cierra el formulario)
        function cerrarModal() {
            window.location.href = "inicio_admin.php"; // Redirige a la página de administración
        }

        // Función para cerrar el formulario
        function cerrarFormulario() {
            window.location.href = "inicio_admin.php"; // Redirige a la página de administración
        }

        <?php if (!empty($mensaje)): ?>
            // Mostrar el modal automáticamente si hay un mensaje
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('modal').style.display = 'flex';
            });
        <?php endif; ?>
    </script>
</body>
</html>