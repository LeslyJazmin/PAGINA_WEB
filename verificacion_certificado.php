<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Certificado</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* Añadimos la imagen de fondo */
            background-image: url('images/fondo.png'); /* Asegúrate que 'fondo.jpg' exista en la ruta especificada */
            background-size: cover; /* Para cubrir toda la pantalla */
            background-repeat: no-repeat; /* Para no repetir la imagen */
            background-attachment: fixed; /* Para que la imagen de fondo se quede fija al hacer scroll */
            background-position: center; /* Para centrar la imagen */
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .certificado-info {
            /* Cambiamos el color de fondo a un azul marino suave */
            background-color:rgb(0, 122, 189); /* Un tono azul suave */
            padding: 35px 30px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }

        .logo {
            width: 80px;
            margin-bottom: 20px;
        }

        h1 {
            color:rgb(6, 37, 137);
            font-size: 26px;
            margin-bottom: 20px;
        }

        p {
            color: #fff;
            margin-bottom: 15px;
            font-size: 16px;
            line-height: 1.6;
        }

        .autenticado {
            color: #2e7d32;
            font-weight: bold;
        }

        .no-autenticado {
            color: #c62828;
            font-weight: bold;
        }

        .footer-msg {
            margin-top: 30px;
            font-size: 14px;
            color: #fff;
        }

        .resaltado {
            display: inline-block;
            background-color: #e3f2fd;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: bold;
            color:rgb(7, 13, 132);
        }

        @media (max-width: 600px) {
            .certificado-info {
                padding: 25px 20px;
            }

            h1 {
                font-size: 22px;
            }

            p {
                font-size: 14px;
            }

            .logo {
                width: 60px;
            }
        }

    </style>
</head>
<body>
    <div class="certificado-info">
        <img src="logo2.png" alt="Logo de la Institución" class="logo">
        <h1>Verificación de Certificado</h1>

        <?php
        // Conexión a la base de datos
        $conn = new mysqli('localhost', 'root', '', 'usuario');
        if ($conn->connect_error) {
            echo "<p class='no-autenticado'>Error al conectar con la base de datos. Por favor, revisa la configuración.</p>";
        } else {
            $conn->set_charset("utf8mb4");

            if (isset($_GET['dni'])) {
                $dni = $_GET['dni'];

                if (strlen($dni) < 8 || !ctype_alnum($dni)) {
                    echo "<p class='no-autenticado'>El formato del DNI no es válido.</p>";
                } else {
                    $sql = "SELECT e.nombre AS nombre_estudiante, c.nombre_curso
                            FROM estudiantes e
                            JOIN inscripciones i ON e.DNI = i.DNI
                            JOIN cursos c ON c.id_curso = i.id_curso
                            WHERE e.DNI = ?";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $dni);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        echo "<p>Este certificado pertenece a:</p>";
                        echo "<p class='resaltado'>" . htmlspecialchars($row['nombre_estudiante']) . "</p>";
                        echo "<p>Con DNI:</p>";
                        echo "<p class='resaltado'>" . htmlspecialchars($dni) . "</p>";
                        echo "<p>Ha completado satisfactoriamente el curso:</p>";
                        echo "<p class='resaltado'>" . htmlspecialchars($row['nombre_curso']) . "</p>";
                        echo "<p class='autenticado'>✅ CERTIFICADO AUTÉNTICO</p>";
                    } else {
                        echo "<p class='no-autenticado'>El certificado con DNI " . htmlspecialchars($dni) . " no se encuentra en nuestra base de datos.</p>";
                    }

                    $stmt->close();
                }
            } else {
                echo "<p>No se proporcionó un número de identificación para verificar.</p>";
            }

            $conn->close();
        }
        ?>

        <p class="footer-msg">Si tienes alguna duda, no dudes en contactarnos.</p>
    </div>
</body>
</html>