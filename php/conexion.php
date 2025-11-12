<?php
$host = "localhost";
$usuario = "root";
$clave = "Pau19921992!"; // o la contraseña de tu MySQL
$bd = "hundir_flota";

$conexion = new mysqli($host, $usuario, $clave, $bd);

// Comprobamos conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
