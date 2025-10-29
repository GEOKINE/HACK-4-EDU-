<?php
// tts.php

header("Content-Type: application/json");

// Leer datos enviados desde JS
$input = json_decode(file_get_contents("php://input"), true);
$text = $input["texto"] ?? "Hola, soy la voz de ejemplo";
$voice = $input["voz"] ?? "spanish_female_1";

// Tu API key de CAMB.AI
$apiKey = "VOCES 4ed4b355-56ce-4ef9-a822-d49fcb7e9247";

// Datos para enviar a la API
$data = [
    "voice" => $voice,
    "input" => $text,
    "output_format" => "mp3"
];

// Inicializar cURL
$ch = curl_init("https://api.camb.ai/v1/tts");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $apiKey",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>
