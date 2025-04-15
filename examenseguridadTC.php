<?php
session_start();

// Función para inicializar o reiniciar las variables de sesión
function inicializarSesion() {
    if (!isset($_SESSION['intentos_por_curso_seguridadTC'])) {
        $_SESSION['intentos_por_curso_seguridadTC'] = [];
    }
    
    // ID 2 corresponde al curso de primeros auxilios
    if (!isset($_SESSION['intentos_por_curso_seguridadTC'][26])) {
        $_SESSION['intentos_por_curso_seguridadTC'][26] = 0;
    }
    
    if (!isset($_SESSION['notas_por_curso_seguridadTC'])) {
        $_SESSION['notas_por_curso_seguridadTC'] = [];
    }
    if (!isset($_SESSION['notas_por_curso_seguridadTC'][26])) {
        $_SESSION['notas_por_curso_seguridadTC'][26] = [];
    }
    if (!isset($_SESSION['examen_completado_por_curso_seguridadTC'][26])) {
        $_SESSION['examen_completado_por_curso_seguridadTC'][26] = false;
    }    
    
    $_SESSION['tiempo_inicio'] = 0;
    $_SESSION['tiempo_restante'] = 10 * 60; // 20 minutos en segundos
    $_SESSION['examen_iniciado'] = false;
    $_SESSION['examen_completado_por_curso_seguridadTC'][26] = false;
    $_SESSION['nota_final_por_curso_seguridadTC'][26] = 0;
    $_SESSION['estado_final_por_curso_seguridadTC'][26] = '';
    $_SESSION['nota_mas_alta_por_curso_seguridadTC'][26] = 0;
    $_SESSION['id_curso'] = 26; // Curso de primeros auxilios
}

// Inicializar sesión si no existe o si se solicita reiniciar
if (!isset($_SESSION['intentos_por_curso_seguridadTC']) || isset($_POST['reiniciar'])) {
    inicializarSesion();
}

$duracion_examen = 20 * 60;

// Procesar la acción de iniciar el examen
if (isset($_POST['iniciar_examen'])) {
    if ($_SESSION['intentos_por_curso_seguridadTC'][26] < 3) {
        $_SESSION['examen_iniciado'] = true;
        $_SESSION['tiempo_inicio'] = time();
        $_SESSION['tiempo_restante'] = $duracion_examen;
        header("Location: examenseguridadTC.php");
        exit();
    }
}

// Función para comprobar si el tiempo ha expirado
function tiempoExpirado() {
    $tiempo_transcurrido = time() - $_SESSION['tiempo_inicio'];
    return $tiempo_transcurrido >= $_SESSION['tiempo_restante'];
}
// Función para obtener las preguntas del examen desde la base de datos
function obtenerPreguntas($id_curso) {
    $conn = new mysqli('localhost', 'root', '', 'usuario');
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $preguntas = [];
    $sql = "SELECT * FROM examen_final WHERE id_curso = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_curso);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $opciones = [$row['opcion_a'], $row['opcion_b'], $row['opcion_c'], $row['opcion_d']];
        $respuesta_correcta = array_search($row['correcta'], ['a', 'b', 'c', 'd']);
        
        $preguntas[] = [
            $row['pregunta'],
            $opciones,
            $respuesta_correcta
        ];
    }

    $stmt->close();
    $conn->close();
    return $preguntas;
}

// Obtener las preguntas del examen
$preguntas = obtenerPreguntas(26);

$mensaje = '';

