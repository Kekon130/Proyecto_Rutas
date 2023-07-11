<?php

$env = parse_ini_file("./config.ini");

function getDistancia2PuntosLineaRecta($origen)
{
  global $env;
  $curl = curl_init();
  try {
    $coordenadasOrigen = getCoordenadas($origen, $curl, $env["email"]);
    $coordenadasDestino = getCoordenadas($env["destino"], $curl, $env["email"]);
    return json_encode(array("distancia" => number_format(calcularModuloDistancia($coordenadasOrigen, $coordenadasDestino), 2)));
  } catch (Exception $e) {
    echo $e->getMessage();
  } finally {
    curl_close($curl);
  }
}

function calcularModuloDistancia($coordenadas1, $coordenadas2)
{
  $radioTierra = 6371; // Radio medio de la Tierra en kilómetros

  // Convertir las coordenadas de grados a radianes
  $latitud1 = deg2rad($coordenadas1["lat"]);
  $longitud1 = deg2rad($coordenadas1["lng"]);
  $latitud2 = deg2rad($coordenadas2["lat"]);
  $longitud2 = deg2rad($coordenadas2["lng"]);

  // Diferencia de latitudes y longitudes
  $deltaLatitud = $latitud2 - $latitud1;
  $deltaLongitud = $longitud2 - $longitud1;

  // Cálculo de la distancia utilizando la fórmula haversine
  $a = sin($deltaLatitud / 2) * sin($deltaLatitud / 2) + cos($latitud1) * cos($latitud2) *
    sin($deltaLongitud / 2) * sin($deltaLongitud / 2);
  $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
  $distancia = $radioTierra * $c;

  return $distancia;
}

?>