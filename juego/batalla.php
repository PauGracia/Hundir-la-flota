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
echo "<script>const estadoTablero = " . json_encode($estadoTablero) . ";</script>";
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
        <div class="scoreboard" id="score-enemy">Puntos: 0</div>
        <h2 class="board-title">Flota Enemiga</h2>
        <div class="enemy-board-with-ships" id="enemy-wrapper">
            <div id="enemy-board" class="board-grid-enemy"></div>
            <div id="enemy-ships-layer" class="ships-layer"></div>
            <div id="enemy-overlay" class="overlay-grid"></div>
        </div>
    </div>
</div>


<div id="mensaje" class="mensaje"></div>
<script src="../assets/js/main.js?v=<?php echo time(); ?>"></script>
<script>
document.addEventListener("DOMContentLoaded", async () => {
    const idPartidaInput = document.getElementById("idPartida");
    const idPartida = idPartidaInput ? idPartidaInput.value : null;
    
    console.log("ID Partida cargado:", idPartida, "Tipo:", typeof idPartida);

    const flotaJugador = <?php echo json_encode($flotaJugador); ?> || [];
    const flotaEnemigo = <?php echo json_encode($flotaEnemigo); ?> || [];
    const disparos = <?php echo json_encode($disparos); ?> || [];
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
            cell.classList.add("cell-player-batalla");
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
            cell.classList.add("cell-enemy-batalla");
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

    const mensajesJuego = [];

    const MAX_MESSAGES = 4;

    function mostrarMensajeCapitan(text){
        // añade mensaje al array
        mensajesJuego.push(text);

        // mantener sólo los últimos MAX_MESSAGES (los más recientes)
        while (mensajesJuego.length > MAX_MESSAGES) {
            mensajesJuego.shift(); // quita el más antiguo
        }

        const contenedor = document.getElementById("mensajes-juego");
        if(!contenedor) return;

        // Queremos mostrar el más reciente arriba: renderizamos el array en orden inverso
        const html = mensajesJuego.slice().map(m => `<p class="msg-line">${m}</p>`).join("");
        contenedor.innerHTML = html;
    }


    function actualizarTurno(turnoActual){
    const playerHeader = document.getElementById("player-header-panel");
    const attackerHeader = document.getElementById("attacker-header-panel");
 
 
    // Limpia los bordes
    playerHeader.classList.remove("turno");
    attackerHeader.classList.remove("turno");
    
    
    if(turnoActual === "jugador"){
        playerHeader.classList.add("turno");
        attackerHeader.classList.remove("turno");
    } else {
        playerHeader.classList.remove("turno");
        attackerHeader.classList.add("turno");
    }
}


let puntos = 0;

function actualizarPuntos(resultado){
    if(resultado === "tocado") puntos += 100;
    else if(resultado === "hundido") puntos += 1000;
    else if(resultado === "agua") puntos -= 10;

    document.getElementById("score-enemy").textContent = "Puntos: " + puntos;
}

function finalizarPartida(ganador){
    if(ganador === usuario) puntos += 5000;

    fetch("../php/guardarProgreso.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
            idPartida: parseInt(document.getElementById("idPartida").value),
            puntos: puntos,
            estadoTablero: estadoTablero
        })
    }).then(r => r.json()).then(data => console.log("Puntos guardados:", data));
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

    // Asegurar que el overlay tenga las dimensiones correctas
    overlay.style.width = 'calc(10 * 40px + 9 * 3px)';
    overlay.style.height = 'calc(10 * 40px + 9 * 3px)';

    for (let y = 1; y <= 10; y++) {
        for (let x = 1; x <= 10; x++) {
            const btn = document.createElement("div");
            btn.classList.add("overlay-cell");
            btn.dataset.x = x;
            btn.dataset.y = y;
            
            // DEBUG: Mostrar coordenadas temporalmente
            btn.title = `Disparar a ${letters[x-1]}${y}`;
            
            // Verificar si ya hay disparo aquí
            const disparoPrevio = disparos.find(d => d.posX === x && d.posY === y && d.propietario === "jugador");
            const celdaReal = document.getElementById(`enemy-${x}-${y}`);
            
            if (disparoPrevio) {
                btn.classList.add("revealed");
                celdaReal.dataset.disparado = "true";
                
                if (disparoPrevio.resultado === "tocado" || disparoPrevio.resultado === "hundido") {
                    btn.innerHTML = "💥";
                    btn.style.color = "yellow";
                    btn.style.fontSize = "24px";
                    btn.style.display = "flex";
                    btn.style.justifyContent = "center";
                    btn.style.alignItems = "center";
                } else {
                    btn.innerHTML = "💧";
                    btn.style.color = "lightblue";
                    btn.style.fontSize = "20px";
                    btn.style.display = "flex";
                    btn.style.justifyContent = "center";
                    btn.style.alignItems = "center";
                }
            }

            btn.addEventListener("click", (e) => {
                console.log(`Click en overlay: ${x},${y}`);
                manejarDisparoJugador(x, y, btn, celdaReal);
            });

            overlay.appendChild(btn);
        }
    }
}

