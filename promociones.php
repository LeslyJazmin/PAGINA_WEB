<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEKADDESH SOLUTION E.R.I.L</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="promocion.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    document.body.style.zoom = "75%";
  });
</script>
<style>
        body {
            zoom: 90%; /* Aplica un zoom del 75% a todo el contenido */
        }
    </style></body>
<a class="whatsapp-float" href="https://wa.me/message/D7LD33NF66RGD1" target="_blank">
        <span> ¡Contactanos!</span>
        <img src="contactanos.png" alt="WhatsApp">
    </a>
    <div class="boton-flotante">
    <a href="indexregistro.php" class="boton-inscripcion">¡Inscríbete ahora!</a>
    <img src="inscripcion-abierta.png" alt="inscribetee" style="width: 15px; height: auto;">
</div>
<body>
    <header>
        <h1>Bienvenido a nuestra Academia de Cursos</h1>
        <nav>
            <ul>
                <li><a href="#cursos.php">Cursos</a></li> <!-- Link a cursos.html -->
                <li><a href="#contacto">Contacto</a></li>
            </ul>
        </nav>
    </header>

    <div class="carrusel">
        <div class="carrusel-fotos">
            <img src="promocion1.jpg" alt="Foto 1">
            <img src="promocion2.avif" alt="Foto 2">
            <img src="promocion3.avif" alt="Foto 3">
            <img src="promocion4.avif" alt="Foto 3">
            <!-- Añade más fotos según sea necesario -->
        </div>
        <button class="prev">❮</button>
        <button class="next">❯</button>
    </div>
    <div class="texto-carrusel">
        <p>MIS CURSOS</p>
    </div>
    <div class="cursos-promocion">
        <div class="curso">
            <img src="images/cur1.jpeg" alt="Curso 1">
            <h3>Curso 1</h3>
            <p>Descripción breve del curso 1.</p>
            <p class="precio">Antes: $100 - Ahora: $50</p>
        </div>
        <div class="curso">
            <img src="images/cur2.jpeg" alt="Curso 2">
            <h3>Curso 2</h3>
            <p>Descripción breve del curso 2.</p>
            <p class="precio">Antes: $150 - Ahora: $75</p>
        </div>
        <div class="curso">
            <img src="images/cur3.jpeg" alt="Curso 3">
            <h3>Curso 3</h3>
            <p>Descripción breve del curso 3.</p>
            <p class="precio">Antes: $200 - Ahora: $100</p>
        </div>
        <div class="curso">
            <img src="images/cur4.jpeg" alt="Curso 4">
            <h3>Curso 4</h3>
            <p>Descripción breve del curso 4.</p>
            <p class="precio">Antes: $120 - Ahora: $60</p>
        </div>
        <!-- Añade más cursos según sea necesario -->
    </div>


    <script>
        const prevButton = document.querySelector('.prev');
const nextButton = document.querySelector('.next');
const carruselFotos = document.querySelector('.carrusel-fotos');
const totalFotos = carruselFotos.children.length;
let index = 0;

function showPhoto(index) {
    const offset = -index * 100;
    carruselFotos.style.transform = `translateX(${offset}%)`;
}

prevButton.addEventListener('click', () => {
    index = (index > 0) ? index - 1 : totalFotos - 1;
    showPhoto(index);
});

nextButton.addEventListener('click', () => {
    index = (index < totalFotos - 1) ? index + 1 : 0;
    showPhoto(index);
});

    </script>

    <section id="sobre-nosotros">
        <h2>Sobre Nosotros</h2>
        <p>Somos un equipo de profesionales dedicados a la enseñanza en línea. Nuestra misión es ofrecer cursos de alta calidad que se adapten a tus necesidades.</p>
    </section>
    <footer class="footer">
    <div class="footer-container">
        <div class="contact-us">
            <h3>CONTÁCTANOS</h3>
            <p>📧 <a href="mailto:capacitacionmekaddesh@gmail.com">capacitacionmekaddesh@gmail.com</a></p>
            <p>📞 <a href="https://wa.me/message/D7LD33NF66RGD1">+51 908 874 797</a></p>
        </div>
        <div class="social-media">
            <h3>VISITA REDES SOCIALES</h3>
            <div class="social-icons">
                <div class="social-icon">
                    <a href="https://www.instagram.com/mekaddeshsolution?utm_source=qr&igsh=MXROOTRpZjY4cTVhcw==" target="_blank">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
                <div class="social-icon">
                    <a href="https://wa.me/message/D7LD33NF66RGD1" target="_blank">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
                <div class="social-icon">
                    <a href="https://web.facebook.com/profile.php?id=61564501761493" target="_blank">
                        <i class="fab fa-facebook"></i>
                    </a>
                </div>
                <div class="social-icon">
                    <a href="https://www.tiktok.com/@tupagina" target="_blank">
                        <i class="fab fa-tiktok"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© Todos los derechos reservados. MEKADDESH SOLUTION E.R.I.L.</p>
    </div>
</footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="index.js"></script>
</body>
</html>
