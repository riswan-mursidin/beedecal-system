<?php

require_once "action/DbClass.php";

$db = new ConfigClass();

$province = $db->dataIndonesia("prov",null);

if ($province == "error") {
    echo "cURL Error #:";
} else {
    echo '<option value="">--PILIH PROVINSI--</option>';

    foreach ($province as $key => $prov){
        echo '<option value="'.$prov["province_id"].'">'.$prov["province"].'</option>';
    }
}
