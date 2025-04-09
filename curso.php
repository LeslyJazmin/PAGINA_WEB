<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['DNI'])) {
    header("Location: aula_virtual.php");
    exit();
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuario";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Obtener el DNI del usuario
$dni = $_SESSION['DNI'];

// Consulta para obtener los cursos inscritos, asegurando que no se repitan
$sqlCursos = "SELECT DISTINCT c.nombre_curso 
               FROM inscripciones i
               JOIN cursos c ON i.id_curso = c.id_curso
               WHERE i.DNI = ?";
$stmtCursos = $conn->prepare($sqlCursos);
$stmtCursos->bind_param("s", $dni);
$stmtCursos->execute();
$resultCursos = $stmtCursos->get_result();

$cursosInscritos = [];
while ($curso = $resultCursos->fetch_assoc()) {
    $cursosInscritos[] = $curso['nombre_curso'];
}
$stmtCursos->close();

// Array asociativo de cursos con sus imágenes y páginas predefinidas
$imagenes_cursos = [
    'Curso IPERC : Identificación de Peligros y evaluación de Riesgos' => 'images/cur1.jpeg',
    'Curso de Soldadura con arco elèctrico electrodo revestido' => 'images/cur3.jpeg', 
    'Curso de primeros auxilios' => 'images/cur2.jpeg',
    'Curso de Uso y manejo de extintores' => 'images/cur4.jpeg', 
    'Curso de Seguridad para Trabajos con Altura' => 'images/SeguridadTA.jpg', 
    'Curso de Seguridad para Trabajos en Caliente' => 'images/trabajosC.jpg',
    'Curso de Seguridad para Trabajos Eléctricos' => 'images/SElectrica.jpg',
    'Curso de Seguridad para Trabajos en Zanjas o Excavaciones' => 'images/seguridadES.jpg',
    'Curso de Indicadores de Gestión de SST' => 'images/indicadoresSST.jpg',
    'Curso de AUTOCAD 2D Y 3D' => 'images/autocad.jpg',
    'Curso de Seguridad en trabajos confinados' => 'images/segurityTC.jpg',
    'Curso de Ofimática' => 'images/ofimatica.jpg',
    'Curso de Homologación 3G en proceso smaw' => 'images/Homologacion3G.jpg',
    'Curso de Homologación 4G en proceso smaw' => 'images/Homologacion4G.jpg',
    'Curso de Homologación 6G en proceso smaw' => 'images/Homologacion6G.jpg'
];
$paginas_cursos = [
    'Curso IPERC' => 'iperc.php',
    'Curso de primeros auxilios' => 'pa.php',
    'Curso de Soldadura con arco eléctrico electrodo revestido' => 'sa.php',
    'Curso de Uso y manejo de extintores' => 'me.php',
    'Curso de Seguridad para Trabajos con Altura' => 'seguridadTA.php', 
    'Curso de Seguridad para Trabajos en Caliente' => 'trabajosC.php',
    'Curso de Seguridad para Trabajos Eléctricos' => 'seguridadE.php',
    'Curso de Seguridad para Trabajos en Excavaciones' => 'seguridaTE.php',
    'Curso de Indicadores de Gestión de SST' => 'indicadoresSST.php',
    'Curso de AUTOCAD 2D Y 3D' => 'autocad.php',
    'Curso de Seguridad en trabajos confinados' => 'seguridadTC.php',
    'Curso de Ofimática' => 'ofimatica.php',
    'Curso de Homologación 3G en proceso smaw' => 'Homologacion3G.php',
    'Curso de Homologación 4G en proceso smaw' => 'Homologacion4G.php',
    'Curso de Homologación 6G en proceso smaw' => 'Homologacion6G.php'
];

// Consulta para obtener cursos dinámicos de la base de datos
$sqlNuevosCursos = "SELECT nombre_curso, imagen, pagina_curso FROM cursos WHERE 1=1";
$resultNuevosCursos = $conn->query($sqlNuevosCursos);

// Agregar cursos dinámicos a los arrays existentes
while ($curso = $resultNuevosCursos->fetch_assoc()) {
    // Solo agregar si el curso no existe ya en los arrays predefinidos
    if (!isset($imagenes_cursos[$curso['nombre_curso']])) {
        $imagenes_cursos[$curso['nombre_curso']] = $curso['imagen'];
    }
    
    if (!isset($paginas_cursos[$curso['nombre_curso']])) {
        $paginas_cursos[$curso['nombre_curso']] = $curso['pagina_curso'];
    }
}

$busqueda = '';
$cursosFiltrados = [];

// Procesar la búsqueda si el usuario realiza una búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $busqueda = trim($_POST['buscar_curso']);

    // Filtrar cursos que contengan el término de búsqueda
    foreach ($cursosInscritos as $curso) {
        if (stripos($curso, $busqueda) !== false) {
            $cursosFiltrados[] = $curso;
        }
    }

    // Si no hay coincidencias, muestra un mensaje
    if (empty($cursosFiltrados)) {
        $cursosFiltrados = ["No estás inscrito en ningún curso que coincida con la búsqueda."];
    }
} else {
    // Si no se realizó ninguna búsqueda, mostrar todos los cursos inscritos
    $cursosFiltrados = $cursosInscritos;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="curso.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <style>
        
    </style>
    <div class="container">
        <aside class="sidebar">
            <nav>
                <h2>MEKADDESH SOLUTION E.R.I.L</h2>
                <ul>
                    <li><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="curso.php" class="selected"><i class="fas fa-book"></i> Cursos</a></li>
                    <li><a href="perfil.php"><i class="fas fa-user"></i> Perfil</a></li>
                    <li><a href="calendario.php"><i class="fas fa-calendar-alt"></i> Calendario</a></li>
                    <li><a href="cerrar_s.php" onclick="return confirm('¿Estás seguro de que deseas cerrar sesión?');"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
                </ul>
            </nav>
        </aside>
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

            function confirmLogout() {
                return confirm("¿Estás seguro de que deseas cerrar sesión?");
            }
        </script>

        <main class="main-content">
            <h1 class="bienvenido-titulo">Bienvenido a tus cursos</h1>

            <!-- Panel de búsqueda debajo del título -->
            <div class="busqueda-container">
                <form method="POST" action="curso.php">
                    <input type="text" name="buscar_curso" placeholder="Buscar tu curso..." value="<?php echo htmlspecialchars($busqueda); ?>">
                    <button type="submit">Buscar</button>
                </form>
            </div>

            <?php if (!empty($cursosFiltrados) && $cursosFiltrados[0] !== "No estás inscrito en ningún curso que coincida con la búsqueda."): ?>
                <!-- Mostrar los cursos filtrados -->
                <div class="cursos-container">
                    <?php foreach ($cursosFiltrados as $curso): ?>
                        <div class="curso-container">
                            <div class="curso-imagen-container">
                                <a href="<?php echo $paginas_cursos[$curso]; ?>">
                                    <img class="curso-imagen" src="<?php echo $imagenes_cursos[$curso]; ?>" alt="Imagen del curso">
                                </a>
                            </div>
                            <div class="curso-info">
                                <p class="course-name"><?php echo $curso; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Mostrar mensaje si no hay coincidencias -->
                <p><?php echo $cursosFiltrados[0]; ?></p>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>


