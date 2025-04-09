<?php 
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['DNI'])) {
    header("Location: aula_virtual.php");
    exit();
}

// Conexión a la base de datos
$host = 'localhost';
$db = 'usuario';
$user = 'root';
$password = '';

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta para obtener la nota más alta
$DNI = $_SESSION['DNI'];
$sql = "SELECT GREATEST(IFNULL(intento1, 0), IFNULL(intento2, 0), IFNULL(intento3, 0)) AS nota_videotestofimatica
        FROM inscripciones 
        WHERE DNI = ? AND id_curso = 27";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $DNI);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['nota_videotest_ofimatica'] = $row['nota_videotestofimatica'];
} else {
    $_SESSION['nota_videotest_ofimatica'] = '--'; // Valor por defecto si no hay datos
}
$stmt->close(); // Cerramos el statement aquí

// Consulta para obtener las fechas del videotest
$sql_fechas = "SELECT fecha_inicio_videotest, hora_inicio_videotest, fecha_fin_videotest, hora_fin_videotest,
                fecha_inicio_examen, hora_inicio_examen, fecha_fin_examen, hora_fin_examen
               FROM examenes 
               WHERE id_curso = 27
               LIMIT 1";

$stmt_fechas = $conn->prepare($sql_fechas);
$stmt_fechas->execute();
$result_fechas = $stmt_fechas->get_result();

if ($result_fechas->num_rows > 0) {
    $fechas = $result_fechas->fetch_assoc();
    $fecha_inicio = $fechas['fecha_inicio_videotest'] ?? '--';
    $hora_inicio = $fechas['hora_inicio_videotest'] ?? '--';
    $fecha_entrega = $fechas['fecha_fin_videotest'] ?? '--';
    $hora_entrega = $fechas['hora_fin_videotest'] ?? '--';

    //examenes 
    $fecha_inicio_examen = $fechas['fecha_inicio_examen'] ?? '--';
    $hora_inicio_examen = $fechas['hora_inicio_examen'] ?? '--';
    $fecha_entrega_examen = $fechas['fecha_fin_examen'] ?? '--';
    $hora_entrega_examen = $fechas['hora_fin_examen'] ?? '--';

} else {
    $fecha_inicio = '--';
    $hora_inicio = '--';
    $fecha_entrega = '--';
    $hora_entrega = '--';

    //examenes
    $fecha_inicio_examen = '--';
    $hora_inicio_examen = '--';
    $fecha_entrega_examen = '--';
    $hora_entrega_examen = '--';
}

$stmt_fechas->close(); // Cerramos el statement aquí

// Consulta para obtener materiales
$material_query = "SELECT link_curso_material, link_curso_video FROM material WHERE id_curso = ? LIMIT 1";
$stmt_material = $conn->prepare($material_query);
$id_curso = 27; // ID fijo del curso en este ejemplo
$stmt_material->bind_param("i", $id_curso);
$stmt_material->execute();
$resultado_material = $stmt_material->get_result();

if ($resultado_material->num_rows > 0) {
    $material = $resultado_material->fetch_assoc();
    $link_material = $material['link_curso_material'];
    $link_video = $material['link_curso_video'];
} else {
    $link_material = null;
    $link_video = null;
}
$stmt_material->close(); // Cerramos el statement aquí

// Ahora cerramos la conexión
$conn->close();

// Mostrar la nota
$nota = $_SESSION['nota_videotest_ofimatica'];

// Verificar si el usuario ha visto el PDF y el video
$pdf_visto = isset($_SESSION['pdf_visto']) ? $_SESSION['pdf_visto'] : false;
$video_visto = isset($_SESSION['video_visto']) ? $_SESSION['video_visto'] : false;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ofimática</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="pa.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.body.style.zoom = "90%";
        if (window.matchMedia("(max-width: 480px)").matches) {
            document.body.style.zoom = "55%";
        }
    });

    window.addEventListener('resize', function() {
        if (window.matchMedia("(max-width: 480px)").matches) {
            document.body.style.zoom = "55%";
        } else {
            document.body.style.zoom = "90%";
        }
    });
