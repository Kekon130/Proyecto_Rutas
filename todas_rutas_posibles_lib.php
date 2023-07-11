<?php

$env = parse_ini_file("./config.ini");

function getInformation($origen)
{
  global $env;
  $curl = curl_init();
  try {

    $coordenadasOrigen = getCoordenadas($origen, $curl, $env["email"]);
    $latitud1 = $coordenadasOrigen["lat"];
    $longitud1 = $coordenadasOrigen["lng"];

    $coordenadasDestino = getCoordenadas($env["destino"], $curl, $env["email"]);
    $latitud2 = $coordenadasDestino["lat"];
    $longitud2 = $coordenadasDestino["lng"];

    $distanciaLineaRecta = array("distancia" => number_format(calcularModuloDistancia($coordenadasOrigen, $coordenadasDestino), 2));
    $distanciaCoche = getRutaMasRapida2puntosCoche($latitud1, $longitud1, $latitud2, $longitud2, $curl);
    $distanciaTransportePublico = getRutaMasRapida2puntosTP($latitud1, $longitud1, $latitud2, $longitud2, $curl);
    return json_encode(array("linea recta" => $distanciaLineaRecta, "coche" => $distanciaCoche, "transporte publico" => $distanciaTransportePublico));
  } catch (Exception $e) {
    echo $e->getMessage();
  } finally {
    curl_close($curl);
  }
}

?>