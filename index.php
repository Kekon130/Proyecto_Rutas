<?php

require("./funciones_comunes_lib.php");
require("./distancia_2_puntos_lib.php");
require("./ruta_coche_lib.php");
require("./ruta_transporte_publico_lib.php");
require("./todas_rutas_posibles_lib.php");

$method = $_SERVER["REQUEST_METHOD"];
$path = $_SERVER["REQUEST_URI"];

if ($method === "GET" && strpos($path, "/distanciaLineaRecta") === 0) {
  echo getDistancia2PuntosLineaRecta($_GET["origen"]);
}

if ($method === "GET" && strpos($path, "/distanciaCoche") === 0) {
  echo getRutaMasRapidaCoche($_GET["origen"]);
}

if ($method === "GET" && strpos($path, "/distanciaTransportePublico") === 0) {
  echo getRutaMasRapidaTransportePublico($_GET["origen"]);
}

if ($method === "GET" && strpos($path, "/informacionDistancia") === 0) {
  echo getInformation($_GET["origen"]);
}

?>