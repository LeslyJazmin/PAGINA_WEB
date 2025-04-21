<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname_usuario = "usuario";

// Conexión a la base de datos de usuarios
$conn_usuario = new mysqli($servername, $username, $password, $dbname_usuario);
if ($conn_usuario->connect_error) {
    die("Conexión fallida a la base de datos de usuarios: " . $conn_usuario->connect_error);
}

// Configuración de la codificación
if (!$conn_usuario->set_charset("utf8mb4")) {
    die("Error al establecer la codificación de caracteres en usuarios: " . $conn_usuario->error);
}

// Manejo de la autenticación de estudiantes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['DNI'])) {
    $DNI = $_POST['DNI'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    if (empty($DNI) || empty($contrasena)) {
        $error_message = "Por favor, ingrese tanto el DNI como la contraseña.";
    } else {
        $sql = "SELECT nombre, correo, telefono, sexo FROM estudiantes WHERE DNI = ? AND contrasena = ?";
        try {
            $stmt = $conn_usuario->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Error en la preparación de la consulta: " . $conn_usuario->error);
            }

            $stmt->bind_param("ss", $DNI, $contrasena);
            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
            }

            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $_SESSION['DNI'] = $DNI;
                $_SESSION['nombre'] = $row['nombre'];
                $_SESSION['correo'] = $row['correo'];
                $_SESSION['telefono'] = $row['telefono'];
                $_SESSION['sexo'] = $row['sexo'];

                // Consultar los cursos inscritos del estudiante por DNI
                $sql_inscripciones = "
                    SELECT c.nombre_curso 
                    FROM inscripciones i
                    JOIN cursos c ON i.id_curso = c.id_curso
                    WHERE i.DNI = ?
                ";
                $stmt_inscripciones = $conn_usuario->prepare($sql_inscripciones);
                $stmt_inscripciones->bind_param("s", $DNI);
                $stmt_inscripciones->execute();
                $result_inscripciones = $stmt_inscripciones->get_result();

                // Guardar cursos inscritos en un arreglo
                $cursos_inscritos = [];
                while ($curso_row = $result_inscripciones->fetch_assoc()) {
                    $cursos_inscritos[] = $curso_row['nombre_curso'];
                }

                // Almacenar cursos en la sesión
                $_SESSION['cursos_inscritos'] = $cursos_inscritos;

                // Redirigir a la página de perfil
                header("Location: perfil.php");
                exit();
            } else {
                $error_message = "DNI o contraseña incorrectos";
            }

            $stmt->close();
        } catch (Exception $e) {
            $error_message = "Error en el sistema: " . $e->getMessage();
        }
    }
}

$conn_usuario->close();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nombre_usuario'])) {
    $nombre = $_POST['nombre_usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    if (empty($nombre) || empty($contrasena)) {
        $error_message = "Por favor, ingrese tanto el nombre de usuario como la contraseña.";
    } else {
        $mysqli_admin = new mysqli($servername, $username, $password, "admin");
        if ($mysqli_admin->connect_error) {
            die("Conexión fallida: " . $mysqli_admin->connect_error);
        }

        // Usar la tabla 'admin' en lugar de 'administrador'
        $sql = "SELECT * FROM admin WHERE nombre_usuario = ? AND contrasena = ?";
        $stmt = $mysqli_admin->prepare($sql);
        $stmt->bind_param("ss", $nombre, $contrasena);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['nombre_usuario'] = $nombre;
            $_SESSION['contrasena'] = $contrasena;
            header("Location: auladmi.php");
            exit();
        } else {
            $error_message = "Usuario o contraseña incorrectos.";
        }
        $stmt->close();
        $mysqli_admin->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEKADDESH SOLUTION E.I.R.L</title>
    <link rel="stylesheet" href="aula.css">
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #dde9c7 ;
            margin: 15% auto;
            padding: 20px;
            border: 3px solid #888;
            border-radius: 7px;
            width: 300px;
            text-align: center;
        }
        .login-container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 300px;
        }
        h2, h3 {
            color: #333;
        }
        .form-row {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #388dde;
            color: #e9f3fd;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #388dde;
        }
        .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .checkbox-container input[type="checkbox"] {
            margin-right: 10px;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            background-color: #388dde ;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
        }
        .button:hover {
            background-color: #7aafe1  ;
        }
    </style>
