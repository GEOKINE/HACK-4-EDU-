<?php
// 🔹 Desactivar warnings/notices para no romper el JSON
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

// Verifica que se haya enviado texto
if (!isset($_POST['texto']) || empty(trim($_POST['texto']))) {
    echo json_encode(["error" => "Texto vacío", "status" => 400]);
    exit;
}

$texto = trim($_POST['texto']);

// Carpeta donde se guardará el audio
$carpetaAudios = __DIR__ . "/../audios/";
if (!file_exists($carpetaAudios)) {
    mkdir($carpetaAudios, 0777, true);
}

// Nombre del archivo de audio
$nombreArchivo = "audio_" . time() . ".wav";
$rutaSalida = $carpetaAudios . $nombreArchivo;

// Ruta completa a espeak.exe (modifica según tu instalación de XAMPP/Windows)
$espeakPath = "C:\\espeak\\command_line\\espeak.exe"; // <- AJUSTA ESTA RUTA

// Comando para generar audio
$comando = "\"$espeakPath\" -v es-la -s 140 \"" . escapeshellcmd($texto) . "\" --stdout > " . escapeshellarg($rutaSalida);

// Ejecutar el comando
exec($comando, $salida, $retorno);

if ($retorno === 0 && file_exists($rutaSalida)) {
    // Devuelve la ruta relativa para el audio, lista para el HTML
    $rutaRelativa = "audios/" . $nombreArchivo;
    echo json_encode(["status" => 200, "audio" => $rutaRelativa]);
} else {
    echo json_encode(["error" => "No se pudo generar el audio", "status" => 500]);
}
?>
