<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>  CURSO DE PRIMEROS AUXILIOS</title>
    <link rel="icon" href="images1/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="estiocurso1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    // Aplicar el zoom del 90% para todas las pantallas
    document.body.style.zoom = "90%";
    
    // Detectar si la pantalla es más pequeña que 480px de ancho
    if (window.matchMedia("(max-width: 480px)").matches) {
      document.body.style.zoom = "55%"; // Aplicar 50% de zoom en pantallas pequeñas
    }
  });
  
  // Agregar un listener para cambiar el zoom si el usuario redimensiona la pantalla
  window.addEventListener('resize', function() {
    if (window.matchMedia("(max-width: 480px)").matches) {
      document.body.style.zoom = "55%"; // Aplicar 50% de zoom en pantallas pequeñas
    } else {
      document.body.style.zoom = "90%"; // Restaurar a 90% en pantallas más grandes
    }
  });
</script>
<style>
        body {
            zoom: 90%; /* Aplica un zoom del 75% a todo el contenido */
        }
    </style>
<body>
    <div class="boton-flotante-arriba">
        <a href="index.html" class="boton-ir-inicio">¡Ir al inicio!</a>
    </div>    
    <a class="whatsapp-float" href="https://wa.me/message/D7LD33NF66RGD1" target="_blank">
        <span> ¡Contactanos!</span>
        <img src="images1/contactanos.png" alt="WhatsApp">
    </a>
    <div class="boton-flotante">
    <a href="indexregistro.php" class="boton-inscripcion">¡Inscríbete ahora!</a>
    <img src="images1/inscripcion-abierta.png" alt="inscribetee" style="width: 15px; height: auto;">
</div>
    <header class="main-header">
        <div class="container">
            <h1>CURSO DE PRIMEROS AUXILIOS</h1>
        </div>
    </header>
    <header class="header-pagina">
    <div class="book">
        <button class="arrow left-arrow" onclick="navigatePages(-1)">&#10094;</button>
        <div class="page" id="page1"></div>
        <div class="page" id="page2"></div>
        <div class="page" id="page3"></div>
        <button class="arrow right-arrow" onclick="navigatePages(1)">&#10095;</button>
    </div>
    <div class="pagination">
        <span class="dot" onclick="showPage(1)"></span>
        <span class="dot" onclick="showPage(2)"></span>
        <span class="dot" onclick="showPage(3)"></span>
    </div>
</header>
<style>
    
        #page1 {
            background-image: url('images1/Primeros Auxilios1.png'); /* Ruta de la imagen para la página 1 */
        }

        #page2 {
            background-image: url('images1/Primeros Auxilios2.png'); /* Ruta de la imagen para la página 2 */
        }

        #page3 {
            background-image: url('images1/Primeros Auxilios3.png'); /* Ruta de la imagen para la página 3 */
        }

</style>
</header>
    <header class="header-cursos">
    <div id="cursos" class="container">
        <h1>🎓 BENEFICIOS:</h1><br>
    </div>
    <div class="container cursos-grid">
        <section class="course">
            <a href="#">
                <img src="images1/certificate.png" alt="MEKADDESH SOLUTION">
            </a>
            <div class="course-text">
                <p><strong>CERTIFICACIÓN RECONOCIDA:<br></strong>MEJORA TU PERFIL PROFESIONAL.</p>
            </div>
        </section>  
        
        <section class="course">
            <a href="#">
                <img src="images1/me.png" alt="MEKADDESH SOLUTION">
            </a>
            <div class="course-text">
                <p><strong>MATERIALES<br> </strong> <strong>EXCLUSIVOS: </strong><br>ACCEDE A <br>RECURSOS <br>ACTUALIZADOS.</p>
            </div>
        </section>
    
        <section class="course">
            <a href="#">
                <img src="images1/new.png" alt="MEKADDESH SOLUTION">
            </a>
            <div class="course-text">
                <p><strong>NETWORKING:<br></strong>CONÉCTATE <br>CON OTROS PROFESIONALES<br> Y AMPLÍA TU RED DE CONTACTOS.</p><br>
            </div>
        </section> 
    </div>
</header>
    <header class="header-secondary">
        <div class="container">
            <h2><strong> 🌐 ¡Únete Ahora! </strong> "La prevención y el conocimiento salvan vidas." <br> ¡Inscríbete 
         ahora y comienza a marcar la diferencia!</h2>
        </div>
    </header>
    <footer>
        <div class="container">
            <strong>&copy; 2024 CURSO DE PRIMEROS AUXILIOS. Todos los derechos reservados.</strong>
        </div>
    </footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="script1.js"></script>
</body>
</html>
