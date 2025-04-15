<?php
session_start();

// Función para inicializar o reiniciar las variables de sesión
function inicializarSesion() {
    if (!isset($_SESSION['intentos_curso_Homologacion6G'])) {
        $_SESSION['intentos_curso_Homologacion6G'] = [];
    }
    
    // ID 2 corresponde al curso de primeros auxilios
    if (!isset($_SESSION['intentos_curso_Homologacion6G'][30])) {
        $_SESSION['intentos_curso_Homologacion6G'][30] = 0;
    }
    
    if (!isset($_SESSION['notas_curso_Homologacion6G'])) {
        $_SESSION['notas_curso_Homologacion6G'] = [];
    }
    if (!isset($_SESSION['notas_curso_Homologacion6G'][30])) {
        $_SESSION['notas_curso_Homologacion6G'][30] = [];
    }
    if (!isset($_SESSION['videotest_completado_curso_Homologacion6G'][30])) {
        $_SESSION['videotest_completado_curso_Homologacion6G'][30] = false;
    }    
    
    $_SESSION['tiempo_inicio'] = 0;
    $_SESSION['tiempo_restante'] = 10 * 60;  // 20 minutos en segundos
    $_SESSION['videotest_iniciado'] = false;
    $_SESSION['videotest_completado_curso_Homologacion6G'][30] = false;
    $_SESSION['nota_final_curso_Homologacion6G'][30] = 0;
    $_SESSION['estado_final_curso_Homologacion6G'][30] = '';
    $_SESSION['nota_mas_alta_curso_Homologacion6G'][30] = 0;
    $_SESSION['id_curso'] = 30; 
}

// Inicializar sesión si no existe o si se solicita reiniciar
if (!isset($_SESSION['intentos_curso_Homologacion6G']) || isset($_POST['reiniciar'])) {
    inicializarSesion();
}

$duracion_examen = 10 * 60;

// Procesar la acción de iniciar el examen
if (isset($_POST['iniciar_videotest'])) {
    if ($_SESSION['intentos_curso_Homologacion6G'][30] < 3) {
        $_SESSION['videotest_iniciado'] = true;
        $_SESSION['tiempo_inicio'] = time();
        $_SESSION['tiempo_restante'] = $duracion_examen;
        header("Location: videotestHomologacion6G.php");
        exit();
    }
}

// Función para comprobar si el tiempo ha expirado
function tiempoExpirado() {
    $tiempo_transcurrido = time() - $_SESSION['tiempo_inicio'];
    return $tiempo_transcurrido >= $_SESSION['tiempo_restante'];
}


// Preguntas y opciones del examen (Cada pregunta vale 4 puntos)
$preguntas = [];
$conn = new mysqli('localhost', 'root', '', 'usuario');

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener las preguntas del curso con id = 1
$sql_preguntas = "SELECT id, pregunta FROM preguntas WHERE id_curso = 30";
$result_preguntas = $conn->query($sql_preguntas);

if ($result_preguntas && $result_preguntas->num_rows > 0) {
    while ($row_pregunta = $result_preguntas->fetch_assoc()) {
        $id_pregunta = $row_pregunta['id'];
        $pregunta = $row_pregunta['pregunta'];

        // Obtener las opciones de respuesta con indicador de si es correcta
        $sql_opciones = "SELECT opcion, es_correcta FROM opciones WHERE pregunta_id = ? ORDER BY id ASC";
        $stmt_opciones = $conn->prepare($sql_opciones);
        $stmt_opciones->bind_param("i", $id_pregunta);
        $stmt_opciones->execute();
        $result_opciones = $stmt_opciones->get_result();

        $opciones = [];
        $respuesta_correcta = -1;
        $indice = 0;

        while ($row_opcion = $result_opciones->fetch_assoc()) {
            $opciones[] = $row_opcion['opcion'];
            if ($row_opcion['es_correcta']) {
                $respuesta_correcta = $indice;
            }
            $indice++;
        }

        // Agregar al arreglo de preguntas
        $preguntas[] = [$pregunta, $opciones, $respuesta_correcta, 4];
        $stmt_opciones->close();
    }
}

$conn->close();

$mostrar_modal = false;

if (count($preguntas) === 0) {
    $mostrar_modal = true;
}

$mensaje = '';

