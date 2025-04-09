<?php
// buscar_curso.php
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=localhost;dbname=usuario", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $id_curso = isset($_GET['id_curso']) ? $_GET['id_curso'] : null;
    
    if ($id_curso) {
        $stmt = $pdo->prepare("SELECT nombre_curso FROM cursos WHERE id_curso = ?");
        $stmt->execute([$id_curso]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado) {
            echo json_encode([
                'success' => true,
                'nombre_curso' => $resultado['nombre_curso']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Curso no encontrado'
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