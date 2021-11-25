<?php

$id_prov = $_GET['prov_id'];

require_once "action/DbClass.php";

$db = new ConfigClass();

$kab_kota = $db->dataIndonesia("kab_kota",$id_prov);


if ($kab_kota == "error") {
    echo "cURL Error #:";
} else {
    echo '<option value="">--PILIH KAB/KOTA--</option>';

    foreach ($kab_kota as $key => $kab){
        echo '<option value="'.$kab["city_id"].'">'.$kab["city_name"].'</option>';
    }
}
