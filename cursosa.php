<?php
session_start(); // Iniciar sesión

// Array asociativo de cursos con sus imágenes y páginas correspondientes
$cursos = [
    'Curso IPERC : Identificación de Peligros y evaluación de Riesgos' => ['imagen' => 'images/cur1.jpeg', 'pagina' => 'iperadmi.php'],
    'Curso de Soldadura con arco elèctrico electrodo revestido' => ['imagen' => 'images/cur3.jpeg', 'pagina' => 'sadmi.php'],
    'Curso de primeros auxilios' => ['imagen' => 'images/cur2.jpeg', 'pagina' => 'padmi.php'],
    'Curso de Uso y manejo de extintores' => ['imagen' => 'images/cur4.jpeg', 'pagina' => 'meadmi.php'],
    'Curso de Seguridad para Trabajos con Altura' => ['imagen' => 'images/SeguridadTA.jpg', 'pagina' => 'seguridadTAadmin.php'],
    'Curso de Seguridad para Trabajos en Caliente' => ['imagen' => 'images/trabajosC.jpg', 'pagina' => 'trabajosCadmin.php'],
    'Curso de Seguridad para Trabajos Eléctricos' => ['imagen' => 'images/SElectrica.jpg', 'pagina' => 'seguridadEadmin.php'],
    'Curso de Seguridad para Trabajos en Zanjas o Excavaciones' => ['imagen' => 'images/seguridadES.jpg', 'pagina' => 'seguridaTEadmin.php'],
    'Curso de Indicadores de Gestión de SST' => ['imagen' => 'images/indicadoresSST.jpg', 'pagina' => 'indicadoresSSTadmin.php'],
    'Curso de AUTOCAD 2D Y 3D' => ['imagen' => 'images/autocad.jpg', 'pagina' => 'autocadadmin.php'],
    'Curso de Seguridad en trabajos confinados' => ['imagen' => 'images/segurityTC.jpg', 'pagina' => 'seguridadTCadmin.php'],
    'Curso de Ofimática' => ['imagen' => 'images/ofimatica.jpg', 'pagina' => 'ofimaticaadmin.php'],
    'Curso de Homologación 3G en proceso smaw' => ['imagen' => 'images/Homologacion3G.jpg', 'pagina' => 'Homologacion3Gadmin.php'],
    'Curso de Homologación 4G en proceso smaw' => ['imagen' => 'images/Homologacion4G.jpg', 'pagina' => 'Homologacion4Gadmin.php'],
    
    'Curso de Homologación 6G en proceso smaw' => ['imagen' => 'images/Homologacion6G.jpg', 'pagina' => 'Homologacion6Gadmin.php'],
];

$busqueda = '';
$resultado_busqueda = [];

// Procesar la búsqueda si el usuario realiza una búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $busqueda = trim($_POST['buscar_cursoadmi']); // Asegúrate de que sea el mismo nombre en el HTML
    if (!empty($busqueda)) {
        foreach ($cursos as $nombre => $info) {
            // stripos() busca de forma insensible a mayúsculas/minúsculas
            if (stripos($nombre, $busqueda) !== false) {
                $resultado_busqueda[$nombre] = $info;
            }
        }
    }
}

// Si no hay búsqueda o no se encontraron resultados, mostrar todos los cursos
if (empty($busqueda) || empty($resultado_busqueda)) {
    $resultado_busqueda = $cursos;
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #ecf0f1;
}

.container {
    display: flex;
    min-height: 111vh;
}

/* Sidebar */
.sidebar {
    width: 240px;
    background-color: #0e3d59;
    color: #fff;
    padding: 20px;
    position: fixed;
    height: 100%;
}

.sidebar h2 {
    font-size: 1.3em;
    margin-bottom: 20px;
    text-align: center;
    font-weight: bold;
}

.sidebar ul {
    list-style: none;
    padding: 0;
}

.sidebar ul li {
    margin-bottom: 15px;
}

.sidebar ul li a {
    text-decoration: none;
    color: #fff;
    display: block;
    padding: 10px;
    border-radius: 5px;
    transition: 0.3s;
}

.sidebar ul li a:hover,
.sidebar ul li a.selected {
    background-color: #145374;
}

       /* Main Content */
        .main-content {
            margin-left: 230px;
            flex: 1;
            padding: 40px;
            margin-left: 290px; 
            background-color:rgb(240, 238, 238);

        }
        .bienvenido-titulo {
            text-align: center;
            color: #333;
        }
        .busqueda-container {
            margin-bottom: 20px;
            text-align: center;
        }
        .busqueda-container input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .busqueda-container button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }.cursos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

.curso-container {
    background-color: #175ca2;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.curso-container:hover {
    transform: translateY(-5px);
}

.curso-imagen-container {
    height: 180px;
    width: 100%;
    overflow: hidden;
    position: relative;
}

.curso-imagen {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease, filter 0.3s ease;
}

.curso-container:hover .curso-imagen {
    transform: scale(1.05);
    filter: brightness(0.7);
}

.curso-info {
    padding: 15px;
}

.course-name {
    margin: 0;
    text-align: center;
    font-size: 15px;
    color: #000;
}

@media (max-width: 768px) {
    .container {
        flex-direction: column;
    }
    .sidebar {
        width: 100%;
    }
    .cursos-grid {
        grid-template-columns: 1fr;
    }
}
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <nav>
                <h2>MEKADDESH SOLUTION E.I.R.L</h2>
                <ul>
                    <li><a href="inicio_admin.php"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="cursosa.php" class="selected"><i class="fas fa-book"></i> Cursos</a></li>
                    <li><a href="auladmi.php"><i class="fas fa-user"></i> Perfil</a></li>
                    <li><a href="calendarioadmi.php"><i class="fas fa-calendar-alt"></i> Calendario</a></li>
                    <li><a href="cerrar_s.php" onclick="return confirmLogout();" class="cerrar-sesion-boton"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <h1 class="bienvenido-titulo">Bienvenido a tus cursos</h1>

            <div class="busqueda-container">
                <form method="POST" action="cursosa.php">
                    <input type="text" name="buscar_cursoadmi" placeholder="Buscar tu curso..." value="<?php echo htmlspecialchars($busqueda); ?>">
                    <button type="submit">Buscar</button>
                </form>
            </div>

            <div class="cursos-grid">
                <?php foreach ($resultado_busqueda as $nombre => $info): ?>
                    <div class="curso-container">
                        <div class="curso-imagen-container">
                            <a href="<?php echo $info['pagina']; ?>">
                                <img class="curso-imagen" src="<?php echo $info['imagen']; ?>" alt="<?php echo $nombre; ?>">
                            </a>
                        </div>
                        <div class="curso-info">
                            <p class="course-name"><?php echo $nombre; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <script>
        function confirmLogout() {
            return confirm("¿Estás seguro de que deseas cerrar sesión?");
        }
    </script>
</body>
</html>