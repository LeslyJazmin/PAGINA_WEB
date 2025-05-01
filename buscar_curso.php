<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEKADDESH SOLUTION E.I.R.L</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">


</head>
<?php
require('fpdf/fpdf.php');

if (isset($_POST['id_curso']) && !empty($_POST['id_curso']) && isset($_POST['DNI']) && !empty($_POST['DNI'])) {
    // Conectar a la base de datos
    $conn = new mysqli('localhost', 'root', '', 'usuario');

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Establecer la codificación de la conexión a UTF-8
    $conn->set_charset("utf8mb4");

    // Obtener el id_curso y DNI del formulario
    $id_curso = $_POST['id_curso'];
    $DNI = $_POST['DNI'];

    // Consulta modificada para verificar tanto el id_curso como el DNI
    $sql = "
        SELECT 
            e.nombre, 
            CONCAT(c.nombre_curso) as nombre_curso,
            c.fecha, 
            i.DNI
        FROM estudiantes e 
        JOIN inscripciones i ON e.DNI = i.DNI
        JOIN cursos c ON c.id_curso = i.id_curso
        WHERE i.id_curso = ? AND i.DNI = ?
    ";

    // Preparar la consulta
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    // Vincular los parámetros
    $stmt->bind_param("is", $id_curso, $DNI);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result(
        $nombre_estudiante, 
        $nombre_curso, 
        $fecha, 
        $dni
    );

    // Mostrar los resultados
    if ($stmt->num_rows > 0) {
        while ($stmt->fetch()) {
            echo "<div class='contenedor-resultados'>";
            echo "<div class='resultado-individual'>";
            echo "<p class='nombre-persona'>$nombre_estudiante</p>";
            echo "<p class='nombre-curso'>$nombre_curso</p>";
            
            // Agregar el botón para descargar el certificado
            echo "<div class='btn-container'>";
            echo "<form method='post' action='descargar_certificado.php' target='_blank'>";
            echo "<input type='hidden' name='id_curso' value='$id_curso'>";
            echo "<input type='hidden' name='DNI' value='$dni'>";
            echo "<button type='submit' class='btn-flotante'>Descargar Certificado</button>";
            echo "</form>";
            echo "</div>";
            
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p>No se encontró un certificado para el DNI y curso especificados.</p>";
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();
} else {
    die("ID del curso o DNI no proporcionado o vacío");
}
?>

<style>
   html, body {
    margin: 0;
    padding: 0;
    height: 100%;
}

body {
    background: 
        url('./images1/ImagenMstr.jpg'), /* Imagen superior */
        url('./images1/fondocertificado.jpg');   /* Imagen de fondo */
    background-size: 
        contain,      /* La primera imagen se adapta al tamaño de la pantalla */
        cover;        /* Tamaño de la imagen de fondo */
    background-position: 
        center,       /* Posición de la primera imagen */
        center;       /* Posición de la imagen de fondo */
    background-repeat: 
        no-repeat,    /* La primera imagen no se repite */
        no-repeat;    /* La imagen de fondo no se repite */
    background-attachment: 
        fixed,        /* Primera imagen fija */
        fixed;        /* Imagen de fondo fija */
}
        .contenido {
            padding: 20px; /* Ajusta el padding según sea necesario */
            border-radius: 10px; /* Opcional: agrega esquinas redondeadas */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Agrega una sombra para dar profundidad */
            background: rgba(255, 255, 255, 0.8); /* Fondo blanco semi-transparente para mejor legibilidad */
        }
.resultado-individual {
  padding: 0.1px;
    max-width: 450px; /* Limita el ancho del contenedor */
    height: 300px; /* Ajusta la altura del contenedor */
    font-size: 5px; /* Ajusta el tamaño de la letra según sea necesario */
    line-height: 1.0;
    margin-bottom: 10px;
    border-radius: 5px; /* Opcional: agrega esquinas redondeadas */
    text-align: right;
}
.nombre-persona {
    font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
    font-size: clamp(24px, 10vw, 48px);
    position: absolute;
    top: 22%; /* Ajustado para la posición correcta del nombre */
    left: 50%;
    transform: translateX(-50%);
    text-align: center;
    width: 80%;
    word-wrap: break-word; /* Permite que el texto se ajuste si es demasiado largo */

}

.nombre-curso {
    position: absolute;
    font-size: calc(20px + 1vw); /* Ajusta el tamaño de la fuente dinámicamente según el ancho de la pantalla */
    font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
    font-style: italic;
    top: 41%;     /* Ajustado de 300px a 260px para subir el texto */
    left: 50%;
    transform: translateX(-50%);
    text-align: center;
    width: 80%;
    white-space: pre-line;
    line-height: 1.3;
    padding: 0 20px;
    word-wrap: break-word; /* Permite que el texto se ajuste si es demasiado largo */
}

/* Estilo general del contenedor del botón */
.btn-container {
    position: fixed; /* Fija el contenedor en una posición específica de la pantalla */
    bottom: 90px; /* Distancia desde el borde inferior de la pantalla. Ajusta este valor para subir el botón. */
    right: 20px; /* Distancia desde el borde derecho de la pantalla */
    z-index: 1000; /* Asegura que el botón quede encima de otros elementos */
}

/* Estilo del botón flotante */
.btn-flotante {
    background-color: #010101; /* Nuevo color de fondo del botón */
    color: white; /* Color del texto */
    border: none; /* Quita el borde del botón */
    padding: 10px 20px; /* Espaciado interno del botón */
    border-radius: 25px; /* Bordes redondeados más pronunciados */
    font-size: 16px; /* Tamaño del texto */
    cursor: pointer; /* Cambia el cursor a una mano al pasar sobre el botón */
    box-shadow: 0px 4px 8px rgba(0,0,0,0.2); /* Sombra del botón */
    transition: background-color 0.3s, transform 0.3s; /* Transición suave para cambios de color y tamaño */
}

.btn-flotante:hover {
    background-color:rgb(17, 113, 210) ; /* Nuevo color de fondo al pasar el cursor sobre el botón */
    transform: scale(1.10); /* Aumenta el tamaño del botón ligeramente al pasar el cursor */
}

.btn-flotante:focus {
    outline: none; /* Quita el contorno del botón al recibir el foco */
    box-shadow: 0 0 5px rgb(17, 113, 210); /* Agrega un efecto de sombra al recibir el foco */
}
</style>