// Procesar respuestas del examen
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['respuestas'])) {
    if (tiempoExpirado()) {
        $_SESSION['notas_por_curso_seguridadTC'][26][] = 0;
        $_SESSION['estado_final_por_curso_seguridadTC'][26] = 'REPROBADO';
        $mensaje = "Tiempo agotado. Has reprobado el examen.";
    } else {
        $respuestas = $_POST['respuestas'];
        $nota = 0;

        // Calcular la nota
        foreach ($respuestas as $index => $respuesta) {
            if ($respuesta == $preguntas[$index][2]) {
                $nota += 4; // Sumar 4 puntos por cada respuesta correcta
            }
        }

        // Guardar la nota en la sesión
        $_SESSION['notas_por_curso_seguridadTC'][26][] = $nota;
        $_SESSION['examen_iniciado'] = false;

        // Evaluar estado
        if ($nota >= 13) {
            $_SESSION['estado_final_por_curso_seguridadTC'][26] = 'APROBADO';
            $_SESSION['nota_final_por_curso_seguridadTC'][26] = $nota;
            $_SESSION['examen_completado_por_curso_seguridadTC'][26] = true;
        } else {
            $_SESSION['estado_final_por_curso_seguridadTC'][26] = 'REPROBADO';
        }

        // Actualizar la mejor nota
        if ($nota > $_SESSION['nota_mas_alta_por_curso_seguridadTC'][26]) {
            $_SESSION['nota_mas_alta_por_curso_seguridadTC'][26] = $nota;
        }

        // Incrementar intento actual en sesión
        $_SESSION['intentos_por_curso_seguridadTC'][26]++;
        $intento_actual = $_SESSION['intentos_por_curso_seguridadTC'][26];

        // Actualizar base de datos
        if (isset($_SESSION['nota_mas_alta_por_curso_seguridadTC'][26]) && isset($_SESSION['DNI'])) {
            $conn = new mysqli('localhost', 'root', '', 'usuario');
            if ($conn->connect_error) {
                die("Error de conexión: " . $conn->connect_error);
            }

            $DNI = $_SESSION['DNI'];
            $nota_mas_alta = $_SESSION['nota_mas_alta_por_curso_seguridadTC'][26];

            // Verificar inscripción
            $sqlVerificar = "SELECT id_curso FROM inscripciones WHERE DNI = ? AND id_curso = 26";
            $stmtVerificar = $conn->prepare($sqlVerificar);
            $stmtVerificar->bind_param("s", $DNI);
            $stmtVerificar->execute();
            $resultadoVerificar = $stmtVerificar->get_result();

            if ($resultadoVerificar->num_rows > 0) {
                // Actualizar el intento correspondiente
                $campo_intento = "intentose{$intento_actual}";
                $sql = "UPDATE inscripciones 
                        SET $campo_intento = ?, examen_final = ? 
                        WHERE DNI = ? AND id_curso = 26";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iis", $nota, $nota_mas_alta, $DNI);

                if ($stmt->execute()) {
                    echo "";
                } else {
                    echo "Error al actualizar intento $intento_actual: " . $stmt->error;
                }

                $stmt->close();
            } else {
                echo "No se encontró inscripción para el DNI $DNI.";
            }

            $stmtVerificar->close();
            $conn->close();
        }

        // Mostrar mensaje al usuario
        if ($intento_actual < 3) {
            $mensaje = "Has completado el intento $intento_actual con $nota puntos. Estado: " . $_SESSION['estado_final_por_curso_seguridadTC'][26];
        } else {
            $mensaje = "En este último intento tu nota es $nota. La nota final de tus 3 intentos ha sido " . $_SESSION['nota_mas_alta_por_curso_seguridadTC'][26];
            $_SESSION['examen_completado_por_curso_seguridadTC'][26] = true;
        }
    }
}


