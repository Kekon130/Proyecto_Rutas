<?php

$env = parse_ini_file("./config.ini");

define("UBICACION", $env["localizacion"]);
define("EMAIL", $env["email"]);

$aux = getDistancia2PuntosLineaRecta("Azuqueca de Henares");
print_r("La distancia desde Azuqueca a la universidad es: " . calcularModuloDistancia($aux["latitud"], $aux["longitud"]));

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
    $resultado = array("latitud" => $aux[0]["lat"], "longitud" => $aux[0]["lon"]);
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
  $localizacion1 = getCoordenadas(UBICACION);
  $localizacion2 = getCoordenadas(parseString($localizacion));
  return array("latitud" => $localizacion1["latitud"] - $localizacion2["latitud"], "longitud" => $localizacion1["longitud"] - $localizacion2["longitud"]);
}

function calcularModuloDistancia($lat, $lon)
{
  return sqrt(pow(abs($lat), 2) + pow(abs($lon), 2));
}
?>