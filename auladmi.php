<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['nombre_usuario'])) {
    header("Location: aula_virtual.php");
    exit();
}

// Procesar la eliminación de la imagen si se solicita
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_image'])) {
    $mysqli_admin = new mysqli("localhost", "root", "", "admin");
    if ($mysqli_admin->connect_error) {
        die("Error de conexión: " . $mysqli_admin->connect_error);
    }

    $stmt = $mysqli_admin->prepare("UPDATE admin SET imagen_perfil = NULL WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $_SESSION['nombre_usuario']);

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
    $mysqli_admin->close();
}

// Procesar la actualización de la contraseña si se envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_password'])) {
    $new_password = $_POST['new_password'];
    // Aquí deberías agregar validaciones de la nueva contraseña (longitud, seguridad, etc.)

    $mysqli_admin_update = new mysqli("localhost", "root", "", "admin");
    if ($mysqli_admin_update->connect_error) {
        die("Error de conexión: " . $mysqli_admin_update->connect_error);
    }

    $stmt_update = $mysqli_admin_update->prepare("UPDATE admin SET contrasena = ? WHERE nombre_usuario = ?");
    $stmt_update->bind_param("ss", $new_password, $_SESSION['nombre_usuario']);

    if ($stmt_update->execute()) {
        echo "<script>alert('Contraseña actualizada con éxito.');</script>";
        // Podrías redirigir o mostrar un mensaje de éxito más visual
    } else {
        echo "<script>alert('Error al actualizar la contraseña: " . $stmt_update->error . "');</script>";
    }

    $stmt_update->close();
    $mysqli_admin_update->close();
}
// Procesar actualización de nombre de usuario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_username'])) {
    $new_username = trim($_POST['new_username']);

    $mysqli = new mysqli("localhost", "root", "", "admin");
    if ($mysqli->connect_error) {
        die("Error de conexión: " . $mysqli->connect_error);
    }

    // Verifica que el nuevo nombre de usuario no esté ya en uso
    $check_stmt = $mysqli->prepare("SELECT * FROM admin WHERE nombre_usuario = ?");
    $check_stmt->bind_param("s", $new_username);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>alert('El nombre de usuario ya está en uso.');</script>";
    } else {
        $update_stmt = $mysqli->prepare("UPDATE admin SET nombre_usuario = ? WHERE nombre_usuario = ?");
        $update_stmt->bind_param("ss", $new_username, $_SESSION['nombre_usuario']);

        if ($update_stmt->execute()) {
            $_SESSION['nombre_usuario'] = $new_username; // Actualiza la sesión
            echo "<script>alert('Nombre de usuario actualizado con éxito.');</script>";
        } else {
            echo "<script>alert('Error al actualizar el nombre de usuario: " . $update_stmt->error . "');</script>";
        }
        $update_stmt->close();
    }

    $check_stmt->close();
    $mysqli->close();
}