// Modificar el procesamiento del reinicio
if (isset($_POST['reiniciar'])) {
    $conn = new mysqli('localhost', 'root', '', 'usuario');
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    $DNI = $_SESSION['DNI'];
    
    $sql = "UPDATE inscripciones SET `intentose1` = 0, `intentose2` = 0, `intentose3` = 0, `examen_final` = 0 WHERE DNI = ? AND id_curso = 26";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $DNI);

    if ($stmt->execute()) {
        $_SESSION['nota_mas_alta_por_curso_seguridadTC'][26] = 0;
        $_SESSION['intentos_por_curso_seguridadTC'][26] = 0;
        $_SESSION['notas_por_curso_seguridadTC'][26] = [];
        $_SESSION['examen_completado_por_curso_seguridadTC'][26] = false;
        $_SESSION['estado_final_por_curso_seguridadTC'][26] = '';
        $mensaje = "Tus intentos y notas han sido actualizados correctamente.";
    } else {
        $mensaje = "Error al actualizar los intentos. Inténtalo de nuevo.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Examen de Seguridad en trabajos confinados</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
        }
        .pregunta {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .pregunta:hover {
            box-shadow: 0 5px 15px rgba(0 ,0,0,0.1);
        }
        .pregunta p {
            font-weight: 500;
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        label:hover {
            color: #3498db;
        }
        input[type="radio"] {
            margin-right: 10px;
        }
        input[type="submit"], .modal-button, .reiniciar {
            background-color: #3498db;
            border: none;
            color: white;
            padding: 12px 24px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
            font-weight: 500;
        }
        input[type="submit"]:hover, .modal-button:hover, .reiniciar:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        .mensaje {
            background-color: #e1f5fe;
            border-color: #b3e5fc;
            color: #0277bd;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 5px;
            font-weight: 500;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 100px auto; /* Agregamos margen superior para bajarlo */
            padding: 30px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
            box-shadow: 0 5px 30px rgba(0,0,0,0.3);
            text-align: center; /* Centramos el contenido */
        }
        .modal-content h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #2c3e50;
        }
        .modal-content p {
            text-align: justify; /* Justificamos el texto */
            margin-bottom: 20px;
            font-size: 16px;
            color: #333;
            line-height: 1.6;
        }
        .modal-button {
            padding: 10px 20px;
            background-color: #007bff; /* Azul */
            color: white;
            border: none;
            cursor: pointer;
        }
        .modal-button:hover {
            background-color: #0056b3; /* Azul más oscuro en hover */
        }
        #timer {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
      /* Opción 1: Usando float */
input.volver {
    background-color: #2980b9;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 5px;
    float: right; /* Añadido para mover a la derecha */
    margin-top: 170px; /* Mueve el botón hacia arriba */
}



input.volver:hover {
    background-color: #2980b9;
}
        input.volver:hover {
            background-color: #2980b9; /* Color de fondo al pasar el mouse */
        }
        .button-container {
            display: flex;
            gap: 10px; /* Espacio entre los botones */
            right:30px;
            
        }
    </style>
<body>

    <div class="container">
        <h1>Examen de Seguridad en trabajos confinados</h1>
    <?php if ($_SESSION['examen_completado_por_curso_seguridadTC'][26]): ?>
    <?php if ($_SESSION['estado_final_por_curso_seguridadTC'][26] == 'APROBADO'): ?>
        <!-- Mensaje de éxito si se aprueba el examen -->
        <div class="mensaje">
            Felicidades! Ya has aprobado el examen. No necesitas realizar más intentos.
        </div>
        <!-- Botón para volver al curso -->
        <form method="post">
            <button type="button" value="Volver a mi curso" class="volver" onclick="window.location.href='seguridadTC.php'">Volver a mi curso</button>
        </form>

    <?php else: ?>
        <!-- Si no está aprobado, y se tiene menos de 3 intentos -->
        <?php if ($_SESSION['intentos_por_curso_seguridadTC'][26] < 3): ?>
            <div class="mensaje">
                Has reprobado el examen con <?php echo $_SESSION['nota_final']; ?> puntos. Te queda 
                <?php echo 3 - $_SESSION['intentos_por_curso_seguridadTC'][26]; ?> intento(s). Puedes intentar nuevamente.
            </div>
            <div class="mensaje">
                El puntaje más alto obtenido hasta ahora es <?php echo max($_SESSION['notas_por_curso_seguridadTC']); ?> puntos.
            </div>
            <button onclick="mostrarModal()" class="modal-button">Continuar con el siguiente intento</button>
        <?php else: ?>
            <!-- Si ya se han agotado los intentos -->
            <form method="post">
                <input type="button" value="Volver a mi curso" class="volver" onclick="window.location.href='seguridadTC.php'">
            </form>
            <div class="mensaje">
                Has agotado tus 3 intentos. Por favor, contacta al instructor para más información o reinicia el examen a continuación.
            </div>
        <?php endif; ?>
    <?php endif; ?>

<!-- Si no se ha completado el Videotest -->
<?php elseif ($_SESSION['intentos_por_curso_seguridadTC'][26] < 3): ?>
    <?php if (!$_SESSION['examen_iniciado']): ?>
        <!-- Botón para iniciar el Videotest -->
        <button onclick="mostrarModal()" class="modal-button">Iniciar Examen</button>
        <input type="button" value="Cerrar y salir" class="modal-button" onclick="window.location.href='seguridadTC.php'">
    <?php else: ?>
        <!-- Temporizador y formulario del examen -->
        <div id="timer">Tiempo restante: 20:00</div>
        <form method="post" id="examenForm">
            <?php foreach ($preguntas as $index => $pregunta): ?>
                <div class="pregunta">
                    <p><strong><?php echo ($index + 1) . '. ' . $pregunta[0]; ?></strong></p>
                    <?php foreach ($pregunta[1] as $opcion_index => $opcion): ?>
                        <label>
                            <input type="radio" name="respuestas[<?php echo $index; ?>]" value="<?php echo $opcion_index; ?>" required>
                            <?php echo $opcion; ?>
                            <?php 
                            $letra_opcion = chr(97 + $opcion_index); // Convierte 0->a, 1->b, 2->c, 3->d
                            echo $letra_opcion . ') ' . $opcion; 
                            ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
            <input type="hidden" name="tiempo_actual" id="tiempo_actual" value="<?php echo $_SESSION['tiempo_restante']; ?>">
            <input type="submit" value="Enviar respuestas">
        </form>
    <?php endif; ?>

<?php endif; ?>

<!-- Mensaje adicional si existe -->
<?php if ($mensaje): ?>
    <div class="mensaje"><?php echo $mensaje; ?></div>
<?php endif; ?>

<!-- Modal para iniciar el Videotest -->
<div id="inicioModal" class="modal">
    <div class="modal-content">
                <h2>Iniciar Examen</h2>
                <p>El examen tiene un tiempo límite de 20 minutos. El tiempo empezará a contar desde el momento en el que inicia su intento y debe presentarse antes de que el tiempo termine, de lo contrario será reprobado. ¿Seguro que desea empezar ahora?</p>
                <form method="post" id="iniciarExamenForm">
                <div class="button-container">
                        <input type="submit" name="iniciar_examen" value="Comenzar Examen" class="modal-button">
                        <button type="button" class="modal-button" onclick="window.location.href='seguridadTC.php'">Volver atrás</button>
            </div>
        </form>
    </div>
</div>

</div>

    <script>
        var tiempoRestante = <?php echo $_SESSION['tiempo_restante']; ?>;
        var timerInterval;
        var examenIniciado = <?php echo $_SESSION['examen_iniciado'] ? 'true' : 'false'; ?>;

        function startTimer() {
            clearInterval(timerInterval);
            timerInterval = setInterval(function () {
                var minutes = Math.floor(tiempoRestante / 60);
                var seconds = tiempoRestante % 60;

                minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            document.getElementById('timer').textContent = "Tiempo restante: " + minutes + ":" + seconds;
            document.getElementById('tiempo_actual').value = tiempoRestante;

            if (--tiempoRestante < 0) {
                clearInterval(timerInterval);
                alert("El tiempo ha expirado. El examen se enviará automáticamente.");
                document.getElementById("examenForm").submit(); // Enviar examen automáticamente
            }
        }, 1000);
}

// Mostrar el modal para iniciar el examen
function mostrarModal() {
    document.getElementById('inicioModal').style.display = 'block';
}

// Si el examen ya está iniciado, inicia el temporizador
if (examenIniciado) {
    startTimer();
}

// Manejar el envío del formulario para iniciar el examen
document.getElementById('iniciarExamenForm').addEventListener('submit', function(e) {
    examenIniciado = true;
    startTimer(); // Iniciar el temporizador al comenzar el examen
});

// Reiniciar el temporizador cuando el usuario vuelve a la página
window.addEventListener('focus', function() {
    if (examenIniciado) {
        startTimer();
    }
});


    </script>
</body>
</html>