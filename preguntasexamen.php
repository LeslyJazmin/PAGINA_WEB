<?php
// Asegúrate de que la conexión a la base de datos esté configurada correctamente
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuario"; // Reemplaza con el nombre de tu base de datos

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_curso = $_POST["id_curso"];
    $preguntas_agregadas = 0;
    
    // Procesar las 5 preguntas
    for ($i = 1; $i <= 5; $i++) {
        $pregunta = $_POST["pregunta_$i"];
        $opcion_a = $_POST["opcion_a_$i"];
        $opcion_b = $_POST["opcion_b_$i"];
        $opcion_c = $_POST["opcion_c_$i"];
        $opcion_d = $_POST["opcion_d_$i"];
        $correcta = $_POST["correcta_$i"];

        $sql = "INSERT INTO examen_final (id_curso, pregunta, opcion_a, opcion_b, opcion_c, opcion_d, correcta)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssss", $id_curso, $pregunta, $opcion_a, $opcion_b, $opcion_c, $opcion_d, $correcta);

        if ($stmt->execute()) {
            $preguntas_agregadas++;
        }
        $stmt->close();
    }
    
    if ($preguntas_agregadas == 5) {
        $mensaje = "Todas las preguntas fueron añadidas correctamente.";
    } else {
        $mensaje = "Se añadieron $preguntas_agregadas de 5 preguntas. Por favor, intente nuevamente.";
    }
}

// Obtener la lista de cursos para el dropdown
$cursos = [];
$sql_cursos = "SELECT id_curso, nombre_curso FROM cursos";
$result_cursos = $conn->query($sql_cursos);

