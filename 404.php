<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 404 - Página no encontrada</title>
    <link rel="stylesheet" href="404.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="stars" id="stars"></div>
    <div class="error-container">
        <div class="astronaut">
            <i class="fas fa-user-astronaut"></i>
        </div>
        <div class="error-number">404</div>
        <div class="error-text">¡Houston, tenemos un problema!</div>
        <div class="error-subtitle">La página que buscas se ha perdido en el espacio profundo</div>
        <a href="index.html" class="home-button">
            <i class="fas fa-rocket"></i> Volver a la Tierra
        </a>
    </div>

    <script>
    // Crear estrellas
    function createStars() {
        const stars = document.getElementById('stars');
        for(let i = 0; i < 50; i++) {
            const star = document.createElement('div');
            star.className = 'star';
            star.style.left = Math.random() * 100 + '%';
            star.style.top = Math.random() * 100 + '%';
            star.style.setProperty('--duration', Math.random() * 3 + 1 + 's');
            stars.appendChild(star);
        }
    }

    // Crear meteoros
    function createMeteor() {
        const meteor = document.createElement('div');
        meteor.className = 'meteor';
        meteor.style.top = Math.random() * 90 + '%';
        meteor.style.left = '100%';
        document.body.appendChild(meteor);
        
        setTimeout(() => {
            meteor.remove();
        }, 2000);
    }

    // Inicializar efectos
    createStars();
    setInterval(createMeteor, 3000);
    </script>
</body>
</html>