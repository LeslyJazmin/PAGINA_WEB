<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Administrador</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="admi.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>MEKADDESH SOLUTION E.R.I.L</h2>
            <ul>
                <li><a href="inicio_admin.php"><i class="fas fa-home"></i> Inicio</a></li>
                <li><a href="cursosa.php"><i class="fas fa-book"></i> Cursos</a></li>
                <li>
                <li>
                <a href="auladmi.php" class="perfil-link <?php echo basename($_SERVER['PHP_SELF']) == 'auladmi.php' ? 'selected' : ''; ?>">
                    <i class="fas fa-user"></i> Perfil
                </a>
            </li>
                <li><a href="calendarioadmi.php"><i class="fas fa-calendar"></i> Calendario</a></li>
                <li><a href="cerrar_s.php" onclick="return confirmLogout();" class="cerrar-sesion-boton"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
            </ul>
          
            <script>
                function confirmLogout() {
                    return confirm("¿Estás seguro de que deseas cerrar sesión?");
                }
            </script>
        </div>
        <div class="main-content">
            <div class="user-info">
                
            <?php
session_start();

// Procesar la subida de la imagen si se ha enviado un archivo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profileImage"])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["profileImage"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verificar si el archivo es una imagen real
    $check = getimagesize($_FILES["profileImage"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "El archivo no es una imagen.";
        $uploadOk = 0;
    }

    // Verificar el tamaño del archivo
    if ($_FILES["profileImage"]["size"] > 500000) {
        echo "Lo siento, tu archivo es demasiado grande.";
        $uploadOk = 0;
    }

    // Permitir ciertos formatos de archivo
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Lo siento, solo se permiten archivos JPG, JPEG, PNG & GIF.";
        $uploadOk = 0;
    }

    // Subir el archivo si todo está bien
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $target_file)) {
            $_SESSION['profileImage'] = $target_file;
            echo "<script>alert('La imagen ha sido subida con éxito.');</script>";
        } else {
            echo "<script>alert('Lo siento, hubo un error subiendo tu archivo.');</script>";
        }
    }
}

// Mostrar la imagen de perfil
$imagePath = isset($_SESSION['profileImage']) ? $_SESSION['profileImage'] : 'images/usuario.png';

