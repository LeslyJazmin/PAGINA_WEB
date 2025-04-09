<?php
session_start();

// Función para inicializar o reiniciar las variables de sesión
function inicializarSesion() {
    if (!isset($_SESSION['intentos_curso_indicadoresSST'])) {
        $_SESSION['intentos_curso_indicadoresSST'] = [];
    }

    if (!isset($_SESSION['intentos_curso_indicadoresSST'][23])) {
        $_SESSION['intentos_curso_indicadoresSST'][23] = 0;
    }

    if (!isset($_SESSION['notas_curso_indicadoresSST'])) {
        $_SESSION['notas_curso_indicadoresSST'] = [];
    }
    if (!isset($_SESSION['notas_curso_indicadoresSST'][23])) {
        $_SESSION['notas_curso_indicadoresSST'][23] = [];
    }
    if (!isset($_SESSION['videotest_completado_curso_indicadoresSST'][23])) {
        $_SESSION['videotest_completado_curso_indicadoresSST'][23] = false;
    }

    $_SESSION['tiempo_inicio'] = 0;
    $_SESSION['tiempo_restante'] = 10 * 60;  // 10 minutos en segundos
    $_SESSION['videotest_iniciado'] = false;
    $_SESSION['videotest_completado_curso_indicadoresSST'][23] = false;
    $_SESSION['nota_final_curso_indicadoresSST'][23] = 0;
    $_SESSION['estado_final_curso_indicadoresSST'][23] = '';
    $_SESSION['nota_mas_alta_curso_indicadoresSST'][23] = 0;
    $_SESSION['id_curso'] = 23; // Curso de Uso y manejo de Extintores
}

// Inicializar sesión si no existe o si se solicita reiniciar
if (!isset($_SESSION['intentos_curso_indicadoresSST']) || isset($_POST['reiniciar'])) {
    inicializarSesion();
}

$duracion_examen = 10 * 60;

// Conectar a la base de datos
$conn = new mysqli('localhost', 'root', '', 'usuario');
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Función para obtener las preguntas y sus opciones desde la base de datos
function obtenerPreguntas() {
    global $conn;
    $preguntas = [];
    $sql = "SELECT * FROM preguntas WHERE id_curso = 23";  // Seleccionar preguntas del curso 23
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Obtener opciones para cada pregunta
            $sqlOpciones = "SELECT * FROM opciones WHERE pregunta_id = " . $row['id'];
            $resultOpciones = $conn->query($sqlOpciones);
            $opciones = [];
            $respuesta_correcta = null;
            
            while ($opcion = $resultOpciones->fetch_assoc()) {
                $opciones[] = $opcion['opcion'];
                if ($opcion['es_correcta'] == 1) {
                    $respuesta_correcta = count($opciones) - 1;  // Indice de la opción correcta
                }
            }
            
            // Almacenar la pregunta con sus opciones y la respuesta correcta
            $preguntas[] = [
                'pregunta' => $row['pregunta'],
                'opciones' => $opciones,
                'respuesta_correcta' => $respuesta_correcta
            ];
        }
    } else {
        echo "No se encontraron preguntas para este curso.";
    }

    return $preguntas;
}

// Obtener las preguntas del examen
$preguntas = obtenerPreguntas();

$mensaje = '';

// Procesar respuestas del examen
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['respuestas'])) {
    if (tiempoExpirado()) {
        $_SESSION['notas_curso_indicadoresSST'][23][] = 0;
        $_SESSION['estado_final_curso_indicadoresSST'][23] = 'REPROBADO';
        $mensaje = "Tiempo agotado. Has reprobado el examen.";
    } else {
        $respuestas = $_POST['respuestas'];
        $nota = 0;

        foreach ($respuestas as $index => $respuesta) {
            if ($respuesta == $preguntas[$index]['respuesta_correcta']) {
                $nota += 4;
            }
        }

        // Guardar la nota en la sesión (para todos los intentos)
        $_SESSION['notas_curso_indicadoresSST'][23][] = $nota;

        // Evaluar estado
        if ($nota >= 13) {
            $_SESSION['estado_final_curso_indicadoresSST'][23] = 'APROBADO';
        } else {
            $_SESSION['estado_final_curso_indicadoresSST'][23] = 'REPROBADO';
        }

        // Actualizar la mejor nota obtenida
        if ($nota > $_SESSION['nota_mas_alta_curso_indicadoresSST'][23]) {
            $_SESSION['nota_mas_alta_curso_indicadoresSST'][23] = $nota;
        }

        // Incrementar intento actual en sesión
        $_SESSION['intentos_curso_indicadoresSST'][23]++;
        $intento_actual = $_SESSION['intentos_curso_indicadoresSST'][23];

        // Mostrar mensaje al usuario
        if ($intento_actual < 3) {
            $mensaje = "Has completado el intento $intento_actual con $nota puntos. Estado: " . $_SESSION['estado_final_curso_indicadoresSST'][23];
        } else {
            // Nota final con la mejor calificación de los intentos
            $_SESSION['nota_final_curso_indicadoresSST'][23] = $_SESSION['nota_mas_alta_curso_indicadoresSST'][23];
            $mensaje = "En este último intento tu nota es $nota. La mejor nota final de tus 3 intentos ha sido " . $_SESSION['nota_final_curso_indicadoresSST'][23];
            $_SESSION['videotest_completado_curso_indicadoresSST'][23] = true;
        }
    }
}

