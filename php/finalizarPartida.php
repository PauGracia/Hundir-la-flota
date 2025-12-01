<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["usuario"])) {
    echo json_encode(["error" => "No hay usuario en sesión"]);
    exit;
}

require_once("conexion.php");

$data = json_decode(file_get_contents("php://input"), true);

$enemigoId = $data["enemigoId"] ?? null;
$enemigoNombre = $data["enemigoNombre"] ?? null;
$idPartida      = $data["idPartida"] ?? null;
$ganador        = $data["ganador"] ?? null;
$estadoTablero  = json_encode($data["estadoTablero"] ?? []);
$puntos         = $data["puntos"] ?? 0;
$tiempo         = $data["tiempo"] ?? 0;

if (!$idPartida || !$ganador) {
    echo json_encode(["error" => "Datos incompletos"]);
    exit;
}

$usuario = $_SESSION["usuario"];

// ===== Verificar que la partida pertenece al usuario =====
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

// ===== Si no hay idAlmirante, buscarlo por nombre =====
if (!$idAlmirante && $enemigoNombre) {
    $stmt_buscar = $conexion->prepare("SELECT id FROM almirantes WHERE nombreAlmirante = ?");
    $stmt_buscar->bind_param("s", $enemigoNombre);
    $stmt_buscar->execute();
    $resA = $stmt_buscar->get_result();

    if ($resA->num_rows > 0) {
        $filaA = $resA->fetch_assoc();
        $idAlmirante = $filaA["id"];
    }
}

// ===== Actualizar partida como finalizada =====
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
error_log("DEBUG finalizarPartida: ganador=$ganador, usuario=$usuario, enemigoNombre=$enemigoNombre, idAlmirante=$idAlmirante");

// ===== Registrar la victoria =====
if ($ok) {
    if (strcasecmp(trim($ganador), trim($usuario)) === 0) {
        $stmt_victoria = $conexion->prepare("UPDATE usuario SET victorias = victorias + 1 WHERE nombreUsuario = ?");
        $stmt_victoria->bind_param("s", $usuario);
        $stmt_victoria->execute();
   } elseif (!empty($enemigoId) && is_numeric($enemigoId)) {
        $stmt_victoria = $conexion->prepare("UPDATE almirantes SET victorias = victorias + 1 WHERE id = ?");
        $stmt_victoria->bind_param("i", $enemigoId);
        $stmt_victoria->execute();
    }
}
if ($ok) {
    echo json_encode(["ok" => true, "message" => "Partida finalizada correctamente"]);
} else {
    echo json_encode(["error" => "Error al finalizar la partida: " . $stmt_update->error]);
}
