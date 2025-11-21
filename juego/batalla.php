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
    die("Partida no encontrada");
}

// Obtener datos de la partida desde BD
$stmt = $conexion->prepare("SELECT * FROM partidas WHERE idPartida = ?");
$stmt->bind_param("i", $idPartida);
$stmt->execute();
$partida = $stmt->get_result()->fetch_assoc();

$flotaJugador = json_decode($partida["flotaJugador"], true);
$flotaEnemigo = json_decode($partida["flotaEnemigo"], true);
$oponente = $partida["nombreOponente"];
$usuario = $_SESSION["usuario"];
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

<body class="body-inicio-juego">

<!-- =============================== -->
<!-- PANEL SUPERIOR (NO TOCAR) -->
<!-- =============================== -->
<div class="header-bar">

    <a href="menuJuego.php" class="header-btn">Salir</a>

    <div class="captain-panel">
        <img src="../assets/img/imagenes/capitan.png" class="captain-img" />
        <div class="attaker-con">
            <p class="attacker-text0">Capitán:</p>
            <p class="captain-text">¡Almirante <?php echo htmlspecialchars($usuario); ?>! </p>
        </div>
    </div>

    <div class="attacker-panel">
       <img id="almirante-img" class="attacker-img" />
        <div class="attaker-con">
            <p class="attacker-text0">Contrincante:</p>
            <p id="almirante-nombre" class="attacker-text">
  <?php echo htmlspecialchars($oponente); ?>
</p>

        </div>
    </div>

</div>

<!-- =============================== -->
<!-- ZONA DE BATALLA (ESTO ES LO NUEVO) -->
<!-- =============================== -->

<div class="battle-container">

    <!-- TABLERO DEL JUGADOR -->
    <div class="player-board-container">
        <h2 class="board-title">Tu Flota</h2>
        <div id="board-player" class="board-grid-player"></div>
    </div>

    <!-- TABLERO DEL ENEMIGO -->
    <div class="enemy-board-container">
        <h2 class="board-title">Flota Enemiga</h2>
        <div id="board-enemy" class="board-grid-enemy"></div>
    </div>

</div>
 <div id="mensaje" class="mensaje"></div>

    <script src="../assets/js/main.js?v=<?php echo time(); ?>"></script>
<script>

    document.addEventListener("DOMContentLoaded", () => {
/* ==========================
   TABLERO DEL JUGADOR
========================== */
const flotaJugador = <?php echo json_encode($flotaJugador); ?>;

const playerBoard = document.getElementById("board-player");

// Crear matriz
let matrizJugador = Array.from({ length: 10 }, () => Array(10).fill(0));

// Colocar barcos
flotaJugador.forEach(b => {
    // Asegurarse de usar los nombres correctos
    const x = b.xInicio - 1; // si en PHP guardaste 1-10
    const y = b.yInicio - 1;
    const size = b.size; // asegúrate de que sea un número válido
    const orientacion = b.orientacion;

    for (let i = 0; i < size; i++) {
        let nx = x + (orientacion === "horizontal" ? i : 0);
        let ny = y + (orientacion === "vertical" ? i : 0);
        if (nx < 0 || nx > 9 || ny < 0 || ny > 9) continue; // evita índices fuera de rango
        matrizJugador[ny][nx] = 1;
    }
});


// Pintar tablero
for (let y = 0; y < 10; y++) {
    for (let x = 0; x < 10; x++) {
        const cell = document.createElement("div");
        cell.classList.add("cell-player");
        if (matrizJugador[y][x] === 1) {
            cell.classList.add("cell-ship");
        }
        playerBoard.appendChild(cell);
    }
}

/* ==========================
   TABLERO DEL ENEMIGO
========================== */
const enemyBoard = document.getElementById("board-enemy");

for (let i = 0; i < 100; i++) {
    const c = document.createElement("div");
    c.classList.add("cell-enemy");
    enemyBoard.appendChild(c);
}
    });
</script>

</body>
</html>
