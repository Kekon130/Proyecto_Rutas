<?php

require("./vendor/autoload.php");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$url = "https://nominatim.openstreetmap.org/search?q=" . $_ENV["LOCALIZACION"] . "&format=json&email=" . $_ENV["EMAIL"];

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