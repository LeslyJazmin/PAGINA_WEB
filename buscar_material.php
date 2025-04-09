<?php
header('Content-Type: application/json');

// Habilitar la visualización de errores (solo para depuración, desactivar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Configuración de la base de datos
    $host = 'localhost';
    $dbname = 'usuario';
    $username = 'root';
    $password = '';

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener el ID del curso
    $id_curso = isset($_GET['id_curso']) ? $_GET['id_curso'] : null;

    if ($id_curso) {
        // Preparar y ejecutar la consulta
        $stmt = $pdo->prepare("SELECT link_curso_material, link_curso_video FROM material WHERE id_curso = ?");
        $stmt->execute([$id_curso]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            echo json_encode([
                'success' => true,
                'material' => $resultado['link_curso_material'],
                'video' => $resultado['link_curso_video']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se encontró material para este ID de curso'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'ID de curso no proporcionado'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
}
?>