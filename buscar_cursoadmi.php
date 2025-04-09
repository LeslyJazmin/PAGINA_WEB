<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda de Cursos</title>
</head>
<body>

<h1>Buscar Cursos</h1>

<!-- Formulario de búsqueda -->
<form method="POST" action="buscar_cursoadmi.php">
    <input type="text" name="buscar_cursoadmi" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar curso...">
    <button type="submit">Buscar</button>
</form>

<h2>Resultados de la búsqueda:</h2>

<div class="cursos-grid">
    <?php foreach ($resultado_busqueda as $nombre => $info): ?>
        <div class="curso-container">
            <a href="<?php echo $info['pagina']; ?>">
                <img src="<?php echo $info['imagen']; ?>" alt="<?php echo $nombre; ?>" class="curso-imagen">
            </a>
            <div class="curso-info">
                <p><?php echo $nombre; ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
