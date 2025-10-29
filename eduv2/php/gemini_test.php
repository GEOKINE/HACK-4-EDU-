<?php
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- Configuración básica ---
$api_key = "AIzaSyDFUEx5AUMqmGrFdET2X-Ey-WUuYJcTrzE";  // Pon aquí tu API Key
$model = "gemini-2.5-flash"; // Modelo actual de Gemini
$url = "https://generativelanguage.googleapis.com/v1beta/models/$model:generateContent?key=$api_key";

// --- Prompt detallado ---
$tema = "Sumas y restas";
$subtemas = "Tipos de sumas y restas";
$duracion = 3; // minutos

$data = [
    "contents" => [[
        "parts" => [[
            "text" => "Genera un guion educativo, didáctico y motivador sobre el tema '$tema', incluyendo los subtemas '$subtemas'. Debe durar aproximadamente $duracion minutos y ser apto para lectura en un mini-podcast. Incluye ejemplos claros y explicaciones paso a paso."
        ]]
    ]]
];

// --- Llamada a la API ---
$options = [
    "http" => [
        "method" => "POST",
        "header" => "Content-Type: application/json",
        "content" => json_encode($data),
        "ignore_errors" => true
    ]
];

$response = @file_get_contents($url, false, stream_context_create($options));

if ($response === false) {
    echo json_encode(["error" => "No se pudo conectar con la API."]);
    exit;
}

$json = json_decode($response, true);

// --- Validación de respuesta ---
if (isset($json['error'])) {
    echo json_encode(["error" => $json['error']['message']]);
    exit;
}

// --- Extraer texto generado ---
$guion = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;

if (!$guion) {
    $guion = "El modelo no generó texto. Revisa la API Key o el modelo usado.";
}

echo json_encode(["guion" => $guion]);

"Genera un texto de tutoria personalizada para estudiantes, didáctico y motivador sobre el tema '$tema', incluyendo los subtemas '$subtemas'. Debes enfatizar en realizarlo lo mas entendible posible como si estuvieras explicando este complejo tema a un niño de primaria evitando caer en tecnisismos tan complejos o si es necesario exlicar los tecnisismos  con un tono amigable y cordial. Incluye ejemplos claros y explicaciones paso a paso con detalle extremos."