// Función separada para manejar disparos
function manejarDisparoJugador(x, y, overlayCell, celdaReal) {
    if (turno !== "jugador") {
        mostrarMensajeCapitan("¡Espere su turno, almirante!");
        return;
    }

    if (celdaReal.dataset.disparado === "true") {
        console.log("Celda ya disparada, ignorando...");
        return;
    }

    console.log(`Procesando disparo en: ${x},${y}`);
    
    // Marcar inmediatamente como disparado
    celdaReal.dataset.disparado = "true";
    overlayCell.classList.add("revealed");

    const ocupado = celdaReal.dataset.occupied === "true";
    const barco = celdaReal.dataset.ship || null;
    const layer = document.getElementById("enemy-ships-layer");

    if (ocupado) {
        // Impacto
        const fuego = document.createElement("div");
        fuego.classList.add("fire-hit-cell");
        const cellSize = 40, gap = 3;
        fuego.style.left = (x-1)*(cellSize+gap) + "px";
        fuego.style.top = (y-1)*(cellSize+gap) + "px";
        fuego.style.width = cellSize + "px";
        fuego.style.height = cellSize + "px";
        fuego.innerHTML = "💥";
        fuego.style.display = "flex";
        fuego.style.justifyContent = "center";
        fuego.style.alignItems = "center";
        fuego.style.fontSize = "24px";
        layer.appendChild(fuego);

        mostrarMensajeCapitan(`¡Impacto en ${letters[x-1]}${y}!`);
        actualizarPuntos("tocado");

        // Verificar si el barco fue hundido
        const todasCeldas = Array.from(document.querySelectorAll(`.cell-enemy-batalla[data-ship='${barco}']`));
        const hundido = todasCeldas.every(c => c.dataset.disparado === "true");
        
        if (hundido) {
            mostrarMensajeCapitan(`¡Almirante! Hemos hundido el ${barco} enemigo!`);
            actualizarPuntos("hundido");
        }
    } else {
        // Agua
        overlayCell.innerHTML = "";
        overlayCell.style.color = "lightblue";
        overlayCell.style.fontSize = "20px";
        overlayCell.style.display = "flex";
        overlayCell.style.justifyContent = "center";
        overlayCell.style.alignItems = "center";
        
        celdaReal.classList.add("miss");
        mostrarMensajeCapitan(`Agua en ${letters[x-1]}${y}`);
        actualizarPuntos("agua");
    }

    // Guardar disparo
    const datosDisparo = {
        idPartida: parseInt(idPartida),
        propietario: "jugador",
        x: x,
        y: y,
        resultado: ocupado ? "tocado" : "agua"
    };

    fetch("../php/guardarDisparo.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(datosDisparo)
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error("Error al guardar disparo:", data.error);
        }
    })
    .catch(error => {
        console.error("Error en fetch:", error);
    });

    // Cambiar turno
    turno = "enemigo";
    actualizarTurno(turno);
    setTimeout(turnoEnemigo, 1200);
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
        celda.innerHTML="💥";
        mostrarMensajeCapitan(`¡Almirante! Han tocado nuestro ${celda.dataset.ship}!`);
    } else{
        celda.classList.add("miss-player");
        celda.innerHTML="🟦";
        mostrarMensajeCapitan("El enemigo ha fallado.");
    }

    // ===== Guardar disparo enemigo =====
    fetch("../php/guardarDisparo.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
            idPartida: parseInt(document.getElementById("idPartida").value),
            propietario: "enemigo",
            x: x,
            y: y,
            resultado: ocupado ? "tocado" : "agua"
        })
    })
    .then(r => r.json())
    .then(data => console.log("Disparo enemigo guardado:", data))
    .catch(err => console.error("Error guardando disparo enemigo:", err));

    turno="jugador";
    mostrarMensajeCapitan("Es su turno, almirante.");
    actualizarTurno("jugador");

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


    /*function debugOverlay() {
    const overlayCells = document.querySelectorAll('.overlay-cell');
    console.log(`Total celdas overlay: ${overlayCells.length}`);
    
    overlayCells.forEach(cell => {
        cell.addEventListener('mouseenter', () => {
            cell.style.background = 'rgba(255, 0, 0, 0.3)';
        });
        cell.addEventListener('mouseleave', () => {
            if (!cell.classList.contains('revealed')) {
                cell.style.background = 'rgba(0, 0, 0, 0.3)';
            }
        });
    });
}

// Llamar después de crearOverlayDisparos()
setTimeout(debugOverlay, 1000);*/


   // ==========================
