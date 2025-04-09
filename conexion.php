<?php
// Datos de conexión a la base de datos
$host = 'localhost';       // Dirección del servidor de base de datos (si es en localhost, usa 'localhost')
$dbname = 'usuario';   // Nombre de la base de datos
$username = 'root';     // Usuario de la base de datos
$password = '';  // Contraseña de la base de datos

try {
    // Establecer la conexión PDO con la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Configurar el modo de error de PDO para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si hay un error de conexión, mostramos un mensaje
    die("Error de conexión: " . $e->getMessage());
}
?>
