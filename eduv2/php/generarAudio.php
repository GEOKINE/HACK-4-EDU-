<?php
header('Content-Type: application/json; charset=utf-8');

// 1️⃣ Validar texto
if (!isset($_POST['texto']) || empty(trim($_POST['texto']))) {
    echo json_encode(['error' => 'No se proporcionó texto']);
    exit;
}

$texto = trim($_POST['texto']);

// 2️⃣ Datos de tu API de OpenAI TTS
$apiKey = 'TU_API_KEY_OPENAI'; // <- reemplaza con tu key
$filename = 'audio_' . time() . '.mp3';
$savePath = __DIR__ . '/../audios/' . $filename;

// 3️⃣ Llamada a la API OpenAI TTS
$ch = curl_init('https://api.openai.com/v1/audio/speech');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $apiKey",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "model" => "gpt-4o-mini-tts",
    "voice" => "alloy",
    "input" => $texto,
    "format" => "mp3"
]));

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$result) {
    echo json_encode(['error' => 'No se pudo generar el audio', 'status' => $httpCode]);
    exit;
}

// 4️⃣ Guardar MP3 en el servidor
file_put_contents($savePath, $result);

// 5️⃣ Devolver URL del MP3
echo json_encode(['url' => "audios/$filename"]);
exit;
