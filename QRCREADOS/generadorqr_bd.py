import mysql.connector
import os
import qrcode
from PIL import Image

# Configuración de la base de datos
db_config = {
    'host': '127.0.0.1',
    'user': 'root',
    'password': '',
    'database': 'usuario'
}

try:
    # Conectar a la base de datos
    conn = mysql.connector.connect(**db_config)
    cursor = conn.cursor(dictionary=True)

    # Verificar estructura de tablas
    cursor.execute("SHOW COLUMNS FROM estudiantes;")
    print(cursor.fetchall())
    cursor.execute("SHOW COLUMNS FROM inscripciones;")
    print(cursor.fetchall())
    cursor.execute("SHOW COLUMNS FROM cursos;")
    print(cursor.fetchall())
    cursor.execute("DESCRIBE examenes;")
    print(cursor.fetchall())
    cursor.execute("SHOW CREATE TABLE examenes;")
    print(cursor.fetchall())

    # Modificar la consulta SQL para usar los campos correctos de todas las tablas
    query = """
    SELECT 
        e.DNI,
        e.nombre,
        e.correo,
        e.telefono,
        e.sexo,
        c.nombre_curso,
        c.fecha,
        c.imagen,
        c.pagina_curso,
        i.examen_final,
        i.nota_videotest,
        i.intento1,
        i.intento2,
        i.intento3,
        i.intentose1,
        i.intentose2,
        i.intentose3,
        ex.fecha_inicio_videotest,
        ex.hora_inicio_videotest,
        ex.fecha_fin_videotest,
        ex.hora_fin_videotest,
        ex.fecha_inicio_examen,
        ex.hora_inicio_examen,
        ex.fecha_fin_examen,
        ex.hora_fin_examen,
        m.link_curso_material,
        m.link_curso_video
    FROM estudiantes e
    INNER JOIN inscripciones i ON e.DNI = i.DNI
    INNER JOIN cursos c ON i.id_curso = c.id_curso
    LEFT JOIN examenes ex ON c.id_curso = ex.id_curso
    LEFT JOIN material m ON c.id_curso = m.id_curso
    WHERE i.examen_final >= 12
    ORDER BY e.DNI, c.nombre_curso
    """
    
    cursor.execute(query)
    estudiantes = cursor.fetchall()

    # Modificar la ruta base para que sea absoluta
    base_dir = os.path.join(os.path.dirname(os.path.dirname(__file__)), "estudiantes_certificados")
    print(f"Carpeta base: {base_dir}")

    # Asegurar que la carpeta existe
    os.makedirs(base_dir, exist_ok=True)

    # Ruta del logotipo ajustada
    logo_path = os.path.join(os.path.dirname(__file__), "extras", "logo.png")
    print(f"Ruta del logo: {logo_path}")

    # Modificar el procesamiento de estudiantes
    for estudiante in estudiantes:
        dni = estudiante['DNI']
        nombre = estudiante['nombre']
        curso = estudiante['nombre_curso']
        nota = estudiante['examen_final']
        
        # Crear carpeta para el estudiante con ruta absoluta
        estudiante_dir = os.path.join(base_dir, f"{dni}_{nombre}")
        os.makedirs(estudiante_dir, exist_ok=True)
        print(f"Carpeta del estudiante creada en: {estudiante_dir}")

        # Modificar el HTML para incluir todos los campos
        html_content = f"""
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de {nombre}</title>
    <style>
        body {{
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }}
        .certificado {{
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }}
        .header {{
            text-align: center;
            margin-bottom: 20px;
        }}
        .header img {{
            max-width: 150px;
            margin-bottom: 10px;
        }}
        .info {{
            margin: 15px 0;
            padding: 10px;
            border-left: 3px solid #4CAF50;
        }}
        .nota {{
            color: #4CAF50;
            font-weight: bold;
        }}
        .examen-info {{
            background: #f9f9f9;
            padding: 10px;
            margin-top: 15px;
            border-radius: 5px;
        }}
        .curso-imagen {{
            max-width: 200px;
            margin: 10px auto;
            display: block;
        }}
        .intentos {{
            margin-top: 10px;
            padding: 10px;
            background-color: #f0f8ff;
            border-radius: 5px;
        }}
        .videotest {{
            color: #2196F3;
            font-weight: bold;
        }}
        .materiales {{
            margin-top: 15px;
            padding: 10px;
            background-color: #e8f5e9;
            border-radius: 5px;
        }}
        .videotest-info {{
            background: #e3f2fd;
            padding: 10px;
            margin-top: 15px;
            border-radius: 5px;
        }}
    </style>
</head>
<body>
    <div class="certificado">
        <div class="header">
            <img src="../../extras/logo.png" alt="Logo">
            <h1>Certificado de Aprobación</h1>
        </div>
        <div class="info">
            <h2>{nombre}</h2>
            <p><strong>DNI:</strong> {dni}</p>
            <p><strong>Correo:</strong> {estudiante['correo']}</p>
            <p><strong>Teléfono:</strong> {estudiante['telefono']}</p>
            <p><strong>Sexo:</strong> {estudiante['sexo']}</p>
            
            <div class="curso-info">
                <h3>Información del Curso</h3>
                <p><strong>Curso:</strong> {curso}</p>
                <p><strong>Fecha:</strong> {estudiante['fecha']}</p>
                <img src="../../{estudiante['imagen']}" alt="Imagen del Curso" class="curso-imagen">
            </div>

            <div class="videotest-info">
                <h3>Información VideoTest</h3>
                <p><strong>Nota VideoTest:</strong> {estudiante['nota_videotest']}</p>
                <p><strong>Inicio:</strong> {estudiante['fecha_inicio_videotest']} {estudiante['hora_inicio_videotest']}</p>
                <p><strong>Fin:</strong> {estudiante['fecha_fin_videotest']} {estudiante['hora_fin_videotest']}</p>
            </div>

            <div class="intentos">
                <h3>Intentos</h3>
                <p><strong>VideoTest:</strong></p>
                <ul>
                    <li>Intento 1: {estudiante['intento1']}</li>
                    <li>Intento 2: {estudiante['intento2']}</li>
                    <li>Intento 3: {estudiante['intento3']}</li>
                </ul>
                <p><strong>Examen:</strong></p>
                <ul>
                    <li>Intento 1: {estudiante['intentose1']}</li>
                    <li>Intento 2: {estudiante['intentose2']}</li>
                    <li>Intento 3: {estudiante['intentose3']}</li>
                </ul>
                <p><strong>Nota Final:</strong> <span class="nota">{nota}</span></p>
            </div>

            <div class="examen-info">
                <h3>Información del Examen Final</h3>
                <p><strong>Inicio:</strong> {estudiante['fecha_inicio_examen']} {estudiante['hora_inicio_examen']}</p>
                <p><strong>Fin:</strong> {estudiante['fecha_fin_examen']} {estudiante['hora_fin_examen']}</p>
            </div>

            <div class="materiales">
                <h3>Material del Curso</h3>
                <p><a href="{estudiante['link_curso_material']}" target="_blank">Ver Material del Curso</a></p>
                <p><a href="{estudiante['link_curso_video']}" target="_blank">Ver Video del Curso</a></p>
                <p><a href="{estudiante['pagina_curso']}" target="_blank">Página del Curso</a></p>
            </div>
        </div>
    </div>
</body>
</html>
        """

        # Guardar HTML
        html_file = os.path.join(estudiante_dir, f"certificado_{curso.replace(' ', '_')}.html")
        with open(html_file, 'w', encoding='utf-8') as f:
            f.write(html_content)

        # Modificar URL del QR para usar la ruta correcta del servidor web
        url = f"http://localhost/PROYECTO-%20PAGINA%20WEB/estudiantes_certificados/{dni}_{nombre}/certificado_{curso.replace(' ', '_')}.html"
        
        # Generar QR
        qr = qrcode.QRCode(
            version=1,
            error_correction=qrcode.constants.ERROR_CORRECT_H,
            box_size=10,
            border=4,
        )
        qr.add_data(url)
        qr.make(fit=True)

        # Crear QR con logo
        qr_image = qr.make_image(fill="black", back_color="white").convert('RGB')
        if os.path.exists(logo_path):
            logo = Image.open(logo_path)
            logo_size = (qr_image.size[0] // 4, qr_image.size[1] // 4)
            logo = logo.resize(logo_size, Image.LANCZOS)
            pos = ((qr_image.size[0] - logo.size[0]) // 2, 
                  (qr_image.size[1] - logo.size[1]) // 2)
            qr_image.paste(logo, pos)

        # Guardar QR
        qr_file = os.path.join(estudiante_dir, f"qr_{curso.replace(' ', '_')}.png")
        qr_image.save(qr_file)
        
        print(f"✓ Generado certificado para {nombre} - {curso}")

except mysql.connector.Error as err:
    print(f"Error de base de datos: {err}")
finally:
    if 'conn' in locals() and conn.is_connected():
        cursor.close()
        conn.close()

print("\n¡Proceso completado!")