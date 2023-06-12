<?php
$localizacion = "Azuqueca de Henares";
$url = "https://nominatim.openstreetmap.org/search?q=Azuqueca%20de%20Henares&format=json&email=sergio.plaza@alumnos.upm.es";

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