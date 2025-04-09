<?php
// Activar reporte de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('error_log', 'C:\xampp\php\logs\php_error.log');

// Verificar si el archivo fpdf.php existe
$fpdf_path = __DIR__ . '/fpdf/fpdf.php';
if (!file_exists($fpdf_path)) {
    die("Error: No se encuentra el archivo FPDF en: " . $fpdf_path);
}

// Intentar incluir FPDF
try {
    require($fpdf_path);
    
    // Crear un PDF de prueba
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, '¡FPDF funciona correctamente!');
    
    // Enviar el PDF al navegador
    header('Content-Type: application/pdf');
    $pdf->Output('D', 'prueba.pdf');
    
} catch (Exception $e) {
    die("Error al cargar FPDF: " . $e->getMessage());
}
?>