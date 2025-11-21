<?php
function generarFlotaEnemiga() {

    // Definición de barcos base
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
            // Rotación aleatoria
            $orientacion = rand(0,1) ? "horizontal" : "vertical";

            // Calcular ancho/alto según orientación
            if ($orientacion === "horizontal") {
                $ancho = $barco["base_alto"];
                $alto  = $barco["base_ancho"];
            } else {
                $ancho = $barco["base_ancho"];
                $alto  = $barco["base_alto"];
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
                // Marcar posiciones ocupadas
                for ($i = 0; $i < $alto; $i++) {
                    for ($j = 0; $j < $ancho; $j++) {
                        $tablero[$y + $i][$x + $j] = 1;
                    }
                }

                // Guardar barco listo para DB
                $flotaFinal[] = [
                    "tipo"        => $barco["tipo"],
                    "size"        => $ancho * $alto,
                    "ancho"       => $ancho,
                    "alto"        => $alto,
                    "orientacion" => $orientacion,
                    "xInicio"     => $x + 1, // coordenadas 1–10
                    "yInicio"     => $y + 1
                ];

                $colocado = true;
            }
        }
    }

    return $flotaFinal;
}
