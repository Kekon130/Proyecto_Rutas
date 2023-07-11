<?php

function getCoordenadas($ubicacion, $curl, $email)
{
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

function parseDate($time)
{
  $time = round($time);
  return array("horas" => floor(($time / 3600)), "minutos" => floor((($time % 3600) / 60)), "segundos" => $time % 60);
}

?>