echo "<style>
    .edit-icon {
        position: absolute;
        top: 0;
        right: 0;
        background-color: rgba(0, 0, 0, 0.5);
        color: white;
        padding: 5px;
        border-radius: 50%;
        font-size: 12px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .image-upload-label:hover .edit-icon {
        opacity: 1;
    }
    .hidden-file-input {
        display: none;
    }
</style>";
// Contenedor principal para la imagen y el mensaje
echo "<div class='profile-message-container'>";

// Contenedor para la imagen
echo "<div class='profile-container'>";
echo "<label for='imageUpload' class='image-upload-label'>";
echo "<img src='$imagePath' alt='Imagen de perfil' id='profileImage' class='profile-image'>";
echo "<div class='edit-icon'><i class='fas fa-pencil-alt'></i></div>";
echo "</label>";
echo "</div>"; // Cierre del contenedor de perfil

// Mensaje de bienvenida
echo "<div class='welcome-message'>Bienvenido, Administrador</div>"; 

echo "</div>"; // Cierre del contenedor principal

// Formulario para subir la imagen
echo "<form id='uploadForm' action='" . $_SERVER['PHP_SELF'] . "' method='POST' enctype='multipart/form-data'>";
echo "<input type='file' name='profileImage' id='imageUpload' accept='image/*' onchange='document.getElementById(\"uploadForm\").submit();' class='hidden-file-input'>";
echo "</form>";


?>

<script>
document.getElementById('imageUpload').addEventListener('change', function() {
    document.getElementById('uploadForm').submit();
});
</script>

<?php

if (isset($_SESSION['nombre_usuario'])) {
    
    $nombre_usuario = $_SESSION['nombre_usuario'];
    $contrasena = $_SESSION['contrasena'] ?? ''; 
} else {
    // Process login if not logged in
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre_usuario = $_POST['nombre_usuario'] ?? '';
        $contrasena = $_POST['contrasena'] ?? ''; 

        
        if (empty($nombre_usuario) || empty($contrasena)) {
            echo "<tr><td colspan='7'>Por favor, inicia sesión como administrador.</td></tr>";
            $nombre_usuario = '';
            $contrasena = '';
        } else {
           
            $mysqli_admin = new mysqli("localhost", "root", "", "admin");
            if ($mysqli_admin->connect_error) {
                die("Conexión fallida a admin: " . $mysqli_admin->connect_error);
            }

           
            $sql_admin = "SELECT * FROM administrador WHERE nombre_usuario = ?";
            $stmt_admin = $mysqli_admin->prepare($sql_admin);
            $stmt_admin->bind_param("s", $nombre_usuario);
            $stmt_admin->execute();
            $result_admin = $stmt_admin->get_result();

            if ($result_admin->num_rows > 0) {
                $admin = $result_admin->fetch_assoc();

                // Comprobar la contraseña
                if ($contrasena == $admin['contrasena']) {
                    $_SESSION['nombre_usuario'] = $nombre_usuario;
                    $_SESSION['contrasena'] = $contrasena;

                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    echo "<tr><td colspan='7'>Contraseña incorrecta.</td></tr>";
                }
            } else {
                echo "<tr><td colspan='7'>Administrador no encontrado.</td></tr>";
            }

            $mysqli_admin->close();
        }
    }

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
        <table>
            <tr>
                <th>DNI</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Contraseña</th>
                <th>Teléfono</th>
                <th>Sexo</th>
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

        <style>
        table {
        border-collapse: collapse;
        width: 100%;
        }

        th, td {
        text-align: left;
        padding: 8px;
        border-bottom: 1px solid #ddd;
        }

        .editable-cell {
        display: flex; /* Activamos Flexbox */
        justify-content: center; /* Centrar horizontalmente */
        align-items: center; /* Alineamos verticalmente al centro */
        }

        .editable {
        flex-grow: 1; /* El texto ocupa el espacio disponible */
        }

        .edit-btn {
        margin-left: 10px; /* Espacio entre el texto y el botón */
        border: none;
        background: none;
        cursor: pointer;
        }

        .course-name-container {
        display: flex; /* Activamos flexbox para el contenedor */
        align-items: center; /* Alineamos el texto y el botón verticalmente */
        }

        .course-name-container .edit-btn {
        margin-left: 5px; /* Agregamos un margen pequeño entre el texto y el botón */
        }
        .phone-container {
        display: flex; /* Activamos flexbox para el contenedor */
        align-items: center; /* Alineamos el texto y el botón verticalmente */
        }

        .phone-container .edit-btn {
        margin-left: 5px; /* Agregamos un margen pequeño entre el texto y el botón */
        }

        </style>
    <table>
        <tr>
            <th>Agregar nuevo estudiante</th>
        </tr>
        <tr>
            <td>Recuerda rellenar y cumplir con todos los datos solicitados</td>
        </tr>
        <tr>
            <td><button onclick="window.location.href='Agregar_nuevo_estudiante.php'">Insertar</button></td>
        </tr>
    </table>

    <table>
        <tr>
            <th>Eliminar estudiante</th>
        </tr>
        <tr>
            <td>Recuerda rellenar y cumplir con todos los datos solicitados</td>
        </tr>
        <tr>
            <td><button onclick="window.location.href='eliminar_estudiante.php'">Suprimir</button></td>
        </td>
        </tr>
    </table>
    <table>
        <tr>
            <th>Reiniciar videotest del estudiantes </th>
        </tr>
        <tr>
            <td>Recuerda que una vez reiniciado se perderan las notas anteriores</td>
        </tr>
        <tr>
            <td><button onclick="window.location.href='reiniciar_videotest_estudiante.php'">Reiniciar</button></td>
        </tr>
    </table>
    <table>
        <tr>
            <th>Reiniciar examen final del estudiantes </th>
        </tr>
        <tr>
            <td>Recuerda que una vez reiniciado se perderan las notas anteriores</td>
        </tr>
        <tr>
            <td><button onclick="window.location.href='reiniciar_examenfinal_estudiante.php'">Reiniciar</button></td>
        </tr>
    </table>
    <table>
        <tr>
            <th>Agregar curso nuevo </th>
        </tr>
        <tr>
            <td>Recuerda rellenar y cumplir con todos los datos solicitados </td>
        </tr>
        <tr>
            <td><button onclick="window.location.href='crear_curso.php'"> Crear curso </button></td>
        </tr>
    </table>
        <table border="1">
        <tr>
            <th>Agregar preguntas para Videotest</th>
            <th>Actualizar preguntas para Videotest</th>
        </tr>
        <tr>
            <td>Recuerda rellenar y cumplir con todos los datos solicitados para agregar las preguntas correctamente</th>
            <td>Recuerda que al actualizar las preguntas se modificara igualmente en el videotest</th>
        </tr>
        <tr>
            <td><button onclick="window.location.href='agregarPreguntasVideo.php'">Agregar Preguntas</button></td>
            <td><button onclick="window.location.href='preguntasvideo.php'">Editar preguntas</button></td>
        </tr>
    </table>

    <table>
        <tr>
            <th>Agregar preguntas para Examen final </th>
        </tr>
        <tr>
            <td>Recuerda rellenar y cumplir con todos los datos solicitados </td>
        </tr>
        <tr>
            <td><button onclick="window.location.href='preguntasexamen.php'"> Editar preguntas </button></td>
        </tr>
    </table>
            </div>
        </div>
    </div>
</body>
</html>
