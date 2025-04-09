<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuario";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to ensure proper handling of special characters
$conn->set_charset("utf8mb4");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $nombre_curso = trim($_POST['nombre_curso']);
    $pagina_curso = trim($_POST['pagina_curso']);

    // Handle file upload
    $target_dir = "uploads/";
    
    // Ensure the uploads directory exists
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Generate a unique filename to prevent overwriting
    $file_extension = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
    $imagen_filename = uniqid() . '.' . $file_extension;
    $imagen_path = $target_dir . $imagen_filename;
    
    // Move uploaded file
    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $imagen_path)) {
        // Prepare SQL statement with error checking
    
        $sql = "INSERT INTO cursos (nombre_curso, fecha, imagen, pagina_curso) 
        VALUES (?, ?, ?, ?)";

        // Prepare statement
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        die("Error preparing statement: " . $conn->error);
        }

        // Bind parameters correctamente (cuatro valores)
        $stmt->bind_param("ssss", 
        $nombre_curso, 
        $fecha,        // Asegúrate de incluir la variable $fecha
        $imagen_path, 
        $pagina_curso
        );

        // Execute statement with error checking
        if ($stmt->execute()) {
            $curso_id = $conn->insert_id;
            $curso_dir = "cursos/" . $curso_id;
            
            // Create course directory
            if (!file_exists($curso_dir)) {
                mkdir($curso_dir, 0777, true);
            }

            

            // Redirect to view course page
            header("Location: view-curse.php?id=" . $curso_id);
            exit();
        } else {
            // Log execution error
            error_log("Execute failed: " . $stmt->error);
            echo "Error creating course: " . $stmt->error;
        }

        // Close statement
        $stmt->close();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

$conn->close();
?>