</script>
<body>
    <div class="container">
        <!-- Barra lateral -->
        <aside class="sidebar">
            <nav>
                <ul>
                    <li><a href="#"><i class="fas fa-home"></i></a></li>
                    <li><a href="curso.php"><i class="fas fa-book"></i></a></li>
                    <li><a href="perfil.php"><i class="fas fa-user"></i></a></li>
                    <li><a href="calendario.php"><i class="fas fa-calendar-alt"></i></a></li>
                    <li><a href="cerrar_s.php" onclick="return confirmLogout();" class="cerrar-sesion-boton"><i class="fas fa-sign-out-alt"></i></a></li>
                </ul>
            </nav>
        </aside>
        <div class="main-content">
            <div class="image-info-container">
                <div class="image-container">
                    <img src="images/ofimatica.jpg" alt="Imagen de ofimatica">
                    <div class="overlay">
                        <h2>Curso de ofimatica</h2>
                    </div>
                </div>
                <div class="table-container">
                    <table>
                        <tr class="table-title">
                        <td>MATERIAL</td>
                    </tr>
                    <tr class="table-link">
                        <td>
                            <i class="fas fa-file-pdf"></i> 
                            <?php if ($link_material): ?>
                                <a href="#" onclick="openPdfWindow('<?php echo $link_material; ?>');">Material - Ofimática</a>
                            <?php else: ?>
                                <span>No hay material disponible</span>
                            <?php endif; ?>
                            <?php if ($pdf_visto): ?>
                                <div class="status-box">✓</div>
                            <?php else: ?>
                                <div class="status-box hidden">✓</div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr class="table-title">
                        <td>AUTOEVALUACIÓN</td>
                    </tr>
                    <tr class="table-link">
                        <td>
                            <i class="fas fa-play-circle"></i> 
                            <?php if ($link_video): ?>
                                <a href="#" onclick="openVideoWindow('<?php echo $link_video; ?>');">Video - ofimática</a>
                            <?php else: ?>
                                <span>No hay video disponible</span>
                            <?php endif; ?>
                            <?php if ($video_visto): ?>
                                <div class="status-box">✓</div>
                            <?php else: ?>
                                <div class="status-box hidden">✓</div>
                            <?php endif; ?>
                        </td>
                    </tr>
                        <tr class="table-link">
                            <td style="width: 100%;">
                                <div class="contenido-celda">
                                    <span>
                                        <i class="fas fa-video"></i>
                                        <a href="#" id="openModal" onclick="checkRequirements(event)">Videotest</a>
                                    </span>
                                    <div class="nota-cuadro">
                                        Nota: 
                                        <span id="notaVideotest">
                                            <?php echo $nota; ?>
                                        </span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tbody>
                            <tr class="table-title">
                                <td>EXAMEN FINAL </td>
                            </tr>
                            <tr class="table-link">
                                <td>
                                <i class="fas fa-book"></i> 
            <?php if ($_SESSION['nota_videotest_ofimatica'] == 16 || $_SESSION['nota_videotest_ofimatica'] == 20): ?>
                <a href="#" id="abrirExamen">Examen final</a>
            <?php else: ?>
                <span>Debes aprobar el Videotest para acceder al examen final.</span>
            <?php endif; ?>
            <?php
            if (isset($_SESSION['nota_final']) && isset($_SESSION['estado'])) {
                echo "<span class='examen-info'>";
                echo "Nota más alta: " . $_SESSION['nota_final'] . " - ";
                echo "Estado: " . $_SESSION['estado'];
                echo "</span>";
            }
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr class="table-title">
                                <td><br>OBTEN TU CERTIFICADO AQUI</td>
                            </tr>
                            <tr class="table-form">
                                <td>
                                <?php
// Incluir el archivo de conexión si no lo has hecho ya
include 'conexion.php';  // Asegúrate de que la conexión a la base de datos esté disponible



// Suponiendo que el DNI del usuario está guardado en la sesión
$DNI_usuario = $_SESSION['DNI'];  // El DNI del usuario que está logueado
$id_curso = 27;  // El ID del curso actual (puedes cambiarlo según el curso específico)

// Consulta SQL para obtener la nota final de este curso para el usuario
$query = "SELECT examen_final FROM inscripciones WHERE DNI = ? AND id_curso = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$DNI_usuario, $id_curso]);
$nota_final = $stmt->fetchColumn();  // Obtener la nota final del examen

