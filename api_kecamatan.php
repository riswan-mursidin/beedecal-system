<?php
require_once "action/DbClass.php";

$id_city = $_GET['city_id'];

$db = new ConfigClass();

$kecamatan = $db->dataIndonesia("kec",$id_city);

if ($kecamatan == "error") {
    echo "cURL Error #:" . $err;
} else {
    echo '<option value="" hidden>KECAMATAN</option>';

    foreach ($kecamatan as $key => $kec){
        echo '<option value="'.$kec["subdistrict_id"].'">'.$kec["subdistrict_name"].'</option>';
    }
}
