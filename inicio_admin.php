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

// Variable para mensajes
$success_message = null;

// Si se hace un POST con el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los datos del formulario
    $link_material = $_POST['link_material'];
    $link_video = $_POST['link_video'];
    $id_curso = $_POST['id_curso'];

    // Verificar si ya existe un registro para el curso
    $sql_check = "SELECT * FROM material WHERE id_curso = :id_curso";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->bindParam(':id_curso', $id_curso);
    $stmt_check->execute();

    if ($stmt_check->rowCount() > 0) {
        // Si ya existe, mostrar el mensaje en un modal
        $success_message = "El material y video del curso ya está registrado. No puede guardar nuevamente, pero puede actualizar los datos.";
    } else {
        if (isset($_POST['guardar'])) {
            // Guardar el nuevo enlace en la base de datos
            $sql = "INSERT INTO material (id_curso, link_curso_material, link_curso_video) 
                    VALUES (:id_curso, :link_material, :link_video)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id_curso', $id_curso);
            $stmt->bindParam(':link_material', $link_material);
            $stmt->bindParam(':link_video', $link_video);
            $stmt->execute();
            $success_message = "Datos guardados correctamente.";
        }
    }

    if (isset($_POST['actualizar'])) {
        // Actualizar los enlaces en la base de datos
        $sql = "UPDATE material 
                SET link_curso_material = :link_material, link_curso_video = :link_video 
                WHERE id_curso = :id_curso";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_curso', $id_curso);
        $stmt->bindParam(':link_material', $link_material);
        $stmt->bindParam(':link_video', $link_video);
        $stmt->execute();
        $success_message = "Datos actualizados correctamente.";
    }
}

// Obtener los datos existentes si es necesario (para mostrar al cargar la página)
$current_data = [];
$id_curso = 2; // Usar el ID correspondiente para la búsqueda actual
$sql = "SELECT link_curso_material, link_curso_video FROM material WHERE id_curso = :id_curso";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_curso', $id_curso);
$stmt->execute();
$current_data = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener todos los cursos disponibles para el campo 'id_curso'
$cursos = [];
$sql = "SELECT id_curso, nombre_curso FROM cursos"; // Asegúrate de tener una tabla 'cursos' con id_curso y nombre_curso
$stmt = $pdo->query($sql);
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <h2>MEKADDESH SOLUTION E.R.I.L</h2>
                <ul>
                    <li><a href="inicio_admin.php" class="selected"><i class="fas fa-home"></i>Inicio</a></li>
                    <li><a href="cursosa.php"><i class="fas fa-book"></i> Cursos</a></li>
                    <li><a href="auladmi.php"><i class="fas fa-user"></i> Perfil</a></li>
                    <li><a href="calendarioadmi.php"><i class="fas fa-calendar-alt"></i> Calendario</a></li>
                    <li><a href="cerrar_s.php" onclick="return confirmLogout();" class="cerrar-sesion-boton"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
                </ul>
            </nav>
        </aside>
        <main>
            <!-- Modal para mensajes de éxito -->
            <?php if (isset($success_message)): ?>
    <div class="modal" id="successModal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <p><?php echo $success_message; ?></p>
        </div>
    </div>
