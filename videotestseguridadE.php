<?php
session_start();

// Función para inicializar o reiniciar las variables de sesión
function inicializarSesion() {
    if (!isset($_SESSION['intentos_curso_seguridadE'])) {
        $_SESSION['intentos_curso_seguridadE'] = [];
    }
    
    // ID 2 corresponde al curso de primeros auxilios
    if (!isset($_SESSION['intentos_curso_seguridadE'][20])) {
        $_SESSION['intentos_curso_seguridadE'][20] = 0;
    }
    
    if (!isset($_SESSION['notas_curso_seguridadE'])) {
        $_SESSION['notas_curso_seguridadE'] = [];
    }
    if (!isset($_SESSION['notas_curso_seguridadE'][20])) {
        $_SESSION['notas_curso_seguridadE'][20] = [];
    }
    if (!isset($_SESSION['videotest_completado_curso_seguridadE'][20])) {
        $_SESSION['videotest_completado_curso_seguridadE'][20] = false;
    }    
    
    $_SESSION['tiempo_inicio'] = 0;
    $_SESSION['tiempo_restante'] = 10 * 60;  // 20 minutos en segundos
    $_SESSION['videotest_iniciado'] = false;
    $_SESSION['videotest_completado_curso_seguridadE'][20] = false;
    $_SESSION['nota_final_curso_seguridadE'][20] = 0;
    $_SESSION['estado_final_curso_seguridadE'][20] = '';
    $_SESSION['nota_mas_alta_curso_seguridadE'][20] = 0;
    $_SESSION['id_curso'] = 20; // Curso de Uso y manejo de Extintores
}

// Inicializar sesión si no existe o si se solicita reiniciar
if (!isset($_SESSION['intentos_curso_seguridadE']) || isset($_POST['reiniciar'])) {
    inicializarSesion();
}

$duracion_examen = 10 * 60;

// Procesar la acción de iniciar el examen
if (isset($_POST['iniciar_videotest'])) {
    if ($_SESSION['intentos_curso_seguridadE'][20] < 3) {
        $_SESSION['videotest_iniciado'] = true;
        $_SESSION['tiempo_inicio'] = time();
        $_SESSION['tiempo_restante'] = $duracion_examen;
        header("Location: videotestseguridadE.php");
        exit();
    }
}

// Función para comprobar si el tiempo ha expirado
function tiempoExpirado() {
    $tiempo_transcurrido = time() - $_SESSION['tiempo_inicio'];
    return $tiempo_transcurrido >= $_SESSION['tiempo_restante'];
}

// Preguntas y opciones del examen (Cada pregunta vale 4 puntos)
$preguntas = [
    ['¿Qué documento es obligatorio antes de iniciar una excavación?', 
     ['Permiso de trabajo', 'Certificado médico', 'Orden de compra de herramientas'], 
     0, 4],

    ['¿Cuál es la profundidad mínima a partir de la cual se deben emplear sistemas de entibado o apuntalamiento?', 
     ['0.5 metros', '1.2 metros', '2 metros'], 
     1, 4],

    ['¿Cuál es el principal riesgo asociado a las excavaciones profundas?', 
     ['Caídas de altura', 'Derrumbe de las paredes', 'Exposición a ruido excesivo'], 
     1, 4],

    ['¿Qué medida se debe tomar si hay presencia de gases tóxicos en una excavación?', 
     ['Seguir trabajando con precaución', 'Ventilar el área y usar detectores de gases', 'Tapar la excavación y esperar'], 
     1, 4],

    ['¿Qué tipo de señalización debe usarse en las zonas de excavación?', 
     ['Señales luminosas únicamente', 'Señales visibles que alerten del peligro de caída y acceso restringido', 'Señales auditivas únicamente'], 
     1, 4]
];


$mensaje = '';

