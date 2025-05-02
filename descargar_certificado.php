<?php
// Al inicio del archivo: errores para depuración
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/php/logs/php_error.log');
error_reporting(E_ALL);

// Incluir librerías
require('fpdf/fpdf.php');
if (!file_exists('phpqrcode/qrlib.php')) {
    error_log("ERROR: No se encuentra la librería PHP QR Code");
    die("Error: Falta la librería PHP QR Code");
}
require('phpqrcode/qrlib.php');

// Función para limpiar el texto
function limpiarTexto($texto) {
    // Convertir a minúsculas y reemplazar espacios por guiones
    $texto = strtolower($texto);
    // Eliminar acentos
    $texto = str_replace(
        array('á', 'é', 'í', 'ó', 'ú', 'ñ', ' '),
        array('a', 'e', 'i', 'o', 'u', 'n', '-'),
        $texto
    );
    // Eliminar cualquier otro caracter especial
    $texto = preg_replace('/[^a-z0-9\-]/', '', $texto);
    return $texto;
}

// Función para traducir mes a español
function mesEnEspanol($fecha) {
    $meses = array(
        'January' => 'enero',
        'February' => 'febrero',
        'March' => 'marzo',
        'April' => 'abril',
        'May' => 'mayo',
        'June' => 'junio',
        'July' => 'julio',
        'August' => 'agosto',
        'September' => 'septiembre',
        'October' => 'octubre',
        'November' => 'noviembre',
        'December' => 'diciembre'
    );
    
    $fecha_formato = date('d \d\e F \d\e\l Y', strtotime($fecha));
    foreach ($meses as $en => $es) {
        $fecha_formato = str_replace($en, $es, $fecha_formato);
    }
    return $fecha_formato;
}

// Definir la clase PDF personalizada
class PDF extends FPDF {
    // Método para generar e insertar un QR en color
    public function AddQRColor($data) {
        try {
            $temp_qr = __DIR__ . '/temp_qr.png';
            // Generar QR con corrección H y módulo 8
            QRcode::png($data, $temp_qr, QR_ECLEVEL_H, 8);
            if (!file_exists($temp_qr)) {
                return false;
            }
            // Nueva posición: 160 mm desde la izquierda, 240 mm desde arriba, tamaño 30×30 mm, 
            $x    = 167;
            $y    = 260;
            $size = 25;
            $this->Image($temp_qr, $x, $y, $size, $size);
            unlink($temp_qr);
            return true;
        } catch (Exception $e) {
            error_log("Error al generar QR con color: " . $e->getMessage());
            return false;
        }
    }
}

// Iniciar buffer de salida
ob_start();

try {
    // Validar parámetros
    if (empty($_POST['DNI']) || empty($_POST['id_curso'])) {
        throw new Exception("Faltan parámetros DNI o id_curso");
    }
    $DNI      = $_POST['DNI'];
    $id_curso = (int) $_POST['id_curso'];

    // Conexión a la base de datos
    $conn = new mysqli('localhost', 'root', '', 'usuario');
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");

    // Consulta parametrizada
    $sql = "SELECT e.nombre,
                   c.nombre_curso,
                   c.fecha,
                   c.duracion_horas
              FROM estudiantes e
         LEFT JOIN inscripciones i ON e.DNI = i.DNI
         LEFT JOIN cursos c       ON c.id_curso = i.id_curso
             WHERE e.DNI = ? AND i.id_curso = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $DNI, $id_curso);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    // Limpiar buffer antes del PDF
    ob_end_clean();

    // Crear PDF A4 en mm
    $pdf = new PDF('P','mm','A4');
    $pdf->AddPage();

    // 1) Insertar fondo a toda la página
    $pdf->Image(__DIR__ . '/images1/CertificadoPDF.png', 0, 0, 210, 297);

    if ($row) {
        // 2) Nombre del estudiante: ajustado a (30, 40)
        $pdf->SetFont('Arial','B',28);
        $pdf->SetTextColor(0,0,0);
        $pdf->SetXY(30, 44);
        $pdf->Cell(150, 10, mb_convert_encoding($row['nombre'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // 3) Nombre del curso: ajustado a (30, 68)
        $pdf->SetFont('Times','B',24);
        $pdf->SetXY(30, 68);
        $pdf->Cell(150, 10, mb_convert_encoding($row['nombre_curso'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // 4) Información de duración y fecha
        $pdf->SetFont('Times','',12);
        $pdf->SetXY(30, 89);
        $texto_duracion = sprintf("Creado el %s y tiene una duración de %.1f horas pedagógicas.", 
            mesEnEspanol($row['fecha']), 
            $row['duracion_horas']
        );
        $pdf->Cell(150, 10, mb_convert_encoding($texto_duracion, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // 5) Preparar URL para el QR con nombre del curso limpio
        $nombre_curso_limpio = limpiarTexto($row['nombre_curso']);
        $qr_data = "http://localhost/PAGINA_WEB/verificacion_certificado.php?curso=" . $nombre_curso_limpio;

        // 6) Insertar QR en color (ahora a la derecha, arriba de la fecha)
        if (!$pdf->AddQRColor($qr_data)) {
            error_log("No se pudo agregar el QR con color");
        }

        // 7) Fecha: ajustado a (130, 255)
        $pdf->SetFont('Times','I',13);
        $pdf->SetXY(130, 255);
        $pdf->Cell(60, 6, mb_convert_encoding($row['fecha'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');

    } else {
        // Si no encuentra datos, muestra un mensaje central
        $pdf->SetFont('Arial','B',20);
        $pdf->SetXY(10, 150);
        $pdf->Cell(190, 10, "No se encontraron datos para DNI: {$DNI}", 0, 1, 'C');
    }

    // Forzar descarga del PDF
    $filename = 'CERTIFICADO MEKADDESH SOLUTION E.I.R.L_' . ($row['nombre'] ?? 'SIN_NOMBRE') . '.pdf';
    $pdf->Output('D', $filename);
    exit;

} catch (Exception $e) {
    // En caso de error, limpiar buffer y mostrar aviso
    ob_end_clean();
    error_log("Error generando PDF: " . $e->getMessage());
    echo "Ocurrió un error al generar el certificado.";
    exit;
}
?>
