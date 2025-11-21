<?php
session_start();

// Si no hay usuario, redirige al login
if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit;
}

$nombreUsuario = $_SESSION["usuario"];

require_once("../php/conexion.php");
$stmt = $conexion->prepare("SELECT * FROM usuario WHERE nombreUsuario = ?");
$stmt->bind_param("s", $nombreUsuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pantalla de organización del juego</title>

    <link
      href="https://fonts.googleapis.com/css2?family=Russo+One&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="../assets/css/styles.css?v=<?php echo time(); ?>"
    />
  </head>

  <body class="body-inicio-juego">
    <!-- Solo para pasar la info a la bbdd -->
    <input type="hidden" id="almirante-nombre-jugador" value="<?php echo htmlspecialchars($usuario['nombreUsuario']); ?>" />

    <!-- Panel superior -->
    <div class="header-bar">
      <!-- BOTÓN VOLVER -->
      <a href="menuJuego.php" class="header-btn">Salir</a>

      <!-- PANEL CAPITÁN -->
      <div class="captain-panel">
        <img src="../assets/img/imagenes/capitan.png" class="captain-img" />
        <div class="attaker-con">
          <p class="attacker-text0">Capitan Paul:</p>
          <p class="captain-text">
            ¡Almirante <?php echo htmlspecialchars($usuario['nombreUsuario']); ?>!  
            Las tropas enemigas se acercan, disponga las naves para la batalla.
          </p>

        </div>
      </div>

      <!-- PANEL ATACANTE -->
      <div class="attacker-panel">
        <img id="almirante-img" class="attacker-img" />

        <div class="attaker-con">
          <p class="attacker-text0">Contrincante:</p>
          <br />
          <p id="almirante-nombre" class="attacker-text"></p>
        </div>
      </div>

      <!-- BOTÓN BATALLA -->
      <button id="btn-batalla" class="header-btn battle">Batalla</button>

    </div>

    <div class="setup-container">
      <!-- PANEL IZQUIERDO -->
      <div class="fleet-panel">
        <h2 class="fleet-title">Flota</h2>

        <div class="fleet-grid">
          <button
            class="ship-btn vertical"
            title="Portaviones"
            data-ship="portaviones"
            data-size="5"
          >
            <!-- IMAGEN VERTICAL para vertical -->
            <img
              src="../assets/img/imagenes/portaaviones.png"
              class="ship-img vertical-img"
            />
            <!-- IMAGEN HORIZONTAL para horizontal -->
            <img
              src="../assets/img/imagenes/rotated_portaaviones.png"
              class="ship-img horizontal-img"
              style="display: none"
            />
            <span class="rotate-btn">⟳</span>
          </button>

          <button class="ship-btn vertical" title="Acorazado" data-ship="acorazado" data-size="5">
            <img
              src="../assets/img/imagenes/acorazado.png"
              class="ship-img vertical-img"
            />
            <img
              src="../assets/img/imagenes/rotated_acorazado.png"
              class="ship-img horizontal-img"
              style="display: none"
            />
            <span class="rotate-btn">⟳</span>
          </button>

          <button
            class="ship-btn vertical"
            title="Destructor"
            data-ship="destructor"
            data-size="4"
          >
            <img
              src="../assets/img/imagenes/destructor.png"
              class="ship-img vertical-img"
            />
            <img
              src="../assets/img/imagenes/rotated_destructor.png"
              class="ship-img horizontal-img"
              style="display: none"
            />
            <span class="rotate-btn">⟳</span>
          </button>

          <button
            class="ship-btn vertical"
            title="Fragata"
            data-ship="fragata"
            data-size="3"
          >
            <img
              src="../assets/img/imagenes/fragata.png"
              class="ship-img vertical-img"
            />
            <img
              src="../assets/img/imagenes/rotated_fragata.png"
              class="ship-img horizontal-img"
              style="display: none"
            />
            <span class="rotate-btn">⟳</span>
          </button>

          <button
            class="ship-btn vertical"
            title="Corbeta"
            data-ship="corbeta1"
            data-size="2"
          >
            <img
              src="../assets/img/imagenes/corbeta.png"
              class="ship-img vertical-img"
            />
            <img
              src="../assets/img/imagenes/rotated_corbeta.png"
              class="ship-img horizontal-img"
              style="display: none"
            />
            <span class="rotate-btn">⟳</span>
          </button>

          <button
            class="ship-btn vertical"
            title="Corbeta"
            data-ship="corbeta2"
            data-size="2"
          >
            <img
              src="../assets/img/imagenes/corbeta.png"
              class="ship-img vertical-img"
            />
            <img
              src="../assets/img/imagenes/rotated_corbeta.png"
              class="ship-img horizontal-img"
              style="display: none"
            />
            <span class="rotate-btn">⟳</span>
          </button>
        </div>

        <div class="help">
          Selecciona un barco → Gíralo si quieres → Haz click en el tablero
        </div>
      </div>

      <!-- TABLERO -->
      <div class="board-container">
        <div class="board-wrapper">
          <div class="labels-top" id="labels-top"></div>

          <div class="board-content">
            <div class="labels-left" id="labels-left"></div>

            <div class="board-with-ships">
              <div id="board" class="board-grid"></div>
              <div id="ships-layer" class="ships-layer"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Sistema de mensajes -->
    <div id="mensaje" class="mensaje"></div>

    <script src="../assets/js/main.js?v=<?php echo time(); ?>"></script>
  </body>
</html>
