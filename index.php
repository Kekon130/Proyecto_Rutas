<?php

require("./vendor/autoload.php");

$env = parse_ini_file("./config.ini");

$url = "https://nominatim.openstreetmap.org/search?q=" . $env["localizacion"] . "&format=json&email=" . $env["email"];

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$res = curl_exec($curl);

if ($res === false) {
  echo "Hubo un error en la petición: " . curl_error($curl);
} else {
  echo $res;
}

curl_close($curl);
?>