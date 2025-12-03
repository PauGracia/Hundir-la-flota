<?php
session_start();

// Si no hay usuario → login
if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit;
}

require_once("../php/conexion.php");

// ID de partida recibido desde main.js
$idPartida = $_GET["id"] ?? null;

if (!$idPartida) {
    die("Partida no encontrada: No se proporcionó ID");
}

// Obtener datos de la partida desde BD
$stmt = $conexion->prepare("SELECT * FROM partidas WHERE idPartida = ?");
$stmt->bind_param("i", $idPartida);
$stmt->execute();
$result = $stmt->get_result();
$partida = $result->fetch_assoc();

// VERIFICAR QUE LA PARTIDA EXISTE
if (!$partida) {
    die("Partida no encontrada con ID: " . $idPartida);
}

// Flotas
$flotaJugador = json_decode($partida["flotaJugador"], true) ?? [];
$flotaEnemigo = json_decode($partida["flotaEnemigo"], true) ?? [];
$oponente = $partida["nombreOponente"] ?? "CPU";

// Datos del usuario (completo desde la BD)
$nombreUsuario = $_SESSION["usuario"];
$stmtUsuario = $conexion->prepare("SELECT * FROM usuario WHERE nombreUsuario = ?");
$stmtUsuario->bind_param("s", $nombreUsuario);
$stmtUsuario->execute();
$resultUsuario = $stmtUsuario->get_result();
$usuario = $resultUsuario->fetch_assoc();

if (!$usuario) {
    die("Usuario no encontrado");
}

// Disparos previos
$stmt2 = $conexion->prepare("SELECT * FROM disparos WHERE idPartida=?");
$stmt2->bind_param("i", $idPartida);
$stmt2->execute();
$disparos = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
$puntos = $partida["puntos"] ?? 0;
$tiempo = $partida["tiempo"] ?? 0;


// Estado guardado del tablero
$estadoTablero = [];
if (isset($partida["estadoTablero"]) && !empty($partida["estadoTablero"])) {
    $estadoTablero = json_decode($partida["estadoTablero"], true);
}

// Debug información
error_log("=== BATALLA CARGADA ===");
error_log("Partida ID: " . $partida['idPartida']);
error_log("Usuario: " . $usuario['nombreUsuario']);
error_log("Estado: " . $partida['estado']);
error_log("Flota Jugador: " . count($flotaJugador) . " barcos");
error_log("Flota Enemigo: " . count($flotaEnemigo) . " barcos");
error_log("Disparos: " . count($disparos) . " registros");

// Pasar el estado a JavaScript

echo "<script>
    window.estadoTablero = " . json_encode($estadoTablero) . ";
    window.puntos = " . json_encode($puntos) . ";
    window.tiempo = " . json_encode($tiempo) . ";
</script>";


?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Batalla Naval</title>

    <link href="https://fonts.googleapis.com/css2?family=Russo+One&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?php echo time(); ?>" />
</head>

<body class="body-batalla-juego">
    <iframe 
    id="audioFrame" 
    src="/Hundir-la-flota/audioPlayerFrame.html" 
    style="display:none">
   </iframe>
   <div class="header-scale-wrapper">
        <div class="header-bar-batalla">
        <a href="menuJuego.php" class="header-btn-batalla">Salir</a>

        <!-- Panel jugador -->
        <div id="player-header-panel" class="player-panel-batalla">
            <img class="attacker-img-batalla" 
                src="../assets/img/perfiles/<?php echo htmlspecialchars($usuario['imagenPerfil'] ?? 'default-avatar.jpg'); ?>" 
                alt="Perfil">
            <div class="attaker-con-batalla">
                <p class="attacker-text0-batalla">Almirante:</p>
                <span class="attacker-text-batalla"><?php echo htmlspecialchars($usuario['nombreUsuario']); ?></span>
            </div>
        </div>

        <!-- Panel atacante -->
        <div id="attacker-header-panel" class="attacker-panel-batalla">
            <img id="almirante-img" class="attacker-img-batalla" />
            <div class="attaker-con-batalla">
            <p class="attacker-text0-batalla">Enemigo:</p>
            <p id="almirante-nombre" class="attacker-text-batalla">
                <?php echo htmlspecialchars($oponente); ?>
            </p>
            </div>
        </div>

        <button id="guardarPartida" class="header-btn-batalla">Guardar partida</button>
        </div>

</div>


<input type="hidden" id="idPartida" value="<?php echo $_GET['id']; ?>">


</div>

<!-- =============================== -->
<!-- ZONA DE BATALLA -->
<!-- =============================== -->
<div class="battle-container">
    <!-- IZQUIERDA: Panel jugador + tablero -->
    <div class="player-section">
        <h2 class="board-title">La Flota</h2>
        <div class="captain-panel-wrapper">
            <!-- Imagen del capitán -->
            <img src="../assets/img/imagenes/capitan.png" class="captain-img-batalla" alt="Capitán" />
            <p class="attacker-text0-batalla">Capitan:</p>
            <!-- Mensajes del juego -->
            <div id="mensajes-juego" class="mensajes-juego"></div>
             <!-- Tablero del jugador -->
        <div id="board-player" class="board-grid-player"></div>
        </div>

       
    </div>

    <!-- DERECHA: Panel enemigo + tablero -->
    <div class="enemy-board-wrapper">
        
        <h2 class="board-title">Flota Enemiga</h2>
        <div class="enemy-board-with-ships" id="enemy-wrapper">
            <div id="enemy-board" class="board-grid-enemy"></div>
            <div id="enemy-ships-layer" class="ships-layer"></div>
            <div id="enemy-overlay" class="overlay-grid"></div>
        </div>
        <div class="score-timer-row">
        <div class="scoreboard" id="score-enemy">Puntos: 0</div>
        <div class="timerboard" id="timer-enemy">Tiempo: 0s</div>
    </div>
    </div>
    <!-- MARCADOR + TEMPORIZADOR (ABAJO) -->
    
</div>




<div id="mensaje" class="mensaje"></div>
<script>
window.JUEGO_DATA = {
    flotaJugador: <?php echo json_encode($flotaJugador); ?>,
    flotaEnemigo: <?php echo json_encode($flotaEnemigo); ?>,
    disparos: <?php echo json_encode($disparos); ?>,
    usuario: <?php echo json_encode($usuario['nombreUsuario']); ?>,
    imagenUsuario: <?php echo json_encode($usuario['imagenPerfil']); ?>,
    puntos: <?php echo json_encode($puntos); ?>,
    tiempo: <?php echo json_encode($tiempo); ?>,
    idPartida: <?php echo json_encode($idPartida); ?>
};
</script>

<script src="../assets/js/main.js?v=<?php echo time(); ?>"></script>





<script>



</script>

</body>
</html>
