<?php
require("./lib_functions.php");

$method = $_SERVER["REQUEST_METHOD"];
$path = $_SERVER["REQUEST_URI"];

if ($method === "GET" && strpos($path, "/distanciaLineaRecta") === 0) {
  try {
    echo getDistancia2PuntosLineaRecta($_GET["origen"]);
  } catch (Exception $ex) {
    echo $ex->getMessage();
  }
}

if ($method === "GET" && strpos($path, "/distanciaCoche") === 0) {
  try {
    echo getRutaMasRapidaCoche($_GET["origen"]);
  } catch (Exception $ex) {
    echo $ex->getMessage();
  }
}

if ($method === "GET" && strpos($path, "/distanciaTransportePublico") === 0) {
  try {
    echo getRutaMasRapidaTransportePublico($_GET["origen"]);
  } catch (Exception $ex) {
    echo $ex->getMessage();
  }
}

if ($method === "GET" && strpos($path, "/distancia") === 0) {
  try {
    echo getInformation($_GET["origen"]);
  } catch (Exception $ex) {
    echo $ex->getMessage();
  }
}
?>