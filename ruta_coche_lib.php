<?php

$env = parse_ini_file("./config.ini");

function getRutaMasRapidaCoche($origen)
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

    return json_encode(getRutaMasRapida2puntosCoche($latitud1, $longitud1, $latitud2, $longitud2, $curl));
  } catch (Exception $e) {
    echo $e->getMessage();
  } finally {
    curl_close($curl);
  }
}

function getRutaMasRapida2puntosCoche($latitud1, $longitud1, $latitud2, $longitud2, $curl)
{
  global $env;
  $api_key = $env["openroute_key"];

  $url = "https://api.openrouteservice.org/v2/directions/driving-car?api_key=" . urlencode($api_key) .
    "&start=" . urlencode($longitud1) . "," . urlencode($latitud1) . "&end=" . urlencode($longitud2) .
    "," . urlencode($latitud2);

  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

  $res = curl_exec($curl);
  if ($res === false) {
    throw new Exception(curl_error($curl), curl_errno($curl));
  } else {
    $aux = json_decode($res, true)["features"][0]["properties"]["summary"];
    return array("distancia" => number_format($aux["distance"] / 1000, 2), "duracion" => parseDate($aux["duration"]));
  }
}

?>