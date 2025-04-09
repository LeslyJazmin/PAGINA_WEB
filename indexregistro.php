<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEKADDESH SOLUTION E.R.I.L</title>
    <link rel="icon" href="images/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="estiloregistro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="hero">
            <img src="images/años.jpeg" alt="MEKADDESH SOLUTION" class="hero-image">
        </div>
        <section class="form-section">
    <div class="form-content">
        <h2>¡¡REGÍSTRATE AHORA!!</h2>
        <!-- Formulario que enviará los datos a WhatsApp -->
        <form id="miFormulario" onsubmit="enviarDatosWhatsApp(event)">
            <label for="nombreyapellidos">Nombre y Apellidos:</label>
            <input type="text" id="nombreyapellidos" name="nombre_y_apellidos" required>

            <label for="pais">País:</label>
            <select id="pais" name="pais" required>
                <option value="Perú">Perú</option>
                <!-- Agrega más opciones según sea necesario -->
            </select>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="telefono">Teléfono:</label>
            <input type="tel" id="telefono" name="telefono" required>

            <label for="quequieresestudiar">¿Qué quieres estudiar?</label>
            <select id="quequieresestudiar" name="quequieresestudiar" required>
                <?php include 'get_cursos.php'; ?>
            </select>

            <br>
            <button type="submit">Enviar</button>
        </form>

        <div id="mensajeExito" class="mensaje-exito" style="display:none;">
            ¡Los datos se han enviado con éxito!
        </div>
    </div>
</section>

<script>
    function enviarDatosWhatsApp(event) {
        event.preventDefault();
        console.log('Formulario enviado'); // Depuración

        // Obtener los valores de los campos del formulario
        var nombre = document.getElementById('nombreyapellidos').value;
        var pais = document.getElementById('pais').value;
        var email = document.getElementById('email').value;
        var telefono = document.getElementById('telefono').value;
        var queEstudiar = document.getElementById('quequieresestudiar').value;

        // Crear el mensaje para enviar a WhatsApp
        var mensaje = `Hola, mi nombre es ${nombre}, soy de ${pais}. Mi correo es ${email}, mi teléfono es ${telefono}. Quiero estudiar: ${queEstudiar}.`;

        // Reemplazar espacios en blanco y caracteres especiales para formar el URL
        var url = `https://wa.me/51908874797?text=${encodeURIComponent(mensaje)}`;

        console.log('URL generada:', url); // Depuración

        // Redirigir a la URL de WhatsApp
        window.location.href = url;
        
        // Mostrar mensaje de éxito (opcional)
        document.getElementById('mensajeExito').style.display = 'block';
    }
</script>

                <!-- Script JavaScript para mostrar el mensaje de éxito -->
                <script src="https://cdn.jsdelivr.net/npm/@tsparticles/confetti@3.0.3/tsparticles.confetti.bundle.min.js"></script>

                <script>
                    const end = Date.now() + 2 * 1000;

                    const colors = ["#232A40", "#32508C", "#2C4373", "#B6CAF2", "#F2F2F2", "#FABD00"];

                    const colors2 = ["#FABD00"];

                    (function frame() {
                        confetti({
                            particleCount: 2,
                            angle: 60,
                            spread: 55,
                            origin: {
                                x: 0
                            },
                            colors: colors,
                        });

                        confetti({
                            particleCount: 2,
                            angle: 120,
                            spread: 55,
                            origin: {
                                x: 1
                            },
                            colors: colors,
                        });

                        if (Date.now() < end) {
                            requestAnimationFrame(frame);
                        }
                    })();
                </script>
            </div>
        </section>
    </div>
</body>

</html>
