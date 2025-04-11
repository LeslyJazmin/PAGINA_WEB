<?php
// Al inicio del archivo
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/php/logs/php_error.log');
error_reporting(E_ALL);

// Asegurarse que el path es correcto
require('fpdf/fpdf.php');

// Verificar que PHP QR Code esté disponible
if (!file_exists('phpqrcode/qrlib.php')) {
    error_log("ERROR: No se encuentra la librería PHP QR Code");
    die("Error: Falta la librería PHP QR Code");
}
require('phpqrcode/qrlib.php');

// Habilitar todos los errores para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/php/logs/php_error.log');

// Verificar que FPDF está disponible
if (!class_exists('FPDF')) {
    error_log("ERROR: La clase FPDF no está disponible");
    die("Error: No se pudo cargar la librería FPDF");
}

// Definir la clase PDF personalizada con mejor manejo del QR
class PDF extends FPDF {
    function AddCertificateWithQR($certificateImage, $qr_path, $data) {
        try {
            // Crear una copia temporal de la imagen del certificado
            $tempImage = 'temp_certificate.png';
            copy($certificateImage, $tempImage);

            // Cargar la imagen del certificado
            $certificate = imagecreatefrompng($tempImage);

            // Cargar el QR
            if (file_exists($qr_path)) {
                $qr = imagecreatefrompng($qr_path);

                // Obtener dimensiones
                $qr_width = imagesx($qr);
                $qr_height = imagesy($qr);

                // Posición del QR (esquina inferior izquierda)
                $x = 50;  // 50 píxeles desde la izquierda
                $y = imagesy($certificate) - $qr_height - 50;   // 50 píxeles desde abajo

                // Copiar el QR sobre el certificado
                imagecopy($certificate, $qr, $x, $y, 0, 0, $qr_width, $qr_height);

                // Agregar texto explicativo
                $color = imagecolorallocate($certificate, 0, 0, 0);
                $font = 'arial.ttf';   // Asegúrate de que este archivo exista
                imagettftext($certificate, 12, 0, $x + $qr_width + 10, $y + ($qr_height / 2), $color, $font, 'Escanea para verificar la autenticidad');

                // Guardar la imagen combinada
                imagepng($certificate, $tempImage);

                // Liberar memoria
                imagedestroy($qr);
            }

            // Agregar la imagen combinada al PDF
            $this->Image($tempImage, 0, 0, 210, 297);

            // Agregar texto del certificado
            $this->SetFont('Times', 'B', 28);
            $this->SetTextColor(0, 0, 0);
            $this->SetXY(50, 110);
            $this->Cell(130, 10, utf8_decode($data['nombre']), 0, 1, 'C');

            $this->SetFont('Times', 'I', 20);
            $this->SetXY(40, 145);
            $this->Cell(130, 10, utf8_decode($data['nombre_curso']), 0, 1, 'C');

            // Limpiar
            imagedestroy($certificate);
            unlink($tempImage);

            return true;
        } catch (Exception $e) {
            error_log("Error al combinar imágenes: " . $e->getMessage());
            return false;
        }
    }

