<?php
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['guardar'])) {
    if (isset($_POST['pregunta'])) {
        $success = true;
        $error_message = "";
        
        foreach ($_POST['pregunta'] as $id => $pregunta) {
            $a = $_POST['opcion_a'][$id];
            $b = $_POST['opcion_b'][$id];
            $c = $_POST['opcion_c'][$id];
            $d = $_POST['opcion_d'][$id];
            $correcta = $_POST['correcta'][$id];

            // Si el ID es numérico, actualiza la pregunta existente
            if (is_numeric($id)) {
                // Verificar si los datos han cambiado antes de actualizar
                $sql = "SELECT pregunta, opcion_a, opcion_b, opcion_c, opcion_d, correcta FROM examen_final WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $stmt->close();
                
                // Solo actualizar si algo cambió
                if ($row['pregunta'] !== $pregunta || $row['opcion_a'] !== $a || $row['opcion_b'] !== $b || 
                    $row['opcion_c'] !== $c || $row['opcion_d'] !== $d || $row['correcta'] !== $correcta) {
                    
                    $sql = "UPDATE examen_final SET pregunta = ?, opcion_a = ?, opcion_b = ?, opcion_c = ?, opcion_d = ?, correcta = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssssi", $pregunta, $a, $b, $c, $d, $correcta, $id);
                    
                    if (!$stmt->execute()) {
                        $success = false;
                        $error_message = "Error al actualizar: " . $stmt->error;
                        break;
                    }
                    $stmt->close();
                }
            } else {
                // Si el ID no es numérico (es 'nuevo'), inserta una nueva pregunta
                $sql = "INSERT INTO examen_final (id_curso, pregunta, opcion_a, opcion_b, opcion_c, opcion_d, correcta) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("issssss", $id_curso, $pregunta, $a, $b, $c, $d, $correcta);
                
                if (!$stmt->execute()) {
                    $success = false;
                    $error_message = "Error al insertar: " . $stmt->error;
                    break;
                }
                $stmt->close();
            }
        }
        
        if ($success) {
            echo "<p class='mensaje'>¡Cambios guardados exitosamente!</p>";
            // Redirigir para evitar reenvío del formulario
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'preguntasexamen.php?id_curso=" . $id_curso . "';
                }, 1500);
            </script>";
        } else {
            echo "<p class='mensaje error'>Error al guardar: " . $error_message . "</p>";
        }
    }
}

// Obtener lista de cursos
$cursos = [];
$result = $conn->query("SELECT id_curso, nombre_curso FROM cursos");
while ($row = $result->fetch_assoc()) {
    $cursos[] = $row;
}

