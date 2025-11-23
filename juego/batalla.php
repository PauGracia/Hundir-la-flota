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
<!-- ZONA DE BATALLA -->
<!-- =============================== -->

<div class="battle-container">

    <!-- TABLERO DEL JUGADOR -->
    <div class="player-board-container">
        <h2 class="board-title">Tu Flota</h2>
        <div id="board-player" class="board-grid-player"></div>
    </div>

    <!-- TABLERO DEL ENEMIGO -->
    <div class="enemy-board-wrapper">
  <div class="enemy-grid">

    <div></div> <!-- esquina superior izquierda vacía -->
    <div class="enemy-labels-top" id="enemy-labels-top"></div>

    <div class="enemy-labels-left" id="enemy-labels-left"></div>

    <div class="enemy-board-with-ships" id="enemy-wrapper">
      <div id="enemy-board" class="board-grid-enemy"></div>
      <div id="enemy-ships-layer" class="ships-layer"></div>
    </div>

  </div>
</div>


</div>



</div>
 <div id="mensaje" class="mensaje"></div>

    <script src="../assets/js/main.js?v=<?php echo time(); ?>"></script>
<script>

document.addEventListener("DOMContentLoaded", () => {

  // ==========================
  // TABLERO DEL JUGADOR
  // ==========================
  const flotaJugador = <?php echo json_encode($flotaJugador); ?> || [];
  const playerBoard = document.getElementById("board-player");

  // Matriz con tipo de barco o null
  let matrizJugador = Array.from({ length: 10 }, () => Array(10).fill(null));

  // Rellenar matriz con información completa (tipo, id opcional)
  flotaJugador.forEach((b, idx) => {
    const tipo = b.tipo ?? b.tipo ?? ("barco" + idx); // protección si campo distinto
    const startX = (b.xInicio ?? 1) - 1;
    const startY = (b.yInicio ?? 1) - 1;
    const ancho = b.ancho ?? 1;
    const alto = b.alto ?? 1;

    for (let dy = 0; dy < alto; dy++) {
      for (let dx = 0; dx < ancho; dx++) {
        const nx = startX + dx;
        const ny = startY + dy;
        if (nx < 0 || nx > 9 || ny < 0 || ny > 9) continue;
        matrizJugador[ny][nx] = {
          tipo: tipo,
          barcoIndex: idx,
          fragiles: 0 // campo para daño??
        };
      }
    }
  });

  // Crear tablero visual con atributos por celda
  const letters = "ABCDEFGHIJ";
  for (let y = 0; y < 10; y++) {
    for (let x = 0; x < 10; x++) {
      const cell = document.createElement("div");
      cell.classList.add("cell-player");
      // atributos útiles
      cell.dataset.x = x + 1; // 1..10
      cell.dataset.y = y + 1; // 1..10
      cell.dataset.col = letters[x]; // A..J (para compatibilidad)
      cell.dataset.row = y + 1;
      cell.id = `player-${x+1}-${y+1}`;

      // si hay barco, marcar y añadir info
      const info = matrizJugador[y][x];
      if (info) {
        cell.classList.add("cell-ship");
        cell.dataset.occupied = "true";
        cell.dataset.ship = info.tipo;
        cell.dataset.barcoIndex = info.barcoIndex;
        cell.title = `${cell.dataset.col}${cell.dataset.row} — ${info.tipo}`;
        // opcional: mostrar texto corto dentro de la celda (p.e. inicial)
        // cell.innerText = info.tipo.charAt(0).toUpperCase();
      } else {
        cell.title = `${cell.dataset.col}${cell.dataset.row}`;
      }

      playerBoard.appendChild(cell);
    }
  }

  // ==========================
  // TABLERO DEL ENEMIGO
  // ==========================
  const flotaEnemigo = <?php echo json_encode($flotaEnemigo); ?> || [];
  const enemyBoard = document.getElementById("enemy-board");


  let matrizEnemigo = Array.from({ length: 10 }, () => Array(10).fill(null));

  flotaEnemigo.forEach((b, idx) => {
    const tipo = b.tipo ?? ("enemigo" + idx);
    const startX = (b.xInicio ?? 1) - 1;
    const startY = (b.yInicio ?? 1) - 1;
    const ancho = b.ancho ?? 1;
    const alto = b.alto ?? 1;

    for (let dy = 0; dy < alto; dy++) {
      for (let dx = 0; dx < ancho; dx++) {
        const nx = startX + dx;
        const ny = startY + dy;
        if (nx < 0 || nx > 9 || ny < 0 || ny > 9) continue;
        matrizEnemigo[ny][nx] = {
          tipo: tipo,
          barcoIndex: idx
        };
      }
    }
  });

  // Pintar el tablero enemigo (visualmente ocultamos barcos para juego real,

  for (let y = 0; y < 10; y++) {
    for (let x = 0; x < 10; x++) {
      const cell = document.createElement("div");
      cell.classList.add("cell-enemy");
      cell.dataset.x = x + 1;
      cell.dataset.y = y + 1;
      cell.dataset.col = letters[x];
      cell.dataset.row = y + 1;
      cell.id = `enemy-${x+1}-${y+1}`;

      const info = matrizEnemigo[y][x];
      if (info) {
        // para juego real NO mostrar la clase que revela el barco; aquí la dejo comentada
        // cell.classList.add("cell-ship"); // <-- descomenta solo para depuración
        cell.dataset.occupied = "true";
        cell.dataset.ship = info.tipo;
        cell.dataset.barcoIndex = info.barcoIndex;
        cell.title = `${cell.dataset.col}${cell.dataset.row} — ${info.tipo} (enemigo)`;
      } else {
        cell.title = `${cell.dataset.col}${cell.dataset.row}`;
      }

      // Ejemplo: listener para disparar en enemigo
      cell.addEventListener("click", (e) => {
        // leer coordenadas y estado
        const tx = e.currentTarget.dataset.x;
        const ty = e.currentTarget.dataset.y;
        const ocupado = e.currentTarget.dataset.occupied === "true";
        const tipoBarco = e.currentTarget.dataset.ship || null;

        // llamar a la lógica de disparo (ajax / ws). Ejemplo de comportamiento visual mínimo:
        if (e.currentTarget.classList.contains("disparado")) {
          // ya disparada
          return;
        }
        e.currentTarget.classList.add("disparado");
        if (ocupado) {
          e.currentTarget.classList.add("hit");
          mostrarMensaje(`¡Impacto en ${e.currentTarget.dataset.col}${e.currentTarget.dataset.row}! (${tipoBarco})`, false);
        } else {
          e.currentTarget.classList.add("miss");
          mostrarMensaje(`Agua en ${e.currentTarget.dataset.col}${e.currentTarget.dataset.row}`, false);
        }

        // ejemplo: enviar la jugada al servidor con fetch(...) aquí
        // fetch('.../disparo.php', { method:'POST', body: JSON.stringify({x:tx,y:ty,idPartida:...}) })
      });

      enemyBoard.appendChild(cell);
    }
  }


  // Mostrar mensajes (usa la función que ya tengas)
  function mostrarMensaje(text, isError = false) {
    const msg = document.getElementById("mensaje");
    if (!msg) return;
    msg.textContent = text;
    msg.className = isError ? "mensaje error" : "mensaje";
    setTimeout(() => { msg.textContent = ""; msg.className = "mensaje"; }, 3500);
  }

 

function colocarBarcosEnemigos(flota) {
  const layer = document.getElementById("enemy-ships-layer");
  layer.innerHTML = "";

  const cellSize = 40;
  const gap = 3;

  flota.forEach(barco => {
    const x = (barco.xInicio ?? 1) - 1;
    const y = (barco.yInicio ?? 1) - 1;
    const ancho = barco.ancho ?? 1;
    const alto = barco.alto ?? 1;

    const widthPx  = ancho * cellSize + (ancho - 1) * gap;
    const heightPx = alto  * cellSize + (alto - 1) * gap;

    const left = x * (cellSize + gap);
    const top  = y * (cellSize + gap);

    const shipDiv = document.createElement("div");
    shipDiv.classList.add("placed-ship");
    shipDiv.style.width = widthPx + "px";
    shipDiv.style.height = heightPx + "px";
    shipDiv.style.left = left + "px";
    shipDiv.style.top  = top + "px";

    let imgSrc;
    if (ancho > alto) {
      imgSrc = `../assets/img/imagenes/rotated_${barco.tipo}.png`;
    } else {
      imgSrc = `../assets/img/imagenes/${barco.tipo}.png`;
    }

    const img = document.createElement("img");
    img.src = imgSrc;
    shipDiv.appendChild(img);
    layer.appendChild(shipDiv);
  });
}





colocarBarcosEnemigos(flotaEnemigo);




});


// letras A-J
const letras = "ABCDEFGHIJ".split("");
const labelsTopEnemy = document.getElementById("enemy-labels-top");
labelsTopEnemy.innerHTML = "";
letras.forEach(l => {
  const d = document.createElement("div");
  d.textContent = l;
  labelsTopEnemy.appendChild(d);
});

// números 1-10
const labelsLeftEnemy = document.getElementById("enemy-labels-left");
labelsLeftEnemy.innerHTML = "";
for (let i = 1; i <= 10; i++) {
  const d = document.createElement("div");
  d.textContent = i;
  labelsLeftEnemy.appendChild(d);
}



</script>

</body>
</html>
