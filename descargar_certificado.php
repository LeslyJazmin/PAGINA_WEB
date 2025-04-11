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
            $this->AddPage();
            $this->Image($tempImage, 0, 0, 210, 297);

            // Agregar texto del certificado
            $this->SetFont('Times', 'B', 28);
            $this->SetTextColor(0, 0, 0);
            $this->SetXY(50, 110);
            $this->Cell(130, 10, mb_convert_encoding($data['nombre'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

            $this->SetFont('Times', 'I', 20);
            $this->SetXY(40, 145);
            $this->Cell(130, 10, mb_convert_encoding($data['nombre_curso'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

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

            // Generar QR con mayor nivel de corrección de errores y más pequeño
            QRcode::png($data, $temp_qr, QR_ECLEVEL_H, 8);

            if (file_exists($temp_qr)) {
                // Posición del QR (parte inferior izquierda)
                $x = 15;     // desde la izquierda
                $y = 255;   // desde arriba (más abajo)
                $size = 25; // tamaño del QR más pequeño

                // Agregar QR sin fondo ni contorno
                $this->Image($temp_qr, $x, $y, $size);

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

    function AddQRColor($data) {
        try {
            // Crear QR temporal
            $temp_qr = __DIR__ . '/temp_qr.png';

            // Generar QR con mayor nivel de corrección de errores y más pequeño
            QRcode::png($data, $temp_qr, QR_ECLEVEL_H, 8);

            if (file_exists($temp_qr)) {
                // Cargar la imagen del QR
                $qr_image = imagecreatefrompng($temp_qr);
                
                // Obtener dimensiones
                $width = imagesx($qr_image);
                $height = imagesy($qr_image);
                
                // Crear nueva imagen con fondo transparente
                $qr_color = imagecreatetruecolor($width, $height);
                
                // Hacer el fondo transparente
                imagealphablending($qr_color, false);
                imagesavealpha($qr_color, true);
                $transparent = imagecolorallocatealpha($qr_color, 255, 255, 255, 127);
                imagefilledrectangle($qr_color, 0, 0, $width, $height, $transparent);
                
                // Copiar el QR manteniendo el color negro
                imagecopy($qr_color, $qr_image, 0, 0, 0, 0, $width, $height);
                
                // Guardar la imagen
                imagepng($qr_color, $temp_qr);
                
                // Liberar memoria
                imagedestroy($qr_image);
                imagedestroy($qr_color);
                
                // Posición del QR (parte inferior izquierda)
                $x = 15;     // desde la izquierda
                $y = 255;   // desde arriba (más abajo)
                $size = 25; // tamaño del QR más pequeño

                // Agregar QR sin fondo ni contorno
                $this->Image($temp_qr, $x, $y, $size);

                // Eliminar archivo temporal
                unlink($temp_qr);
                return true;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error al generar QR con color: " . $e->getMessage());
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
    // Verificar DNI e id_curso
    if (!isset($_POST['DNI']) || empty($_POST['DNI'])) {
        throw new Exception("DNI no proporcionado");
    }
    
    if (!isset($_POST['id_curso']) || empty($_POST['id_curso'])) {
        throw new Exception("ID del curso no proporcionado");
    }

    $DNI = $_POST['DNI'];
    $id_curso = $_POST['id_curso'];

    // Conexión a la base de datos
    $conn = new mysqli('localhost', 'root', '', 'usuario');
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");

    // Consulta SQL modificada para filtrar por id_curso y DNI
    $sql = "SELECT 
                e.nombre, 
                CONCAT(c.nombre_curso) as nombre_curso,
                c.fecha
            FROM estudiantes e
            JOIN inscripciones i ON e.DNI = i.DNI
            JOIN cursos c ON c.id_curso = i.id_curso
            WHERE e.DNI = ? AND i.id_curso = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $DNI, $id_curso);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $pdf = new PDF();
        $pdf->AddPage();

        // Agregar imagen de fondo
        $pdf->Image('certificadoimagen.png', 0, 0, 210, 297);

        // Nombre del estudiante
        $pdf->SetFont('Times', 'B', 28);
        $pdf->SetXY(50, 110);
        $pdf->Cell(130, 10, mb_convert_encoding($row['nombre'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // Nombre del curso (sin texto adicional)
        $pdf->SetFont('Times', 'I', 20);
        $pdf->SetXY(40, 145);
        $pdf->Cell(130, 10, mb_convert_encoding($row['nombre_curso'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        // Fecha
        $pdf->SetFont('Times', 'I', 14);
        $pdf->SetXY(120, 220);
        $pdf->Cell(60, 10, mb_convert_encoding($row['fecha'], 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');

        // Datos para el QR - Cambia esta línea
        $qr_data = "http://localhost/PAGINA_WEB/verificacion_certificado.php?dni={$DNI}&curso=" . urlencode($row['nombre_curso']);
        
        // Depuración
        error_log("URL del QR: " . $qr_data);

        // Generar el QR con color personalizado (RGB 53, 76, 133)
        if (!$pdf->AddQRColor($qr_data)) {
            error_log("No se pudo agregar el QR con color");
            // Intentar con el método normal como respaldo
            if (!$pdf->AddQR($qr_data)) {
                error_log("No se pudo agregar el QR");
            }
        }

    } else {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetXY(10, 150);
        $pdf->Cell(190, 10, mb_convert_encoding("No se encontraron resultados para el DNI: " . $DNI, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
    }

    // Limpiar cualquier salida anterior
    ob_end_clean();

    // Configurar headers
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="certificado_' . $DNI . '.pdf"');
    header('Cache-Control: private, no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Salida del PDF
    $pdf->Output('D', 'certificado_' . $DNI . '.pdf');

    // Cerrar conexiones
    $stmt->close();
    $conn->close();
    exit();

} catch (Exception $e) {
    // Si hay algún error, limpiar el buffer
    ob_end_clean();

    // Crear PDF de error
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, mb_convert_encoding('Error al generar el certificado', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, mb_convert_encoding('Por favor, verifique sus datos e intente nuevamente.', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

    // Configurar headers
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="error_certificado.pdf"');

    // Salida del PDF de error
    $pdf->Output('D', 'error_certificado.pdf');
    exit();
}
?>
