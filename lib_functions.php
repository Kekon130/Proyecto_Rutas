<?php
$env = parse_ini_file("./config.ini");

function getCoordenadas($ubicacion)
{
  $email = parse_ini_file("./config.ini")["email"];
  $resultado = null;
  $url = "https://nominatim.openstreetmap.org/search?q=" . $ubicacion . "&format=json&email=" .
    $email;
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

  $res = curl_exec($curl);

  if ($res === false) {
    echo "Hubo un error en la petición: " . curl_error($curl);
  } else {
    $aux = json_decode($res, true);
    $resultado = array("lat" => $aux[0]["lat"], "lon" => $aux[0]["lon"]);
  }
  curl_close($curl);
  return $resultado;
}

function getParDeCoordenadas($origen)
{
  $destino = parse_ini_file("./config.ini")["localizacion"];
  $coordenadasOrigen = getCoordenadas(parseString($origen));
  $coordenadasDestino = getCoordenadas(parseString($destino));
  return array("origen" => $coordenadasOrigen, "destino" => $coordenadasDestino);
}

//El proposito de esta función es reemplazar los espacios en blanco por %20 ya que la API no los admite
function parseString($string)
{
  return str_replace(" ", "%20", $string);
}

function getDistancia2PuntosLineaRecta($origen, $destino)
{
  return array("Distancia" => calcularModuloDistancia($origen, $destino));
}

function calcularModuloDistancia($coordenadas1, $coordenadas2)
{
  $radioTierra = 6371; // Radio medio de la Tierra en kilómetros

  // Convertir las coordenadas de grados a radianes
  $latitud1 = deg2rad($coordenadas1["lat"]);
  $longitud1 = deg2rad($coordenadas1["lon"]);
  $latitud2 = deg2rad($coordenadas2["lat"]);
  $longitud2 = deg2rad($coordenadas2["lon"]);

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

function getRuta($latitud1, $longitud1, $latitud2, $longitud2)
{
  $resultado = null;
  $api_key = parse_ini_file("./config.ini")["openroute_key"];
  $url = "https://api.openrouteservice.org/v2/directions/driving-car?api_key=" . $api_key .
    "&start=" . $longitud1 . "," . $latitud1 . "&end=" . $longitud2 . "," . $latitud2;

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

  $res = curl_exec($curl);
  if ($res === false) {
    echo curl_error($curl);
  } else {
    $aux = json_decode($res, true)["features"][0]["properties"]["summary"];
    $resultado = array("distancia" => $aux["distance"], "duracion" => $aux["duration"]);
  }
  curl_close($curl);
  return $resultado;
}

function getRutaMasRapida2puntos($origen, $destino)
{

}
?>