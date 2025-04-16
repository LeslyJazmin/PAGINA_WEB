<?php
session_start();
include 'db_connection.php';

// ID del curso que estás buscando
$id_curso = '4';

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
    <title>Curso de IPERC</title>
    <link rel="stylesheet" href="admi.css">
    <link rel="stylesheet" href="styles1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                        <li><a href="iperadmi.php" style="color: #000;">Iperc</a></li>
                        <li><a href="sadmi.php" style="color: #000;">Soldadura con arco elèctrico electrodo revestido</a></li>
                        <li><a href="padmi.php" style="color: #000;">Primeros Auxilios</a></li>
                        <li><a href="meadmi.php" style="color: #000;">Uso y manejo de extintores</a></li>
                        <li><a href="seguridadTAadmin.php" style="color: #000;">Seguridad para Trabajos con Altura</a></li>
                        <li><a href="trabajosCadmin.php" style="color: #000;">Seguridad para Trabajos en Caliente</a></li>
                        <li><a href="seguridadEadmin.php" style="color: #000;">Seguridad para Trabajos Eléctricos</a></li>
                        <li><a href="seguridaTEadmin.php" style="color: #000;">Seguridad para Trabajos en Zanjas o Excavaciones</a></li>
                        <li><a href="indicadoresSSTadmin.php" style="color: #000;">Indicadores de Gestión de SST</a></li>
                        <li><a href="autocadadmin.php" style="color: #000;">AUTOCAD 2D Y 3D</a></li>
                        <li><a href="seguridadTCadmin.php" style="color: #000;">Seguridad en trabajos confinados</a></li>
                        <li><a href="ofimaticaadmin.php" style="color: #000;">Ofimática</a></li>
                        <li><a href="Homologacion3Gadmin.php" style="color: #000;">Homologación 3G en proceso smaw</a></li>
                        <li><a href="Homologacion4Gadmin.php" style="color: #000;">Homologación 4G en proceso smaw</a></li>
                        <li><a href="Homologacion6Gadmin.php" style="color: #000;">Homologación 6G en proceso smaw</a></li>
                    </ul>
                </li>
                <li><a href="auladmi.php"><i class="fas fa-user"></i> Perfil</a></li>
                <li><a href="calendarioadmi.php"><i class="fas fa-calendar-alt"></i> Calendario</a></li>
                <li><a href="cerrar_s.php" onclick="return confirmLogout();" class="cerrar-sesion-boton"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
            </ul>
        </div>

        <style>
            .sidebar ul ul {
                display: none;
                list-style: none;
                padding: 0;
                margin: 0;
                width: 180px;
            }
            .sidebar ul li:hover ul {
                display: block;
                position: absolute;
                background-color: #0a507e;
                padding: 10px;
                border: 1px solid #ddd;
            }
            .sidebar ul ul li {
                padding: 10px;
                border-bottom: 1px solid #ddd;
            }
            .sidebar ul ul li a {
                color: #000;
                text-decoration: none;
            }
            .sidebar ul ul li a:hover {
                color: #000;
            }
        </style>

        <script>
            function confirmLogout() {
                return confirm("¿Estás seguro de que deseas cerrar sesión?");
            }
        </script>

        <div class="main-content">
            <div class="contenido-principal">
                <h1 style="text-align: center;">Estudiantes en <?= htmlspecialchars($nombre_curso) ?></h1>
                <table>
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
                                <td colspan="3" style="text-align: center;">No se encontraron registros.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <form action="guardar_datos.php" method="POST">
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
                                    <!-- Selector de fecha para Videotest -->
                                    <label for="fecha_inicio_videotest">Fecha de Inicio:</label>
                                    <input type="date" name="fecha_inicio_videotest" id="fecha_inicio_videotest" 
                                           value="<?php echo $current_data ? $current_data['fecha_inicio_videotest'] : ''; ?>" required>
                                    <br>
                                    <!-- Selector de hora de inicio para Videotest -->
                                    <label for="hora_inicio_videotest">Hora de Inicio:</label>
                                    <input type="time" name="hora_inicio_videotest" id="hora_inicio_videotest" 
                                           value="<?php echo $current_data ? $current_data['hora_inicio_videotest'] : ''; ?>" required>
                                    <br>
                                    <label for="fecha_fin_videotest">Fecha de Fin:</label>
                                    <input type="date" name="fecha_fin_videotest" id="fecha_fin_videotest" 
                                           value="<?php echo $current_data ? $current_data['fecha_fin_videotest'] : ''; ?>" required>
                                    <br>
                                    <!-- Selector de hora de fin para Videotest -->
                                    <label for="hora_fin_videotest">Hora de Fin:</label>
                                    <input type="time" name="hora_fin_videotest" id="hora_fin_videotest" 
                                           value="<?php echo $current_data ? $current_data['hora_fin_videotest'] : ''; ?>" required>
                                    <br>
                                </td>
                                <td>
                                    <!-- Selector de fecha para Examen Final -->
                                    <label for="fecha_inicio_examen">Fecha de Inicio:</label>
                                    <input type="date" name="fecha_inicio_examen" id="fecha_inicio_examen" 
                                           value="<?php echo $current_data ? $current_data['fecha_inicio_examen'] : ''; ?>" required>
                                    <br>
                                    <!-- Selector de hora de inicio para examen -->
                                    <label for="hora_inicio_examen">Hora de Inicio:</label>
                                    <input type="time" name="hora_inicio_examen" id="hora_inicio_examen" 
                                           value="<?php echo $current_data ? $current_data['hora_inicio_examen'] : ''; ?>" required>
                                    <br>
                                    <label for="fecha_fin_examen">Fecha de Fin:</label>
                                    <input type="date" name="fecha_fin_examen" id="fecha_fin_examen" 
                                           value="<?php echo $current_data ? $current_data['fecha_fin_examen'] : ''; ?>" required>
                                    <br>
                                    <!-- Selector de hora de fin para examen -->
                                    <label for="hora_fin_examen">Hora de Fin:</label>
                                    <input type="time" name="hora_fin_examen" id="hora_fin_examen" 
                                           value="<?php echo $current_data ? $current_data['hora_fin_examen'] : ''; ?>" required>
                                    <br>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <!-- Campo oculto para el id_curso con valor 3 -->
                    <input type="hidden" name="id_curso" value="4">

                    <!-- Botones de guardado y actualización -->
                    <div class="button-container">
                        <button type="submit" name="guardar" class="update-btn">Guardar</button>
                        <button type="submit" name="actualizar" class="update-btn">Actualizar</button>
                    </div>
                </form>
                <style>
                    .main-content {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        width: 100%;
                    }
                    
                    .contenido-principal {
                        width: 100%;
                        max-width: 1200px;
                        margin: 0 auto;
                        padding: 20px;
                        box-sizing: border-box;
                    }
                    
                    table {
                        width: 100%;
                        max-width: 1000px;
                        margin: 20px auto;
                        border-collapse: collapse;
                    }
                    
                    th, td {
                        padding: 15px;
                        text-align: left;
                        border: 1px solid #ddd;
                        vertical-align: middle;  /* Centra los elementos verticalmente */
                    }
                    
                    input[type="date"],
                    input[type="time"] {
                        width: 100%;
                        padding: 5px;
                        margin: 5px 0;
                        box-sizing: border-box;
                    }
                    
                    th {
                        width: 200px;  /* Ajusta el ancho de las celdas de los encabezados */
                    }
                    
                    label {
                        font-weight: bold;
                        margin-top: 10px;
                        display: block;  /* Asegura que los labels estén por encima de los campos */
                    }
                    
                    .button-container {
                        display: flex;
                        justify-content: center;
                        margin-top: 20px;
                        gap: 10px;
                        flex-direction: row; /* Asegura que los botones estén en una línea */
                    }
                    
                    .update-btn {
                        padding: 10px 20px;
                        background-color: #0a507e;
                        color: white;
                        border: none;
                        border-radius: 5px;
                        cursor: pointer;
                        transition: background-color 0.3s;
                        display: inline-block; /* Asegura que los botones se muestren en línea */
                        margin: 0 5px; /* Añade un pequeño margen entre botones */
                    }
                    
                    .update-btn:hover {
                        background-color: #083d5f;
                    }
                </style>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Cierra la conexión después de que todo haya terminado
$conn->close();
?>