// Procesar respuestas del examen
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['respuestas'])) {
    if (tiempoExpirado()) {
        $_SESSION['notas_curso_Homologacion6G'][30][] = 0;
        $_SESSION['estado_final_curso_Homologacion6G'][30] = 'REPROBADO';
        $mensaje = "Tiempo agotado. Has reprobado el examen.";
    } else {
        $respuestas = $_POST['respuestas'];
        $nota = 0;

        foreach ($respuestas as $index => $respuesta) {
            if ($respuesta == $preguntas[$index][2]) {
                $nota += 4;
            }
        }

        // Guardar la nota en la sesión
        $_SESSION['notas_curso_Homologacion6G'][30][] = $nota;
        $_SESSION['videotest_iniciado'] = false;

        // Evaluar estado
        if ($nota >= 13) {
            $_SESSION['estado_final_curso_Homologacion6G'][30] = 'APROBADO';
            $_SESSION['nota_final_curso_Homologacion6G'][30] = max($_SESSION['notas_curso_Homologacion6G'][30]);
            $_SESSION['videotest_completado_curso_Homologacion6G'][30] = true;
        } else {
            $_SESSION['estado_final_curso_Homologacion6G'][30] = 'REPROBADO';
        }

        // Actualizar la mejor nota
        if ($nota > $_SESSION['nota_mas_alta_curso_Homologacion6G'][30]) {
            $_SESSION['nota_mas_alta_curso_Homologacion6G'][30] = $nota;
        }

        // Incrementar intento actual en sesión
        $_SESSION['intentos_curso_Homologacion6G'][30]++;
        $intento_actual = $_SESSION['intentos_curso_Homologacion6G'][30];

        // Actualizar base de datos
        if (isset($_SESSION['nota_mas_alta_curso_Homologacion6G'][30]) && isset($_SESSION['DNI'])) {
            $conn = new mysqli('localhost', 'root', '', 'usuario');
            if ($conn->connect_error) {
                die("Error de conexión: " . $conn->connect_error);
            }

            $DNI = $_SESSION['DNI'];
            $nota_mas_alta = $_SESSION['nota_mas_alta_curso_Homologacion6G'][30];

            // Verificar inscripción
            $sqlVerificar = "SELECT id_curso FROM inscripciones WHERE DNI = ? AND id_curso = 30";
            $stmtVerificar = $conn->prepare($sqlVerificar);
            $stmtVerificar->bind_param("s", $DNI);
            $stmtVerificar->execute();
            $resultadoVerificar = $stmtVerificar->get_result();

            if ($resultadoVerificar->num_rows > 0) {
                // Actualizar el intento correspondiente
                $campo_intento = "intento{$intento_actual}";
                $sql = "UPDATE inscripciones 
                        SET $campo_intento = ?, nota_videotest = ? 
                        WHERE DNI = ? AND id_curso = 30";

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
            $mensaje = "Has completado el intento $intento_actual con $nota puntos. Estado: " . $_SESSION['estado_final_curso_Homologacion6G'][30];
        } else {
            $mensaje = "En este último intento tu nota es $nota. La nota final de tus 3 intentos ha sido " . $_SESSION['nota_mas_alta_curso_Homologacion6G'][30];
            $_SESSION['videotest_completado_curso_Homologacion6G'][30] = true;
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
    
    $sql = "UPDATE inscripciones SET `intento1` = 0, `intento2` = 0, `intento3` = 0, `nota_videotest` = 0 WHERE DNI = ? AND id_curso = 30";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $DNI);

    if ($stmt->execute()) {
        $_SESSION['nota_mas_alta_curso_Homologacion6G'][30] = 0;
        $_SESSION['intentos_curso_Homologacion6G'][30] = 0;
        $_SESSION['notas_curso_Homologacion6G'][30] = [];
        $_SESSION['videotest_completado_curso_Homologacion6G'][30] = false;
        $_SESSION['estado_final_curso_Homologacion6G'][30] = '';
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
    <title>Homologación 6G en proceso smaw</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="estilosvideotest.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
</head>
<body>
<h1>Videotest de Homologación 6G en proceso smaw</h1>

<div class="modal-background">
<div class="container">

<!-- Verificación si el Videotest ha sido completado -->
<?php if ($_SESSION['videotest_completado_curso_Homologacion6G'][30]): ?>
    <?php if ($_SESSION['estado_final_curso_Homologacion6G'][30] == 'APROBADO'): ?>
        <!-- Mensaje de éxito si se aprueba el examen -->
        <div class="mensaje">
            Felicidades! Ya has aprobado el examen con una nota de <?php echo $_SESSION['nota_final_curso_Homologacion6G'][30]; ?> puntos.
            No necesitas realizar más intentos.
        </div>
        <!-- Botón para volver al curso -->
        <form method="post">
            <button type="button" value="Volver a mi curso" class="volver" onclick="window.location.href='Homologacion6G.php'">Volver a mi curso</button>
        </form>

    <?php else: ?>
        <!-- Si no está aprobado, y se tiene menos de 3 intentos -->
        <?php if ($_SESSION['intentos_curso_Homologacion6G'][30] < 3): ?>
            <div class="mensaje">
                Has reprobado el examen con <?php echo $_SESSION['nota_final']; ?> puntos. Te queda 
                <?php echo 3 - $_SESSION['intentos_curso_Homologacion6G'][30]; ?> intento(s). Puedes intentar nuevamente.
            </div>
            <div class="mensaje">
                El puntaje más alto obtenido hasta ahora es <?php echo max($_SESSION['notas_curso_Homologacion6G']); ?> puntos.
            </div>
            <button onclick="mostrarModal()" class="modal-button">Continuar con el siguiente intento</button>
        <?php else: ?>
            <!-- Si ya se han agotado los intentos -->
            <form method="post">
                <input type="button" value="Volver a mi curso" class="volver" onclick="window.location.href='Homologacion6G.php'">
            </form>
            <div class="mensaje">
                Has agotado tus 3 intentos. Por favor, contacta al instructor para más información o reinicia el examen a continuación.
            </div>
        <?php endif; ?>
    <?php endif; ?>

<!-- Si no se ha completado el Videotest -->
<?php elseif ($_SESSION['intentos_curso_Homologacion6G'][30] < 3): ?>
    <?php if (!$_SESSION['videotest_iniciado']): ?>
        <!-- Botón para iniciar el Videotest -->
        <?php if (!$mostrar_modal): ?>
            <form method="post">
                <button type="submit" name="iniciar_videotest" class="modal-button">Iniciar Videotest</button>
            </form>
        <?php endif; ?>
        <input type="button" value="Cerrar y salir" class="modal-button" onclick="window.location.href='Homologacion6G.php'">
    <?php else: ?>
        <!-- Temporizador y formulario del examen -->
        <div id="timer">Tiempo restante: 10:00</div>
        <form method="post" id="examenForm">
            <?php foreach ($preguntas as $index => $pregunta): ?>
                <div class="pregunta">
                    <p><strong><?php echo ($index + 1) . '. ' . $pregunta[0]; ?></strong></p>
                    <?php foreach ($pregunta[1] as $opcion_index => $opcion): ?>
                        <label>
                            <input type="radio" name="respuestas[<?php echo $index; ?>]" value="<?php echo $opcion_index; ?>" required>
                            <?php echo $opcion; ?>
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
        <h2>Iniciar Videotest</h2>
        <p>El videotest consta de un tiempo límite de 10 minutos. Recuerda una vez iniciado el videotest ya no podrás salir de él y si deseas cerrar automáticamente se te agregará la nota respectiva de acuerdo a lo que marcaste.</p>
        <form method="post" id="iniciarExamenForm">
            <div class="button-container">
                <input type="submit" name="iniciar_videotest" value="Comenzar Videotest" class="modal-button">
                <button type="button" class="modal-button" onclick="window.location.href='Homologacion6G.php '">Cancelar</button>
            </div>
        </form>
    </div>
</div>

</div>

    <script>
        var tiempoRestante = 600; // 10 minutos en segundos
        var timerInterval;
        var examenIniciado = <?php echo $_SESSION['videotest_iniciado'] ? 'true' : 'false'; ?>;

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
                document.getElementById("examenForm").submit();  // Enviar examen automáticamente
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
    startTimer();  // Iniciar el temporizador al comenzar el examen
});

// Reiniciar el temporizador cuando el usuario vuelve a la página
window.addEventListener('focus', function() {
    if (examenIniciado) {
        startTimer();
    }
});
    </script>

        <!-- Modal para "No hay preguntas registradas" -->
<div id="modalSinPreguntas" class="modal-overlay" style="display: <?= $mostrar_modal ? 'flex' : 'none' ?>;">
    <div class="modal-content">
        <h3>No hay preguntas registradas</h3>
        <p>Actualmente no hay preguntas disponibles para este curso.</p>
        <button onclick="window.location.href='Homologacion6G.php'">Cerrar</button>
    </div>
</div>

</body>
</html>    

