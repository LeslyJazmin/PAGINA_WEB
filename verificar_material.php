<?php
// 1. Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuario"; // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// 2. Consulta SQL usando JOIN para obtener el nombre del curso
// Asumimos que tienes una tabla 'cursos' con 'id_curso' y 'nombre_curso'
$sql = "SELECT
            m.id_curso,
            c.nombre_curso,
            m.link_curso_material,
            m.link_curso_video
        FROM
            material AS m
        INNER JOIN
            cursos AS c ON m.id_curso = c.id_curso";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="material.css"> 
    <title>Links de Cursos y Material</title>
</head>
<body>

    <div class="container">
        <h1>Enlaces de Material y Video por Curso</h1>

        <table>
            <thead>
                <tr>
                    <th>Nombre del Curso</th>
                    <th>Link Material</th>
                    <th>Link Video</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td data-label='Nombre del Curso'>" . htmlspecialchars($row["nombre_curso"]) . "</td>";

                        // Verificar y mostrar el link del material
                        if (!empty($row["link_curso_material"])) {
                            echo '<td data-label="Link Material"><a href="' . htmlspecialchars($row["link_curso_material"]) . '" target="_blank">Ver Material</a></td>';
                        } else {
                            echo '<td data-label="Link Material" class="no-link">No disponible</td>';
                        }

                        // Verificar y mostrar el link del video
                        if (!empty($row["link_curso_video"])) {
                            echo '<td data-label="Link Video"><a href="' . htmlspecialchars($row["link_curso_video"]) . '" target="_blank">Ver Video</a></td>';
                        } else {
                            echo '<td data-label="Link Video" class="no-link">No disponible</td>';
                        }

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No se encontraron materiales de cursos.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="button-group">
            <a href="inicio_admin.php" class="btn btn-primary">Volver al Inicio</a>
        </div>
    </div>

    <?php
    // Cerrar la conexión a la base de datos
    $conn->close();
    ?>

</body>
</html>