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
    // Método para dibujar un rectángulo con bordes redondeados
    public function RoundedRect($x, $y, $w, $h, $r, $style = '') {
        $k = $this->k;
        $hp = $this->h;
        if ($style == 'F') {
            $op = 'f';
        } elseif ($style == 'FD' || $style == 'DF') {
            $op = 'B';
        } else {
            $op = 'S';
        }
        $MyArc = 4 / 3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));
        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));
        $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);
        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);
        $xc = $x + $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    private function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c', $x1 * $this->k, ($h - $y1) * $this->k, $x2 * $this->k, ($h - $y2) * $this->k, $x3 * $this->k, ($h - $y3) * $this->k));
    }

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

    // Consulta parametrizada con más información
    $sql = "SELECT e.nombre,
                   e.DNI,
                   e.correo,
                   e.telefono,
                   c.nombre_curso,
                   c.fecha,
                   c.duracion_horas,
                   i.examen_final as nota_final
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
    $pdf->Image(__DIR__ . '/images1/CertificadoPDF.png', 0, 0, 210, 297);    if ($row) {  
        
        // 2) Nombre del estudiante - Estilo adaptado de nombre-persona
        $pdf->SetFont('Arial','I',28); // Volvemos al tamaño original
        $pdf->SetTextColor(0,0,0);
        $pdf->SetXY(30, 44);
        // Ajustamos el espaciado de línea para simular line-height: 0.5
        $pdf->Cell(150, 8, mb_convert_encoding($row['nombre'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // 3) Nombre del curso - Estilo adaptado de nombre-curso
        $pdf->SetFont('Arial','B',24); // Volvemos al tamaño original
        // Crear efecto de borde verde simulando text-shadow
        $pdf->SetXY(30, 68);
        // Finalmente el texto en blanco encima
        $pdf->SetXY(30, 68);
        $pdf->SetTextColor(255,255,255);
        $pdf->Cell(150, 10, mb_convert_encoding(strtoupper($row['nombre_curso']), 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // 4) DNI y datos del estudiante (subido 5 unidades más)
        $pdf->SetXY(30, 170);
        $pdf->SetDrawColor(128, 0, 128);
        $pdf->SetFillColor(252, 228, 252);
        $pdf->RoundedRect(55, 167, 100, 12, 3.5, 'FD');
        
        $pdf->SetFont('Times','B',12);
        $pdf->SetTextColor(75, 0, 130);
        $pdf->Cell(150, 6, mb_convert_encoding("DNI: " . $row['DNI'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        
        // Restaurar colores por defecto para el siguiente elemento
        $pdf->SetDrawColor(0);
        $pdf->SetFillColor(255);
        $pdf->SetTextColor(0);

        // 5) Información de duración, fecha y nota
        $pdf->SetFont('Arial','I',12);
        $pdf->SetXY(30, 89);
        $texto_duracion = sprintf("Curso Creado el %s con una duración de %.1f horas pedagógicas.",
            mesEnEspanol($row['fecha']),
            $row['duracion_horas']
        );
        $pdf->Cell(150, 6, mb_convert_encoding($texto_duracion, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
        
        // 6) Nota final (subido 5 unidades más)
        $pdf->SetXY(30, 190);
        $pdf->SetDrawColor(0, 102, 204);
        $pdf->SetFillColor(240, 248, 255);
        $pdf->RoundedRect(55, 187, 100, 12, 3.5, 'FD');
        
        $pdf->SetFont('Times','B',12);
        $pdf->SetTextColor(0, 51, 153);
        $texto_nota = sprintf("Calificación obtenida: %d de nota", $row['nota_final']);
        $pdf->Cell(150, 6, mb_convert_encoding($texto_nota, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // 7) Código de registro (subido 5 unidades más)
        $pdf->SetXY(30, 210);
        $pdf->SetDrawColor(46, 139, 87);
        $pdf->SetFillColor(240, 255, 240);
        $pdf->RoundedRect(55, 207, 100, 12, 3.5, 'FD');
        
        $codigo_registro = "REG-" . $row['DNI'] . "-" . date('Ymd');
        $pdf->SetTextColor(25, 111, 61);
        $pdf->SetFont('Times','B',11);
        $pdf->Cell(150, 6, mb_convert_encoding("Código de registro: " . $codigo_registro, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // Restaurar colores por defecto
        $pdf->SetDrawColor(0);
        $pdf->SetFillColor(255);
        $pdf->SetTextColor(0);

        // 8) Preparar URL para el QR
        $nombre_curso_limpio = limpiarTexto($row['nombre_curso']);
        $qr_data = "http://localhost/PAGINA_WEB/verificacion_certificado.php?curso=" . $nombre_curso_limpio;
        
        // 9) Insertar QR
        if (!$pdf->AddQRColor($qr_data)) {
            error_log("No se pudo agregar el QR con color");
        }

        // 10) Fecha actual en formato español
        date_default_timezone_set('America/Lima');
        $fecha_actual = mesEnEspanol(date('Y-m-d'));
        $pdf->SetFont('Times','I',11);
        $pdf->SetXY(130, 255);
        $pdf->Cell(60, 6, mb_convert_encoding($fecha_actual, 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');

    } else {
        // Si no encuentra datos, muestra un mensaje central
        $pdf->SetFont('Arial','B',20);
        $pdf->SetXY(10, 150);
        $pdf->Cell(190, 10, "No se encontraron datos para DNI: {$DNI}", 0, 1, 'C');
    }

    // Forzar descarga del PDF
    $filename = 'CERTIFICADO MEKADDESH SOLUTION E.I.R.L ' . ($row['nombre'] ?? 'SIN_NOMBRE') . '.pdf';
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