// Ahora, verificamos si la nota es suficiente para obtener el certificado
if ($nota_final == 16 || $nota_final == 20):
?>
    <form action="buscar_curso.php" method="post" class="form-certificado">
        <label for="id_curso">Nombre del curso:</label>
        <span> Ofimática </span>
        <input type="hidden" id="id_curso" name="id_curso" value="27">
        <br>
        <label for="DNI">DNI:</label>
        <input type="text" id="DNI" name="DNI" value="<?php echo htmlspecialchars($DNI_usuario); ?>" readonly>
        <br>
        <button type="submit">DESCARGAR</button>
    </form>
<?php else: ?>
    <p>Debes aprobar el examen final con una nota de 16 o 20 para obtener el certificado.</p>
<?php endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Panel lateral para el examen -->
        <div id="examenPanel">
            <span id="cerrarPanel">&times;</span>
            <div class="panel-content">
                <h2>Examen ofimatica</h2>
                <img src="images/ofimi.jpg" alt="Descripción de la imagen" style="width: 100%; margin-bottom: 20px; border-radius: 5px;">
                <h3>Informacion</h3>
                <div class="exam-details">
                <div class="detail-container">
                 <i class="fas fa-calendar-alt"></i>
                    <span id="fecha_entrega">Fecha de inicio: <?php echo htmlspecialchars($fecha_inicio_examen); ?></span>
                </div>
                <div class="detailes-container">
                <i class="fas fa-clock"></i> 
                    <span id="hora_entrega">Hora de inicio: <?php echo htmlspecialchars($hora_inicio_examen); ?></span>
                </div>
                <div class="detail-container">
                <i class="fas fa-calendar-alt"></i>
                    <span id="fecha_entrega">Fecha de entrega: <?php echo htmlspecialchars($fecha_entrega_examen); ?></span>
                </div>
                <div class="detailes-container">
                 <i class="fas fa-clock"></i> 
                    <span id="hora_entrega">Hora de entrega: <?php echo htmlspecialchars($hora_entrega_examen); ?></span>
                </div>
                    <div class="detail-container">
                        <i class="fas fa-clock"></i>
                        <span>Límite de tiempo: 20 minutos</span>
                    </div>
                    <div class="detail-container">
                        <i class="fas fa-undo-alt"></i>
                        <span>Cantidad de intentos: 3</span>
                    </div>
                    <div class="detail-container">
                    <i class="fas fa-trophy"></i>
                    <span>Puntaje más alto: <span id="highestScorePA"><?php echo isset($_SESSION['nota_mas_alta_por_curso_ofimatica'][27]) ? $_SESSION['nota_mas_alta_por_curso_ofimatica'][27] : 0; ?></span></span>
                </div>
                    
                <form method="POST" action="examenofimatica.php" id="examForm">
    <input type="hidden" name="iniciar_examen" value="1">
    <button class="fullscreen-button" id="iniciarIntento" style="display: none;">Iniciar intento</button>
