<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "usuario");

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Verificar si se seleccionó un curso
$id_curso = isset($_POST['id_curso']) && !empty($_POST['id_curso']) ? $_POST['id_curso'] : null;

// Limpiar preguntas duplicadas si se solicita
if (isset($_GET['limpiar_duplicados']) && $id_curso) {
    // Primero, identificar las preguntas duplicadas
    $sql_duplicados = "SELECT pregunta, COUNT(*) as count FROM examen_final WHERE id_curso = ? GROUP BY pregunta HAVING count > 1";
    $stmt_duplicados = $mysqli->prepare($sql_duplicados);
    $stmt_duplicados->bind_param("i", $id_curso);
    $stmt_duplicados->execute();
    $result_duplicados = $stmt_duplicados->get_result();
    
    $eliminados = 0;
    
    while ($row_duplicados = $result_duplicados->fetch_assoc()) {
        $pregunta = $row_duplicados['pregunta'];
        
        // Obtener todos los IDs de las preguntas duplicadas
        $sql_ids = "SELECT id FROM examen_final WHERE id_curso = ? AND pregunta = ? ORDER BY id";
        $stmt_ids = $mysqli->prepare($sql_ids);
        $stmt_ids->bind_param("is", $id_curso, $pregunta);
        $stmt_ids->execute();
        $result_ids = $stmt_ids->get_result();
        
        $ids = [];
        while ($row_ids = $result_ids->fetch_assoc()) {
            $ids[] = $row_ids['id'];
        }
        
        // Mantener el primer ID y eliminar el resto
        if (count($ids) > 1) {
            array_shift($ids); // Eliminar el primer ID (mantenerlo)
            
            // Eliminar el resto de IDs
            foreach ($ids as $id) {
                $sql_delete = "DELETE FROM examen_final WHERE id = ?";
                $stmt_delete = $mysqli->prepare($sql_delete);
                $stmt_delete->bind_param("i", $id);
                $stmt_delete->execute();
                $eliminados++;
            }
        }
    }
    
    if ($eliminados > 0) {
        echo "<script>alert('Se eliminaron $eliminados preguntas duplicadas.');</script>";
    } else {
        echo "<script>alert('No se encontraron preguntas duplicadas.');</script>";
    }
}

// Procesar actualizaciones si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar_todo'])) {
    $actualizaciones = 0;
    $errores = 0;
    
    // Procesar cada pregunta
    foreach ($_POST['preguntas'] as $id => $datos) {
        $pregunta = $datos['pregunta'];
        $opcion_a = $datos['opcion_a'];
        $opcion_b = $datos['opcion_b'];
        $opcion_c = $datos['opcion_c'];
        $opcion_d = $datos['opcion_d'];
        $correcta = $datos['correcta'];
        
        // Verificar si la pregunta ya existe para este curso (excluyendo la pregunta actual)
        $sql_check = "SELECT id FROM examen_final WHERE pregunta = ? AND id_curso = ? AND id != ?";
        $stmt_check = $mysqli->prepare($sql_check);
        $stmt_check->bind_param("sii", $pregunta, $id_curso, $id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            $errores++;
        } else {
            // Actualizar la pregunta si no es duplicada
            $sql = "UPDATE examen_final SET 
                    pregunta = ?, 
                    opcion_a = ?, 
                    opcion_b = ?, 
                    opcion_c = ?, 
                    opcion_d = ?, 
                    correcta = ? 
                    WHERE id = ?";
                    
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ssssssi", $pregunta, $opcion_a, $opcion_b, $opcion_c, $opcion_d, $correcta, $id);
            
            if ($stmt->execute()) {
                $actualizaciones++;
            } else {
                $errores++;
            }
        }
        $stmt_check->close();
    }
    
    if ($actualizaciones > 0) {
        echo "<script>alert('Se actualizaron $actualizaciones preguntas con éxito.');</script>";
    }
    if ($errores > 0) {
        echo "<script>alert('Hubo $errores errores al actualizar algunas preguntas. Verifica que no haya duplicados.');</script>";
    }
}

// Eliminar preguntas seleccionadas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_seleccionados'])) {
    $eliminados = 0;
    
    if (isset($_POST['eliminar']) && is_array($_POST['eliminar'])) {
        foreach ($_POST['eliminar'] as $id) {
            $sql = "DELETE FROM examen_final WHERE id = ?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $eliminados++;
            }
        }
    }
    
    if ($eliminados > 0) {
        echo "<script>alert('Se eliminaron $eliminados preguntas con éxito.');</script>";
    } else {
        echo "<script>alert('No se eliminó ninguna pregunta.');</script>";
    }
}

// Obtener lista de cursos
$cursos = [];
$result = $mysqli->query("SELECT id_curso, nombre_curso FROM cursos");
while ($row = $result->fetch_assoc()) {
    $cursos[] = $row;
}

