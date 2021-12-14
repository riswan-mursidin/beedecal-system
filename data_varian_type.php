<?php  
$type_id = $_GET['type'];
$pr = $_GET['jenispr'];
$owner = $_GET['id'];

require_once "action/DbClass.php";

$db = new ConfigClass;

if($pr == "Custom"){
    $views = $db->selectTable("type_galeri","id_type",$type_id,"id_owner",$owner);
    $row=mysqli_fetch_assoc($views);
    $fulldash = $row['fullbodydash_harga_type'] != "" ? '<option value="'.$row['fullbodydash_harga_type'].'- fulldash">Rp.'.number_format($row['fullbodydash_harga_type'],2,",",".").' (Fulldash)</option>' : '';
    $fullbody = $row['fullbody_harga_type'] != "" ? '<option value="'.$row['fullbody_harga_type'].'- fullbody">Rp.'.number_format($row['fullbody_harga_type'],2,",",".").' (Fullbody)</option>' : '';
    $lite = $row['lite_harga_type'] != "" ? '<option value="'.$row['lite_harga_type'].'- lite">Rp.'.number_format($row['lite_harga_type'],2,",",".").' (Lite)</option>' : '';
    echo '<option value="">PILIH</option>';
    echo $fullbody;
    echo $fulldash;
    echo $lite;
}

?>