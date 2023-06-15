<?php
require("./lib_functions.php");

$method = $_SERVER["REQUEST_METHOD"];
$path = $_SERVER["REQUEST_URI"];

if ($method === "GET" && strpos($path, "/distanciaLineaRecta") === 0) {
  try {
    echo json_encode(getDistancia2PuntosLineaRecta($_GET["localizacion"]));
  } catch (Exception $ex) {
    echo $ex->getMessage();
  }
}
?>