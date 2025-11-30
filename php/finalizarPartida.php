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
$ganador = $data["ganador"] ?? null;
$estadoTablero = json_encode($data["estadoTablero"] ?? []);
$puntos = $data["puntos"] ?? 0;
$tiempo = $data["tiempo"] ?? 0;

if (!$idPartida || !$ganador) {
    echo json_encode(["error" => "Datos incompletos"]);
    exit;
}

$usuario = $_SESSION["usuario"];

// Verificar que la partida pertenece al usuario
$stmt_check = $conexion->prepare("SELECT * FROM partidas WHERE idPartida = ? AND nombreUsuario = ?");
$stmt_check->bind_param("is", $idPartida, $usuario);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Partida no encontrada o no pertenece al usuario"]);
    exit;
}

$partida = $result->fetch_assoc();
$idAlmirante = $partida['idAlmirante'];

// ===== Actualizar la partida como finalizada =====
$stmt_update = $conexion->prepare("
    UPDATE partidas 
    SET estado = 'finalizada', 
        ganador = ?, 
        puntos = ?, 
        tiempo = ?, 
        estadoTablero = ? 
    WHERE idPartida = ? AND nombreUsuario = ?
");
$stmt_update->bind_param("siisis", $ganador, $puntos, $tiempo, $estadoTablero, $idPartida, $usuario);
$ok = $stmt_update->execute();

// ===== Incrementar victorias =====
if ($ok) {
    if (strcasecmp(trim($ganador), trim($usuario)) === 0) {
        // Victoria del jugador
        $stmt_victoria = $conexion->prepare("UPDATE usuario SET victorias = victorias + 1 WHERE nombreUsuario = ?");
        $stmt_victoria->bind_param("s", $usuario);
        $stmt_victoria->execute();
    } else {
        // Victoria del almirante enemigo
        if ($idAlmirante) {
            $stmt_victoria = $conexion->prepare("UPDATE almirantes SET victorias = victorias + 1 WHERE id = ?");
            $stmt_victoria->bind_param("i", $idAlmirante);
            $stmt_victoria->execute();
        }
    }
}



if ($ok) {
    echo json_encode(["ok" => true, "message" => "Partida finalizada correctamente"]);
} else {
    echo json_encode(["error" => "Error al finalizar la partida: " . $stmt_update->error]);
}