<body>
    <?php
    $error_message = '';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Aquí iría la lógica de autenticación
        // Por ahora, solo mostraremos un mensaje de error de ejemplo
        $error_message = "Credenciales incorrectas. Por favor, intente de nuevo.";
    }
    ?>

    <div id="userTypeModal" class="modal">
        <div class="modal-content">
            <h2>Seleccione el tipo de usuario</h2>
            <a href="#" class="button" onclick="selectUserType('student')">Alumno</a>
            <a href="#" class="button" onclick="selectUserType('admin')">Administrador</a>
        </div>
    </div>
    <div id="loginContainer" class="login-container" style="display: none;">
    <h2>MEKADDESH SOLUTION E.I.R.L</h2>
    <?php if (!empty($error_message)): ?>
        <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <div id="studentForm" style="display: none;">
        <h3>Iniciar sesión como Alumno</h3>
        <form method="post" action="">
            <div class="form-row">
                <label for="DNI">DNI:</label>
                <input type="text" id="DNI" name="DNI" required>
            </div>
            <div class="form-row">
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <div class="checkbox-container">
                <input type="checkbox" id="recordar" name="recordar">
                <label for="recordar">Recordar contraseña</label>
            </div>
            <input type="submit" value="Ingresar">
        </form>
    </div>

    <div id="adminForm" style="display: none;">
        <h3>Iniciar sesión como Administrador</h3>
        <form method="post" action="">
            <div class="form-row">
                <label for="nombre_usuario">Nombre de Usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" required>
            </div>
            <div class="form-row">
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            <div class="checkbox-container">
                <input type="checkbox" id="recordar_admin" name="recordar_admin">
                <label for="recordar_admin">Recordar contraseña</label>
            </div>
            <input type="submit" value="Ingresar">
        </form>
    </div>
    <a href="#" class="button" onclick="showModal()">Cambiar tipo de usuario</a>
</div>

<script>
    window.onload = function() {
        showModal();
        loadStoredCredentials(); // Cargar credenciales almacenadas
    }

    function showModal() {
        document.getElementById('userTypeModal').style.display = 'block';
        document.getElementById('loginContainer').style.display = 'none';
    }

    function selectUserType(type) {
        document.getElementById('userTypeModal').style.display = 'none';
        document.getElementById('loginContainer').style.display = 'block';
        if (type === 'student') {
            document.getElementById('studentForm').style.display = 'block';
            document.getElementById('adminForm').style.display = 'none';
        } else {
            document.getElementById('studentForm').style.display = 'none';
            document.getElementById('adminForm').style.display = 'block';
        }
    }

    // Cargar las credenciales almacenadas
    function loadStoredCredentials() {
        const dniInput = document.getElementById('DNI');
        const passwordInput = document.getElementById('contraseña');
        const adminUserInput = document.getElementById('nombre');
        const adminPasswordInput = document.getElementById('contrasena');

        // Cargar la contraseña almacenada al cambiar el DNI
        dniInput.oninput = function() {
            const storedPassword = localStorage.getItem(dniInput.value);
            if (storedPassword) {
                passwordInput.value = storedPassword; // Completar automáticamente la contraseña
            } else {
                passwordInput.value = ''; // Limpiar si no hay contraseña almacenada
            }
        };

        // Cargar la contraseña almacenada al cambiar el nombre de usuario
        adminUserInput.oninput = function() {
            const storedAdminPassword = localStorage.getItem(adminUserInput.value);
            if (storedAdminPassword) {
                adminPasswordInput.value = storedAdminPassword; // Completar automáticamente la contraseña
            } else {
                adminPasswordInput.value = ''; // Limpiar si no hay contraseña almacenada
            }
        };
    }

    // Guardar la contraseña si la opción está seleccionada
    document.querySelectorAll('form').forEach(form => {
        form.onsubmit = function(e) {
            const dniInput = this.querySelector('input[type="text"]'); // DNI o Nombre de Usuario
            const passwordInput = this.querySelector('input[type="password"]'); // Contraseña
            const checkbox = this.querySelector('input[type="checkbox"]'); // Checkbox de recordar

            // Guardar o eliminar la contraseña en base a si el checkbox está seleccionado
            if (checkbox.checked) {
                localStorage.setItem(dniInput.value, passwordInput.value); // Guardar la contraseña
            } else {
                localStorage.removeItem(dniInput.value); // Eliminar la contraseña
            }
        };
    });
</script>
</body>
</html>

