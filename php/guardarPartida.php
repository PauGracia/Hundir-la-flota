<?php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION["usuario"])) {
    echo json_encode(["error" => "No hay usuario en sesión"]);
    exit;
}

require_once("conexion.php");

// Recibir datos del POST
$data = json_decode(file_get_contents("php://input"), true);

$jugador = $data["jugador"] ?? null;
$oponente = $data["oponente"] ?? null;
$flotaJugador = $data["flotaJugador"] ?? null;

if (!$jugador || !$oponente) {
    echo json_encode(["error" => "Falta el nombre del jugador u oponente"]);
    exit;
}

if (!$flotaJugador || count($flotaJugador) < 6) {
    echo json_encode(["error" => "La flota del jugador está incompleta"]);
    exit;
}

// Generar flota enemiga aleatoria
include("generarFlotaEnemiga.php");
$flotaEnemigo = generarFlotaEnemiga();

// Convertir flotas a JSON para guardar en la tabla partidas
$flotaJugadorJson = json_encode($flotaJugador);
$flotaEnemigoJson = json_encode($flotaEnemigo);

// Crear partida
$stmt = $conexion->prepare("
    INSERT INTO partidas 
    (nombreUsuario, nombreOponente, flotaJugador, flotaEnemigo, idAlmirante) 
    VALUES (?, ?, ?, ?, NULL)
");
$stmt->bind_param("ssss", $jugador, $oponente, $flotaJugadorJson, $flotaEnemigoJson);
$stmt->execute();

$idPartida = $conexion->insert_id;

// Preparar statement para insertar barcos
$stmtBarco = $conexion->prepare("
    INSERT INTO barcos 
    (idPartida, propietario, tipoBarco, size, ancho, alto, orientacion, xInicio, yInicio)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

// Función para normalizar tipos de barco según enum
function normalizarTipoBarco($tipo) {
    $map = [
        "portaviones" => "portaaviones",
        "acorazado" => "acorazado",
        "destructor" => "destructor",
        "fragata" => "fragata",
        "corbeta1" => "corbeta",
        "corbeta2" => "corbeta"
    ];

    return $map[$tipo] ?? null;
}

// Insertar barcos del jugador
foreach ($flotaJugador as $barco) {
    $propietario = "jugador";
    $tipoBarco = normalizarTipoBarco($barco["tipo"]);

    if (!$tipoBarco) {
        echo json_encode(["error" => "Tipo de barco inválido: {$barco['tipo']}"]);
        exit;
    }

    $stmtBarco->bind_param(
        "issiiisii",
        $idPartida,
        $propietario,
        $tipoBarco,
        $barco["size"],
        $barco["ancho"],
        $barco["alto"],
        $barco["orientacion"],
        $barco["xInicio"],
        $barco["yInicio"]
    );
    $stmtBarco->execute();
}

// Insertar barcos del enemigo
foreach ($flotaEnemigo as $barco) {
    $propietario = "enemigo";
    $tipoBarco = $barco["tipo"]; // ya viene correcto de generarFlotaEnemiga()

    $stmtBarco->bind_param(
        "issiiisii",
        $idPartida,
        $propietario,
        $tipoBarco,
        $barco["size"],
        $barco["ancho"],
        $barco["alto"],
        $barco["orientacion"],
        $barco["xInicio"],
        $barco["yInicio"]
    );
    $stmtBarco->execute();
}

echo json_encode(["ok" => true, "idPartida" => $idPartida]);
