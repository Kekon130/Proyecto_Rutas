<?php

$env = parse_ini_file("./config.ini");

$prueba = getCoordenadas($env["localizacion"], $env["email"]);
print_r($prueba);

function getCoordenadas($ubicacion, $email)
{
  $resultado = null;
  $url = "https://nominatim.openstreetmap.org/search?q=" . $ubicacion . "&format=json&email=" . $email;
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
?>