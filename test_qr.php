<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!file_exists('phpqrcode/qrlib.php')) {
    die("Error: No se encuentra la librería PHP QR Code");
}

require('phpqrcode/qrlib.php');

// Intentar generar un QR
$tempfile = 'test.png';
QRcode::png('Test QR Code', $tempfile);

if (file_exists($tempfile)) {
    echo '<h1>QR generado correctamente</h1>';
    echo '<img src="' . $tempfile . '" />';
    unlink($tempfile);
} else {
    echo 'Error al generar QR';
}

echo '<a href="http://localhost/PROYECTO-%20PAGINA%20WEB/AULA_VIRTUAL/test_qr.php">Volver</a>';
?>