// Obtener preguntas si se seleccionó un curso
$preguntas = [];
if ($id_curso) {
    // Usar DISTINCT para evitar duplicados
    $sql = "SELECT * FROM examen_final WHERE id_curso = ? ORDER BY id";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id_curso);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Almacenar preguntas en un array con índice único
    $preguntas_unicas = [];
    while ($row = $result->fetch_assoc()) {
        // Usar la pregunta como clave para evitar duplicados
        $preguntas_unicas[$row['id']] = $row;
    }
    
    // Convertir el array asociativo a array indexado
    $preguntas = array_values($preguntas_unicas);
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <title>Editar Preguntas del Examen Final</title>
    <link rel="stylesheet" href="preguntasvideo.css">
    <style>
        /* Estilos adicionales para mejorar la presentación de las preguntas */
        .pregunta {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .pregunta textarea {
            width: 100%;
            min-height: 100px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 16px;
            resize: vertical;
        }
        
        .opcion {
            background-color: #fff;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            border-left: 4px solid #4CAF50;
        }
        
        .opcion label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        
        .opcion input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 5px;
        }
        
        .botones {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
        }
        
        .guardar {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        
        .eliminar {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        
        .guardar:hover, .eliminar:hover {
            opacity: 0.8;
        }
        
        h2 {
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        
        h4 {
            color: #555;
            margin: 15px 0 10px 0;
        }
        
        .mensaje-duplicado {
            background-color: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            border-left: 4px solid #ffc107;
        }
        
        .botones-principales {
            display: flex;
            justify-content: center;
            margin: 20px 0;
            gap: 20px;
        }
        
        .boton-principal {
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            font-weight: bold;
        }
        
        .actualizar-todo {
            background-color: #4CAF50;
            color: white;
        }
        
        .eliminar-seleccionados {
            background-color: #f44336;
            color: white;
        }
        
        .limpiar-duplicados {
            background-color: #ff9800;
            color: white;
        }
        
        .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .checkbox-container input[type="checkbox"] {
            margin-right: 10px;
        }
        
        .info-mensaje {
            background-color: #e3f2fd;
            color: #0d47a1;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            border-left: 4px solid #2196f3;
        }
    </style>
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
            <h2>Editar Preguntas del Examen Final</h2>
            
            <div class="info-mensaje">
                <p>Se encontraron <?= count($preguntas) ?> preguntas para este curso. Si notas preguntas duplicadas, utiliza el botón "Limpiar preguntas duplicadas" para eliminar automáticamente las duplicadas.</p>
            </div>
            
            <div class="botones-principales">
                <a href="?id_curso=<?= $id_curso ?>&limpiar_duplicados=1" class="boton-principal limpiar-duplicados" onclick="return confirm('¿Estás seguro de que deseas eliminar las preguntas duplicadas?')">Limpiar preguntas duplicadas</a>
            </div>
            
            <form method="POST">
                <input type="hidden" name="id_curso" value="<?= $id_curso ?>">
                
                <?php foreach ($preguntas as $pregunta) : ?>
                    <div class="pregunta">
                        <div class="checkbox-container">
                            <input type="checkbox" name="eliminar[]" value="<?= $pregunta['id'] ?>" id="eliminar_<?= $pregunta['id'] ?>">
                            <label for="eliminar_<?= $pregunta['id'] ?>">Seleccionar para eliminar</label>
                        </div>
                        
                        <input type="hidden" name="preguntas[<?= $pregunta['id'] ?>][id]" value="<?= $pregunta['id'] ?>">
                        
                        <label>Pregunta:</label>
                        <textarea name="preguntas[<?= $pregunta['id'] ?>][pregunta]" required><?= htmlspecialchars($pregunta['pregunta']) ?></textarea>
                        
                        <h4>Opciones:</h4>
                        <div class="opcion">
                            <label>Opción A:</label>
                            <input type="text" name="preguntas[<?= $pregunta['id'] ?>][opcion_a]" value="<?= htmlspecialchars($pregunta['opcion_a']) ?>" required>
                            <input type="radio" name="preguntas[<?= $pregunta['id'] ?>][correcta]" value="a" <?= $pregunta['correcta'] == 'a' ? 'checked' : '' ?>>
                            <label>Correcta</label>
                        </div>
                        
                        <div class="opcion">
                            <label>Opción B:</label>
                            <input type="text" name="preguntas[<?= $pregunta['id'] ?>][opcion_b]" value="<?= htmlspecialchars($pregunta['opcion_b']) ?>" required>
                            <input type="radio" name="preguntas[<?= $pregunta['id'] ?>][correcta]" value="b" <?= $pregunta['correcta'] == 'b' ? 'checked' : '' ?>>
                            <label>Correcta</label>
                        </div>
                        
                        <div class="opcion">
                            <label>Opción C:</label>
                            <input type="text" name="preguntas[<?= $pregunta['id'] ?>][opcion_c]" value="<?= htmlspecialchars($pregunta['opcion_c']) ?>" required>
                            <input type="radio" name="preguntas[<?= $pregunta['id'] ?>][correcta]" value="c" <?= $pregunta['correcta'] == 'c' ? 'checked' : '' ?>>
                            <label>Correcta</label>
                        </div>
                        
                        <div class="opcion">
                            <label>Opción D:</label>
                            <input type="text" name="preguntas[<?= $pregunta['id'] ?>][opcion_d]" value="<?= htmlspecialchars($pregunta['opcion_d']) ?>" required>
                            <input type="radio" name="preguntas[<?= $pregunta['id'] ?>][correcta]" value="d" <?= $pregunta['correcta'] == 'd' ? 'checked' : '' ?>>
                            <label>Correcta</label>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="botones-principales">
                    <button type="submit" name="actualizar_todo" class="boton-principal actualizar-todo">Actualizar todas las preguntas</button>
                    <button type="submit" name="eliminar_seleccionados" class="boton-principal eliminar-seleccionados" onclick="return confirm('¿Estás seguro de eliminar las preguntas seleccionadas?')">Eliminar preguntas seleccionadas</button>
                </div>
                
                <div class="botones">
                    <a href="inicio_admin.php" class="cerrar">Volver al Panel de Administrador</a>
                </div>
            </form>
        <?php elseif ($id_curso) : ?>
            <p>No hay preguntas registradas para este curso.</p>
        <?php endif; ?>
    </div>
</body>
</html> 