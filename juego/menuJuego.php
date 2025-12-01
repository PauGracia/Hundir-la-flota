<?php
session_start();

// Si no hay usuario, redirige al login
if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit;
}

// Array para info del usuario
$nombreUsuario = $_SESSION["usuario"];

require_once("../php/conexion.php");
$stmt = $conexion->prepare("SELECT * FROM usuario WHERE nombreUsuario = ?");
$stmt->bind_param("s", $nombreUsuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Cargar partidas guardadas
$stmt2 = $conexion->prepare("
    SELECT idPartida, fecha, estado
    FROM partidas
    WHERE nombreUsuario = ?
      AND estado <> 'finalizada'
    ORDER BY fecha DESC
");
$stmt2->bind_param("s", $nombreUsuario);
$stmt2->execute();
$partidasGuardadas = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

// Saber si hay partidas guardadas
$hayPartidas = !empty($partidasGuardadas);





?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Menú Principal - Hundir la Flota</title>

    <!-- Fuente Google estilo naval -->
    <link href="https://fonts.googleapis.com/css2?family=Russo+One&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/styles.css?v=<?php echo time(); ?>" />
   
  </head>
  <audio id="sonidoHover" src="../assets/sounds/hover.mp3" preload="auto"></audio>
  <audio id="sonidoClick" src="../assets/sounds/click.mp3" preload="auto"></audio>


  <body class="menu">
    <!-- Para que suene la musica -->
   <!--<iframe 
    id="audioFrame" 
    src="/Hundir-la-flota/audioPlayerFrame.html" 
    style="display:none">
   </iframe>-->

    <!-- BARRA SUPERIOR -->
    <header class="topbar">
      <div class="topbar__fondo"></div>
      <div class="topbar__contenido">
        <h1 class="topbar__titulo">⚓ Hundir la Flota ⚓</h1>
        <a href="perfil.php" class="topbar__perfil">
          <img class="topbar__foto" src="../assets/img/perfiles/<?php echo htmlspecialchars($usuario['imagenPerfil'] ?? 'default-avatar.jpg'); ?>" alt="Perfil">
          <span class="topbar__usuario"><?php echo htmlspecialchars($usuario['nombreUsuario']); ?></span>
        </a>

      </div>
    </header>

   <!-- MODAL CARGAR PARTIDA -->
<div id="modalCargar" class="modal oculto">
  <div class="modal__contenido">
    <h2 class="modal__titulo">⛴️ Cargar Partida ⛴️</h2>

    <?php if ($hayPartidas): ?>
      <ul class="modal__lista">
        <?php foreach ($partidasGuardadas as $p): ?>
          <li class="modal__item">
            <span>Partida #<?php echo $p["idPartida"]; ?></span>
            <small>
              <strong>Estado:</strong> <?php echo ucfirst($p["estado"]); ?><br>
              <strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($p["fecha"])); ?>
            </small>
            
            <a href="batalla.php?id=<?php echo $p['idPartida']; ?>" 
              class="modal__btn modal__btn--ok"
              onclick="console.log('Cargando partida ID: <?php echo $p['idPartida']; ?>')">
              Continuar Partida
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p class="modal__texto">No tienes partidas guardadas.<br>¡Inicia una nueva partida!</p>
    <?php endif; ?>

    <div class="modal__botones">
      <button id="cerrarModalCargar" class="modal__btn modal__btn--cancel">Cerrar</button>
    </div>
  </div>
</div>




    <!-- CONTENIDO PRINCIPAL -->
    <main class="menu__contenedor">
      <section class="menu__opciones">
        <a href="#" 
          class="menu__btn <?php echo !$hayPartidas ? 'btn--disabled' : ''; ?>" 
          id="btnCargarPartida"
          <?php echo !$hayPartidas ? 'disabled' : ''; ?>>
          Cargar Partida
        </a>

        <a href="inicioJuego.php" class="menu__btn">Hundir la Flota</a>
        <a href="ranking.php" class="menu__btn">Ranking</a>
        <a href="settings.php" class="menu__btn">Settings</a>
        <a href="#" class="menu__btn" id="btnSalir">Salir</a>

      </section>
    </main>
    <!-- MODAL DE CONFIRMACIÓN -->
    <div id="modalSalir" class="modal oculto">
      <div class="modal__contenido">
        <h2 class="modal__titulo">¿Salir del juego?</h2>
        <p class="modal__texto">Se cerrará tu sesión y volverás al inicio.</p>

        <div class="modal__botones">
          <button id="confirmarSalir" class="modal__btn modal__btn--ok">Salir</button>
          <button id="cancelarSalir" class="modal__btn modal__btn--cancel">Cancelar</button>
        </div>
      </div>
    </div>

        <!-- FOOTER -->
    <footer class="bottombar">
      <div class="bottombar__fondo"></div>
      <div class="bottombar__contenido">
        <p class="bottombar__texto">© Pau Gracia López</p>
      </div>
    </footer>
    
    <script src="../assets/js/main.js?v=<?php echo time(); ?>"></script>
 <script>
// Debug temporal - eliminar después de probar
/*console.log("=== DEBUG RÁPIDO ===");
console.log("btnCargarPartida encontrado:", !!document.getElementById('btnCargarPartida'));
console.log("modalCargar encontrado:", !!document.getElementById('modalCargar'));
console.log("modalCargar tiene clase oculto:", document.getElementById('modalCargar')?.classList.contains('oculto'));

// Probar manualmente desde consola: testModal()
window.testModal = function() {
    const modal = document.getElementById('modalCargar');
    if (modal) {
        console.log("✅ Abriendo modal manualmente");
        modal.classList.remove('oculto');
        return "Modal abierto";
    }
    return "❌ Modal no encontrado";
};

// Test automático
setTimeout(() => {
    console.log("--- TEST AUTOMÁTICO ---");
    const btn = document.getElementById('btnCargarPartida');
    const modal = document.getElementById('modalCargar');
    
    if (btn && modal) {
        console.log("✅ Elementos encontrados");
        console.log("🔘 Botón deshabilitado?", btn.hasAttribute('disabled'));
        console.log("📦 Modal oculto?", modal.classList.contains('oculto'));
        
        // Simular click si no está deshabilitado
        if (!btn.hasAttribute('disabled')) {
            console.log("🖱️ Simulando click...");
            btn.click();
        } else {
            console.log("⏸️ Botón deshabilitado - no se simula click");
        }
    } else {
        console.error("❌ No se encontraron elementos para test");
    }
}, 1000);*/
</script>
  </body>
</html>
