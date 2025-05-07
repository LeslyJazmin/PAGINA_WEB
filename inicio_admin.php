<?php 
session_start(); // Iniciar sesión

// Configuración de la base de datos
$host = 'localhost'; // Cambia esto por tu host
$dbname = 'usuario'; // Cambia esto por el nombre de tu base de datos
$username = 'root'; // Cambia esto por tu nombre de usuario
$password = ''; // Cambia esto por tu contraseña

try {
    // Conexión a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Establecer el modo de error a excepción
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Error de conexión: ' . $e->getMessage();
}

$mysqli_usuario = new mysqli("localhost", "root", "", "usuario");
        if ($mysqli_usuario->connect_error) {
            die("Conexión fallida a usuario: " . $mysqli_usuario->connect_error);
        }

        $sql_usuario = "SELECT DNI, nombre, correo, contrasena, telefono, sexo FROM estudiantes";
        $result_usuario = $mysqli_usuario->query($sql_usuario);

        if ($result_usuario === false) {
            // Output the error message if the query fails
            die("Error en la consulta: " . $mysqli_usuario->error);
        }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir material</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="admi.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <nav>
                <h2>MEKADDESH SOLUTION E.I.R.L</h2>
                <ul>
                    <li><a href="inicio_admin.php" class="selected"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="cursosa.php"><i class="fas fa-book"></i> Cursos</a></li>
                    <li><a href="auladmi.php"><i class="fas fa-user"></i> Perfil</a></li>
                    <li><a href="calendarioadmi.php"><i class="fas fa-calendar-alt"></i> Calendario</a></li>
                    <li><a href="cerrar_s.php" onclick="return confirmLogout();" class="cerrar-sesion-boton"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
                </ul>
            </nav>
        </aside>

<div class="main-content">
<div class="user-info">
        <h3 class="titulo-admin">Panel de Gestión de Estudiantes y Cursos</h3>
        <table>
            <tr>
                <th>DNI</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Contraseña</th>
                <th>Teléfono</th>
                <th>Sexo</th>
                <th>Cursos</th>
            </tr>
            <?php
            if ($result_usuario->num_rows > 0) {
                while ($row = $result_usuario->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['DNI']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['correo']); ?></td>
                        <td class="editable-cell">
                            <span class="editable" contenteditable="false"><?php echo htmlspecialchars($row['contrasena']); ?></span>
                            <button class="edit-btn" onclick="editCell(this, '<?php echo $row['DNI']; ?>', 'contrasena')">🖊️</button>
                        </td>
                        <td>
                            <div class="phone-container">
                                <span class="editable" contenteditable="false"><?php echo htmlspecialchars($row['telefono']); ?></span>
                                <button class="edit-btn" onclick="editCell(this, '<?php echo $row['DNI']; ?>', 'telefono')">🖊️</button>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($row['sexo']); ?></td>

                        <!-- Columna Cursos con botón -->
                        <td>
                            <form action="ver_cursos.php" method="GET">
                                <input type="hidden" name="dni" value="<?php echo htmlspecialchars($row['DNI']); ?>">
                                <button type="button" onclick="mostrarCursos('<?php echo $row['DNI']; ?>')">Cursos</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='6'>No hay estudiantes registrados.</td></tr>";
            }

            // Close the database connection
            $mysqli_usuario->close();
            ?>
        </table>
<!-- Modal -->
<div id="modalCursos" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal()">&times;</span>
        <h2>Cursos del estudiante</h2>
        <div id="cursosContenido">Cargando...</div>
    </div>
</div>

<style>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 1000;
}
.modal-content {
    background-color: #fff;
    margin: 10% auto;
    padding: 20px;
    border-radius: 10px;
    width: 50%;
    position: relative;
}
.close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 25px;
    cursor: pointer;
}
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap');

@keyframes parpadeo {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

.titulo-admin {
    font-family: 'Poppins', sans-serif;
    font-size: 2.2em;
    font-weight: 600;
    text-align: center;
    background: linear-gradient(90deg, #007acc, #00c6ff);
    color: #fff;
    padding: 20px 30px;
    border-radius: 15px;
    margin-bottom: 35px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    letter-spacing: 1.5px;
    animation: parpadeo 2s infinite;
    text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5);
}

</style>
<script>
function mostrarCursos(dni) {
    var modal = document.getElementById("modalCursos");
    var contenido = document.getElementById("cursosContenido");

    modal.style.display = "block";
    contenido.innerHTML = "Cargando...";

    // AJAX para traer los cursos desde PHP
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "obtener_cursos.php?dni=" + dni, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            contenido.innerHTML = xhr.responseText;
        } else {
            contenido.innerHTML = "Error al cargar cursos.";
        }
    };
    xhr.send();
}

