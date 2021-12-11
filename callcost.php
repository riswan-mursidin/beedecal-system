<?php  

require_once "action/rajaOngkir.php";

$api = new RajaOnkir();

$kurir = $_POST['kurir'];
$tujuan = $_POST['tujuan'];
$berat = $_POST['berat'];

$costs = $api->checkOngkir($kurir,$tujuan,$berat);

?>