</form>
<script>
 document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar elementos necesarios
    const startDateElement = document.querySelectorAll('#fecha_entrega')[0];
    const startTimeElement = document.querySelectorAll('#hora_entrega')[0];
    const endDateElement = document.querySelectorAll('#fecha_entrega')[1];
    const endTimeElement = document.querySelectorAll('#hora_entrega')[1];
    const iniciarIntentoButton = document.getElementById('iniciarIntento');
    const panelContent = document.querySelector('.panel-content');

    // Analizar fechas y horas de inicio y fin
    const startDateStr = startDateElement.textContent.replace('Fecha de inicio: ', '').trim();
    const startTimeStr = startTimeElement.textContent.replace('Hora de inicio: ', '').trim();
    const endDateStr = endDateElement.textContent.replace('Fecha de entrega: ', '').trim();
    const endTimeStr = endTimeElement.textContent.replace('Hora de entrega: ', '').trim();

    function parseCustomDate(dateStr, timeStr) {
        // Split the date and time
        const [day, month, year] = dateStr.split('/');
        const [hours, minutes] = timeStr.split(':');
        
        // Create a new Date object (month is 0-indexed in JavaScript)
        return new Date(year, month - 1, day, hours, minutes);
    }

    function checkExamAvailability() {
        const currentDateTime = new Date();
        const startDateTime = parseCustomDate(startDateStr, startTimeStr);
        const endDateTime = parseCustomDate(endDateStr, endTimeStr);

        // Verificar si el examen ya ha expirado
        if (currentDateTime > endDateTime) {
            iniciarIntentoButton.style.display = 'none';
            displayMessage('Lo siento, el período de examen ya expiró. No puede realizar el examen.', 'error');
            return false;
        }

        // Verificar si el examen está disponible
        if (currentDateTime >= startDateTime) {
            iniciarIntentoButton.style.display = 'block';
            
            // Eliminar cualquier mensaje existente
            const existingMessage = panelContent.querySelector('.exam-message');
            if (existingMessage) {
                existingMessage.remove();
            }
            
            return true;
        } else {
            // Calcular el tiempo restante hasta el inicio del examen
            const timeDiff = startDateTime.getTime() - currentDateTime.getTime();
            const hoursDiff = Math.floor(timeDiff / (1000 * 60 * 60));
            const minutesDiff = Math.floor((timeDiff % (1000 * 60 * 60)) / (1000 * 60));

            iniciarIntentoButton.style.display = 'none';
            displayMessage(`El examen se habilitará el ${startDateStr} a las ${startTimeStr}. Tiempo restante: ${hoursDiff} horas y ${minutesDiff} minutos.`, 'info');
            return false;
        }
    }

    function displayMessage(message, type = 'info') {
        // Eliminar cualquier mensaje existente
        const existingMessage = panelContent.querySelector('.exam-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        // Crear nuevo elemento de mensaje
        const messageElement = document.createElement('div');
        messageElement.className = `exam-message ${type}`;
        messageElement.textContent = message;

        // Insertar el mensaje antes del primer contenedor de detalles
        const firstDetailContainer = panelContent.querySelector('.detail-container');
        firstDetailContainer.parentNode.insertBefore(messageElement, firstDetailContainer);
    }

    // Verificación inicial de disponibilidad
    checkExamAvailability();

    // Verificación continua de disponibilidad (cada segundo para mayor precisión)
    const availabilityInterval = setInterval(checkExamAvailability, 1000);

    // Agregar evento de clic al botón de inicio de examen
    iniciarIntentoButton.addEventListener('click', function(e) {
        if (!checkExamAvailability()) {
            e.preventDefault();
        }
    });
});
</script>
<style>
    .exam-message {
    padding: 10px 15px;
    margin-bottom: 15px;
    border-radius: 5px;
    font-weight: bold;
}

.exam-message.info {
    background-color: #e7f3fe;
    border-left: 5px solid #2196F3;
    color: #005cbf;
}

