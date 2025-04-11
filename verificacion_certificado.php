<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Certificado</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('images/fondo.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            animation: fadeIn 1.5s ease-in-out;
        }

        .certificado-info {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 35px 30px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            text-align: center;
            max-width: 500px;
            width: 100%;
            animation: slideIn 1s ease-out;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .certificado-info:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
        }

        .logo {
            width: 80px;
            margin-bottom: 20px;
            animation: rotateIn 1s ease-in-out;
        }

        h1 {
            color: #2c3e50;
            font-size: 26px;
            margin-bottom: 20px;
            animation: textFadeIn 2s ease-in-out;
        }

        p {
            color: #34495e;
            margin-bottom: 15px;
            font-size: 16px;
            line-height: 1.6;
            animation: textFadeIn 2s ease-in-out;
        }

        .autenticado {
            color: #27ae60;
            font-weight: bold;
            animation: textGlow 1.5s infinite alternate;
        }

        .no-autenticado {
            color: #e74c3c;
            font-weight: bold;
            animation: textGlow 1.5s infinite alternate;
        }

        .footer-msg {
            margin-top: 30px;
            font-size: 14px;
            color: #7f8c8d;
        }

        .resaltado {
            display: inline-block;
            background-color: #ecf0f1;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: bold;
            color: #2980b9;
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

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes rotateIn {
            from { transform: rotate(-360deg); opacity: 0; }
            to { transform: rotate(0); opacity: 1; }
        }

        @keyframes textFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes textGlow {
            from { text-shadow: 0 0 10px rgba(255, 255, 255, 0.5); }
            to { text-shadow: 0 0 20px rgba(255, 255, 255, 1); }
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

            if (isset($_GET['dni']) && isset($_GET['curso'])) {
                $dni = $_GET['dni'];
                $curso = $_GET['curso'];
            
                // Validar DNI y curso
                if (strlen($dni) < 8 || !ctype_alnum($dni)) {
                    echo "<p class='no-autenticado'>El formato del DNI no es válido.</p>";
                } else {
                    // Consulta para obtener solo el curso específico
                    $sql = "SELECT e.nombre AS nombre_estudiante, e.DNI, c.nombre_curso
                            FROM estudiantes e
                            JOIN inscripciones i ON e.DNI = i.DNI
                            JOIN cursos c ON c.id_curso = i.id_curso
                            WHERE e.DNI = ? AND c.nombre_curso = ?";
            
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $dni, $curso);
                    $stmt->execute();
                    $result = $stmt->get_result();
            
                    if ($result->num_rows > 0) {
                        echo "<p class='autenticado'>¡Certificado verificado con éxito!</p>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<p><strong>Estudiante:</strong> " . htmlspecialchars($row['nombre_estudiante']) . "</p>";
                            echo "<p><strong>DNI:</strong> " . htmlspecialchars($row['DNI']) . "</p>";
                            echo "<p><strong>Curso:</strong> " . htmlspecialchars($row['nombre_curso']) . "</p>";
                        }
                    } else {
                        echo "<p class='no-autenticado'>No se encontraron resultados para el DNI y el curso proporcionados.</p>";
                    }
                }
            } else {
                echo "<p class='no-autenticado'>DNI o curso no proporcionado en la URL.</p>";
            }
            
            $stmt->close();
        }
        ?>
        <p class="footer-msg">Si tienes alguna duda, no dudes en <a href="https://api.whatsapp.com/message/D7LD33NF66RGD1?autoload=1&app_absent=0" target="_blank" style="color: #25D366; text-decoration: none; font-weight: bold;">contactarnos por WhatsApp</a>.</p>
    </div>
</body>
</html>