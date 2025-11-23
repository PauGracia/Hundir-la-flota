<?php
function generarFlotaEnemiga() {

    $barcos = [
        ["tipo" => "portaaviones", "base_ancho" => 2, "base_alto" => 5],
        ["tipo" => "acorazado",    "base_ancho" => 1, "base_alto" => 5],
        ["tipo" => "destructor",   "base_ancho" => 1, "base_alto" => 4],
        ["tipo" => "fragata",      "base_ancho" => 1, "base_alto" => 3],
        ["tipo" => "corbeta",      "base_ancho" => 1, "base_alto" => 2],
        ["tipo" => "corbeta",      "base_ancho" => 1, "base_alto" => 2],
    ];

    $tablero = array_fill(0, 10, array_fill(0, 10, 0));
    $flotaFinal = [];

    foreach ($barcos as $barco) {

        $colocado = false;

        while (!$colocado) {

            // Mayor aleatoriedad: pesos 60% horizontal, 40% vertical
            $orientacion = rand(1, 100) <= 60 ? "horizontal" : "vertical";

            if ($orientacion === "horizontal") {
                $ancho = $barco["base_alto"];
                $alto  = $barco["base_ancho"];
            } else {
                $ancho = $barco["base_ancho"];
                $alto  = $barco["base_alto"];
            }

            $x = rand(0, 10 - $ancho);
            $y = rand(0, 10 - $alto);

            // Verificar colisión + adyacencia
            $ok = true;
            for ($i = -1; $i <= $alto; $i++) {
                for ($j = -1; $j <= $ancho; $j++) {
                    $ny = $y + $i;
                    $nx = $x + $j;
                    if ($ny >= 0 && $ny < 10 && $nx >= 0 && $nx < 10) {
                        if ($tablero[$ny][$nx] === 1) {
                            $ok = false;
                            break 2;
                        }
                    }
                }
            }

            if ($ok) {
                // Marcar posiciones ocupadas
                for ($i = 0; $i < $alto; $i++) {
                    for ($j = 0; $j < $ancho; $j++) {
                        $tablero[$y + $i][$x + $j] = 1;
                    }
                }

                // Guardar barco
                $flotaFinal[] = [
                    "tipo"        => $barco["tipo"],
                    "size"        => $ancho * $alto,
                    "ancho"       => $ancho,
                    "alto"        => $alto,
                    "orientacion" => $orientacion,
                    "xInicio"     => $x + 1,
                    "yInicio"     => $y + 1
                ];

                $colocado = true;
            }
        }
    }

    return $flotaFinal;
}
