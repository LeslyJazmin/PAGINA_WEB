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

// Obtener lista de cursos
$cursos = [];
$result = $conn->query("SELECT id_curso, nombre_curso FROM cursos");
while ($row = $result->fetch_assoc()) {
    $cursos[] = $row;
}

// Verificar si se seleccionó un curso
$id_curso = isset($_POST['id_curso']) ? $_POST['id_curso'] : null;
$preguntas_existentes = 0;

// Verificar si el curso ya tiene preguntas registradas
if ($id_curso) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM preguntas WHERE id_curso = ?");
    $stmt->bind_param("i", $id_curso);
    $stmt->execute();
    $stmt->bind_result($preguntas_existentes);
    $stmt->fetch();
    $stmt->close();
}

// Si se envió el formulario y no hay preguntas previas
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agregar']) && $preguntas_existentes == 0) {
    for ($i = 0; $i < 5; $i++) { // 5 preguntas
        $pregunta_texto = $_POST['pregunta'][$i];

        // Insertar la pregunta en la base de datos
        $sql = "INSERT INTO preguntas (id_curso, pregunta) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $id_curso, $pregunta_texto);
        $stmt->execute();
        $pregunta_id = $stmt->insert_id;

        // Insertar opciones para la pregunta
        for ($j = 0; $j < 3; $j++) { // 3 opciones por pregunta
            $opcion_texto = $_POST['opciones'][$i][$j];
            $es_correcta = ($_POST['correcta'][$i] == $j) ? 1 : 0;

            $sql = "INSERT INTO opciones (pregunta_id, opcion, es_correcta) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isi", $pregunta_id, $opcion_texto, $es_correcta);
            $stmt->execute();
        }
    }

    echo "<p class='mensaje'>¡Preguntas y opciones agregadas exitosamente!</p>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Preguntas</title>
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
            <button type="submit">Cargar</button>
            <a href="auladmi.php" class="cerrar">Cerrar</a>
            </div>
        </form>

        <?php if ($id_curso) : ?>
            <?php if ($preguntas_existentes > 0) : ?>
                <p class='mensaje'>¡Las preguntas y opciones ya están agregadas para este curso!</p>
            <?php else : ?>
                <h2>Agregar 5 Preguntas</h2>
                <form method="POST">
                    <input type="hidden" name="id_curso" value="<?= $id_curso ?>">

                    <?php for ($i = 0; $i < 5; $i++) : ?>
                        <div class="pregunta">
                            <label>Pregunta <?= $i + 1 ?>:</label>
                            <input type="text" name="pregunta[]" required>
                            
                            <h4>Opciones:</h4>
                            <div class="opciones">
                                <?php for ($j = 0; $j < 3; $j++) : ?>
                                    <div class="opcion">
                                        <input type="text" name="opciones[<?= $i ?>][]" required>
                                        <input type="radio" name="correcta[<?= $i ?>]" value="<?= $j ?>" required>
                                        <label>Correcta</label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                    <div class="botones">
                    <button type="submit" name="agregar">Agregar Preguntas</button>
                    <a href="auladmi.php" class="cerrar">Cerrar</a>
                    </div>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
