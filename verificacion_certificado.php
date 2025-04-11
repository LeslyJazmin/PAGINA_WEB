<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Certificado</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3a86ff;
            --secondary-color: #8338ec;
            --accent-color: #ff006e;
            --success-color: #27ae60;
            --error-color: #e74c3c;
            --text-color: #2b2d42;
            --light-color: #f8f9fa;
            --transition-speed: 0.3s;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
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
            background-color: rgba(218, 213, 213, 0.95);
            padding: 35px 30px;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.18);
            text-align: center;
            max-width: 500px;
            width: 100%;
            animation: slideIn 1s ease-out;
            transition: transform 0.5s ease, box-shadow 0.5s ease;
            border: 1px solidrgb(180, 172, 172);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .certificado-info:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--accent-color));
            animation: shimmerBorder 2s infinite linear;
            background-size: 200% 100%;
        }

        .certificado-info:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        }

        .logo {
            width: 100px;
            margin-bottom: 20px;
            animation: rotateIn 1s ease-in-out, pulse 3s infinite ease-in-out;
            filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.1));
            transition: transform 0.3s ease;
        }
        
        .logo:hover {
            transform: scale(1.1) rotate(5deg);
        }

        h1 {
            color: var(--text-color);
            font-size: 26px;
            margin-bottom: 20px;
            animation: textFadeIn 2s ease-in-out;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            position: relative;
            display: inline-block;
        }

        h1:after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            transform: scaleX(0);
            transform-origin: left;
            animation: expandLine 1.5s forwards ease-out;
        }

        p {
            color: var(--text-color);
            margin-bottom: 15px;
            font-size: 16px;
            line-height: 1.6;
            animation: textFadeIn 2s ease-in-out;
        }

        .autenticado {
            color: var(--success-color);
            font-weight: bold;
            animation: pulse 1.5s infinite alternate;
            font-size: 20px;
            margin: 20px 0;
            padding: 15px;
            background-color: rgba(39, 174, 96, 0.1);
            border-radius: 10px;
            border-left: 4px solid var(--success-color);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.2);
            transition: all 0.3s ease;
        }

        .autenticado:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(39, 174, 96, 0.3);
        }

        .no-autenticado {
            color: var(--error-color);
            font-weight: bold;
            animation: shake 0.5s ease-in-out, pulse 1.5s infinite alternate;
            font-size: 20px;
            margin: 20px 0;
            padding: 15px;
            background-color: rgba(231, 76, 60, 0.1);
            border-radius: 10px;
            border-left: 4px solid var(--error-color);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.2);
            transition: all 0.3s ease;
        }

        .no-autenticado:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(231, 76, 60, 0.3);
        }

        .footer-msg {
            margin-top: 30px;
            font-size: 14px;
            color: #7f8c8d;
            border-top: 1px dashed #ddd;
            padding-top: 15px;
            animation: textFadeIn 2.5s ease-in-out;
        }

        .resaltado {
            display: inline-block;
            background: linear-gradient(120deg, #f8f9fa, #e9ecef);
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: bold;
            color: var(--primary-color);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            margin: 3px 0;
            position: relative;
            z-index: 1;
            overflow: hidden;
        }
        
        .resaltado:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
            transition: all 0.5s;
            z-index: -1;
        }
        
        .resaltado:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .resaltado:hover:before {
            left: 100%;
        }
        
        .info-container {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #e0e0e0;
            animation: slideUp 1s ease-out;
            transition: all 0.3s ease;
        }
        
        .info-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        }
        
        .autenticidad {
            font-style: italic;
            color: var(--success-color);
            margin-top: 20px;
            padding: 15px;
            border-top: 1px dashed #ddd;
            background-color: rgba(39, 174, 96, 0.05);
            border-radius: 10px;
            animation: glow 2s infinite alternate;
            transition: all 0.3s ease;
        }
        
        .info-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            transition: all 0.3s ease;
            animation: slideUp 1.2s ease-out;
        }
        
        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .info-item:hover {
            transform: translateX(5px);
        }
        
        .info-label {
            font-weight: bold;
            color: #7f8c8d;
            margin-bottom: 8px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
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
                width: 80px;
            }
            
            .autenticado, .no-autenticado {
                font-size: 18px;
                padding: 12px;
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes rotateIn {
            from { transform: rotate(-10deg) scale(0.8); opacity: 0; }
            to { transform: rotate(0) scale(1); opacity: 1; }
        }

        @keyframes textFadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            from { transform: scale(1); }
            to { transform: scale(1.03); }
        }
        
        @keyframes expandLine {
            from { transform: scaleX(0); }
            to { transform: scaleX(1); }
        }
        
        @keyframes shimmerBorder {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }
        
        @keyframes glow {
            from { box-shadow: 0 0 5px rgba(39, 174, 96, 0.1); }
            to { box-shadow: 0 0 20px rgba(39, 174, 96, 0.2); }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
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

            if (isset($_GET['dni']) && isset($_GET['nombre_curso'])) {
                $dni = $_GET['dni'];
                $curso = urldecode($_GET['nombre_curso']); // Decodificar el curso
                
                // Añadir log para depuración
                error_log("Verificando certificado - DNI: " . $dni . ", Curso: " . $curso);
            
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
                    
                    // Añadir log para depuración
                    error_log("Resultados de la consulta: " . $result->num_rows . " filas encontradas");
            
                    if ($result->num_rows > 0) {
                        echo "<p class='autenticado'>¡Certificado verificado con éxito!</p>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='info-container'>";
                            echo "<div class='info-item'>";
                            echo "<div class='info-label'>Estudiante</div>";
                            echo "<div class='resaltado'>" . htmlspecialchars($row['nombre_estudiante']) . "</div>";
                            echo "</div>";
                            
                            echo "<div class='info-item'>";
                            echo "<div class='info-label'>DNI</div>";
                            echo "<div class='resaltado'>" . htmlspecialchars($row['DNI']) . "</div>";
                            echo "</div>";
                            
                            echo "<div class='info-item'>";
                            echo "<div class='info-label'>Curso</div>";
                            echo "<div class='resaltado'>" . htmlspecialchars($row['nombre_curso']) . "</div>";
                            echo "</div>";
                            
                            echo "</div>";
                            
                            // Añadir mensaje de autenticidad
                            echo "<p class='autenticidad'>Este certificado ha sido verificado y es auténtico.</p>";
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
        <p class="footer-msg">Si tienes alguna duda, no dudes en contactarnos.</p>
    </div>
</body>
</html>