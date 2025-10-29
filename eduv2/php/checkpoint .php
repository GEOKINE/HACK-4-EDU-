<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start();

require_once "conexion.php";

// --- Carpeta logs (opcional) ---
$log_file = __DIR__ . "/../logs/debug.log";

// --- Captura datos POST ---
$tema = $_POST['tema'] ?? '';
$subtemas = $_POST['subtemas'] ?? '';
$duracion = $_POST['duracion'] ?? '';

if (empty($tema) || empty($subtemas) || empty($duracion)) {
    echo json_encode(["error" => "Faltan datos en la solicitud."]);
    ob_end_flush();
    exit;
}

// --- Reset de usos_actuales si ha pasado 1 minuto ---
$conn->query("UPDATE api_keys 
              SET usos_actuales = 0, timestamp_reset = NOW() 
              WHERE timestamp_reset <= NOW() - INTERVAL 1 MINUTE");

// --- Selección de API Key disponible ---
$tabla_api_keys = "api_keys";
$stmt = $conn->query("SELECT * FROM $tabla_api_keys WHERE usos_actuales < 5 ORDER BY usos_actuales ASC LIMIT 1");
$key_row = $stmt->fetch_assoc();

if (!$key_row) {
    echo json_encode(["error" => "No hay API Keys disponibles en este momento."]);
    ob_end_flush();
    exit;
}

$api_key = $key_row['api_key'];
$key_id = $key_row['id'];

// --- Llamada a la API de Gemini ---
$model = "gemini-2.5-flash"; // Modelo actual de Gemini
$url = "https://generativelanguage.googleapis.com/v1beta/models/$model:generateContent?key=$api_key";

$data = [
    "contents" => [[
        "parts" => [[
            "text" => "Genera un texto de tutoria personalizada para estudiantes, didáctico y motivador sobre el tema '$tema', incluyendo los subtemas '$subtemas'. Debes enfatizar en realizarlo lo mas entendible posible como si estuvieras explicando este complejo tema a un niño de primaria evitando caer en tecnisismos tan complejos o si es necesario exlicar los tecnisismos  con un tono amigable y cordial. Incluye ejemplos claros y explicaciones paso a paso con detalle extremos usa emojis ppara hacer mas amigable el texto."

        ]]
    ]]
];

$options = [
    "http" => [
        "method" => "POST",
        "header" => "Content-Type: application/json",
        "content" => json_encode($data),
        "ignore_errors" => true
    ]
];

$response = @file_get_contents($url, false, stream_context_create($options));

// --- Logs opcionales ---
/*
if (!file_exists(__DIR__ . "/../logs")) mkdir(__DIR__ . "/../logs", 0777, true);
file_put_contents($log_file, date("Y-m-d H:i:s") . " | Datos recibidos → Tema: $tema | Subtemas: $subtemas | Duración: $duracion\n", FILE_APPEND);
file_put_contents($log_file, date("Y-m-d H:i:s") . " | Usando API Key ID $key_id\n", FILE_APPEND);
file_put_contents($log_file, date("Y-m-d H:i:s") . " | Respuesta cruda API: $response\n\n", FILE_APPEND);
*/

if ($response === false) {
    echo json_encode(["error" => "No se pudo conectar con el modelo Gemini."]);
    ob_end_flush();
    exit;
}

$json = json_decode($response, true);
if (isset($json['error'])) {
    echo json_encode(["error" => $json['error']['message']]);
    ob_end_flush();
    exit;
}

$guion = $json['candidates'][0]['content']['parts'][0]['text'] ?? "No se generó texto.";

// --- Actualizar contadores ---
$conn->query("UPDATE $tabla_api_keys 
              SET usos_actuales = usos_actuales + 1, usos = usos + 1 
              WHERE id = $key_id");

// --- Devolver JSON limpio ---
echo json_encode(["guion" => $guion]);

ob_end_flush();
?>
