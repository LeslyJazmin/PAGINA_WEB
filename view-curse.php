<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuario";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get course ID from URL
$curso_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get course details
$sql = "SELECT * FROM cursos WHERE id_curso = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $curso_id);
$stmt->execute();
$result = $stmt->get_result();
$curso = $result->fetch_assoc();

if (!$curso) {
    die("Curso no encontrado");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($curso['nombre_curso']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f1eeee;
        }

        .course-header {
            position: relative;
            height: 200px;
            background-size: cover;
            background-position: center;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-bottom: 20px;
        }

        .course-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
        }

        .course-header h1 {
            position: relative;
            z-index: 1;
            font-size: 2em;
            padding: 20px;
        }

        .section-header {
            background-color: #005587;
            color: white;
            padding: 15px;
            margin: 20px 0;
            font-weight: bold;
        }

        .content-section {
            background: white;
            padding: 20px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .material-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .material-item:last-child {
            border-bottom: none;
        }

        .material-item img {
            width: 24px;
            height: 24px;
        }

        .score {
            float: right;
            padding: 5px 10px;
            background: #f0f0f0;
            border-radius: 3px;
        }

        .certificate-section {
            background-color: #005587;
            color: white;
            padding: 15px;
            text-align: center;
            margin-top: 20px;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
           /* Estilos para el botón */
           .btn-volver {
            background-color: #007bff; /* Azul */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn-volver:hover {
            background-color: #0056b3; /* Azul oscuro */
        }
    </style>
</head>
<body>
    <div class="course-header" style="background-image: url('<?php echo htmlspecialchars($curso['imagen']); ?>')">
        <h1><?php echo htmlspecialchars($curso['nombre_curso']); ?></h1>
    </div>
<!-- Botón "Volver Atrás" -->
<button class="btn-volver" onclick="volverAtras()">Volver Atrás</button>

<script>
    // Función para redirigir a aulaadmin.php
    function volverAtras() {
        window.location.href = "auladmi.php";
    }
</script>
    <div class="container">
        <div class="section-header">MATERIAL</div>
        <div class="content-section">
          
        </div>

        <div class="section-header">AUTOEVALUACIÓN</div>
        <div class="content-section">
            <div class="material-item">
                <img src="icons/video.png" alt="Video">
                <a href="cursos/<?php echo $curso_id; ?>/evaluacion.txt">
                    Video - <?php echo htmlspecialchars($curso['nombre_curso']); ?>
                </a>
            </div>
            <div class="material-item">
                <img src="icons/test.png" alt="Test">
                Videotest
                <span class="score">Nota: 0</span>
            </div>
        </div>

        <div class="section-header">EXAMEN FINAL</div>
        <div class="content-section">
            <p>Debes aprobar el Videotest para acceder al examen final.</p>
        </div>

        <div class="section-header">OBTEN TU CERTIFICADO AQUI</div>
        <div class="content-section">
            <p>Se debe aprobar el examen final con una nota de 14/20 para obtener el certificado.</p>
        </div>
    </div>
</body>
</html>