.exam-message.error {
    background-color: #ffdddd;
    border-left: 5px solid #f44336;
    color: #d8000c;
}
</style>
                </div>
            </div>
        </div>

        <script>
            function actualizarPuntajeMasAltoPA() {
                var puntajeMasAlto = <?php echo isset($_SESSION['nota_mas_alta_ofimatica']) ? $_SESSION['nota_mas_alta_ofimatica'] : 0; ?>;
                document.getElementById('highestScorePA').textContent = puntajeMasAlto;
            }

            window.onload = function() {
                actualizarPuntajeMasAltoPA();
            };
        </script>
        <style>
            .form-certificado input[type="text"] {
                width: calc(120px - 12px); /* Ajustar al ancho del formulario */
                padding: 8px;
                font-size: 14px;
                color: #000; /* Cambiar el color del texto a negro */
                background-color: #f2f2f2; /* Fondo gris claro */
                border: 1px solid #ccc; /* Borde gris */
                border-radius: 4px; /* Bordes redondeados */
                margin-bottom: 12px; /* Separación entre campos */
                pointer-events: none; /* Deshabilitado visualmente */
                text-align: center; /* Centrar el texto dentro del cuadro */
            }
            .exam-details {
                margin: 20px 0;
            }

            .detail-container {
                display: flex;
                align-items: center;
                width: 100%;
                padding: 10px;
                border: 1px solid #e0e0e0;
                border-radius: 5px;
                background-color: #f9f9f9;
                box-sizing: border-box;
                margin-bottom: 10px;
            }

            .detail-container i {
                margin-right: 15px;
                font-size: 20px;
            }
            .detailes-container {
                display: flex;
                align-items: left;
                width: 300px;
                padding: 10px;
                border: 1px solid #e0e0e0;
                border-radius: 5px;
                background-color: #0a507e;
                box-sizing: border-box;
                margin-bottom: 10px;
                color: #f4f4f4;
            }

            .detailes-container i {
                margin-right: 15px;
                font-size: 20px;
                color: #f4f4f4;
            }

            #iniciarIntento {
                padding: 10px 20px;
                background-color: #007bff;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                width: 100%;
            }

            #iniciarIntento:hover {
                background-color: #0056b3;
            }
        </style>

        <script>
            document.getElementById('abrirExamen').addEventListener('click', function(e) {
                e.preventDefault();
                document.getElementById('examenPanel').classList.add('active');
            });

            document.getElementById('cerrarPanel').addEventListener('click', function() {
                document.getElementById('examenPanel').classList.remove('active');
            });

            function confirmLogout() {
                return confirm("¿Estás seguro de que deseas cerrar sesión?");
            }
        </script>

        <div id="videotestModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeModal">&times;</span>
                <h2> Videotest de Ofimática </h2>
                <img src="images/ofinati.jpg" alt="Imagen de Ofimática">
              <h3>Información</h3>
        <!-- Contenedor para el mensaje de disponibilidad -->
        <div id="mensajeDisponibilidad" class="availability-message-container">
            <!-- Este mensaje se actualizará dinámicamente desde el JS -->
        </div>
        <div class="exam-details">
            <div class="detail-container">
                <i class="fas fa-calendar-alt"></i>
                <span id="fecha_inicio">Fecha de inicio: <?php echo htmlspecialchars($fecha_inicio); ?></span>
            </div>
            <div class="detailes-container">
                <i class="fas fa-clock"></i> 
                <span id="hora_inicio">Hora de inicio: <?php echo htmlspecialchars($hora_inicio); ?></span>
            </div>
            <div class="detail-container">
                <i class="fas fa-calendar-alt"></i>
                <span id="fecha_entrega">Fecha de entrega: <?php echo htmlspecialchars($fecha_entrega); ?></span>
            </div>
            <div class="detailes-container">
                <i class="fas fa-clock"></i> 
                <span id="hora_entrega">Hora de entrega: <?php echo htmlspecialchars($hora_entrega); ?></span>
            </div>
            <div class="detail-container">
                <i class="fas fa-hourglass-start"></i> 
                <span> Límite de tiempo: 10 minutos</span>
            </div>
            <div class="detail-container">
                <i class="fas fa-undo-alt"></i> 
                <span> Cantidad de intentos: 3</span>
            </div>
        </div>

        <button class="fullscreen-button" id="fullscreenBtn" onclick="redirigirAVideoTest()">Iniciar videotest</button>
    </div>
</div>
<script>
// Obtener las fechas y horas de inicio y entrega desde PHP
var fechaInicio = "<?php echo $fecha_inicio; ?>";
var horaInicio = "<?php echo $hora_inicio; ?>";
var fechaEntrega = "<?php echo $fecha_entrega; ?>";
var horaEntrega = "<?php echo $hora_entrega; ?>";

// Función para convertir la fecha de formato d/m/Y a un objeto Date
function convertirFecha(fecha, hora) {
    var fechaParts = fecha.split("/"); // Divide la fecha por "/"
    var horaParts = hora.split(":"); // Divide la hora por ":"
    return new Date(fechaParts[2], fechaParts[1] - 1, fechaParts[0], horaParts[0], horaParts[1]);
}

// Convertir las fechas a objetos Date
var fechaInicioDate = convertirFecha(fechaInicio, horaInicio);
var fechaEntregaDate = convertirFecha(fechaEntrega, horaEntrega);

