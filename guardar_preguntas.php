<?php
session_start(); // Iniciar la sesión

// Conectar a la base de datos
$conn = new mysqli('localhost', 'root', '', 'usuario');

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se han enviado las preguntas
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger el ID del curso
    $id_curso = $_POST['id_curso'];

    // Recorrer las preguntas
    $preguntasGuardadas = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'pregunta_') === 0) {
            $numeroPregunta = str_replace('pregunta_', '', $key);
            $pregunta = $value;

            // Recoger las respuestas
            $respuesta_a = $_POST["respuesta_{$numeroPregunta}_A"];
            $respuesta_b = $_POST["respuesta_{$numeroPregunta}_B"];
            $respuesta_c = $_POST["respuesta_{$numeroPregunta}_C"];
            $respuesta_d = $_POST["respuesta_{$numeroPregunta}_D"];
            $respuesta_correcta = $_POST["respuesta_correcta_{$numeroPregunta}"];

            // Preparar la consulta
            $stmt = $conn->prepare("INSERT INTO preguntas (id_curso, pregunta, respuesta_a, respuesta_b, respuesta_c, respuesta_d, respuesta_correcta) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssss", $id_curso, $pregunta, $respuesta_a, $respuesta_b, $respuesta_c, $respuesta_d, $respuesta_correcta);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                $preguntasGuardadas[] = $pregunta; // Guardar la pregunta guardada
            } else {
                echo "Error al guardar la pregunta: " . $stmt->error;
            }
        }
    }

    // Cerrar la conexión
    $stmt->close();
    $conn->close();

    // Mensaje de éxito
    if (count($preguntasGuardadas) > 0) {
        echo "Preguntas guardadas exitosamente: " . implode(", ", $preguntasGuardadas);
    } else {
        echo "No se guardó ninguna pregunta.";
    }
} else {
    echo "No se recibieron datos.";
}
?>