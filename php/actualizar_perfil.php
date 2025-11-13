<?php
session_start();

// conexión (conexion.php está en el mismo directorio php/)
require_once __DIR__ . '/conexion.php';

function flash($mensaje, $tipo = 'info') {
    $_SESSION['flash_mensaje'] = $mensaje;
    $_SESSION['flash_tipo'] = $tipo;
}


if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit;
}

$nombreUsuarioAntiguo = $_SESSION['usuario'];
$nuevoNombre = isset($_POST['nombreUsuario']) ? trim($_POST['nombreUsuario']) : $nombreUsuarioAntiguo;
$nuevaPass = isset($_POST['pass1']) ? trim($_POST['pass1']) : '';
$imagenPerfilRuta = null; // ruta relativa que guardaremos en la DB (ej: assets/img/perfiles/abc.jpg)

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
        flash('Error al subir la imagen.', 'error');

        header('Location: ../juego/perfil.php'); 
        exit;
    }

    if ($file['size'] > $maxSize) {
        flash('La imagen es demasiado grande (máx 2MB).','error');
        
        header('Location: ../juego/perfil.php');
        exit;
    }

    if (!in_array($ext, $allowedExt)) {
        flash('Formato no permitido. Usa: jpg, jpeg, png, gif, webp.', 'error');
        
        header('Location: ../juego/perfil.php');
        exit;
    }

    // comprobar que es realmente una imagen
    $check = @getimagesize($tmpName);
    if ($check === false) {
        flash('El archivo no parece una imagen válida.', 'error');
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
        flash('No se pudo guardar la imagen en el servidor.', 'error');
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

    flash('Perfil actualizado correctamente.', 'success');
    
} catch (Exception $e) {
    flash('Error al actualizar el perfil.', 'error');
    
}

// redirigir de nuevo al perfil
header('Location: ../juego/perfil.php'); 
exit;
