<?php

$env = parse_ini_file("./config.ini");

function getRutaMasRapidaTransportePublico($origen)
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

    return json_encode(getRutaMasRapida2puntosTP($latitud1, $longitud1, $latitud2, $longitud2, $curl));
  } catch (Exception $e) {
    echo $e->getMessage();
  } finally {
    curl_close($curl);
  }
}

function getRutaMasRapida2puntosTP($latitud1, $longitud1, $latitud2, $longitud2, $curl)
{
  global $env;
  $api_key = $env["here_public_transport_key"];

  $url = "https://transit.router.hereapi.com/v8/routes?origin=" . urlencode($latitud1) . "," .
    urlencode($longitud1) . "&destination=" . urlencode($latitud2) . "," . urlencode($longitud2) .
    "&apikey=" . urlencode($api_key);

  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_URL, $url);

  $res = curl_exec($curl);
  if ($res === false) {
    throw new Exception(curl_error($curl), curl_errno($curl));
  } else {
    $aux = json_decode($res, true)["routes"][0]["sections"];
    return array("distancia" => number_format(getDistanceOfTrip($aux), 2), "tiempo" => getTimeOfTrip($aux));
  }
}

function getDistanceOfTrip($array)
{
  $distance = 0;
  for ($i = 0; $i < count($array); $i++) {
    $section = $array[$i];
    $coordenadas1 = $section["departure"]["place"]["location"];
    $coordenadas2 = $section["arrival"]["place"]["location"];
    $distance += calcularModuloDistancia($coordenadas1, $coordenadas2);
  }
  return $distance;
}

function getTimeOfTrip($array)
{
  $time = 0;
  for ($i = 0; $i < count($array); $i++) {
    $section = $array[$i];
    $date1 = $section["departure"]["time"];
    $date2 = $section["arrival"]["time"];
    $time += getDiffDates($date1, $date2);
  }
  return parseDate($time);
}

function getDiffDates($inicDate, $endDate)
{
  $date1 = new DateTime($inicDate);
  $date2 = new DateTime($endDate);
  $diff = $date1->diff($date2);
  return $diff->s + ($diff->i * 60) + ($diff->h * 3600);
}

?>