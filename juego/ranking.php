<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit;
}

// Cargar conexión (usa MySQLi)
require_once "../php/conexion.php"; // Debe crear $conexion (mysqli)

$usuarioLogado = $_SESSION["usuario"];

// Modo de ranking
$modo = $_GET['modo'] ?? 'puntos';

// Paginación
$porPagina = 20;
$pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina - 1) * $porPagina;

// ======================================
//         RANKING POR PUNTOS
// ======================================
if ($modo === 'puntos') {

    // Total de registros
    $sqlTotal = $conexion->prepare("SELECT COUNT(*) AS total FROM partidas WHERE estado = 'finalizada'");
    $sqlTotal->execute();
    $resTotal = $sqlTotal->get_result()->fetch_assoc();
    $totalRegistros = $resTotal['total'];
    $totalPaginas = ceil($totalRegistros / $porPagina);

    // Ranking por puntos
    $sql = $conexion->prepare("
        SELECT p.nombreUsuario, p.puntos, p.tiempo, u.imagenPerfil
        FROM partidas p
        JOIN usuario u ON u.nombreUsuario = p.nombreUsuario
        WHERE p.estado = 'finalizada'
        ORDER BY p.puntos DESC, p.tiempo ASC
        LIMIT ?, ?
    ");

    $sql->bind_param("ii", $offset, $porPagina);
    $sql->execute();
    $ranking = $sql->get_result();
}

// ======================================
//        RANKING POR VICTORIAS
// ======================================
if ($modo === 'victorias') {

    // Total usuarios
    $sqlTotal = $conexion->prepare("SELECT COUNT(*) AS total FROM usuario");
    $sqlTotal->execute();
    $resTotal = $sqlTotal->get_result()->fetch_assoc();
    $totalRegistros = $resTotal['total'];
    $totalPaginas = ceil($totalRegistros / $porPagina);

    // Ranking por victorias (desempate = menos partidas)
    $sql = $conexion->prepare("
        SELECT 
            u.nombreUsuario,
            u.victorias,
            (SELECT COUNT(*) FROM partidas p WHERE p.nombreUsuario = u.nombreUsuario) AS totalPartidas,
            u.imagenPerfil
        FROM usuario u
        ORDER BY u.victorias DESC, totalPartidas ASC
        LIMIT ?, ?
    ");

    $sql->bind_param("ii", $offset, $porPagina);
    $sql->execute();
    $ranking = $sql->get_result();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ranking</title>
<link rel="stylesheet" href="../assets/css/styles.css?v=<?php echo time(); ?>">
<link href="https://fonts.googleapis.com/css2?family=Russo+One&display=swap" rel="stylesheet" />

</head>

<body class="body-ranking">
<div class="ranking-container">
    <h1 id="titulo-ranking">Ranking</h1>

    <!-- Pestañas -->
    <div class="tabs">
        <a class="tab <?php echo $modo === 'puntos' ? 'activo' : ''; ?>" href="?modo=puntos">🔥 Puntos por partida</a>
        <a class="tab <?php echo $modo === 'victorias' ? 'activo' : ''; ?>" href="?modo=victorias">🏆 Ranking por victorias</a>
    </div>

    <table>
        <tr>
            <th>Pos</th>
            <th>Avatar</th>
            <th>Jugador</th>
            <?php if ($modo === 'puntos'): ?>
                <th>Puntos</th>
                <th>Tiempo (s)</th>
            <?php else: ?>
                <th>Victorias</th>
                <th>Total Partidas</th>
            <?php endif; ?>
        </tr>

        <?php 
        $pos = $offset + 1; 
        while ($fila = $ranking->fetch_assoc()):
            $esYo = ($fila['nombreUsuario'] === $usuarioLogado);
        ?>
        <tr class="<?php echo $esYo ? 'yo' : ''; ?>">
            <td><?php echo $pos++; ?></td>
            <td>
                <img class="avatar" src="../assets/img/perfiles/<?php echo $fila['imagenPerfil']; ?>" alt="avatar">
            </td>

            <td><?php echo htmlspecialchars($fila['nombreUsuario']); ?></td>

            <?php if ($modo === 'puntos'): ?>
                <td><?php echo $fila['puntos']; ?></td>
                <td><?php echo $fila['tiempo']; ?></td>
            <?php else: ?>
                <td><?php echo $fila['victorias']; ?></td>
                <td><?php echo $fila['totalPartidas']; ?></td>
            <?php endif; ?>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- Paginación -->
    <div class="paginacion">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <a class="<?php echo $i == $pagina ? 'activa' : ''; ?>" 
               href="?modo=<?php echo $modo; ?>&pagina=<?php echo $i; ?>">
               <?php echo $i; ?>
            </a>
        <?php endfor; ?>
    </div>
    <div class="volver-contenedor">
        <button class="btn-volver" onclick="location.href='menuJuego.php'">Volver</button>
    </div>

</div>

</body>
</html>