// Procesar respuestas del examen
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['respuestas'])) {
    if (tiempoExpirado()) {
        $_SESSION['notas_curso_seguridadE'][20][] = 0;
        $_SESSION['estado_final_curso_seguridadE'][20] = 'REPROBADO';
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
        $_SESSION['notas_curso_seguridadE'][20][] = $nota;
        $_SESSION['videotest_iniciado'] = false;

        // Evaluar estado
        if ($nota >= 13) {
            $_SESSION['estado_final_curso_seguridadE'][20] = 'APROBADO';
            $_SESSION['nota_final_curso_seguridadE'][20] = max($_SESSION['notas_curso_seguridadE'][20]);
            $_SESSION['videotest_completado_curso_seguridadE'][20] = true;
        } else {
            $_SESSION['estado_final_curso_seguridadE'][20] = 'REPROBADO';
        }

        // Actualizar la mejor nota
        if ($nota > $_SESSION['nota_mas_alta_curso_seguridadE'][20]) {
            $_SESSION['nota_mas_alta_curso_seguridadE'][20] = $nota;
        }

        // Incrementar intento actual en sesión
        $_SESSION['intentos_curso_seguridadE'][20]++;
        $intento_actual = $_SESSION['intentos_curso_seguridadE'][20];

        // Actualizar base de datos
        if (isset($_SESSION['nota_mas_alta_curso_seguridadE'][20]) && isset($_SESSION['DNI'])) {
            $conn = new mysqli('localhost', 'root', '', 'usuario');
            if ($conn->connect_error) {
                die("Error de conexión: " . $conn->connect_error);
            }

            $DNI = $_SESSION['DNI'];
            $nota_mas_alta = $_SESSION['nota_mas_alta_curso_seguridadE'][20];

            // Verificar inscripción
            $sqlVerificar = "SELECT id_curso FROM inscripciones WHERE DNI = ? AND id_curso = 20";
            $stmtVerificar = $conn->prepare($sqlVerificar);
            $stmtVerificar->bind_param("s", $DNI);
            $stmtVerificar->execute();
            $resultadoVerificar = $stmtVerificar->get_result();

            if ($resultadoVerificar->num_rows > 0) {
                // Actualizar el intento correspondiente
                $campo_intento = "intento{$intento_actual}";
                $sql = "UPDATE inscripciones 
                        SET $campo_intento = ?, nota_videotest = ? 
                        WHERE DNI = ? AND id_curso = 20";

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
            $mensaje = "Has completado el intento $intento_actual con $nota puntos. Estado: " . $_SESSION['estado_final_curso_seguridadE'][20];
        } else {
            $mensaje = "En este último intento tu nota es $nota. La nota final de tus 3 intentos ha sido " . $_SESSION['nota_mas_alta_curso_seguridadE'][20];
            $_SESSION['videotest_completado_curso_seguridadE'][20] = true;
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
    
    $sql = "UPDATE inscripciones SET `intento1` = 0, `intento2` = 0, `intento3` = 0, `nota_videotest` = 0 WHERE DNI = ? AND id_curso = 20";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $DNI);

    if ($stmt->execute()) {
        $_SESSION['nota_mas_alta_curso_seguridadE'][20] = 0;
        $_SESSION['intentos_curso_seguridadE'][20] = 0;
        $_SESSION['notas_curso_seguridadE'][20] = [];
        $_SESSION['videotest_completado_curso_seguridadE'][20] = false;
        $_SESSION['estado_final_curso_seguridadE'][20] = '';
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
    <title>Seguridad para Trabajos Eléctricos</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="estilosvideotest.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
</head>
<body>
<h1>Videotest de Seguridad para Trabajos Eléctricos</h1>

<div class="modal-background">
<div class="container">

<!-- Verificación si el Videotest ha sido completado -->
<?php if ($_SESSION['videotest_completado_curso_seguridadE'][20]): ?>
    <?php if ($_SESSION['estado_final_curso_seguridadE'][20] == 'APROBADO'): ?>
        <!-- Mensaje de éxito si se aprueba el examen -->
        <div class="mensaje">
            Felicidades! Ya has aprobado el examen con una nota de <?php echo $_SESSION['nota_final_curso_seguridadE'][20]; ?> puntos.
            No necesitas realizar más intentos.
        </div>
        <!-- Botón para volver al curso -->
        <form method="post">
            <button type="button" value="Volver a mi curso" class="volver" onclick="window.location.href='seguridadE.php'">Volver a mi curso</button>
        </form>

    <?php else: ?>
        <!-- Si no está aprobado, y se tiene menos de 3 intentos -->
        <?php if ($_SESSION['intentos_curso_seguridadE'][20] < 3): ?>
            <div class="mensaje">
                Has reprobado el examen con <?php echo $_SESSION['nota_final']; ?> puntos. Te queda 
                <?php echo 3 - $_SESSION['intentos_curso_seguridadE'][20]; ?> intento(s). Puedes intentar nuevamente.
            </div>
            <div class="mensaje">
                El puntaje más alto obtenido hasta ahora es <?php echo max($_SESSION['notas_curso_seguridadE']); ?> puntos.
            </div>
            <button onclick="mostrarModal()" class="modal-button">Continuar con el siguiente intento</button>
        <?php else: ?>
            <!-- Si ya se han agotado los intentos -->
            <form method="post">
                <input type="button" value="Volver a mi curso" class="volver" onclick="window.location.href='seguridadE.php'">
            </form>
            <div class="mensaje">
                Has agotado tus 3 intentos. Por favor, contacta al instructor para más información o reinicia el examen a continuación.
            </div>
        <?php endif; ?>
    <?php endif; ?>

<!-- Si no se ha completado el Videotest -->
<?php elseif ($_SESSION['intentos_curso_seguridadE'][20] < 3): ?>
    <?php if (!$_SESSION['videotest_iniciado']): ?>
        <!-- Botón para iniciar el Videotest -->
        <button onclick="mostrarModal()" class="modal-button">Iniciar Videotest</button>
        <input type="button" value="Cerrar y salir" class="modal-button" onclick="window.location.href='seguridadE.php'">
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
                <button type="button" class="modal-button" onclick="window.location.href='seguridadE.php '">Cancelar</button>
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
</body>
</html>    

