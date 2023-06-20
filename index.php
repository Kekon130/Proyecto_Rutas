<?php
require("./lib_functions.php");

$method = $_SERVER["REQUEST_METHOD"];
$path = $_SERVER["REQUEST_URI"];

if ($method === "GET" && strpos($path, "/distanciaLineaRecta") === 0) {
  try {
    $destino = $_GET["destino"];
    $ubicaciones = getParDeCoordenadas($destino);
    echo json_encode(getDistancia2PuntosLineaRecta($ubicaciones["origen"], $ubicaciones["destino"]));
  } catch (Exception $ex) {
    echo $ex->getMessage();
  }
}

if ($method === "GET" && strpos($path, "/distanciaCoche") === 0) {
  try {
    $destino = $_GET["destino"];
    $ubicaciones = getParDeCoordenadas($destino);
    getRuta($ubicaciones["origen"]["lat"], $ubicaciones["origen"]["lon"], $ubicaciones["destino"]["lat"], $ubicaciones["destino"]["lon"]);
  } catch (Exception $ex) {
    echo $ex->getMessage();
  }
}
?>