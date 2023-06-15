<?php
$env = parse_ini_file("./config.ini");

define("UBICACION", $env["localizacion"]);
define("EMAIL", $env["email"]);

function getCoordenadas($ubicacion)
{
  $resultado = null;
  $url = "https://nominatim.openstreetmap.org/search?q=" . $ubicacion . "&format=json&email=" . EMAIL;
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

//El proposito de esta función es que la api no admite espacios en blanco en las ubicaciones y por eso se reemplazan por %20
function parseString($string)
{
  return str_replace(" ", "%20", $string);
}

function getDistancia2PuntosLineaRecta($localizacion)
{
  $coordenadas1 = getCoordenadas(parseString(UBICACION));
  $coordenadas2 = getCoordenadas(parseString($localizacion));
  return array("Distancia" => calcularModuloDistancia($coordenadas1, $coordenadas2));
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
  $a = sin($deltaLatitud / 2) * sin($deltaLatitud / 2) + cos($latitud1) * cos($latitud2) * sin($deltaLongitud / 2) * sin($deltaLongitud / 2);
  $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
  $distancia = $radioTierra * $c;

  return $distancia;
}
?>