// Función para comprobar si el tiempo ha expirado
function tiempoExpirado() {
    $tiempo_transcurrido = time() - $_SESSION['tiempo_inicio'];
    return $tiempo_transcurrido >= $_SESSION['tiempo_restante'];
}

// Función para iniciar el videotest
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['iniciar_videotest'])) {
    $_SESSION['videotest_iniciado'] = true;
    $_SESSION['tiempo_inicio'] = time();  // Guardar el tiempo de inicio
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indicadores de Gestión de SST</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="estilosvideotest.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<h1>Videotest de Indicadores de Gestión de SST</h1>

<div class="modal-background">
<div class="container">

<?php if ($_SESSION['videotest_completado_curso_indicadoresSST'][23]): ?>
    <?php if ($_SESSION['estado_final_curso_indicadoresSST'][23] == 'APROBADO'): ?>
        <div class="mensaje">
            Felicidades! Ya has aprobado el examen con una nota de <?php echo $_SESSION['nota_final_curso_indicadoresSST'][23]; ?> puntos.
            No necesitas realizar más intentos.
        </div>
        <form method="post">
            <button type="button" class="volver" onclick="window.location.href='indicadoresSST.php'">Volver a mi curso</button>
        </form>
    <?php else: ?>
        <?php if ($_SESSION['intentos_curso_indicadoresSST'][23] < 3): ?>
            <div class="mensaje">
                Has reprobado el examen con <?php echo $_SESSION['nota_final']; ?> puntos. Te queda
                <?php echo 3 - $_SESSION['intentos_curso_indicadoresSST'][23]; ?> intento(s). Puedes intentar nuevamente.
            </div>
            <div class="mensaje">
                El puntaje más alto obtenido hasta ahora es <?php echo max($_SESSION['notas_curso_indicadoresSST']); ?> puntos.
            </div>
            <button onclick="mostrarModal()" class="modal-button">Continuar con el siguiente intento</button>
        <?php else: ?>
            <form method="post">
                <input type="button" value="Volver a mi curso" class="volver" onclick="window.location.href='indicadoresSST.php'">
            </form>
            <div class="mensaje">
                Has agotado tus 3 intentos. Por favor, contacta al instructor para más información o reinicia el examen a continuación.
            </div>
        <?php endif; ?>
    <?php endif; ?>

<?php elseif ($_SESSION['intentos_curso_indicadoresSST'][23] < 3): ?>
    <?php if (!$_SESSION['videotest_iniciado']): ?>
        <form method="POST">
            <button type="submit" name="iniciar_videotest" class="modal-button">Iniciar Videotest</button>
        </form>
        <input type="button" value="Cerrar y salir" class="modal-button" onclick="window.location.href='indicadoresSST.php'">
    <?php else: ?>
        <div id="timer">Tiempo restante: 10:00</div>
        <form action="" method="POST">
            <?php
            if (!empty($preguntas)) {
                foreach ($preguntas as $index => $pregunta) {
                    echo "<fieldset>";
                    echo "<legend>{$pregunta['pregunta']}</legend>";
                    foreach ($pregunta['opciones'] as $key => $opcion) {
                        echo "<input type='radio' name='respuestas[$index]' value='$key' required> $opcion<br>";
                    }
                    echo "</fieldset>";
                }
            }
            ?>
            <button type="submit" class="submit-button">Enviar respuestas</button>
        </form>
    <?php endif; ?>
<?php endif; ?>

</div>
</div>

<script src="temporalizador.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>
