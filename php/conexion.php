<?php
require_once __DIR__ . "/env.php";

// Cargar variables del archivo .env
loadEnv(__DIR__ . "/../.env");

// Obtener credenciales del entorno
$host = getenv("DB_HOST");
$usuario = getenv("DB_USER");
$clave = getenv("DB_PASS");
$bd = getenv("DB_NAME");

// Conectar a MySQL
$conexion = new mysqli($host, $usuario, $clave, $bd);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
