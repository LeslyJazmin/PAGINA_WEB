<?php
header('Content-Type: text/html; charset=utf-8');

// Simular un formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = $_POST['codigo'];

    // Simular datos de cursos
    $cursos = [
        [
            "codigo" => "CURSO001",
            "nombre_persona" => "Juan Pérez",
            "nombre_curso" => "Desarrollo Web",
            "cantidad_horas" => 40,
            "fecha" => "2024-06-01",
            "nombre_empresa" => "Empresa ABC"
        ],
        [
            "codigo" => "CURSO002",
            "nombre_persona" => "María López",
            "nombre_curso" => "Marketing Digital",
            "cantidad_horas" => 30,
            "fecha" => "2024-07-01",
            "nombre_empresa" => "Empresa XYZ"
        ]
    ];

    // Buscar el curso por el código
    $resultadoHtml = "";
    $encontrado = false;

    foreach ($cursos as $curso) {
        if ($curso['codigo'] === $codigo) {
            $resultadoHtml .= "<h3>Información del Curso:</h3>";
            $resultadoHtml .= "Nombre: " . htmlspecialchars($curso['nombre_persona']) . "<br>";
            $resultadoHtml .= "Curso: " . htmlspecialchars($curso['nombre_curso']) . "<br>";
            $resultadoHtml .= "Cantidad de horas: " . htmlspecialchars($curso['cantidad_horas']) . "<br>";
            $resultadoHtml .= "Fecha: " . htmlspecialchars($curso['fecha']) . "<br>";
            $resultadoHtml .= "Nombre de la empresa: " . htmlspecialchars($curso['nombre_empresa']) . "<br>";
            $encontrado = true;
            break;
        }
    }

    // Si no se encontró el curso
    if (!$encontrado) {
        $resultadoHtml = "No se encontraron resultados para el código ingresado.";
    }
} else {
    $resultadoHtml = "";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Búsqueda</title>
</head>
<body>
    <div class="container">
        <section class="form-section">
            <div class="form-content">
                <form action="" method="post">
                    <label for="codigo">Código:</label>
                    <input type="text" id="codigo" name="codigo" required>
                    <button type="submit">Buscar</button>
                </form>
            </div>
            <div class="resultados">
                <?php
                // Mostrar el resultado de la búsqueda
                echo $resultadoHtml;
                ?>
            </div>
        </section>
    </div>
</body>
</html>

