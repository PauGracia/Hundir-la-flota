<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit;
}

// Leer datos desde la sesión si los tienes, o desde GET
$tipo = $_GET['tipo'] ?? 'victoria'; // victoria por defecto
$nombre = $_GET['nombre'] ?? $_SESSION['usuario'];
$foto = $_GET['foto'] ?? 'default-avatar.jpg';

// Fondo según el resultado
if ($tipo === 'victoria') {
    $mensaje = '¡Victoria!';
    $fondo = 'victoria.jpg';
} else {
    $mensaje = "Has sido derrotado por $nombre";
    $fondo = 'derrota.png';
}


// Determinar la ruta correcta
if ($foto === 'default-avatar.jpg') {
    $fotoPath = '../assets/img/perfiles/' . $foto; // avatar por defecto
} elseif (str_starts_with($foto, 'Almirante_')) {
    $fotoPath = '../assets/img/almirantes/' . $foto; // enemigos
} else {
    $fotoPath = '../assets/img/perfiles/' . $foto; // usuarios reales
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
</head>
<body class="body-resultado" style="background: url('../assets/img/imagenes/<?php echo $fondo; ?>') no-repeat center center fixed; background-size: cover;">
<div class="result-container-resultado">
    <h1><?php echo $mensaje; ?></h1>
    <img src="<?php echo htmlspecialchars($fotoPath); ?>" alt="Avatar">
    <p><?php echo htmlspecialchars($nombre); ?></p>

    <div>
        <button onclick="location.href='ranking.php'">Ranking</button>
        <button onclick="location.href='menuJuego.php'">Menú</button>
    </div>
</div>
<script src="../assets/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
