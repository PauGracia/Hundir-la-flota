<?php
session_start();
require_once("conexion.php");

$idPartida = $_GET['idPartida'] ?? 0;

if(!$idPartida){
    echo json_encode(["error"=>"No se proporcionó ID"]);
    exit;
}

// Validar que el usuario tiene permisos
$stmt = $conexion->prepare("SELECT * FROM partidas WHERE idPartida=? AND nombreUsuario=?");
$stmt->bind_param("is", $idPartida, $_SESSION["usuario"]);
$stmt->execute();
$partida = $stmt->get_result()->fetch_assoc();

if(!$partida){
    echo json_encode(["error"=>"Partida no encontrada o sin permisos"]);
    exit;
}

// Cargar disparos
$stmt2 = $conexion->prepare("SELECT * FROM disparos WHERE idPartida=?");
$stmt2->bind_param("i", $idPartida);
$stmt2->execute();
$disparos = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

// Preparar respuesta
echo json_encode([
    "flotaJugador"   => json_decode($partida["flotaJugador"], true),
    "flotaEnemigo"   => json_decode($partida["flotaEnemigo"], true),
    "estadoTablero"  => isset($partida["estadoTablero"]) ? json_decode($partida["estadoTablero"], true) : [],
    "disparos"       => $disparos,
    "puntos" => $partida["puntos"],
    "tiempo" => $partida["tiempo"]
]);