if ($result_cursos->num_rows > 0) {
    while ($row = $result_cursos->fetch_assoc()) {
        $cursos[$row["id_curso"]] = $row["nombre_curso"];
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <title>Gestión de Preguntas de Examen Final</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #003366, #005bb5);
            margin: 0;
            padding: 20px;
            color: white;
        }

        .container {
            width: 70%;
            margin: auto;
            background: #005bb5;
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.8s ease-out;
        }

        h2 {
            text-align: center;
            color: #ffffff;
            font-size: 22px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #ffffff;
            color: #333;
            outline: none;
            margin-bottom: 20px;
        }

        .pregunta-container {
            background: linear-gradient(120deg, #a3bfdc, #e3efff);
            padding: 12px;
            margin-bottom: 12px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease-in-out;
            animation: slideIn 0.5s ease-out;
            animation-fill-mode: both;
        }

        .pregunta-container:hover {
            transform: translateY(-2px);
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.25);
        }

        .pregunta-container h3 {
            color: #003366;
            margin-top: 0;
            margin-bottom: 8px;
            font-size: 17px;
        }

        .pregunta-container label {
            font-weight: bold;
            display: block;
            color: #003366;
            font-size: 15px;
            margin-bottom: 4px;
        }

        input[type="text"] {
            width: 100%;
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #005bb5;
            background: #f8f9fa;
            color: black;
            font-size: 15px;
            margin-bottom: 6px;
        }

        .opciones {
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-top: 8px;
        }

        .opcion {
            background: #0073e6;
            padding: 6px;
            margin: 2px 0;
            border-radius: 4px;
            display: flex;
            align-items: flex-start;
            transition: all 0.3s ease;
            cursor: pointer;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .opcion:hover {
            background: #005bb5;
            transform: translateX(5px);
        }

        .opcion::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                120deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            transition: 0.5s;
        }

        .opcion:hover::before {
            left: 100%;
        }

        .opcion input[type="radio"] {
            margin-right: 8px;
            transform: scale(1.1);
            margin-top: 2px;
        }

        .opcion label {
            color: white;
            font-size: 16px;
            width: 100%;
            display: flex;
            align-items: flex-start;
        }

        .opcion label textarea {
            width: 100%;
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #005bb5;
            background: #f8f9fa;
            color: black;
            font-size: 15px;
            margin-left: 6px;
            min-height: 35px;
            resize: none;
            overflow: hidden;
            line-height: 1.3;
        }

        .botones {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 15px;
        }

        .botones button {
            width: 100%;
            padding: 10px;
            background: #00bfff;
            color: white;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .botones button:hover {
            background: #0099cc;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .botones button:active {
            transform: translateY(1px);
        }

        .botones button::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent,
                rgba(255, 255, 255, 0.1),
                transparent
            );
            transform: rotate(45deg);
            transition: 0.5s;
        }

        .botones button:hover::after {
            left: 100%;
        }

        .mensaje {
            text-align: center;
            color: #00ff00;
            font-weight: bold;
            padding: 10px;
            background: #003366;
            border-radius: 5px;
            margin-top: 20px;
        }

        /* Animaciones */
        @keyframes fadeIn {
            from { 
                opacity: 0; 
                transform: translateY(-20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Aplicar animación de entrada escalonada a las preguntas */
        <?php for ($i = 1; $i <= 5; $i++): ?>
        .pregunta-container:nth-child(<?php echo $i + 1; ?>) {
            animation-delay: <?php echo $i * 0.1; ?>s;
        }
        <?php endfor; ?>
    </style>
    <script>
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }

        // Aplicar auto-resize a todos los textareas cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            const textareas = document.querySelectorAll('textarea');
            textareas.forEach(textarea => {
                textarea.addEventListener('input', function() {
                    autoResize(this);
                });
                // Ajustar altura inicial
                autoResize(textarea);
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h2>Añadir Preguntas al Examen Final</h2>
        <?php if ($mensaje): ?>
            <div class="mensaje"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div>
                <label for="id_curso">Seleccionar Curso:</label>
                <select name="id_curso" id="id_curso" required>
                    <option value="">Seleccionar un curso</option>
                    <?php foreach ($cursos as $id => $nombre): ?>
                        <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($nombre); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <div class="pregunta-container">
                <h3>Pregunta <?php echo $i; ?></h3>
                <div>
                    <label for="pregunta_<?php echo $i; ?>">Pregunta:</label>
                    <input type="text" name="pregunta_<?php echo $i; ?>" id="pregunta_<?php echo $i; ?>" required>
                </div>
                <div>
                    <label>Opciones:</label>
                    <div class="opciones">
                        <div class="opcion">
                            <input type="radio" id="opcion_a_<?php echo $i; ?>" name="correcta_<?php echo $i; ?>" value="a" required>
                            <label for="opcion_a_<?php echo $i; ?>">A: <textarea name="opcion_a_<?php echo $i; ?>" required oninput="autoResize(this)"></textarea></label>
                        </div>
                        <div class="opcion">
                            <input type="radio" id="opcion_b_<?php echo $i; ?>" name="correcta_<?php echo $i; ?>" value="b" required>
                            <label for="opcion_b_<?php echo $i; ?>">B: <textarea name="opcion_b_<?php echo $i; ?>" required oninput="autoResize(this)"></textarea></label>
                        </div>
                        <div class="opcion">
                            <input type="radio" id="opcion_c_<?php echo $i; ?>" name="correcta_<?php echo $i; ?>" value="c" required>
                            <label for="opcion_c_<?php echo $i; ?>">C: <textarea name="opcion_c_<?php echo $i; ?>" required oninput="autoResize(this)"></textarea></label>
                        </div>
                        <div class="opcion">
                            <input type="radio" id="opcion_d_<?php echo $i; ?>" name="correcta_<?php echo $i; ?>" value="d" required>
                            <label for="opcion_d_<?php echo $i; ?>">D: <textarea name="opcion_d_<?php echo $i; ?>" required oninput="autoResize(this)"></textarea></label>
                        </div>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
            
            <div class="botones">
                <button type="submit">Añadir Preguntas</button>
                <button type="button" onclick="window.location.href='auladmi.php';">Cerrar</button>
            </div>
        </form>
    </div>
</body>
</html>