function cerrarModal() {
    document.getElementById("modalCursos").style.display = "none";
}
</script>


        <script>
        function editCell(btn, dni, field) {
            var span = btn.previousElementSibling;
            if (span.contentEditable === "false") {
                span.contentEditable = "true";
                span.focus();
                btn.innerHTML = '💾'; // Cambia el icono al guardar
            } else {
                span.contentEditable = "false";
                btn.innerHTML = '🖊️'; // Cambia el icono al editar

                var updatedValue = span.textContent;

                // Aquí se envían los datos al archivo update_user.php
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "update_user.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        alert(xhr.responseText); 
                    }
                };
                xhr.send("dni=" + dni + "&field=" + field + "&value=" + encodeURIComponent(updatedValue));
            }
        }

        </script>

    <table>
        <tr>
            <th>Agregar nuevo estudiante</th>
            <th>Eliminar estudiante</th>
            <th>Eliminar estudiante por curso</th>
        </tr>
        <tr>
            <td>Recuerda rellenar y cumplir con todos los datos solicitados</td>
            <td>Recuerda rellenar y cumplir con todos los datos solicitados</td>
            <td>Recuerda rellenar y cumplir con todos los datos solicitados</td>
        </tr>
        <tr>
            <td><button onclick="window.location.href='Agregar_nuevo_estudiante.php'">Insertar</button></td>
            <td><button onclick="window.location.href='eliminar_estudiante.php'">Eliminar</button></td>
            <td><button onclick="window.location.href='eliminar_estudiante_curso.php'">Eliminar</button></td>
        </tr>
    </table>

    <table>
        <tr>
            <th>Subir link del material y video</th>
        </tr>
        <tr>
            <td>Solo se permitira subir link y se actualizara automaticamente al curso correspondiente</td>
        </tr>
        <tr>
            <td><button onclick="window.location.href='subida_material.php'">Subir</button></td>
        </tr>
    </table>
    <!--table>
        <tr>
            <th>Agregar curso nuevo </th>
        </tr>
        <tr>
            <td>Recuerda rellenar y cumplir con todos los datos solicitados </td>
        </tr>
        <tr>
            <td><button onclick="window.location.href='crear_curso.php'"> Crear curso </button></td>
        </tr>
    </table-->
        <table border="1">
        <tr>
            <th>Agregar preguntas para Videotest</th>
            <th>Actualizar preguntas para Videotest</th>
        </tr>
        <tr>
            <td>Recuerda rellenar y cumplir con todos los datos solicitados</th>
            <td>Recuerda que al actualizar las preguntas se modificara igualmente en el videotest</th>
        </tr>
        <tr>
            <td><button onclick="window.location.href='agregarPreguntasVideo.php'">Agregar Preguntas</button></td>
            <td><button onclick="window.location.href='preguntasvideo.php'">Editar preguntas</button></td>
        </tr>
    </table>

    <table>
        <tr>
            <th>Agregar preguntas para Examen final</th>
            <th>Actualizar preguntas para Examen final</th>
        </tr>
        <tr>
            <td>Recuerda rellenar y cumplir con todos los datos solicitados</td>
            <td>Recuerda que al actualizar las preguntas se modificará igualmente en el examen final</td>
        </tr>
        <tr>
            <td><button onclick="window.location.href='preguntasexamen.php'">Agregar Preguntas</button></td>
            <td><button onclick="window.location.href='editar_preguntasexamen.php'">Editar preguntas</button></td>
        </tr>
    </table>
    
    <table>
        <tr>
            <th>Reiniciar videotest del estudiantes </th>
            <th>Reiniciar examen final del estudiantes </th>
        </tr>
        <tr>
            <td>Recuerda que una vez reiniciado se perderan las notas anteriores</td>
            <td>Recuerda que una vez reiniciado se perderan las notas anteriores</td>
        </tr>
        <tr>
            <td><button onclick="window.location.href='reiniciar_videotest_estudiante.php'">Reiniciar</button></td>
            <td><button onclick="window.location.href='reiniciar_examenfinal_estudiante.php'">Reiniciar</button></td>
        </tr>
    </table>
            </div>
        </div>
    </div>

</body>
</html>
