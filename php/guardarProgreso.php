<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["usuario"])) {
    echo json_encode(["error" => "No hay usuario en sesión"]);
    exit;
}

require_once("conexion.php");

$data = json_decode(file_get_contents("php://input"), true);

$idPartida = $data["idPartida"] ?? null;
$flotaJugador = json_encode($data["flotaJugador"] ?? []);
$flotaEnemigo = json_encode($data["flotaEnemigo"] ?? []);
$estadoTablero = json_encode($data["estadoTablero"] ?? []);
$puntos = $data["puntos"] ?? 0;
$tiempo = $data["tiempo"] ?? 0;


if (!$idPartida) {
    echo json_encode(["error" => "ID de partida no recibido"]);
    exit;
}

$usuario = $_SESSION["usuario"];

// Verificar que la partida pertenece al usuario
$stmt_check = $conexion->prepare("SELECT idPartida FROM partidas WHERE idPartida = ? AND nombreUsuario = ?");
$stmt_check->bind_param("is", $idPartida, $usuario);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Partida no encontrada o no pertenece al usuario"]);
    exit;
}

// Actualizar partida con estado completo
$stmt = $conexion->prepare("
    UPDATE partidas 
    SET flotaJugador = ?, 
        flotaEnemigo = ?, 
        estadoTablero = ?, 
        puntos = ?, 
        tiempo = ?, 
        estado = 'batalla'
    WHERE idPartida = ? AND nombreUsuario = ?
");

$stmt->bind_param("sssiiis", 
    $flotaJugador, 
    $flotaEnemigo, 
    $estadoTablero, 
    $puntos, 
    $tiempo, 
    $idPartida, 
    $usuario
);

if ($stmt->execute()) {
    echo json_encode(["ok" => true, "message" => "Partida guardada correctamente"]);
} else {
    echo json_encode(["error" => "Error al guardar: " . $stmt->error]);
}
?>