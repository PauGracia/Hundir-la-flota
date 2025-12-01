<?php
session_start();
if (!isset($_SESSION["usuario"])) {
  header("Location: ../index.php");
  exit;
}

require_once("../php/conexion.php");

$nombreUsuario = $_SESSION["usuario"];
$stmt = $conexion->prepare("SELECT * FROM usuario WHERE nombreUsuario = ?");
$stmt->bind_param("s", $nombreUsuario);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Consulta de estadísticas contra cada almirante
$statsQuery = "
    SELECT
        a.nombreAlmirante,
        a.imagenAlmirante,
        a.victorias AS victoriasAlmirante,
        COALESCE(SUM(
            CASE
                WHEN p.ganador = ? 
                     AND SUBSTRING_INDEX(p.nombreOponente, ' ', -1) = SUBSTRING_INDEX(a.nombreAlmirante, ' ', -1)
                THEN 1 ELSE 0
            END
        ), 0) AS victoriasUsuario
    FROM almirantes a
    LEFT JOIN partidas p
        ON (p.nombreUsuario = ? OR p.nombreOponente = ?)
    GROUP BY a.id
    ORDER BY victoriasAlmirante DESC, victoriasUsuario DESC
";

$stmtStats = $conexion->prepare($statsQuery);
$stmtStats->bind_param("sss", $nombreUsuario, $nombreUsuario, $nombreUsuario);
$stmtStats->execute();
$statsResult = $stmtStats->get_result();
$almirantesStats = $statsResult->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Perfil de Usuario</title>
  <link href="https://fonts.googleapis.com/css2?family=Russo+One&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/styles.css?v=<?php echo time(); ?>">
</head>
<audio id="sonidoHover" src="../assets/sounds/hover.mp3" preload="auto"></audio>
  <audio id="sonidoClick" src="../assets/sounds/click.mp3" preload="auto"></audio>

<body class="perfil">

  <main class="perfil__contenedor">
    <!-- Columna izquierda: información -->
    <section class="perfil__info">
      <div class="perfil__info-card">
        <img class="perfil__foto-grande" src="../assets/img/perfiles/<?php echo htmlspecialchars($usuario['imagenPerfil'] ?? 'default-avatar.jpg'); ?>" alt="Foto de perfil">
        <h2><?php echo htmlspecialchars($usuario['nombreUsuario']); ?></h2>

        <div class="perfil__victorias">
          <img src="../assets/img/icons/trophy.png" alt="Trophy" class="perfil__icono-trophy">
          <span><strong>Victorias totales:</strong> <?php echo $usuario['victorias']; ?></span>
        </div>

       <h3>Historial contra almirantes</h3>
      <div class="perfil__almirantes-lista">
        <?php foreach ($almirantesStats as $alm): ?>
          <div class="perfil__almirante">
            <img src="../assets/img/almirantes/<?php echo htmlspecialchars($alm['imagenAlmirante']); ?>" 
                alt="<?php echo htmlspecialchars($alm['nombreAlmirante']); ?>">
            <div class="perfil__almirante-info">
              <p class="perfil__almirante-nombre"><?php echo htmlspecialchars($alm['nombreAlmirante']); ?></p>
              <p>🏆 <?php echo $alm['victoriasUsuario']; ?> | ❌ <?php echo $alm['victoriasAlmirante']; ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </section>

    <!-- Columna derecha: configuración -->
    <section class="perfil__config">
      <h1 class="perfil__titulo">Datos personales:</h1>

      <form action="../php/actualizar_perfil.php" method="POST" enctype="multipart/form-data" class="perfil__form">
        <label>Nombre de usuario:</label>
        <input type="text" name="nombreUsuario" 
            value="<?php echo htmlspecialchars($usuario['nombreUsuario'], ENT_QUOTES, 'UTF-8'); ?>" required>

        <label>Nueva contraseña:</label>
        <input type="password" name="pass1" id="pass1" placeholder="Dejar vacío para no cambiar">

        <label>Repetir nueva contraseña:</label>
        <input type="password" name="pass2" id="pass2" placeholder="Repetir contraseña">


        <label>Foto de perfil:</label>
        <input type="file" name="imagenPerfil" accept="image/*">

        <p><strong>Miembro desde:</strong> <?php echo $usuario['fecha']; ?></p>

        <button type="submit" class="btn btn--primary">Guardar cambios</button>
        <a href="menuJuego.php" class="btn btn--secondary">Volver</a>
      </form>
    </section>
  </main>

  <!-- Mensaje flash -->
  <div id="mensaje" class="mensaje oculto"></div>
  
  <!-- Primero cargamos main.js, donde está definida mostrarMensaje -->
  <script src="../assets/js/main.js?v=<?php echo time(); ?>"></script>

  <!-- Luego mostramos cualquier mensaje flash desde PHP -->
  <?php
    if (isset($_SESSION['flash_mensaje'])):
      $msg = json_encode($_SESSION['flash_mensaje']);
      $tipo = json_encode($_SESSION['flash_tipo'] ?? 'info');
  ?>
      <script>
        document.addEventListener('DOMContentLoaded', () => {
          mostrarMensaje(<?php echo $msg; ?>, <?php echo $tipo; ?>);
        });
      </script>
  <?php
      unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);
    endif;
  ?>
</body>
</html>
