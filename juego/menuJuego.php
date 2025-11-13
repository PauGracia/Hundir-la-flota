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

?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Menú Principal - Hundir la Flota</title>

    <!-- Fuente Google bonita estilo naval -->
    <link href="https://fonts.googleapis.com/css2?family=Russo+One&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/styles.css?v=<?php echo time(); ?>" />
   
  </head>

  <body class="menu">
    <!-- BARRA SUPERIOR -->
    <header class="topbar">
      <div class="topbar__fondo"></div>
      <div class="topbar__contenido">
        <h1 class="topbar__titulo">⚓ Hundir la Flota ⚓</h1>
        <a href="perfil.php" class="topbar__perfil">
          <img class="topbar__foto" src="../assets/img/perfiles/<?php echo htmlspecialchars($usuario['imagenPerfil'] ?? 'default-avatar.jpg'); ?>" ...>

          <button class="topbar__usuario"><?php echo htmlspecialchars($usuario['nombreUsuario']); ?></button>
        </a>
      </div>
    </header>


    <!-- CONTENIDO PRINCIPAL -->
    <main class="menu__contenedor">
      <section class="menu__opciones">
        <a href="juego/juego.html" class="menu__btn">Hundir la Flota</a>
        <a href="#" class="menu__btn">Ranking</a>
        <a href="#" class="menu__btn">Settings</a>
        <a href="#" class="menu__btn" id="btnSalir">Salir</a>

      </section>
    </main>
        <!-- FOOTER -->
    <footer class="bottombar">
      <div class="bottombar__fondo"></div>
      <div class="bottombar__contenido">
        <p class="bottombar__texto">© Pau Gracia López</p>
      </div>
    </footer>
    <script src="../assets/js/main.js?v=<?php echo time(); ?>"></script>
  </body>
</html>
