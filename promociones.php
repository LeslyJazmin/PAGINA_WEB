<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEKADDESH SOLUTION E.I.R.L</title>
    <link rel="icon" href="images/favicon2.png" type="image/x-icon">
    <link rel="stylesheet" href="promocion.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        document.body.style.zoom = "85%";
      });
    </script>
</head>
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

<header>
    <h1>Bienvenido a nuestra Academia de Cursos</h1>
</header>
    <header class="referido-banner">
        <h4>🎉 ¡Comparte y gana! El descuento por referido se activa solo si tu referido se matricula con nosotros. ¡Invita, anima y asegura tu beneficio! 💸✨</h4>
    </header>
    <section class="form-section">
    <div class="form-content">
        <!-- FORMULARIO -->
            <div class="formulario">
            <h2>¡¡REGÍSTRATE AHORA!!</h2>

            <form id="miFormulario" onsubmit="enviarDatosWhatsApp(event)">
                
                <!-- PASO 1 -->
                <div class="paso1">
                <label for="nombreyapellidos">Nombre y Apellidos:</label>
                <input type="text" id="nombreyapellidos" name="nombre_y_apellidos" required>

                <label for="pais">País:</label>
                <select id="pais" name="pais" required>
                    <option value="Perú">Perú</option>
                </select>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" required>

                <label for="quequieresestudiar">¿Qué quieres estudiar?</label>
                <select id="quequieresestudiar" name="quequieresestudiar" required>
                    <?php include 'get_cursos.php'; ?>
                </select>

                <button type="button" onclick="mostrarPaso2()">Siguiente ➜</button>
                </div>

                <!-- PASO 2 -->
                <div class="paso2" style="display: none;">
                <h3>Datos del referido (si aplica):</h3>

                <label for="referido_nombre">Nombre del referido:</label>
                <input type="text" id="referido_nombre" name="referido_nombre" placeholder="Nombre completo del referido">

                <label for="referido_dni">DNI del referido:</label>
                <input type="text" id="referido_dni" name="referido_dni" placeholder="DNI del referido">

                <label for="referido_telefono">Celular del referido:</label>
                <input type="tel" id="referido_telefono" name="referido_telefono" placeholder="Celular del referido">

                <label for="referido_email">Email del referido:</label>
                <input type="email" id="referido_email" name="referido_email" placeholder="Correo del referido">

                <button type="submit">Enviar</button>
                </div>
            </form>

            <div id="mensajeExito" class="mensaje-exito" style="display:none;">
                ¡Los datos se han enviado con éxito!
            </div>
            </div>

        <!-- IMAGEN AL LADO DERECHO -->
        <div class="imagen-formulario">
        <img src="images1/referidos.png" alt="referidos">
        </div>
    </div>
    </section>
        <!-- Script - mostrar segunda parte del formulario -->
        <script>
        function mostrarPaso2() {
            // Oculta el paso 1
            document.querySelector('.paso1').style.display = 'none';

            // Muestra el paso 2
            document.querySelector('.paso2').style.display = 'block';
        }

        function enviarDatosWhatsApp(event) {
            event.preventDefault();

            // Obtener los valores de los campos del formulario
            var nombre = document.getElementById('nombreyapellidos').value;
            var pais = document.getElementById('pais').value;
            var email = document.getElementById('email').value;
            var telefono = document.getElementById('telefono').value;
            var queEstudiar = document.getElementById('quequieresestudiar').value;

            // Obtener los datos del referido (pueden estar vacíos)
            var referidoNombre = document.getElementById('referido_nombre').value;
            var referidoDni = document.getElementById('referido_dni').value;
            var referidoTelefono = document.getElementById('referido_telefono').value;
            var referidoEmail = document.getElementById('referido_email').value;

            // Crear el mensaje principal del estudiante
            var mensaje = `Datos del estudiante:
            Nombre: ${nombre}
            País: ${pais}
            Correo: ${email}
            Teléfono: ${telefono}
            Curso de interés: ${queEstudiar}`;

            // Agregar datos del referido solo si alguno fue llenado
            if (referidoNombre || referidoDni || referidoTelefono || referidoEmail) {
            mensaje += `\n\nDatos del referido:\nNombre: ${referidoNombre}\nDNI: ${referidoDni}\nCelular: ${referidoTelefono}\nCorreo: ${referidoEmail}`;
            }

            // Número de destino
            var numero = "51908874797";
        
            // Codificar mensaje para la URL de WhatsApp
            var url = `https://api.whatsapp.com/send?phone=${numero}&text=${encodeURIComponent(mensaje)}`;

            // Redirigir a WhatsApp con el mensaje
            window.location.href = url;
        }
        </script>
            </div>
        </section>
    </div>
</body>

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
        <p>© Todos los derechos reservados. MEKADDESH SOLUTION E.I.R.L.</p>
    </div>
</footer>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="index.js"></script>
</body>
</html>