// Función para verificar la disponibilidad del videotest y mostrar u ocultar el botón
function verificarDisponibilidad() {
    var boton = document.getElementById('fullscreenBtn');
    var mensajeElement = document.getElementById("mensajeDisponibilidad");
    var fechaActual = new Date();

    // Comparar fecha y hora exactas con la fecha de inicio y entrega
    if (fechaActual >= fechaInicioDate && fechaActual <= fechaEntregaDate) {
        boton.style.display = "inline-block";  // Mostrar el botón si es dentro del rango
        mensajeElement.innerHTML = "El videotest está disponible ahora.";
        mensajeElement.classList.remove("expired");  // Eliminar la clase "expired" si no está vencido
        mensajeElement.classList.add("available");   // Agregar la clase "available"
        mensajeElement.style.display = "block";  // Mostrar el mensaje
    } else if (fechaActual > fechaEntregaDate) {
        boton.style.display = "none";  // Ocultar el botón después de la fecha de entrega
        mensajeElement.innerHTML = "El videotest ya venció. No se pueden realizar más intentos.";
        mensajeElement.classList.remove("available"); // Eliminar la clase "available"
        mensajeElement.classList.add("expired");  // Agregar la clase "expired"
        mensajeElement.style.display = "block";  // Mostrar el mensaje
    } else {
        boton.style.display = "none";  // Ocultar el botón si no es el momento adecuado
        mensajeElement.innerHTML = "El videotest estará disponible el " + fechaInicioDate.toLocaleDateString() + " a las " + horaInicio;
        mensajeElement.classList.remove("expired"); // Eliminar la clase "expired"
        mensajeElement.classList.remove("available"); // Eliminar la clase "available"
        mensajeElement.style.display = "block";  // Mostrar el mensaje
    }
}

// Llamar a la función para verificar disponibilidad cuando la página carga
window.onload = verificarDisponibilidad;

// Función para redirigir al usuario a videotest.php
function redirigirAVideoTest() {
    window.location.href = 'videotestofimatica.php';  // Redirige a videotest.php
}

// Función para cerrar el modal
document.getElementById("closeModal").onclick = function() {
    document.getElementById("videotestModal").style.display = "none";
};

// Si se hace clic fuera del contenido del modal, también lo cerramos
window.onclick = function(event) {
    if (event.target === document.getElementById("videotestModal")) {
        document.getElementById("videotestModal").style.display = "none";
    }
};

</script>
        <script>
           let pdfViewed = false;
            let videoViewed = false;

            function markAsViewed(type) {
                if (type === 'pdf') {
                    pdfViewed = true;
                    document.querySelectorAll('.table-link .status-box')[0].classList.remove('hidden');
                } else if (type === 'video') {
                    videoViewed = true;
                    document.querySelectorAll('.table-link .status-box')[1].classList.remove('hidden');
                }
            }

            function checkRequirements(event) {
                event.preventDefault();
                if (!pdfViewed || !videoViewed ) {
                    alert("Debes ver el PDF y el video antes de abrir el videotest.");
                } else {
                    document.getElementById('videotestModal').style.display = 'block';
                }
            }

            function openPdfWindow(link) {
                if (!link) {
                    alert("El material PDF no está disponible.");
                    return;
                }
                window.open(
                    link, 
                    "pdfWindow", 
                    "width=800,height=600,toolbar=no,menubar=no,scrollbars=yes,resizable=yes"
                );
                markAsViewed('pdf');
            }

            function openVideoWindow(link) {
                if (!link) {
                    alert("El video no está disponible.");
                    return;
                }
                window.open(
                    link, 
                    "videoWindow", 
                    "width=800,height=600,toolbar=no,menubar=no,scrollbars=yes,resizable=yes"
                );
                markAsViewed('video');
            }

            document.getElementById('confirmBtn').onclick = function() {
                confirmModal.style.display = 'none';
                window.location.href = "videotestofimatica.php"; // Cambia este enlace al videotest
            };

            document.getElementById('cancelBtn').onclick = function() {
                confirmModal.style.display = 'none';
            };

            document.getElementById('closeModal').onclick = function() {
                document.getElementById('videotestModal').style.display = 'none';
            };

            document.getElementById('closeConfirmModal').onclick = function() {
                document.getElementById('confirmModal').style.display = 'none';
            };

            document.getElementById('fullscreenBtn').onclick = function() {
                document.getElementById('confirmModal').style.display = 'block';
            };
        </script>
    </div>
</body>
</html>