<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['DNI'])) {
    header("Location: aula_virtual.php");
    exit();
}

// Procesar la eliminación de la imagen si se solicita
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_image'])) {
    $mysqli = new mysqli("localhost", "root", "", "usuario");
    if ($mysqli->connect_error) {
        die("Error de conexión: " . $mysqli->connect_error);
    }
    
    $stmt = $mysqli->prepare("UPDATE estudiantes SET imagen_perfil = NULL WHERE DNI = ?");
    $stmt->bind_param("s", $_SESSION['DNI']);
    
    if ($stmt->execute()) {
        // Limpiar la variable de la imagen actual
        $imagenBase64 = '';
        echo "<script>
            alert('La imagen ha sido eliminada con éxito.');
            document.getElementById('profileImage').src = 'images/usuario.png';
        </script>";
    } else {
        echo "<script>alert('Error al eliminar la imagen: " . $stmt->error . "');</script>";
    }
    
    $stmt->close();
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil del Usuario</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="perfil.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
.main-content {
    padding: 20px;
    max-width: 900px;
    margin: 0 auto;
}


.tabla-usuario, .tabla-cursos {
    margin: 20px auto; /* Centra las tablas horizontalmente */
    border-collapse: separate;
    width: 90%; /* Ancho reducido para mejor presentación */
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}

.tabla-usuario th, .tabla-cursos th {
    background-color: #0a507e;
    color: white;
    font-weight: bold;
    padding: 15px;
    text-align: left;
    border: none;
    width: 30%; /* Ancho fijo para la columna de encabezados */
}

.tabla-usuario td, .tabla-cursos td {
    padding: 12px 15px;
    border-bottom: 1px solid #edf2f7;
    color: #2d3748;
}

.tabla-usuario td {
    text-align: left;
}

.tabla-cursos td {
    text-align: center; /* Centra el contenido en la tabla de cursos */
}

/* Estilo específico para el título de cursos inscritos */
.cursos-inscritos {
    width: 90%;
    margin: 0 auto;
}

.cursos-inscritos h4 {
    margin: 25px 0 15px 0;
    color: #2d3748;
    font-size: 1.2em;
    text-align: left;
}

.tabla-usuario tr:hover, .tabla-cursos tr:hover {
    background-color: #f8fafc;
}

.tabla-usuario tr:last-child td, .tabla-cursos tr:last-child td {
    border-bottom: none;
}


/* Responsividad para las tablas */
@media (max-width: 768px) {
    .tabla-usuario, .tabla-cursos {
        width: 95%; 
        margin-bottom: 15px;
    }

    .tabla-usuario th, .tabla-usuario td,
    .tabla-cursos th, .tabla-cursos td {
        padding: 10px;
        font-size: 0.9em;
    }

    .tabla-usuario th {
        width: 40%; 
    }
}
.cursos-inscritos {
    width: 90%;
    margin: 0 auto;
}

.cursos-inscritos h4 {
    margin: 30px 0 20px 0; 
    color: #2d3748;
    font-size: 1.5em; 
    text-align: left;
    font-weight: 600; 
    text-transform: uppercase; 
    letter-spacing: 0.5px;
}
.cursos-inscritos h4 {
        font-size: 1.3em; 
    }
   
.tabla-usuario td, .tabla-cursos td {
    padding: 12px 15px;
    border-bottom: 1px solid #edf2f7;
    color: #2d3748;
    text-align: justify; 
}


.tabla-cursos td {
    text-align: left;
}

