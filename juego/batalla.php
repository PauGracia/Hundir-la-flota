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
<!-- PANEL SUPERIOR -->
<!-- =============================== -->
<div class="header-bar">

    <a href="menuJuego.php" class="header-btn">Salir</a>

    <div class="captain-panel">
        <img src="../assets/img/imagenes/capitan.png" class="captain-img" />
        <div class="attaker-con">
            <p class="attacker-text0">Capitán:</p>
            <p class="captain-text">¡Almirante <?php echo htmlspecialchars($usuario); ?>!</p>
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

            <div class="enemy-board-with-ships" id="enemy-wrapper">
                <div id="enemy-board" class="board-grid-enemy"></div>
                <div id="enemy-ships-layer" class="ships-layer"></div>
                <div id="enemy-overlay" class="overlay-grid"></div>
            </div>

        </div>
    </div>

</div>

<div id="mensaje" class="mensaje"></div>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const flotaJugador = <?php echo json_encode($flotaJugador); ?> || [];
    const flotaEnemigo = <?php echo json_encode($flotaEnemigo); ?> || [];
    const playerBoard = document.getElementById("board-player");
    const enemyBoard = document.getElementById("enemy-board");
    const letters = "ABCDEFGHIJ";

    let matrizJugador = Array.from({ length: 10 }, () => Array(10).fill(null));
    let matrizEnemigo = Array.from({ length: 10 }, () => Array(10).fill(null));

    let turno = null; // "jugador" o "enemigo"
    let partidaIniciada = false;

    // ==========================
    // TABLERO DEL JUGADOR
    // ==========================
    flotaJugador.forEach((b, idx) => {
        const tipo = b.tipo ?? ("barco"+idx);
        const startX = (b.xInicio ?? 1) - 1;
        const startY = (b.yInicio ?? 1) - 1;
        const ancho = b.ancho ?? 1;
        const alto = b.alto ?? 1;

        for (let dy=0; dy<alto; dy++) {
            for (let dx=0; dx<ancho; dx++) {
                const nx = startX+dx;
                const ny = startY+dy;
                if(nx<0 || nx>9 || ny<0 || ny>9) continue;
                matrizJugador[ny][nx] = {tipo: tipo, barcoIndex: idx};
            }
        }
    });

    for(let y=0;y<10;y++){
        for(let x=0;x<10;x++){
            const cell = document.createElement("div");
            cell.classList.add("cell-player");
            cell.dataset.x = x+1;
            cell.dataset.y = y+1;
            cell.dataset.col = letters[x];
            cell.dataset.row = y+1;
            cell.id = `player-${x+1}-${y+1}`;

            const info = matrizJugador[y][x];
            if(info){
                cell.classList.add("cell-ship");
                cell.dataset.occupied="true";
                cell.dataset.ship=info.tipo;
                cell.title=`${cell.dataset.col}${cell.dataset.row} — ${info.tipo}`;
            } else {
                cell.title=`${cell.dataset.col}${cell.dataset.row}`;
            }
            playerBoard.appendChild(cell);
        }
    }

    // ==========================
    // TABLERO DEL ENEMIGO
    // ==========================
    flotaEnemigo.forEach((b, idx)=>{
        const tipo = b.tipo ?? ("enemigo"+idx);
        const startX = (b.xInicio ?? 1)-1;
        const startY = (b.yInicio ?? 1)-1;
        const ancho = b.ancho ?? 1;
        const alto = b.alto ?? 1;

        for(let dy=0;dy<alto;dy++){
            for(let dx=0;dx<ancho;dx++){
                const nx = startX+dx;
                const ny = startY+dy;
                if(nx<0 || nx>9 || ny<0 || ny>9) continue;
                matrizEnemigo[ny][nx] = {tipo: tipo, barcoIndex: idx};
            }
        }
    });

    // Pintar tablero enemigo
    for(let y=0;y<10;y++){
        for(let x=0;x<10;x++){
            const cell = document.createElement("div");
            cell.classList.add("cell-enemy");
            cell.dataset.x = x+1;
            cell.dataset.y = y+1;
            cell.dataset.col = letters[x];
            cell.dataset.row = y+1;
            cell.id = `enemy-${x+1}-${y+1}`;

            const info = matrizEnemigo[y][x];
            if(info){
                cell.dataset.occupied="true";
                cell.dataset.ship = info.tipo;
                cell.dataset.barcoIndex = info.barcoIndex;
            }
            enemyBoard.appendChild(cell);
        }
    }

    // ==========================
    // FUNCIONES
    // ==========================
    function mostrarMensaje(text,isError=false){
        const msg = document.getElementById("mensaje");
        msg.textContent = text;
        msg.className = isError?"mensaje error":"mensaje";
        setTimeout(()=>{ msg.textContent=""; msg.className="mensaje"; }, 3500);
    }

    function mostrarMensajeCapitan(text){
        const capText = document.querySelector(".captain-text");
        capText.textContent = text;
    }

    function colocarBarcosEnemigos(flota){
        const layer = document.getElementById("enemy-ships-layer");
        layer.innerHTML="";
        const cellSize = 40;
        const gap=3;

        flota.forEach(barco=>{
            const x = (barco.xInicio ?? 1)-1;
            const y = (barco.yInicio ?? 1)-1;
            const ancho = barco.ancho ?? 1;
            const alto = barco.alto ?? 1;
            const widthPx = ancho*cellSize + (ancho-1)*gap;
            const heightPx = alto*cellSize + (alto-1)*gap;
            const left = x*(cellSize+gap);
            const top = y*(cellSize+gap);

            const shipDiv = document.createElement("div");
            shipDiv.classList.add("placed-ship");
            shipDiv.style.width=widthPx+"px";
            shipDiv.style.height=heightPx+"px";
            shipDiv.style.left=left+"px";
            shipDiv.style.top=top+"px";

            const img = document.createElement("img");
            img.src = ancho>alto?`../assets/img/imagenes/rotated_${barco.tipo}.png`:`../assets/img/imagenes/${barco.tipo}.png`;
            shipDiv.appendChild(img);
            layer.appendChild(shipDiv);
        });
    }

    function crearOverlayDisparos() {
    const overlay = document.getElementById("enemy-overlay");
    overlay.innerHTML = "";

    for(let y=1; y<=10; y++){
        for(let x=1; x<=10; x++){
            const btn = document.createElement("div");
            btn.classList.add("overlay-cell");
            btn.dataset.x = x;
            btn.dataset.y = y;

            btn.addEventListener("click", () => {
    if(turno !== "jugador"){
        mostrarMensajeCapitan("¡Espere su turno, almirante!");
        return;
    }

    const celdaReal = document.getElementById(`enemy-${x}-${y}`);
    if(celdaReal.dataset.disparado === "true") return;

    celdaReal.dataset.disparado = "true"; // registrar disparo
    btn.classList.add("revealed");

    const ocupado = celdaReal.dataset.occupied === "true";
    const barco = celdaReal.dataset.ship || null;

    const layer = document.getElementById("enemy-ships-layer");

    if(ocupado){
        // Crear fuego dentro de la capa ships-layer, sobre la celda tocada
        const fuego = document.createElement("div");
        fuego.classList.add("fire-hit-cell");

        // calcular posición absoluta relativa a layer
        const cellSize = 40;
        const gap = 3;
        fuego.style.left = (x-1)*(cellSize+gap) + "px";
        fuego.style.top  = (y-1)*(cellSize+gap) + "px";
        fuego.style.width = cellSize + "px";
        fuego.style.height = cellSize + "px";
        fuego.innerHTML = "🔥";

        layer.appendChild(fuego);

        mostrarMensajeCapitan(`¡Impacto en ${celdaReal.dataset.col}${celdaReal.dataset.row}!`);

        // Verificar si el barco ha sido hundido
        const todasCeldas = Array.from(document.querySelectorAll(`.cell-enemy[data-ship='${barco}']`));
        const hundido = todasCeldas.every(c => c.dataset.disparado === "true");
        if(hundido){
            mostrarMensajeCapitan(`¡Almirante! Hemos hundido el ${barco} enemigo!`);
        }

    } else {
        celdaReal.classList.add("miss");
        mostrarMensajeCapitan(`Agua en ${celdaReal.dataset.col}${celdaReal.dataset.row}`);
    }

    turno = "enemigo";
    setTimeout(turnoEnemigo, 1200);
});


            overlay.appendChild(btn);
        }
    }
}


    function turnoEnemigo(){
        mostrarMensajeCapitan("El enemigo está disparando…");
        let x,y,celda;
        do{
            x = Math.floor(Math.random()*10)+1;
            y = Math.floor(Math.random()*10)+1;
            celda=document.getElementById(`player-${x}-${y}`);
        } while(celda.classList.contains("disparado"));

        celda.classList.add("disparado");
        const ocupado = celda.dataset.occupied==="true";

        if(ocupado){
            celda.classList.add("hit-player");
            celda.innerHTML="🔥";
            mostrarMensajeCapitan(`¡Almirante! Han tocado nuestro ${celda.dataset.ship}!`);
        } else{
            celda.classList.add("miss-player");
            celda.innerHTML="⭕";
            mostrarMensajeCapitan("El enemigo ha fallado.");
        }

        turno="jugador";
        mostrarMensajeCapitan("Es su turno, almirante.");
    }

    function sorteoInicial(){
        if(partidaIniciada) return;
        const empiezaJugador = Math.random()<0.5;
        turno = empiezaJugador?"jugador":"enemigo";

        if(empiezaJugador){
            mostrarMensajeCapitan("¡Almirante! Hemos ganado el sorteo, usted dispara primero.");
        } else{
            mostrarMensajeCapitan("Almirante… el enemigo ha ganado el sorteo. ¡Prepárese!");
            setTimeout(turnoEnemigo,1500);
        }

        partidaIniciada=true;
    }

    // ==========================
    // INICIALIZACION
    // ==========================
    colocarBarcosEnemigos(flotaEnemigo);
    crearOverlayDisparos();
    sorteoInicial();
});
</script>

</body>
</html>