// Procesar actualización de correo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_email'])) {
    $new_email = trim($_POST['new_email']);

    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('El correo electrónico no es válido.');</script>";
    } else {
        $mysqli = new mysqli("localhost", "root", "", "admin");
        if ($mysqli->connect_error) {
            die("Error de conexión: " . $mysqli->connect_error);
        }

        $update_stmt = $mysqli->prepare("UPDATE admin SET Email = ? WHERE nombre_usuario = ?");
        $update_stmt->bind_param("ss", $new_email, $_SESSION['nombre_usuario']);

        if ($update_stmt->execute()) {
            echo "<script>alert('Correo electrónico actualizado con éxito.');</script>";
        } else {
            echo "<script>alert('Error al actualizar el correo electrónico: " . $update_stmt->error . "');</script>";
        }

        $update_stmt->close();
        $mysqli->close();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Administrador</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="admi.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .admin-table th, .admin-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .admin-table th {
            background-color: #0a507e;
            font-weight: bold;
        }

        .edit-password-icon {
            cursor: pointer;
            margin-left: 5px;
        }

        .edit-password-form {
            display: none; /* Oculto por defecto */
            margin-top: 10px;
        }
        .edit-user-icon {
            cursor: pointer;
            margin-left: 5px;
        }

        .edit-user-form {
            display: none; /* Oculto por defecto */
            margin-top: 10px;
        }
        .edit-email-icon {
            cursor: pointer;
            margin-left: 5px;
        }

        .edit-email-form {
            display: none; /* Oculto por defecto */
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2>MEKADDESH SOLUTION E.I.R.L</h2>
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

                function showEditPasswordForm() {
                    document.getElementById('editPasswordForm').style.display = 'block';
                }
                function showEditNombreForm() {
                    document.getElementById('editNombreForm').style.display = 'block';
                }
                function showEditEmailForm() {
                    document.getElementById('editEmailForm').style.display = 'block';
                }
            </script>
        </div>
        <div class="main-content">
            <div class="user-info">

            <?php
            // Procesar la subida de la imagen si se ha enviado un archivo
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profileImage"])) {
                $maxFileSize = 2 * 1024 * 1024; // 2MB en bytes
                $fileSize = $_FILES["profileImage"]["size"];

                if ($fileSize > $maxFileSize) {
                    echo "<div class='upload-message error'>El tamaño de la imagen no debe exceder 2MB</div>";
                } else {
                    $imagen = file_get_contents($_FILES["profileImage"]["tmp_name"]);

                    $mysqli_admin = new mysqli("localhost", "root", "", "admin");
                    if ($mysqli_admin->connect_error) {
                        die("Error de conexión: " . $mysqli_admin->connect_error);
                    }

                    $stmt = $mysqli_admin->prepare("UPDATE admin SET imagen_perfil = ? WHERE nombre_usuario = ?");
                    $stmt->bind_param("ss", $imagen, $_SESSION['nombre_usuario']);

                    if ($stmt->execute()) {
                        echo "<div class='upload-message success'>La imagen ha sido actualizada con éxito.</div>";
                    } else {
                        echo "<div class='upload-message error'>Error al actualizar la imagen: " . $stmt->error . "</div>";
                    }

                    $stmt->close();
                    $mysqli_admin->close();
                }
            }

            // Obtener la imagen de perfil de la base de datos
            $imagenBase64 = '';
            if (isset($_SESSION['nombre_usuario'])) {
                $mysqli_admin = new mysqli("localhost", "root", "", "admin");
                if ($mysqli_admin->connect_error) {
                    die("Error de conexión: " . $mysqli_admin->connect_error);
                }

                // Mantener "admin" como nombre de la tabla
                $stmt = $mysqli_admin->prepare("SELECT imagen_perfil FROM admin WHERE nombre_usuario = ?");
                $stmt->bind_param("s", $_SESSION['nombre_usuario']);
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($row = $resultado->fetch_assoc()) {
                    if ($row['imagen_perfil']) {
                        $imagenBase64 = base64_encode($row['imagen_perfil']);
                    }
                }

                $stmt->close();
                $mysqli_admin->close();
            }

            // Mensaje de recomendación con formato mejorado
            echo "<div class='recommendations-mini'>
                    <p>
                        <i class='fas fa-image'></i>
                        Imagen de perfil: <span class='highlight'>máx. 2MB</span> <span class='highlight'>JPG/PNG</span>
                    </p>
                </div>";

            // Resto del código de visualización
            echo "<div class='profile-message-container'>";
            echo "<div class='profile-container'>";
            echo "<label for='imageUpload' class='image-upload-label'>";
            if ($imagenBase64) {
                echo "<img src='data:image/jpeg;base64," . $imagenBase64 . "' alt='Imagen de perfil' id='profileImage' class='profile-image'>";
                // Agregar botón de eliminar
                echo "<form method='POST' style='display: inline;'>";
                echo "<input type='hidden' name='delete_image' value='1'>";
                echo "<button type='submit' class='delete-image-btn' onclick='return confirm(\"¿Estás seguro de que deseas eliminar tu foto de perfil?\")'><i class='fas fa-trash'></i></button>";
                echo "</form>";
            } else {
                echo "<img src='images/usuario.png' alt='Imagen de perfil' id='profileImage' class='profile-image'>";
            }
            echo "<div class='edit-icon'><i class='fas fa-pencil-alt'></i></div>";
            echo "</label>";
            echo "</div>";

            echo "<div class='welcome-message'>Bienvenido, " . htmlspecialchars($_SESSION['nombre_usuario']) . "</div>";
            echo "</div>";

            // Formulario para subir la imagen
            echo "<form id='uploadForm' action='" . $_SERVER['PHP_SELF'] . "' method='POST' enctype='multipart/form-data'>";
            echo "<input type='file' name='profileImage' id='imageUpload' accept='image/*' onchange='document.getElementById(\"uploadForm\").submit();' class='hidden-file-input'>";
            echo "</form>";

            ?>

            <script>
            document.getElementById('imageUpload').addEventListener('change', function() {
                document.getElementById('uploadForm').submit();
            });
            function toggleEditForm() {
        const form = document.getElementById('editUserForm');
        form.style.display = form.style.display === 'block' ? 'none' : 'block';
    }
            </script>

            <div class="admin-details">
                <h2>Información del Administrador</h2>
                
                <?php
                if (isset($_SESSION['nombre_usuario'])) {
                    $nombre_usuario = $_SESSION['nombre_usuario'];

                    $mysqli_admin_info = new mysqli("localhost", "root", "", "admin");
                    if ($mysqli_admin_info->connect_error) {
                        die("Error de conexión: " . $mysqli_admin_info->connect_error);
                    }

                    $stmt_info = $mysqli_admin_info->prepare("SELECT nombre_usuario, Email , contrasena FROM admin WHERE nombre_usuario = ?");
                    $stmt_info->bind_param("s", $nombre_usuario);
                    $stmt_info->execute();
                    $result_info = $stmt_info->get_result();

                    if ($admin_info = $result_info->fetch_assoc()) {
                        echo "<table class='admin-table'>";
                        echo "<thead>";
                        echo "<tr>";
                        echo "<th>Campo</th>";
                        echo "<th>Valor</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        echo "<tr>";
                        echo "<td>Nombre Completo</td>";
                        echo "<td>" . htmlspecialchars($admin_info['nombre_usuario']) .  " <i class='fas fa-pencil-alt edit-user-icon' onclick='showEditNombreForm()'></i></td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td>Email</td>";
                        echo "<td>" . htmlspecialchars($admin_info['Email']) .  " <i class='fas fa-pencil-alt edit-email-icon' onclick='showEditEmailForm()'></i></td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td>Contraseña</td>";
                        echo "<td>" . htmlspecialchars($admin_info['contrasena']) . " <i class='fas fa-pencil-alt edit-password-icon' onclick='showEditPasswordForm()'></i></td>";
                        echo "</tr>";
                        echo "</tbody>";
                        echo "</table>";
                        

                        // Formulario para editar la contraseña (inicialmente oculto)
                        echo "<div id='editPasswordForm' class='edit-password-form'>";
                        echo "<h3>Editar Contraseña</h3>";
                        echo "<form method='POST'>";
                        echo "<label for='new_password'>Nueva Contraseña:</label>";
                        echo "<input type='password' id='new_password' name='new_password' required><br><br>";
                        echo "<button type='submit'>Guardar Nueva Contraseña</button>";
                        echo "</form>";
                        echo "</div>";

                        // Formulario para editar la contraseña (inicialmente oculto)
                        echo "<div id='editNombreForm' class='edit-user-form'>";
                        echo "<h3>Editar Nombre de Usuario</h3>";
                        echo "<form method='POST'>";
                        echo "<label for='new_username'>Nueva Nombre de Usuario:</label>";
                        echo "<input type='user' id='new_username' name='new_username' required><br><br>";
                        echo "<button type='submit'>Guardar Nueva nombre de Usuario</button>";
                        echo "</form>";
                        echo "</div>";
                        
                        // Formulario para editar la contraseña (inicialmente oculto)
                        echo "<div id='editEmailForm' class='edit-email-form'>";
                        echo "<h3>Editar Email</h3>";
                        echo "<form method='POST'>";
                        echo "<label for='new_email'>Nuevo Email: </label>";
                        echo "<input type='email' id='new_email' name='new_email' required><br><br>";
                        echo "<button type='submit'>Guardar Nuevo Email</button>";
                        echo "</form>";
                        echo "</div>";
                        
                    } else {
                        echo "<p>No se encontró información del administrador.</p>";
                    }

                    $stmt_info->close();
                    $mysqli_admin_info->close();
                } else {
                    echo "<p>Por favor, inicia sesión para ver la información del administrador.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>