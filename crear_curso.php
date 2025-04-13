<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Curso</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #053660;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        form {
            background-color: #00000070;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 700px;
            max-width: 90%;
        }

        h2 {
            text-align: center;
            color: #ffffff;
            margin-top: 0;
            margin-bottom: 30px;
        }

        label {
            font-size: 14px;
            color: #ffffff;
            margin-bottom: 5px;
            display: block;
        }

        input[type="text"], input[type="file"], textarea, select {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border: 2px solid #ffffff;
            border-radius: 5px;
            background-color: #e6f2ff;
            font-size: 14px;
            color: #333;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        input[type="submit"], button {
            background-color: #007acc;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover, button:hover {
            background-color: #005f99;
        }

        .form-buttons {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 20px;
        }

        .form-buttons button {
            flex: 1;
            width: auto;
        }

        .section-header {
            background-color: #007acc;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .section-header h3 {
            margin: 0;
            font-size: 18px;
        }

        small {
            color: #ccc;
            display: block;
            margin-top: 5px;
        }
        input[type="date"] {
            padding: 8px 12px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 675px;
            outline: none;
        }

        input[type="date"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
    </style>
</head>
<body>
    <form action="save-course.php" method="POST" enctype="multipart/form-data">
        <h2>Crear Nuevo Curso</h2>
        <div>
            <label for="nombre_curso">Nombre del Curso:</label>
            <input type="text" id="nombre_curso" name="nombre_curso" required>
        </div>

        <div>
            <label for="fecha">Fecha de creación del Curso:</label>
            <input type="date" id="fecha_creacion" name="fecha" required>
        </div>

        <div>
            <label for="imagen">Imagen del Curso:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*" required>
        </div>

        <div>
            <label for="pagina_curso">Página del Curso (nombre del archivo PHP):</label>
            <input type="text" id="pagina_curso" name="pagina_curso" placeholder="Ej: nuevo_curso.php" required>
            <small>Ingrese el nombre del archivo PHP que mostrará los detalles del curso</small>
        </div>
        
        <div class="form-buttons">
            <button type="submit">Crear Curso</button>
            <button type="button" onclick="cerrarFormulario()">Cerrar</button>
        </div>
    </form>

    <script>
        // Función para cerrar el formulario
        function cerrarFormulario() {
            window.location.href = "auladmi.php";
        }
    </script>
</body>
</html>