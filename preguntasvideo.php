<?php
// Conexión a la base de datos
$host = "localhost";
$user = "root"; 
$password = ""; 
$database = "usuario"; 

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Verificar si se seleccionó un curso
$id_curso = isset($_POST['id_curso']) && !empty($_POST['id_curso']) ? $_POST['id_curso'] : 
            (isset($_GET['id_curso']) && !empty($_GET['id_curso']) ? $_GET['id_curso'] : null);

// Si se envió el formulario para guardar cambios
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar'])) {
    if (isset($_POST['pregunta'])) {
        foreach ($_POST['pregunta'] as $pregunta_id => $nueva_pregunta) {
            // Evitar actualizar si la pregunta no cambió
            $sql = "SELECT pregunta FROM preguntas WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $pregunta_id);
            $stmt->execute();
            $stmt->bind_result($pregunta_actual);
            $stmt->fetch();
            $stmt->close();

            if ($pregunta_actual !== $nueva_pregunta) {
                $sql = "UPDATE preguntas SET pregunta = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $nueva_pregunta, $pregunta_id);
                $stmt->execute();
            }
        }
    }

    if (isset($_POST['opciones'])) {
        foreach ($_POST['opciones'] as $opcion_id => $nueva_opcion) {
            // Evitar actualizar si la opción no cambió
            $sql = "SELECT opcion FROM opciones WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $opcion_id);
            $stmt->execute();
            $stmt->bind_result($opcion_actual);
            $stmt->fetch();
            $stmt->close();

            if ($opcion_actual !== $nueva_opcion) {
                $sql = "UPDATE opciones SET opcion = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $nueva_opcion, $opcion_id);
                $stmt->execute();
            }
        }
    }

    if (isset($_POST['correcta'])) {
        foreach ($_POST['correcta'] as $pregunta_id => $opcion_correcta) {
            // Primero, desmarcar todas las opciones de esta pregunta
            $sql = "UPDATE opciones SET es_correcta = 0 WHERE pregunta_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $pregunta_id);
            $stmt->execute();

            // Luego, marcar la opción seleccionada como correcta
            $sql = "UPDATE opciones SET es_correcta = 1 WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $opcion_correcta);
            $stmt->execute();
        }
    }

    echo "<p class='mensaje'>¡Cambios guardados exitosamente!</p>";
}

// Obtener lista de cursos
$cursos = [];
$result = $conn->query("SELECT id_curso, nombre_curso FROM cursos");
while ($row = $result->fetch_assoc()) {
    $cursos[] = $row;
}

// Obtener preguntas y opciones si se seleccionó un curso
$preguntas = [];
if ($id_curso) {
    $sql = "SELECT p.id AS pregunta_id, p.pregunta, o.id AS opcion_id, o.opcion, o.es_correcta 
            FROM preguntas p 
            JOIN opciones o ON p.id = o.pregunta_id 
            WHERE p.id_curso = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_curso);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $preguntas[$row['pregunta_id']]['pregunta'] = $row['pregunta'];
        $preguntas[$row['pregunta_id']]['opciones'][] = [
            'opcion_id' => $row['opcion_id'],
            'opcion' => $row['opcion'],
            'es_correcta' => $row['es_correcta']
        ];
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar preguntas de Videotest</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="preguntasvideo.css">
</head>
<body>
    <div class="container">
        <h2>Seleccionar Curso</h2>
        <form method="POST">
            <label for="id_curso">Elige un curso:</label>
            <select name="id_curso" id="id_curso" required>
                <option value="">Selecciona un curso</option>
                <?php foreach ($cursos as $curso) : ?>
                    <option value="<?= $curso['id_curso'] ?>" <?= ($id_curso == $curso['id_curso']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($curso['nombre_curso']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <div class="botones">
                <button type="submit" class="cargar">Cargar Preguntas</button>
                <a href="inicio_admin.php" class="cerrar">Cerrar</a>
            </div>
        </form>

        <?php if ($id_curso && !empty($preguntas)) : ?>
            <h2>Editar Preguntas y Opciones</h2>
            <form method="POST">
                <input type="hidden" name="id_curso" value="<?= $id_curso ?>">
                <?php foreach ($preguntas as $pregunta_id => $datos) : ?>
                    <div class="pregunta">
                        <label>Pregunta:</label>
                        <input type="text" name="pregunta[<?= $pregunta_id ?>]" value="<?= htmlspecialchars($datos['pregunta']) ?>" required>
                        
                        <h4>Opciones:</h4>
                        <?php foreach ($datos['opciones'] as $opcion) : ?>
                            <div class="opcion">
                                <input type="text" name="opciones[<?= $opcion['opcion_id'] ?>]" value="<?= htmlspecialchars($opcion['opcion']) ?>" required>
                                <input type="radio" name="correcta[<?= $pregunta_id ?>]" value="<?= $opcion['opcion_id'] ?>" <?= $opcion['es_correcta'] ? 'checked' : '' ?>>
                                <label>Correcta</label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
                
                <div class="botones">
                    <button type="submit" name="guardar" class="guardar">Guardar cambios</button>
                    <a href="inicio_admin.php" class="cerrar">Cerrar</a>
                </div>
            </form>
        <?php elseif ($id_curso) : ?>
            <p>No hay preguntas registradas para este curso.</p>
        <?php endif; ?>
    </div>
</body>
</html>