    function AddQR($data) {
        try {
            // Crear QR temporal
            $temp_qr = __DIR__ . '/temp_qr.png';
            
            // Añadir log para depuración
            error_log("Generando QR con datos: " . $data);

            // Generar QR con mayor tamaño y nivel de corrección de errores
            QRcode::png($data, $temp_qr, QR_ECLEVEL_H, 10);

            if (file_exists($temp_qr)) {
                // Posición del QR - Parte inferior izquierda
                $x = 15;     // Posición desde la izquierda
                $y = 260;    // Posición vertical ajustada a la parte inferior
                $size = 25;  // Tamaño del QR más pequeño

                // Fondo con color RGB(53, 76, 133)
                $this->SetFillColor(53, 76, 133);
                $this->Rect($x - 8, $y - 8, $size + 16, $size + 16, 'F');
                
                // Borde decorativo
                $this->SetDrawColor(255, 255, 255);
                $this->SetLineWidth(0.5);
                $this->Rect($x - 8, $y - 8, $size + 16, $size + 16);
                
                // Borde interior
                $this->SetDrawColor(200, 200, 200);
                $this->Rect($x - 5, $y - 5, $size + 10, $size + 10);

                // Agregar QR
                $this->Image($temp_qr, $x, $y, $size);
                
                // Añadir texto decorativo
                $this->SetFont('Arial', 'B', 6);
                $this->SetTextColor(255, 255, 255);
                $this->SetXY($x - 8, $y + $size + 10);
                $this->Cell($size + 16, 4, 'VERIFICAR CERTIFICADO', 0, 1, 'C');

                // Eliminar archivo temporal
                unlink($temp_qr);
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error al generar QR: " . $e->getMessage());
            return false;
        }
    }

    function AddQRSimple($qr_path) {
        try {
            // Posiciones fijas para el QR
            $x = 15;     // desde la izquierda
            $y = 240;   // desde arriba
            $size = 25; // tamaño del QR

            error_log("Intentando agregar QR desde: " . $qr_path);

            // Verificar existencia del archivo
            if (!file_exists($qr_path)) {
                error_log("QR no encontrado en: " . $qr_path);
                return false;
            }

            // Agregar QR
            $this->Image($qr_path, $x, $y, $size);

            error_log("QR agregado exitosamente");
            return true;
        } catch (Exception $e) {
            error_log("Error al agregar QR: " . $e->getMessage());
            return false;
        }
    }
}

// Añadir log de inicio
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/php/logs/php_error.log');
error_log("=== Inicio generación de certificado ===");

// Iniciar buffer de salida
ob_start();

try {
    // Verificar DNI
    if (!isset($_POST['DNI']) || empty($_POST['DNI'])) {
        throw new Exception("DNI no proporcionado");
    }

    $DNI = $_POST['DNI'];
    $id_curso = isset($_POST['id_curso']) ? $_POST['id_curso'] : null;

    // Conexión a la base de datos
    $conn = new mysqli('localhost', 'root', '', 'usuario');
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");

    // Consulta SQL modificada para incluir id_curso
    $sql = "SELECT e.nombre, c.nombre_curso, c.fecha
            FROM estudiantes e
            JOIN inscripciones i ON e.DNI = i.DNI
            JOIN cursos c ON c.id_curso = i.id_curso
            WHERE e.DNI = ?";
    
    // Si se proporciona id_curso, añadirlo a la consulta
    if ($id_curso !== null) {
        $sql .= " AND c.id_curso = ?";
    }

    $stmt = $conn->prepare($sql);
    
    // Vincular parámetros según si se proporciona id_curso o no
    if ($id_curso !== null) {
        $stmt->bind_param("si", $DNI, $id_curso);
    } else {
        $stmt->bind_param("s", $DNI);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    // Añadir log para depuración
    error_log("Consulta SQL ejecutada para DNI: " . $DNI);
    error_log("Número de resultados: " . $result->num_rows);

    if ($row = $result->fetch_assoc()) {
        // Limpiar cualquier salida anterior
        ob_end_clean();

        // Crear nuevo PDF
        $pdf = new PDF('P', 'mm', 'A4');
        
        // Configurar márgenes
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false);
        
        // Agregar única página
        $pdf->AddPage();

        // Agregar imagen de fondo
        $pdf->Image('certificadoimagen.png', 0, 0, 210, 297);

        // Nombre del estudiante
        $pdf->SetFont('Times', 'B', 28);
        $pdf->SetXY(50, 110);
        $pdf->Cell(130, 10, mb_convert_encoding($row['nombre'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // Nombre del curso
        $pdf->SetFont('Times', 'I', 20);
        $pdf->SetXY(40, 145);
        $pdf->Cell(130, 10, mb_convert_encoding($row['nombre_curso'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // Datos para el QR
        $curso_encoded = urlencode($row['nombre_curso']);
        $qr_data = "http://localhost/Cloneproyecto/PAGINA_WEB/verificacion_certificado.php?dni={$DNI}&nombre_curso={$curso_encoded}";
        
        // Generar y agregar QR
        $pdf->AddQR($qr_data);

        // Configurar headers
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="certificado_' . $DNI . '.pdf"');
        header('Cache-Control: private, no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // Salida del PDF
        $pdf->Output('D', 'certificado_' . $DNI . '.pdf');
    } else {
        // Limpiar cualquier salida anterior
        ob_end_clean();

        // Crear PDF de error
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'No se encontraron resultados', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, utf8_decode("No se encontró certificado para el DNI: " . $DNI), 0, 1, 'C');

        // Configurar headers
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="no_encontrado.pdf"');
        
        // Salida del PDF
        $pdf->Output('D', 'no_encontrado.pdf');
    }

    // Cerrar conexiones
    $stmt->close();
    $conn->close();
    exit();

} catch (Exception $e) {
    // Limpiar cualquier salida anterior
    ob_end_clean();

    // Crear PDF de error
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Error al generar el certificado', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, utf8_decode('Por favor, inténtelo de nuevo más tarde.'), 0, 1, 'C');

    // Configurar headers para descarga
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="error_certificado.pdf"');
    
    // Salida del PDF
    $pdf->Output('D', 'error_certificado.pdf');
    exit();
}
?>
