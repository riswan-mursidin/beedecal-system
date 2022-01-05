<?php  

$kurir = $_GET['kurir'];
$asal = $_GET['asal'];
$tujuan = $_GET['tujuan'];
$berat = $_GET['berat'];

require_once "action/rajaOngkir.php";
$rajaongkir = new RajaOngkir();

$data = $rajaongkir->checkOngkir($kurir, $asal, $tujuan, $berat);

if($data == "error"){
    echo '<option value="">error</option>';
}else{
    echo '<option value="">PILIH PAKET</option>';
    foreach($data->costs as $d){
        echo '<option value="'.$d->cost[0]->value.' - '.$d->service.' - '.$d->cost[0]->etd.'">Rp.'.number_format($d->cost[0]->value,2,",",".").' (Paket: '.$d->service.' Estimasi: '.$d->cost[0]->etd. ')</option>';
    }
}
?>