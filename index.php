<?php
require("./lib_functions.php");

$method = $_SERVER["REQUEST_METHOD"];
$path = $_SERVER["REQUEST_URI"];

echo $method;
echo $path;

if ($method === "GET" && $path === "/distanciaLineaRecta") {
  try {
    echo json_encode(getDistancia2PuntosLineaRecta($_GET["localizacion"]));
  } catch (Exception $ex) {
    echo $ex->getMessage();
  }
}
?>