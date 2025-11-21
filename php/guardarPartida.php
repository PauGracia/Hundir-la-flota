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

// Función para calcular ancho y alto según tipo y orientación
function calcularDimensiones($tipo, $vertical) {
    switch ($tipo) {
        case "portaviones":
            return $vertical ? [2, 5] : [5, 2];
        case "acorazado":
            return $vertical ? [1, 5] : [5, 1];
        case "destructor":
            return $vertical ? [1, 4] : [4, 1];
        case "fragata":
            return $vertical ? [1, 3] : [3, 1];
        case "corbeta":
            return $vertical ? [1, 2] : [2, 1];
        default:
            return [1,1]; // fallback
    }
}

// Insertar barcos del jugador
foreach ($flotaJugador as $barco) {
    $propietario = "jugador";
    $tipoBarco = normalizarTipoBarco($barco["tipo"]);
    if (!$tipoBarco) {
        echo json_encode(["error" => "Tipo de barco inválido: {$barco['tipo']}"]);
        exit;
    }

    [$ancho, $alto] = calcularDimensiones($tipoBarco, $barco["orientacion"] === "vertical");

    $size = $barco["size"];
    $orientacion = $barco["orientacion"];
    $xInicio = $barco["xInicio"];
    $yInicio = $barco["yInicio"];

    $stmtBarco->bind_param(
        "issiiisii",
        $idPartida,
        $propietario,
        $tipoBarco,
        $size,
        $ancho,
        $alto,
        $orientacion,
        $xInicio,
        $yInicio
    );
    $stmtBarco->execute();
}

// Insertar barcos del enemigo
foreach ($flotaEnemigo as $barco) {
    $propietario = "enemigo";
    $tipoBarco = $barco["tipo"];
    [$ancho, $alto] = calcularDimensiones($tipoBarco, $barco["orientacion"] === "vertical");

    $size = $barco["size"];
    $orientacion = $barco["orientacion"];
    $xInicio = $barco["xInicio"];
    $yInicio = $barco["yInicio"];

    $stmtBarco->bind_param(
        "issiiisii",
        $idPartida,
        $propietario,
        $tipoBarco,
        $size,
        $ancho,
        $alto,
        $orientacion,
        $xInicio,
        $yInicio
    );
    $stmtBarco->execute();
}



echo json_encode(["ok" => true, "idPartida" => $idPartida]);
