<?php
require("./lib_functions.php");

$method = $_SERVER["REQUEST_METHOD"];
$path = $_SERVER["REQUEST_URI"];

if ($method === "GET" && strpos($path, "/distanciaLineaRecta") === 0) {
  try {
    $origen = $_GET["destino"];
    echo json_encode(getDistancia2PuntosLineaRecta($origen));
  } catch (Exception $ex) {
    echo $ex->getMessage();
  }
}

if ($method === "GET" && strpos($path, "/distanciaCoche") === 0) {
  try {
    $destino = $_GET["destino"];
    $ubicaciones = getParDeCoordenadas($destino);
    echo json_encode(getRutaMasRapida2puntos($ubicaciones["origen"], $ubicaciones["destino"]));
  } catch (Exception $ex) {
    echo $ex->getMessage();
  }
}
?>