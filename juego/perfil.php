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

<body class="perfil">
  <main class="perfil__contenedor">
    <section class="perfil__card">
      <h1 class="perfil__titulo">Perfil del Almirante</h1>
       <img class="topbar__fotoPerfil" src="../assets/img/perfiles/<?php echo htmlspecialchars($usuario['imagenPerfil'] ?? 'default-avatar.jpg'); ?>" ...>

      <form action="../php/actualizar_perfil.php" method="POST" enctype="multipart/form-data" class="perfil__form">

        <input type="text" name="nombreUsuario" 
            value="<?php echo htmlspecialchars($usuario['nombreUsuario'], ENT_QUOTES, 'UTF-8'); ?>" required>

        <label>Nueva contraseña:</label>
        <input type="password" name="pass" placeholder="Dejar vacío para no cambiar">

        <label>Foto de perfil:</label>
        <input type="file" name="imagenPerfil" accept="image/*">

        <p><strong>Victorias:</strong> <?php echo $usuario['victorias']; ?></p>
        <p><strong>Miembro desde:</strong> <?php echo $usuario['fecha']; ?></p>

        <button type="submit" class="btn btn--primary">Guardar cambios</button>
        <a href="menuJuego.php" class="btn btn--secondary">Volver al menú</a>
      </form>
    </section>
  </main>
  <div id="mensaje" class="mensaje oculto"></div>
  <?php
    if (isset($_SESSION['flash_mensaje'])) {
    $msg = json_encode($_SESSION['flash_mensaje']);
    $tipo = json_encode($_SESSION['flash_tipo'] ?? 'info');
    echo "<script>
    document.addEventListener('DOMContentLoaded', () => {
        mostrarMensaje($msg, $tipo);
    });
    </script>";

    unset($_SESSION['flash_mensaje'], $_SESSION['flash_tipo']);
    }
  ?>

<script src="../assets/js/main.js?v=<?php echo time(); ?>"></script>

</body>
</html>
