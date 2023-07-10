<?php
$env = parse_ini_file("./config.ini");

function getCoordenadas($ubicacion, $curl)
{
  $email = parse_ini_file("./config.ini")["email"];
  $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($ubicacion) .
    "&format=json&email=" . urlencode($email);

  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

  $res = curl_exec($curl);

  if ($res === false) {
    throw new Exception(curl_error($curl), curl_errno($curl));
  } else {
    $aux = json_decode($res, true);
    return array("lat" => $aux[0]["lat"], "lng" => $aux[0]["lon"]);
  }
}

function getDistancia2PuntosLineaRecta($origen)
{
  global $env;
  $curl = curl_init();
  try {
    $coordenadasOrigen = getCoordenadas($origen, $curl);
    $coordenadasDestino = getCoordenadas($env["destino"], $curl);
    return json_encode(array("distancia" => calcularModuloDistancia($coordenadasOrigen, $coordenadasDestino)));
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

function getRutaMasRapidaCoche($origen)
{
  global $env;
  $curl = curl_init();
  try {
    $coordenadasOrigen = getCoordenadas($origen, $curl);
    $latitud1 = $coordenadasOrigen["lat"];
    $longitud1 = $coordenadasOrigen["lng"];

    $coordenadasDestino = getCoordenadas($env["destino"], $curl);
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
    return array("distancia" => $aux["distance"], "duracion" => $aux["duration"]);
  }
}

function getRutaMasRapidaTransportePublico($origen)
{
  global $env;
  $curl = curl_init();
  try {
    $coordenadasOrigen = getCoordenadas($origen, $curl);
    $latitud1 = $coordenadasOrigen["lat"];
    $longitud1 = $coordenadasOrigen["lng"];

    $coordenadasDestino = getCoordenadas($env["destino"], $curl);
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
    return array("distancia" => getDistanceOfTrip($aux), "tiempo" => getTimeOfTrip($aux));
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
  return array("horas" => ($time / 3600), "minutos" => (($time % 3600) / 60), "segundos" => $time % 60);
}

function getDiffDates($inicDate, $endDate)
{
  $date1 = new DateTime($inicDate);
  $date2 = new DateTime($endDate);
  $diff = $date1->diff($date2);
  return $diff->s + ($diff->i * 60) + ($diff->h * 3600);
}

function getInformation($origen)
{
  global $env;
  $curl = curl_init();
  try {

    $coordenadasOrigen = getCoordenadas($origen, $curl);
    $latitud1 = $coordenadasOrigen["lat"];
    $longitud1 = $coordenadasOrigen["lng"];

    $coordenadasDestino = getCoordenadas($env["destino"], $curl);
    $latitud2 = $coordenadasDestino["lat"];
    $longitud2 = $coordenadasDestino["lng"];

    $distanciaLineaRecta = calcularModuloDistancia($coordenadasOrigen, $coordenadasDestino);
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