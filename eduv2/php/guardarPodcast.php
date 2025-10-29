<?php
header('Content-Type: application/json');
require_once "conexion.php";

if (!isset($_FILES['archivo']) || !isset($_POST['autor']) || !isset($_POST['tema']) || !isset($_POST['categoria'])) {
    echo json_encode(["error" => "Faltan datos"]);
    exit;
}

$archivo = file_get_contents($_FILES['archivo']['tmp_name']);
$autor = $_POST['autor'];
$tema = $_POST['tema'];
$categoria = $_POST['categoria'];

// Guardamos en DB (tabla podcasts con columnas id, autor, tema, categoria, audio)
$stmt = $conn->prepare("INSERT INTO podcasts (autor, tema, categoria, audio) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssb", $autor, $tema, $categoria, $archivo);
$stmt->execute();
$stmt->close();

echo json_encode(["mensaje" => "Podcast guardado correctamente"]);