// INICIALIZACION
// ==========================
colocarBarcosEnemigos(flotaEnemigo);
crearOverlayDisparos();
// Restaurar disparos del jugador
restaurarDisparosJugador(disparos.filter(d => d.propietario === "jugador"));
sorteoInicial();

    // ==========================
    // BOTÓN GUARDAR PARTIDA - SOLO UNA VEZ
    // ==========================
    document.getElementById("guardarPartida").addEventListener("click", async () => {
        const idPartida = document.getElementById("idPartida").value;

        // Recolectar estado actual de los disparos
        const estadoActual = {
            // Disparos del jugador
            disparosJugador: Array.from(document.querySelectorAll('.cell-enemy[data-disparado="true"]')).map(celda => ({
                x: parseInt(celda.dataset.x),
                y: parseInt(celda.dataset.y),
                resultado: celda.classList.contains('miss') ? 'agua' : 'tocado'
            })),
            // Disparos del enemigo  
            disparosEnemigo: Array.from(document.querySelectorAll('.cell-player.disparado')).map(celda => ({
                x: parseInt(celda.dataset.x),
                y: parseInt(celda.dataset.y),
                resultado: celda.classList.contains('hit-player') ? 'tocado' : 'agua'
            })),
            turnoActual: turno,
            partidaIniciada: partidaIniciada
        };

        try {
            const respuesta = await fetch("../php/guardarProgreso.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    idPartida: parseInt(idPartida),
                    flotaJugador: flotaJugador,
                    flotaEnemigo: flotaEnemigo,
                    estadoTablero: estadoActual  // Guardar el estado actual
                })
            });

            const data = await respuesta.json();
            console.log("Respuesta guardado:", data);

            if (data.ok) {
                mostrarMensaje("Partida guardada correctamente");
            } else {
                mostrarMensaje("Error al guardar: " + (data.error || "Desconocido"), true);
            }
        } catch (error) {
            console.error("Error:", error);
            mostrarMensaje("Error de conexión al guardar", true);
        }
    });

    // ==========================
    // RESTAURAR DISPAROS PREVIOS
    // ==========================
    disparos.forEach(d => {
    const x = d.posX;
    const y = d.posY;

    if(d.propietario === "jugador"){ 
        const celda = document.getElementById(`enemy-${x}-${y}`);
        if (!celda) return;

        celda.dataset.disparado = "true";
        const layer = document.getElementById("enemy-ships-layer");

        if(d.resultado === "tocado" || d.resultado === "hundido"){
            const fuego = document.createElement("div");
            fuego.classList.add("fire-hit-cell");
            const cellSize = 40, gap = 3;
            fuego.style.left = (x-1)*(cellSize+gap) + "px";
            fuego.style.top = (y-1)*(cellSize+gap) + "px";
            fuego.style.width = cellSize + "px";
            fuego.style.height = cellSize + "px";
            fuego.innerHTML = "💥";
            layer.appendChild(fuego);
        } else {
            celda.classList.add("miss");
        }
    }
    else {
        const celda = document.getElementById(`player-${x}-${y}`);
        if (!celda) return;

        celda.classList.add("disparado");

        if(d.resultado === "tocado" || d.resultado === "hundido"){
            celda.classList.add("hit-player");
            celda.innerHTML = "💥";
        } else {
            celda.classList.add("miss-player");
            celda.innerHTML = "🟦";
        }
    }
});

function restaurarDisparosJugador(disparosJugador){
    disparosJugador.forEach(d => {
        const x = d.posX;
        const y = d.posY;

        // Overlay cell
        const overlayCell = document.querySelector(`#enemy-overlay .overlay-cell[data-x="${x}"][data-y="${y}"]`);
        if(!overlayCell) return;

        // Marcar como disparado (transparente)
        overlayCell.classList.add("revealed");

        // Mostrar fuego o agua
        if(d.resultado === "tocado" || d.resultado === "hundido"){
            overlayCell.innerHTML = "💥";
            overlayCell.style.color = "yellow";
            overlayCell.style.fontSize = "24px";
            overlayCell.style.display = "flex";
            overlayCell.style.justifyContent = "center";
            overlayCell.style.alignItems = "center";
        } else {
            overlayCell.innerHTML = "";
            overlayCell.style.color = "white";
            overlayCell.style.fontSize = "16px";
            overlayCell.style.display = "flex";
            overlayCell.style.justifyContent = "center";
            overlayCell.style.alignItems = "center";
        }
    });
}



});

</script>

</body>
</html>
