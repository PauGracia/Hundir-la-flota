<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST["usuario"]);
    $password = $_POST["password"];

    if (empty($usuario) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Usuario o contraseña vacíos."]);
        exit;
    }

    // Comprobamos si ya existe ese usuario
    $consulta = $conexion->prepare("SELECT id FROM USUARIO WHERE nombreUsuario = ?");
    $consulta->bind_param("s", $usuario);
    $consulta->execute();
    $consulta->store_result();

    if ($consulta->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "El usuario ya existe."]);
    } else {
        // Ciframos la contraseña
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $insertar = $conexion->prepare("INSERT INTO USUARIO (nombreUsuario, pass) VALUES (?, ?)");
        $insertar->bind_param("ss", $usuario, $hash);

        if ($insertar->execute()) {
            echo json_encode(["success" => true, "message" => "Usuario creado con éxito."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al registrar usuario."]);
        }
    }

    $consulta->close();
    $conexion->close();
}
?>
