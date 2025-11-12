<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
include("conexion.php");


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"]);
    $password = $_POST["password"];

    $consulta = $conexion->prepare("SELECT pass FROM USUARIO WHERE nombreUsuario = ?");
    $consulta->bind_param("s", $usuario);
    $consulta->execute();
    $resultado = $consulta->get_result();

    if ($fila = $resultado->fetch_assoc()) {
        if (password_verify($password, $fila["pass"])) {
            echo json_encode(["success" => true, "message" => "Inicio de sesión correcto."]);
        } else {
            echo json_encode(["success" => false, "message" => "Contraseña incorrecta."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "El usuario no existe."]);
    }

    $consulta->close();
    $conexion->close();
}
?>
