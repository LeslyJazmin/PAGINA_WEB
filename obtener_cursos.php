<?php
$host = 'localhost';
$dbname = 'usuario';
$username = 'root';
$password = '';

if (isset($_GET['dni'])) {
    $dni = $_GET['dni'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // JOIN entre inscripciones y cursos para obtener el nombre del curso
        $sql = "
            SELECT c.nombre_curso 
            FROM inscripciones i
            INNER JOIN cursos c ON i.id_curso = c.id_curso
            WHERE i.DNI = ?
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$dni]);

        $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($cursos) > 0) {
            echo "<ul>";
            foreach ($cursos as $curso) {
                echo "<li>" . htmlspecialchars($curso['nombre_curso']) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "Este estudiante no está matriculado en ningún curso.";
        }

    } catch (PDOException $e) {
        echo "Error al obtener cursos: " . $e->getMessage();
    }
}
?>
