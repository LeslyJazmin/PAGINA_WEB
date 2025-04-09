<?php
// Iniciar la sesión para almacenar el mensaje
session_start();

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuario";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Función para convertir la fecha de YYYY-MM-DD a DD/MM/YYYY
function convertirFecha($fecha) {
    // Verificar si la fecha no está vacía
    if (!empty($fecha)) {
        // Si la fecha ya está en formato DD/MM/YYYY, devolverla tal cual
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $fecha)) {
            return $fecha;
        }
        
        // Si está en formato YYYY-MM-DD, convertirla a DD/MM/YYYY
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            // Convertir de YYYY-MM-DD a DD/MM/YYYY
            $fechaObj = DateTime::createFromFormat('Y-m-d', $fecha);
            return $fechaObj ? $fechaObj->format('d/m/Y') : null;
        }
        
        return null;
    }
    return null;
}

// Verificar si se ha enviado el formulario de guardar o actualizar
if (isset($_POST['guardar']) || isset($_POST['actualizar'])) {
    // Recoger el id del curso y las demás variables
    $id_curso = $_POST['id_curso']; // ID del curso

    // Recoger las fechas y horas del formulario
    $fecha_inicio_videotest = convertirFecha($_POST['fecha_inicio_videotest']);
    $fecha_fin_videotest = convertirFecha($_POST['fecha_fin_videotest']);
    $hora_inicio_videotest = $_POST['hora_inicio_videotest'];
    $hora_fin_videotest = $_POST['hora_fin_videotest'];

    $fecha_inicio_examen = convertirFecha($_POST['fecha_inicio_examen']);
    $fecha_fin_examen = convertirFecha($_POST['fecha_fin_examen']);
    $hora_inicio_examen = $_POST['hora_inicio_examen'];
    $hora_fin_examen = $_POST['hora_fin_examen'];

    // Si se presionó el botón de actualizar, se realiza un UPDATE
    if (isset($_POST['actualizar'])) {
        $sql = "UPDATE examenes SET 
            fecha_inicio_videotest = ?, 
            hora_inicio_videotest = ?, 
            fecha_fin_videotest = ?, 
            hora_fin_videotest = ?, 
            fecha_inicio_examen = ?, 
            hora_inicio_examen = ?, 
            fecha_fin_examen = ?, 
            hora_fin_examen = ? 
            WHERE id_curso = ?";

        // Preparar la sentencia
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssssi", 
            $fecha_inicio_videotest, 
            $hora_inicio_videotest, 
            $fecha_fin_videotest, 
            $hora_fin_videotest, 
            $fecha_inicio_examen, 
            $hora_inicio_examen, 
            $fecha_fin_examen, 
            $hora_fin_examen,
            $id_curso
        );
        
        // Ejecutar la consulta de actualización
        if ($stmt->execute()) {
            $_SESSION['message'] = "Se han actualizado la fecha y hora correctamente.";
        } else {
            $_SESSION['message'] = "Error al actualizar: " . $stmt->error;
        }

    } else {
        // Si se presionó el botón de guardar, se hace un INSERT
        $sql = "INSERT INTO examenes (
            id_curso, 
            fecha_inicio_videotest, 
            hora_inicio_videotest, 
            fecha_fin_videotest, 
            hora_fin_videotest, 
            fecha_inicio_examen, 
            hora_inicio_examen, 
            fecha_fin_examen, 
            hora_fin_examen
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Preparar la sentencia
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "issssssss", 
            $id_curso, 
            $fecha_inicio_videotest, 
            $hora_inicio_videotest, 
            $fecha_fin_videotest, 
            $hora_fin_videotest, 
            $fecha_inicio_examen, 
            $hora_inicio_examen, 
            $fecha_fin_examen, 
            $hora_fin_examen
        );
        
        // Ejecutar la consulta de inserción
        if ($stmt->execute()) {
            $_SESSION['message'] = "Datos guardados correctamente";
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
        }
    }

    // Cerrar statement
    $stmt->close();

    // Redirigir a la misma página para mostrar el modal
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Cerrar conexión
$conn->close();
?>

<!-- Mostrar el Modal si hay un mensaje en la sesión -->
<!-- Mostrar el Modal si hay un mensaje en la sesión -->
<?php if (isset($_SESSION['message'])): ?>
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p class="modal-text"><?php echo $_SESSION['message']; ?></p>
            <button class="modal-button" onclick="closeModal()">Cerrar</button>
        </div>
    </div>

    <style>
        /* Estilos del modal */
        .modal {
            display: flex;
            justify-content: center;
            align-items: center;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(173, 216, 230, 0.7); /* Celeste transparente */
        }

        .modal-content {
            background-color: rgba(18, 128, 245, 0.8); /* Azul transparente */
            margin: 5% auto;
            padding: 30px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-text {
            color: #000; /* Letra negra */
            text-align: center;
            font-size: 1.2em; /* Letra un poco grande */
            margin-bottom: 20px;
        }

        .close {
            color: #000;
            float: right;
            font-size: 28px;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .close:hover,
        .close:focus {
            color: #555;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-button {
            background-color: #000;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            display: block;
            margin: 0 auto;
        }

        .modal-button:hover {
            background-color: #333;
        }
    </style>

    <script>
        // Función para cerrar el modal y redirigir a la página cursosa.php
        function closeModal() {
            document.getElementById('modal').style.display = 'none';
            window.location.href = 'cursosa.php'; // Redirige a cursosa.php
        }
    </script>

<?php
// Limpiar mensaje de sesión después de mostrar
unset($_SESSION['message']);
endif;
?>

