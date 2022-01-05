<?php  


require_once "action/DbClass.php";

$db = new ConfigClass;

echo showAddress("prov");


function showAddress($param, $idprov=null, $idkab=null, $idkec=null){
    global $db;        
    if($param == "prov"){
        $func_prov = $db->dataIndonesia("prov",null);
        foreach($func_prov as $key => $prov){
            $provv_id = $prov['province_id'];
            $func_kab = $db->dataIndonesia("kab_kota",$provv_id);
            foreach($func_kab as $key => $kab){
                $city_id = $kab['city_id'];
                $func_kec = $db->dataIndonesia("kec",$city_id);
                foreach($func_kec as $key => $kec){
                    $subdistrict_id = $kec['subdistrict_id'];
                    $subdistrict_name = $kec['subdistrict_name'];
                    $query = "INSERT INTO kecamatan (subdistrict_id,subdistrict_name,city_id) VALUES('$subdistrict_id','$subdistrict_name','$city_id')";
                    $result = mysqli_query($db->conn,$query);
                }
            }
        }
    }
}

?>