<?php endif; ?>

            <div class="contenido-principal">
                <form action="" method="POST">
                    <div class="form-container">
                        <table class="centered-table">
                            <thead>
                                <tr>
                                    <th>ID DEL CURSO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                    <label for="id_curso">Selecciona el curso:</label>
                                    <select name="id_curso" id="id_curso" class="styled-select" required>
                                        <?php foreach ($cursos as $curso): ?>
                                            <option value="<?php echo $curso['id_curso']; ?>" <?php echo (isset($current_data['id_curso']) && $current_data['id_curso'] == $curso['id_curso']) ? 'selected' : ''; ?>>
                                                <?php echo $curso['nombre_curso']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                        <br>
                                    </td>
                                </tr>
                                <thead>
                                    <tr>
                                        <th>LINK DEL MATERIAL</th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td>
                                        <label for="link_material">Link del material:</label>
                                        <input type="url" name="link_material" id="link_curso_material" 
                                               value="<?php echo isset($current_data['link_curso_material']) ? $current_data['link_curso_material'] : ''; ?>" required>
                                        <br>
                                    </td>
                                </tr>
                                <thead>
                                    <tr>
                                        <th>LINK DEL VIDEO</th>
                                    </tr>
                                </thead>
                                <tr>
                                    <td>
                                        <label for="link_video">Link del video:</label>
                                        <input type="url" name="link_video" id="link_curso_video" 
                                               value="<?php echo isset($current_data['link_curso_video']) ? $current_data['link_curso_video'] : ''; ?>" required>
                                        <br>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="button-container">
                                            <button type="submit" name="guardar" class="update-btn">Guardar</button>
                                            <button type="submit" name="actualizar" class="update-btn">Actualizar</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <style>
        main {
            margin-left: 90px; /* Ajusta el valor según el espacio que desees */
            width: calc(100% - 90px); /* Ajustar el ancho para evitar desbordamiento */
            display: flex;
            flex-direction: column;
            align-items: center; /* Centrar horizontalmente */
        }

        .styled-select {
            font-family: Arial, sans-serif;
            font-size: 16px; /* Ajusta según tus enlaces */
            color:rgb(0, 0, 0); /* Mismo color de los links */
            text-decoration: none;
            border: 1px solid #007bff;
            border-radius: 5px;
            padding: 8px 12px;
            background-color: transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%; /* Cambiar a 100% para que se ajuste al contenedor */
            max-width: 1000px; /* Mantener un ancho máximo */
        }

        .styled-select:hover {
            background-color: #007bff;
            color: #fff; /* Cambia el color del texto al pasar */
        }

        .styled-select option {
            font-family: inherit;
            font-size: inherit;
            color: #000; /* Color del texto dentro del menú */
            background-color: #fff;
            padding: 8px;
        }

        .styled-select option:hover {
            background-color: #007bff;
            color: #fff;
        }

        /* Estilo para los inputs de tipo URL */
        input[type="url"] {
            width: 100%; /* Cambiar a 100% para que se ajuste al contenedor */
            max-width: 1000px; /* Mantener un ancho máximo */
            padding: 10px; /* Espaciado interno */
            font-size: 16px; /* Tamaño del texto */
            border: 1px solid #ccc; /* Borde suave */
            border-radius: 5px; /* Bordes redondeados */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Sombra ligera */
            outline: none; /* Sin borde al hacer clic */
            transition: border-color 0.3s ease, box-shadow 0.3s ease; /* Animación */
        }

        /* Efecto al enfocar */
        input[type="url"]:focus {
            border-color: #4CAF50; /* Cambia el color del borde al enfocar */
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.6); /* Sombra al enfocar */
        }

        /* Etiquetas para los inputs */
        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block; /* Asegura que las etiquetas estén encima de los inputs */
        }

        /* Asegura que las celdas contenedoras se ajusten */
        td {
            padding: 10px;
        }

        /* Estilos para el modal */
        .modal {
            display: block; /* Cambia a "none" por defecto */
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            text-align: center;
        }
        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close-btn:hover,
        .close-btn:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        /* Centrar la tabla horizontal y verticalmente */
        .centered-table {
            background-color:rgb(213, 218, 219);
            margin: 0 auto; /* Centra horizontalmente */
            border-collapse: collapse; /* Colapsa bordes */
            text-align: left;
            width: 100%; /* Cambiar a 100% para que se ajuste al contenedor */
            max-width: 800px; /* Mantener un ancho máximo */
        }

        .form-container {
            display: flex;
            justify-content: center; /* Centro horizontal */
            align-items: center; /* Centro vertical */
            width: 100%; /* Asegurar que ocupe todo el ancho disponible */
            padding: 20px;
            box-sizing: border-box;
        }
        
        .centered-table th, 
        .centered-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .centered-table th {
            background-color:#0a507e;
            text-align: center;
        }
        
        /* Estilos para los botones */
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
    
    <script>
        function closeModal() {
            document.getElementById('successModal').style.display = 'none';
        }

        // Cierra automáticamente el modal después de 5 segundos
        setTimeout(closeModal, 5000);
    </script>
</body>
</html>
