<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEKADDESH SOLUTION E.R.I.L</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    document.body.style.zoom = "85%";
  });
</script>
<style>
        body {
            zoom: 90%; /* Aplica un zoom del 75% a todo el contenido */
        }
    </style>
<body>
    <h2>Actualizar Contraseña</h2>
    <form action="cambiar_contrasena.php" method="POST">
    <label for="codigo">ID:</label>
    <input type="text" name="codigo" id="codigo" required><br>

    <input type="submit" value="Cambiar Contraseña">
</form>
<style>
    body { 
    background: url('images/principal.jpeg') no-repeat center center;
    background-size: cover;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 130vh;
}
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff; /* Light Alice Blue */
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 130vh;
        }

        h2 {
            color: #007bff; /* Blue */
        }

        form {
            background-color: #e6f7ff; /* Light Blue */
            border: 1px solid #b3d9ff; /* Light Blue Border */
            border-radius: 8px;
            padding: 20px;
            max-width: 300px;
            width: 400%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #007bff; /* Blue */
        }

        input[type="text"],
        input[type="password"] {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #b3d9ff; /* Light Blue Border */
            border-radius: 4px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #007bff; /* Blue */
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3; /* Darker Blue */
        }
    </style>
</body>
</html>

