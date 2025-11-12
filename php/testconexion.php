<?php
$host = "localhost";
$usuario = "root"; // o tu usuario
$clave = "Pau19921992!";       // tu contraseña
$bd = "hundir_flota";

$conexion = @new mysqli($host, $usuario, $clave, $bd);

if ($conexion->connect_error) {
    echo "Error de conexión: " . $conexion->connect_error;
} else {
    echo "Conexión correcta!";
}
?>