// Obtener preguntas del examen final
$preguntas = [];
if ($id_curso) {
    $sql = "SELECT * FROM examen_final WHERE id_curso = ? ORDER BY id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_curso);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $preguntas[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examen Final</title>
    <link rel="stylesheet" href="preguntasvideo.css">
    <style>
        .mensaje {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            text-align: center;
        }
        .mensaje.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .pregunta {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .opcion {
            margin: 10px 0;
            display: flex;
            align-items: center;
        }
        .opcion label {
            min-width: 30px;
            font-weight: bold;
            margin-right: 10px;
        }
        .opcion input[type="text"] {
            flex-grow: 1;
            margin-right: 10px;
        }
    </style>
    <script>
        // Función para enviar el formulario automáticamente al cambiar de curso
        function cargarPreguntas() {
            document.getElementById('form_curso').submit();
        }
    </script>
</head>
<body>
<div class="container">
    <h2>Seleccionar Curso</h2>
    <form method="POST" id="form_curso">
        <label for="id_curso">Elige un curso:</label>
        <select name="id_curso" id="id_curso" required onchange="cargarPreguntas()">
            <option value="">Selecciona un curso</option>
            <?php foreach ($cursos as $curso): ?>
                <option value="<?= $curso['id_curso'] ?>" <?= ($id_curso == $curso['id_curso']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($curso['nombre_curso']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div class="botones">
            <button type="submit" class="cargar">Cargar Preguntas</button>
            <a href="auladmi.php" class="cerrar">Cerrar</a>
        </div>
    </form>

    <?php if ($id_curso): ?>
        <?php if (!empty($preguntas)): ?>
            <h2>Editar Preguntas del Examen Final</h2>
            <form method="POST">
                <input type="hidden" name="id_curso" value="<?= $id_curso ?>">
                <?php foreach ($preguntas as $preg): ?>
                    <div class="pregunta">
                        <label>Pregunta:</label>
                        <input type="text" name="pregunta[<?= $preg['id'] ?>]" value="<?= htmlspecialchars($preg['pregunta']) ?>" required>
                        <h4>Opciones:</h4>
                        <div class="opcion">
                            <label>A:</label>
                            <input type="text" name="opcion_a[<?= $preg['id'] ?>]" value="<?= htmlspecialchars($preg['opcion_a']) ?>" required>
                            <input type="radio" name="correcta[<?= $preg['id'] ?>]" value="a" <?= ($preg['correcta'] == 'a') ? 'checked' : '' ?>> Correcta
                        </div>
                        <div class="opcion">
                            <label>B:</label>
                            <input type="text" name="opcion_b[<?= $preg['id'] ?>]" value="<?= htmlspecialchars($preg['opcion_b']) ?>" required>
                            <input type="radio" name="correcta[<?= $preg['id'] ?>]" value="b" <?= ($preg['correcta'] == 'b') ? 'checked' : '' ?>> Correcta
                        </div>
                        <div class="opcion">
                            <label>C:</label>
                            <input type="text" name="opcion_c[<?= $preg['id'] ?>]" value="<?= htmlspecialchars($preg['opcion_c']) ?>" required>
                            <input type="radio" name="correcta[<?= $preg['id'] ?>]" value="c" <?= ($preg['correcta'] == 'c') ? 'checked' : '' ?>> Correcta
                        </div>
                        <div class="opcion">
                            <label>D:</label>
                            <input type="text" name="opcion_d[<?= $preg['id'] ?>]" value="<?= htmlspecialchars($preg['opcion_d']) ?>" required>
                            <input type="radio" name="correcta[<?= $preg['id'] ?>]" value="d" <?= ($preg['correcta'] == 'd') ? 'checked' : '' ?>> Correcta
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="botones">
                    <button type="submit" name="guardar" class="guardar">Guardar cambios</button>
                    <a href="auladmi.php" class="cerrar">Cerrar</a>
                </div>
            </form>
            
            <h2>Agregar Nuevas Preguntas</h2>
        <?php else: ?>
            <h2>Crear Preguntas para el Examen Final</h2>
        <?php endif; ?>
        
        <form method="POST">
            <input type="hidden" name="id_curso" value="<?= $id_curso ?>">
            
            <?php for($i = 1; $i <= 5; $i++): ?>
            <div class="pregunta">
                <h3>Pregunta <?= $i ?></h3>
                <label>Pregunta:</label>
                <input type="text" name="pregunta[nuevo<?= $i ?>]" placeholder="Ingrese la pregunta" required>
                <h4>Opciones:</h4>
                <div class="opcion">
                    <label>A:</label>
                    <input type="text" name="opcion_a[nuevo<?= $i ?>]" placeholder="Opción A" required>
                    <input type="radio" name="correcta[nuevo<?= $i ?>]" value="a" checked> Correcta
                </div>
                <div class="opcion">
                    <label>B:</label>
                    <input type="text" name="opcion_b[nuevo<?= $i ?>]" placeholder="Opción B" required>
                    <input type="radio" name="correcta[nuevo<?= $i ?>]" value="b"> Correcta
                </div>
                <div class="opcion">
                    <label>C:</label>
                    <input type="text" name="opcion_c[nuevo<?= $i ?>]" placeholder="Opción C" required>
                    <input type="radio" name="correcta[nuevo<?= $i ?>]" value="c"> Correcta
                </div>
                <div class="opcion">
                    <label>D:</label>
                    <input type="text" name="opcion_d[nuevo<?= $i ?>]" placeholder="Opción D" required>
                    <input type="radio" name="correcta[nuevo<?= $i ?>]" value="d"> Correcta
                </div>
            </div>
            <?php endfor; ?>
            
            <div class="botones">
                <button type="submit" name="guardar" class="guardar">Guardar preguntas</button>
                <a href="auladmi.php" class="cerrar">Cerrar</a>
            </div>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
