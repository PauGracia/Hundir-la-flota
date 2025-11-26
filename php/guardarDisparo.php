<?php
session_start();
require_once("conexion.php");

// Habilitar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["error" => "No se recibieron datos"]);
    exit;
}

$idPartida = $data["idPartida"] ?? null;
$propietario = $data["propietario"] ?? null;
$x = $data["x"] ?? null;
$y = $data["y"] ?? null;
$resultado = $data["resultado"] ?? null;

// Verificar que todos los datos necesarios están presentes
if (!$idPartida || !$propietario || !$x || !$y || !$resultado) {
    echo json_encode(["error" => "Faltan datos requeridos"]);
    exit;
}

// Verificar que la partida existe y pertenece al usuario
$stmt_check = $conexion->prepare("SELECT idPartida FROM partidas WHERE idPartida = ? AND nombreUsuario = ?");
$stmt_check->bind_param("is", $idPartida, $_SESSION["usuario"]);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Partida no encontrada o no tienes permisos"]);
    exit;
}

// Insertar el disparo
$stmt = $conexion->prepare("INSERT INTO disparos (idPartida, propietario, posX, posY, resultado) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issis", $idPartida, $propietario, $x, $y, $resultado);

if ($stmt->execute()) {
    echo json_encode(["ok" => true, "id" => $stmt->insert_id]);
} else {
    echo json_encode(["error" => "Error al guardar disparo: " . $stmt->error]);
}
?>