<?php
session_start();

// conexión (conexion.php está en el mismo directorio php/)
require_once __DIR__ . '/conexion.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit;
}

$nombreUsuarioAntiguo = $_SESSION['usuario'];
$nuevoNombre = isset($_POST['nombreUsuario']) ? trim($_POST['nombreUsuario']) : $nombreUsuarioAntiguo;
$nuevaPass = isset($_POST['pass']) ? trim($_POST['pass']) : '';
$imagenPerfilRuta = "null"; // ruta relativa que guardaremos en la DB (ej: assets/img/perfiles/abc.jpg)

// --- Subida y validación de imagen ---
if (!empty($_FILES['imagenPerfil']['name'])) {
    $file = $_FILES['imagenPerfil'];

    // validaciones básicas
    $maxSize = 2 * 1024 * 1024; // 2 MB
    $allowedExt = ['jpg','jpeg','png','gif','webp'];
    $tmpName = $file['tmp_name'];
    $origName = basename($file['name']);
    $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

    // chequeos
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['flash_error'] = 'Error al subir la imagen.';
        $_SESSION['flash_tipo'] = 'error';
        header('Location: ../juego/perfil.php'); 
        exit;
    }

    if ($file['size'] > $maxSize) {
        $_SESSION['flash_error'] = 'La imagen es demasiado grande (máx 2MB).';
        $_SESSION['flash_tipo'] = 'error';
        header('Location: ../juego/perfil.php');
        exit;
    }

    if (!in_array($ext, $allowedExt)) {
        $_SESSION['flash_error'] = 'Formato no permitido. Usa: jpg, jpeg, png, gif, webp.';
        $_SESSION['flash_tipo'] = 'error';
        header('Location: ../juego/perfil.php');
        exit;
    }

    // comprobar que es realmente una imagen
    $check = @getimagesize($tmpName);
    if ($check === false) {
        $_SESSION['flash_error'] = 'El archivo no parece una imagen válida.';
        $_SESSION['flash_tipo'] = 'error';
        header('Location: ../juego/perfil.php');
        exit;
    }

    // crear carpeta si no existe
    $targetDir = __DIR__ . '/../assets/img/perfiles/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // nombre único
    $nuevoNombreArchivo = uniqid('pf_', true) . '.' . $ext;
    $targetPath = $targetDir . $nuevoNombreArchivo;

    if (!move_uploaded_file($tmpName, $targetPath)) {
        $_SESSION['flash_error'] = 'No se pudo guardar la imagen en el servidor.';
        $_SESSION['flash_tipo'] = 'error';
        header('Location: ../juego/perfil.php');
        exit;
    }

    // ruta que guardamos en la DB
    $imagenPerfilRuta = $nuevoNombreArchivo; // Solo el nombre del archivo

    // (Opcional) podrías eliminar la imagen anterior si no es la default
    // Para ello tendrías que leer la ruta antigua antes de actualizar y unlinkla aquí con cuidado.
}

// --- Preparar SQL dinámico ---
try {
    if (!empty($nuevaPass)) {
        $hash = password_hash($nuevaPass, PASSWORD_DEFAULT);
        if ($imagenPerfilRuta !== null) {
            $sql = "UPDATE usuario SET nombreUsuario = ?, pass = ?, imagenPerfil = ? WHERE nombreUsuario = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ssss", $nuevoNombre, $hash, $imagenPerfilRuta, $nombreUsuarioAntiguo);
        } else {
            $sql = "UPDATE usuario SET nombreUsuario = ?, pass = ? WHERE nombreUsuario = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sss", $nuevoNombre, $hash, $nombreUsuarioAntiguo);
        }
    } else {
        // no cambia contraseña
        if ($imagenPerfilRuta !== null) {
            $sql = "UPDATE usuario SET nombreUsuario = ?, imagenPerfil = ? WHERE nombreUsuario = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sss", $nuevoNombre, $imagenPerfilRuta, $nombreUsuarioAntiguo);
        } else {
            $sql = "UPDATE usuario SET nombreUsuario = ? WHERE nombreUsuario = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ss", $nuevoNombre, $nombreUsuarioAntiguo);
        }
    }

    $stmt->execute();

    // actualizar sesión con el nuevo nombre (si se cambió)
    $_SESSION['usuario'] = $nuevoNombre;

    $_SESSION['flash_success'] = 'Perfil actualizado correctamente.';
    $_SESSION['flash_tipo'] = 'success';
} catch (Exception $e) {
    $_SESSION['flash_error'] = 'Error al actualizar el perfil.';
    $_SESSION['flash_tipo'] = 'error';
}

// redirigir de nuevo al perfil
header('Location: ../juego/perfil.php'); 
exit;
