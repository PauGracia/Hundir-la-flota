<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="../assets/css/styles.css?v=<?php echo time(); ?>">
</head>

<body class="settings">
    <div class="settings__card">
        <h2 class="settings__titulo">⚙ Ajustes de sonido</h2>

        <div class="settings__slider-container">
            <label class="settings__label">Volumen de la música</label>
            <input type="range" id="volumenMusica" min="0" max="1" step="0.01">
        </div>

        <a href="menuJuego.php" class="settings__btn-volver">Volver al menú</a>
    </div>

  
    <script src="../assets/js/main.js?v=<?php echo time(); ?>"></script>

</body>
</html>
