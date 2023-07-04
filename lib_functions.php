<?php
$env = parse_ini_file("./config.ini");

function getCoordenadas($ubicacion)
{
  $email = parse_ini_file("./config.ini")["email"];
  $resultado = null;
  $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($ubicacion) .
    "&format=json&email=" . urlencode($email);
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

  $res = curl_exec($curl);

  if ($res === false) {
    $resultado = array("error" => curl_error($curl));
  } else {
    $aux = json_decode($res, true);
    $resultado = array("lat" => $aux[0]["lat"], "lng" => $aux[0]["lon"]);
  }
  curl_close($curl);
  return $resultado;
}

function getParDeCoordenadas($origen)
{
  $destino = parse_ini_file("./config.ini")["localizacion"];
  $coordenadasOrigen = getCoordenadas($origen);
  $coordenadasDestino = getCoordenadas($destino);
  return array("origen" => $coordenadasOrigen, "destino" => $coordenadasDestino);
}

function getDistancia2PuntosLineaRecta($origen)
{
  $coordenadas = getParDeCoordenadas($origen);
  return json_encode(calcularModuloDistancia($coordenadas["origen"], $coordenadas["destino"]));
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

  return array("distancia" => $distancia);
}

function getRutaMasRapida2puntosCoche($latitud1, $longitud1, $latitud2, $longitud2)
{
  $resultado = null;
  $api_key = parse_ini_file("./config.ini")["openroute_key"];

  $url = "https://api.openrouteservice.org/v2/directions/driving-car?api_key=" . urlencode($api_key) .
    "&start=" . urlencode($longitud1) . "," . urlencode($latitud1) . "&end=" . urlencode($longitud2) .
    "," . urlencode($latitud2);

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

  $res = curl_exec($curl);
  if ($res === false) {
    $resultado = array("error" => curl_error($curl));
  } else {
    $aux = json_decode($res, true)["features"][0]["properties"]["summary"];
    $resultado = array("distancia" => $aux["distance"], "duracion" => $aux["duration"]);
  }
  curl_close($curl);
  return $resultado;
}

function getRutaMasRapidaCoche($origen)
{
  $coordenadas = getParDeCoordenadas($origen);
  $latitud1 = $coordenadas["origen"]["lat"];
  $longitud1 = $coordenadas["origen"]["lng"];
  $latitud2 = $coordenadas["destino"]["lat"];
  $longitud2 = $coordenadas["destino"]["lng"];
  return json_encode(getRutaMasRapida2puntosCoche($latitud1, $longitud1, $latitud2, $longitud2));
}

function getRutaMasRapidaTransportePublico($origen)
{
  $coordenadas = getParDeCoordenadas($origen);
  $latitud1 = $coordenadas["origen"]["lat"];
  $longitud1 = $coordenadas["origen"]["lng"];
  $latitud2 = $coordenadas["destino"]["lat"];
  $longitud2 = $coordenadas["destino"]["lng"];
  return json_encode(getRutaMasRapida2puntosTP($latitud1, $longitud1, $latitud2, $longitud2));
}

function getRutaMasRapida2puntosTP($latitud1, $longitud1, $latitud2, $longitud2)
{
  $resultado = null;
  $api_key = parse_ini_file("./config.ini")["here_public_transport_key"];

  $url = "https://transit.router.hereapi.com/v8/routes?origin=" . urlencode($latitud1) . "," .
    urlencode($longitud1) . "&destination=" . urlencode($latitud2) . "," . urlencode($longitud2) .
    "&apikey=" . urlencode($api_key);
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

  $res = curl_exec($curl);
  if ($res === false) {
    $resultado = array("error" => curl_error($curl));
  } else {
    $aux = json_decode($res, true)["routes"][0]["sections"];
    $resultado = array("distancia" => getDistanceOfTrip($aux), "tiempo" => getTimeOfTrip($aux));
  }
  curl_close($curl);
  return $resultado;
}

function getDistanceOfTrip($array)
{
  $distance = 0;
  for ($i = 0; $i < count($array); $i++) {
    $section = $array[$i];
    $coordenadas1 = $section["departure"]["place"]["location"];
    $coordenadas2 = $section["arrival"]["place"]["location"];
    $distance += calcularModuloDistancia($coordenadas1, $coordenadas2)["distancia"];
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
  $coordenadas = getParDeCoordenadas($origen);
  $latitud1 = $coordenadas["origen"]["lat"];
  $longitud1 = $coordenadas["origen"]["lng"];
  $latitud2 = $coordenadas["destino"]["lat"];
  $longitud2 = $coordenadas["destino"]["lng"];

  $distanciaLineaRecta = calcularModuloDistancia($coordenadas["origen"], $coordenadas["destino"]);
  $distanciaCoche = getRutaMasRapida2puntosCoche($latitud1, $longitud1, $latitud2, $longitud2);
  $distanciaTransportePublico = getRutaMasRapida2puntosTP($latitud1, $longitud1, $latitud2, $longitud2);
  return json_encode(array("linea recta" => $distanciaLineaRecta, "coche" => $distanciaCoche, "transporte publico" => $distanciaTransportePublico));
}
?>