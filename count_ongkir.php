<?php  

require_once "action/rajaOngkir.php";
$kurir = $_GET['kurir'];
$asal = $_GET['asal'];
$tujuan = $_GET['tujuan'];
$berat = $_GET['berat'];

$rajaongkir = new RajaOngkir();

$data = $rajaongkir->checkOngkir($kurir, $asal, $tujuan, $berat);

if($data == "error"){
    echo '<option value="">error</option>';
}else{
    foreach($data->costs as $d){
        echo '<option value="">Rp.'.number_format($d->cost[0]->value,2,",",".").' (Paket: '.$d->service.' Estimasi: '.$d->cost[0]->etd. ')</option>';
    }
}
?>