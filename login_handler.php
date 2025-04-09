<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_type = $_POST['user_type'] ?? '';

    if ($user_type === 'student') {
        $dbname = "usuario";
        $DNI = $_POST['DNI'] ?? '';
        $contrasena = $_POST['contrasena'] ?? '';

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        $sql = "SELECT nombre, correo, telefono, sexo FROM estudiantes WHERE DNI = ? AND contrasena = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $DNI, $contrasena);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['DNI'] = $DNI;
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['correo'] = $row['correo'];
            $_SESSION['telefono'] = $row['telefono'];
            $_SESSION['sexo'] = $row['sexo'];

            // Consultar cursos inscritos
            $sql_cursos = "SELECT c.nombre_curso FROM inscripciones i 
                          JOIN cursos c ON i.id_curso = c.id_curso 
                          WHERE i.DNI = ?";
            $stmt_cursos = $conn->prepare($sql_cursos);
            $stmt_cursos->bind_param("s", $DNI);
            $stmt_cursos->execute();
            $result_cursos = $stmt_cursos->get_result();
            
            $cursos = [];
            while ($curso = $result_cursos->fetch_assoc()) {
                $cursos[] = $curso['nombre_curso'];
            }
            $_SESSION['cursos_inscritos'] = $cursos;

            header("Location: perfil.php");
            exit();
        }
        $conn->close();
    } elseif ($user_type === 'admin') {
        $dbname = "admin";
        $nombre_usuario = $_POST['nombre_usuario'] ?? '';
        $contrasena = $_POST['contrasena'] ?? '';

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        $sql = "SELECT * FROM admin WHERE nombre_usuario = ? AND contrasena = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nombre_usuario, $contrasena);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['admin'] = true;
            $_SESSION['nombre'] = $nombre_usuario;
            header("Location: auladmi.php");
            exit();
        }
        $conn->close();
    }
}

// Si llegamos aquí, la autenticación falló
header("Location: index.php?error=1");
exit();
?>