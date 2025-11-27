<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit;
}

$tipo = $_GET['tipo'] ?? 'victoria'; // victoria por defecto
$nombre = $_GET['nombre'] ?? $_SESSION['usuario'];
$foto = $_GET['foto'] ?? 'default-avatar.jpg';

if ($tipo === 'victoria') {
    $mensaje = '¡Victoria!';
    $fondo = 'victoria.jpg';
} else {
    $mensaje = "Has sido derrotado por $nombre";
    $fondo = 'derrota.png';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo ucfirst($tipo); ?></title>
<link rel="stylesheet" href="../assets/css/styles.css?v=<?php echo time(); ?>">
<link href="https://fonts.googleapis.com/css2?family=Russo+One&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?php echo time(); ?>" />

</head>
<body-resultado>

<div class="result-container-resultado">
    <h1><?php echo $mensaje; ?></h1>
    <img src="../assets/img/perfiles/<?php echo htmlspecialchars($foto); ?>" alt="Avatar">
    <p><?php echo htmlspecialchars($nombre); ?></p>
    <div>
        <button onclick="location.href='ranking.php'">Ranking</button>
        <button onclick="location.href='menuJuego.php'">Menú</button>
    </div>
</div>

</body-resultado>
</html>
