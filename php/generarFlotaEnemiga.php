<?php
function generarFlotaEnemiga() {

    // Definición REAL de barcos
    $barcos = [
        ["tipo" => "portaaviones", "size" => 10, "ancho" => 5, "alto" => 2],  // 2x5
        ["tipo" => "acorazado",    "size" => 5,  "ancho" => 5, "alto" => 1],
        ["tipo" => "destructor",   "size" => 4,  "ancho" => 4, "alto" => 1],
        ["tipo" => "fragata",      "size" => 3,  "ancho" => 3, "alto" => 1],
        ["tipo" => "corbeta",      "size" => 2,  "ancho" => 2, "alto" => 1],
        ["tipo" => "corbeta",      "size" => 2,  "ancho" => 2, "alto" => 1],
    ];

    // Tablero vacío
    $tablero = array_fill(0, 10, array_fill(0, 10, 0));
    $flotaFinal = [];

    foreach ($barcos as $barco) {

        $colocado = false;

        while (!$colocado) {

            // Rotación
            $orientacion = rand(0,1) ? "horizontal" : "vertical";

            // Ajustar ancho/alto según orientación
            if ($orientacion === "horizontal") {
                $ancho = $barco["ancho"];
                $alto  = $barco["alto"];
            } else {
                $ancho = $barco["alto"];
                $alto  = $barco["ancho"];
            }

            // Posición aleatoria
            $x = rand(0, 10 - $ancho);
            $y = rand(0, 10 - $alto);

            // Verificar colisión
            $ok = true;
            for ($i = 0; $i < $alto; $i++) {
                for ($j = 0; $j < $ancho; $j++) {

                    if ($tablero[$y + $i][$x + $j] === 1) {
                        $ok = false;
                        break 2;
                    }
                }
            }

            if ($ok) {

                // Marcar posiciones
                for ($i = 0; $i < $alto; $i++) {
                    for ($j = 0; $j < $ancho; $j++) {
                        $tablero[$y + $i][$x + $j] = 1;
                    }
                }

                // Guardar barco listo para DB
                $flotaFinal[] = [
                    "tipo"        => $barco["tipo"],
                    "size"        => $barco["size"],
                    "ancho"       => $ancho,
                    "alto"        => $alto,
                    "orientacion" => $orientacion,
                    "xInicio"     => $x + 1, // ajustar a 1–10
                    "yInicio"     => $y + 1
                ];

                $colocado = true;
            }
        }
    }

    return $flotaFinal;
}
