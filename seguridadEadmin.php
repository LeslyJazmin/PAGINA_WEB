<?php
session_start();
include 'db_connection.php';

// ID del curso que estás buscando
$id_curso = '20';

// Consulta para obtener los estudiantes matriculados junto con el nombre del curso
$sql = "
    SELECT inscripciones.DNI, inscripciones.nota_videotest, inscripciones.examen_final, cursos.nombre_curso 
    FROM inscripciones 
    INNER JOIN cursos ON inscripciones.id_curso = cursos.id_curso 
    WHERE inscripciones.id_curso = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_curso);
$stmt->execute();
$result = $stmt->get_result();

// Obtener el nombre del curso para mostrarlo
$nombre_curso = null;
if ($result->num_rows > 0) {
    // Recuperar el nombre del curso del primer resultado
    $row = $result->fetch_assoc();
    $nombre_curso = $row['nombre_curso'];
    // Volver al inicio del resultado para mostrar todos los estudiantes
    $result->data_seek(0);
}

// Cerrar la consulta
$stmt->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curso de Seguridad para Trabajos Eléctricos</title>
    <link rel="stylesheet" href="admi.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Reset básico */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
        }

        /* Sidebar */
        .sidebar {
            width: 200px;
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            background-color: #0a507e;
            color: white;
            padding-top: 20px;
        }

        .sidebar h2 {
            padding: 0 15px;
            margin-bottom: 30px;
            font-size: 18px;
            line-height: 1.2;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            transition: background-color 0.3s;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.selected {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar ul li a i {
            margin-right: 10px;
            width: 20px;
        }

        /* Contenido principal */
        .contenido-principal {
          width: 1029px;
          height: 710.594px;
          transform: translateX(177px ,2px);
        }

        /* Título principal */
        .contenido-principal h1 {
            color: #000;
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: normal;
        }

        /* Tablas */
        .tabla-estudiantes,
        .formulario-fechas table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .tabla-estudiantes th,
        .formulario-fechas th {
            background-color: #0a507e;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: normal;
        }

        .tabla-estudiantes td,
        .formulario-fechas td {
            padding: 10px;
            border: 1px solid #ddd;
            background-color: white;
        }

        /* Formularios */
        .formulario-fechas {
            margin-top: 20px;
        }

        .formulario-fechas input[type="date"],
        .formulario-fechas input[type="time"] {
            width: 100%;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 0;
        }

        /* Botones */
        .botones-accion {
            margin-top: 20px;
        }

        .update-btn {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 5px 15px;
            margin-right: 10px;
            cursor: pointer;
            font-size: 14px;
        }

        .update-btn:hover {
            background-color: #e5e5e5;
        }

        /* Encabezados de sección */
        .seccion-titulo {
            background-color: #0a507e;
            color: white;
            padding: 10px;
            margin-bottom: 0;
            font-weight: normal;
        }

        /* Contenedor de tabla */
        .tabla-container {
            margin-bottom: 30px;
        }

        @media (max-width: 1400px) {
            .contenido-principal {
                margin-left: 350px;
                width: calc(100% - 400px);
            }
        }

        @media (max-width: 1200px) {
            .contenido-principal {
                margin-left: 300px;
                width: calc(100% - 350px);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            .contenido-principal {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <h2>MEKADDESH SOLUTION E.R.I.L</h2>
            <ul>
                <li><a href="inicio_admin.php"><i class="fas fa-home"></i> Inicio</a></li>
                <li>
                    <a href="cursosa.php" class="selected"><i class="fas fa-book"></i> Cursos</a>
                    <ul class="subcursos">
                        <li><a href="iperadmi.php">Iperc</a></li>
                        <li><a href="sadmi.php">Soldadura con arco elèctrico electrodo revestido</a></li>
                        <li><a href="padmi.php">Primeros Auxilios</a></li>
                        <li><a href="meadmi.php">Uso y manejo de extintores</a></li>
                        <li><a href="seguridadTAadmin.php">Seguridad para Trabajos con Altura</a></li>
                        <li><a href="trabajosCadmin.php">Seguridad para Trabajos en Caliente</a></li>
                        <li><a href="seguridadEadmin.php">Seguridad para Trabajos Eléctricos</a></li>
                        <li><a href="seguridaTEadmin.php">Seguridad para Trabajos en Zanjas o Excavaciones</a></li>
                        <li><a href="indicadoresSSTadmin.php">Indicadores de Gestión de SST</a></li>
                        <li><a href="autocadadmin.php">AUTOCAD 2D Y 3D</a></li>
                        <li><a href="seguridadTCadmin.php">Seguridad en trabajos confinados</a></li>
                        <li><a href="ofimaticaadmin.php">Ofimática</a></li>
                        <li><a href="Homologacion3Gadmin.php">Homologación 3G en proceso smaw</a></li>
                        <li><a href="Homologacion4Gadmin.php">Homologación 4G en proceso smaw</a></li>
                        <li><a href="Homologacion6Gadmin.php">Homologación 6G en proceso smaw</a></li>
                    </ul>
                </li>
                <li><a href="auladmi.php"><i class="fas fa-user"></i> Perfil</a></li>
                <li><a href="calendarioadmi.php"><i class="fas fa-calendar-alt"></i> Calendario</a></li>
                <li><a href="cerrar_s.php" onclick="return confirmLogout();" class="cerrar-sesion-boton"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
            </ul>
        </div>

        <script>
            function confirmLogout() {
                return confirm("¿Estás seguro de que deseas cerrar sesión?");
            }
        </script>

        <div class="contenido-principal">
            <h1>Estudiantes en <?= htmlspecialchars($nombre_curso) ?></h1>
            
            <div class="tabla-container">
                <table class="tabla-estudiantes">
                    <thead>
                        <tr>
                            <th>DNI</th>
                            <th>Nota Videotest</th>
                            <th>Examen Final</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['DNI']) ?></td>
                                    <td><?= htmlspecialchars($row['nota_videotest']) ?></td>
                                    <td><?= htmlspecialchars($row['examen_final']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3">No se encontraron registros.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="formulario-fechas">
                <table>
                    <thead>
                        <tr>
                            <th>Videotest</th>
                            <th>Examen Final</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <label>Fecha de Inicio:</label>
                                <input type="date" name="fecha_inicio_videotest" required>
                                
                                <label>Hora de Inicio:</label>
                                <input type="time" name="hora_inicio_videotest" required>
                                
                                <label>Fecha de Fin:</label>
                                <input type="date" name="fecha_fin_videotest" required>
                                
                                <label>Hora de Fin:</label>
                                <input type="time" name="hora_fin_videotest" required>
                            </td>
                            <td>
                                <label>Fecha de Inicio:</label>
                                <input type="date" name="fecha_inicio_examen" required>
                                
                                <label>Hora de Inicio:</label>
                                <input type="time" name="hora_inicio_examen" required>
                                
                                <label>Fecha de Fin:</label>
                                <input type="date" name="fecha_fin_examen" required>
                                
                                <label>Hora de Fin:</label>
                                <input type="time" name="hora_fin_examen" required>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="botones-accion">
                    <button type="submit" name="guardar" class="update-btn">Guardar</button>
                    <button type="submit" name="actualizar" class="update-btn">Actualizar</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Cierra la conexión después de que todo haya terminado
$conn->close();
?>