.delete-image-btn {
    position: absolute;
    bottom: 5px;
    left: 5px;
    background-color: rgba(255, 0, 0, 0.7);
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.profile-container:hover .delete-image-btn {
    opacity: 1;
}

.delete-image-btn:hover {
    background-color: rgba(255, 0, 0, 0.9);
}

.profile-container {
    position: relative;
}
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <nav>
                <h2>MEKADDESH SOLUTION E.I.R.L</h2>
                <ul>
                    <li><a href="#"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="curso.php"><i class="fas fa-book"></i> Cursos</a></li>
                    <li><a href="perfil.php" class="selected"><i class="fas fa-user"></i> Perfil</a></li>
                    <li><a href="calendario.php"><i class="fas fa-calendar-alt"></i> Calendario</a></li>
                    <li><a href="cerrar_s.php" onclick="return confirmLogout();" class="cerrar-sesion-boton"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
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
            <?php
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

            // Mensaje de recomendación con formato mejorado y estructura HTML actualizada
            echo "<div class='recommendations-mini'>
                    <p>
                        <i class='fas fa-info-circle'></i>
                        <span class='recommendation-text'>
                            Imagen de perfil:&nbsp;
                            <span class='highlight'>máx. 2MB</span>
                            <span class='highlight'>JPG/PNG</span>
                        </span>
                    </p>
                </div>";

            // Obtener la imagen de perfil de la base de datos
            $imagenBase64 = '';
            if (isset($_SESSION['DNI'])) {
                $stmt = $conn->prepare("SELECT imagen_perfil FROM estudiantes WHERE DNI = ?");
                $stmt->bind_param("s", $_SESSION['DNI']);
                $stmt->execute();
                $resultado = $stmt->get_result();
                
                if ($row = $resultado->fetch_assoc()) {
                    if ($row['imagen_perfil']) {
                        $imagenBase64 = base64_encode($row['imagen_perfil']);
                    }
                }
                $stmt->close();
            }

            // Procesar la subida de la imagen si se ha enviado un archivo
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profileImage"])) {
                $maxFileSize = 2 * 1024 * 1024; // 2MB en bytes
                $fileSize = $_FILES["profileImage"]["size"];
                
                if ($fileSize > $maxFileSize) {
                    echo "<div class='upload-message error'>
                            <i class='fas fa-exclamation-circle'></i>
                            El tamaño de la imagen no debe exceder 2MB
                          </div>";
                } else {
                    $imagen = file_get_contents($_FILES["profileImage"]["tmp_name"]);
                    
                    $stmt = $conn->prepare("UPDATE estudiantes SET imagen_perfil = ? WHERE DNI = ?");
                    $stmt->bind_param("ss", $imagen, $_SESSION['DNI']);
                    
                    if ($stmt->execute()) {
                        echo "<div class='upload-message success'>
                                <i class='fas fa-check-circle'></i>
                                La imagen ha sido actualizada con éxito
                              </div>";
                    } else {
                        echo "<div class='upload-message error'>
                                <i class='fas fa-times-circle'></i>
                                Error al actualizar la imagen: " . $stmt->error . "
                              </div>";
                    }
                    
                    $stmt->close();
                }
            }

            // Variables de sesión para mostrar en el perfil
            $nombre = $_SESSION['nombre'] ?? 'Usuario';
            $correo = $_SESSION['correo'] ?? 'No disponible';
            $telefono = $_SESSION['telefono'] ?? 'No disponible';
            $sexo = $_SESSION['sexo'] ?? 'No disponible';

            // Consulta para obtener los cursos inscritos y las notas
            $dni = $_SESSION['DNI'];
            $sqlCursos = "SELECT c.nombre_curso, 
                   i.id_curso, 
                   CASE 
                       WHEN (IFNULL(i.intento1, 0) = 0 AND IFNULL(i.intento2, 0) = 0 AND IFNULL(i.intento3, 0) = 0) 
                       THEN 'No disponible' 
                       ELSE GREATEST(IFNULL(i.intento1, 0), IFNULL(i.intento2, 0), IFNULL(i.intento3, 0)) 
                   END AS nota_videotest, 
                   IFNULL(i.examen_final, 'No disponible') AS examen_final
            FROM inscripciones i
            JOIN cursos c ON i.id_curso = c.id_curso
            WHERE i.DNI = ?
             ";
        
            $stmtCursos = $conn->prepare($sqlCursos);
            if ($stmtCursos === false) {
                die("Error en la preparación de la consulta: " . $conn->error);
            }

            $stmtCursos->bind_param("s", $dni);
            $stmtCursos->execute();
            $resultCursos = $stmtCursos->get_result();

            echo "<div class='bienvenida'>";
            echo "<div class='profile-container'>";
            echo "<label for='imageUpload'>";
            if ($imagenBase64) {
                echo "<img src='data:image/jpeg;base64," . $imagenBase64 . "' alt='Imagen de perfil' id='profileImage'>";
                // Agregar botón de eliminar
                echo "<form method='POST' style='display: inline;'>";
                echo "<input type='hidden' name='delete_image' value='1'>";
                echo "<button type='submit' class='delete-image-btn' onclick='return confirm(\"¿Estás seguro de que deseas eliminar tu foto de perfil?\")'><i class='fas fa-trash'></i></button>";
                echo "</form>";
            } else {
                echo "<img src='images/usuario.png' alt='Imagen de perfil' id='profileImage'>";
            }
            echo "<div class='edit-icon'><i class='fas fa-pencil-alt'></i></div>";
            echo "</label>";
            echo "</div>";

            echo "<form id='uploadForm' action='" . $_SERVER['PHP_SELF'] . "' method='POST' enctype='multipart/form-data' style='display: none;'>";
            echo "<input type='file' name='profileImage' id='imageUpload' accept='image/*' onchange='document.getElementById(\"uploadForm\").submit();'>";
            echo "</form>";

            echo "<h3 class='bienvenido-titulo'> Bienvenido, $nombre 🎓</h3>";
            echo "</div>";

            echo "<table class='tabla-usuario'>";
            echo "<tr><th>NOMBRE COMPLETO</th><td>$nombre</td></tr>";
            echo "<tr><th>DIRECCIÓN DE CORREO ELECTRÓNICO</th><td>$correo</td></tr>";
            echo "<tr><th>TELÉFONO</th><td>$telefono</td></tr>";
            echo "<tr><th>SEXO</th><td>$sexo</td></tr>";
            echo "</table>"; // Cerrar tabla de usuario

            // Nueva sección para los cursos inscritos
            echo "<div class='cursos-inscritos'>";
            echo "<h4>CURSOS INSCRITOS</h4>";
            echo "<table class='tabla-cursos'>";
            echo "<tr><th>NOMBRE DEL CURSO</th><th>NOTA VIDEOTEST</th><th>EXAMEN FINAL</th></tr>";

            if ($resultCursos->num_rows > 0) {
                while ($row = $resultCursos->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['nombre_curso'] . "</td>";
                    echo "<td>" . ($row['nota_videotest'] ?? 'No disponible') . "</td>";
                    echo "<td>" . ($row['examen_final'] ?? 'No disponible') . "</td>"; // Mostrar examen final
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No tienes cursos inscritos.</td></tr>";
            }
            echo "</table>";
            echo "</div>"; // Cerrar sección de cursos inscritos

            $stmtCursos->close();
            $conn->close();
            ?>
        </main>
    </div>
</body>
</html>
