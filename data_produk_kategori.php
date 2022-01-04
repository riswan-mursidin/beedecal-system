<?php  

$kategori = $_GET['jenisp'];
$jenis = $_GET['jenispr'];
$owner = $_GET['id'];

require_once "action/DbClass.php";

$db = new ConfigClass;

if($jenis == "Custom"){
    if($kategori == "Other"){
        
    }else{
        $views = $db->selectTable("merek_galeri","id_owner",$owner,"jenis_merek",$kategori);
        echo '<option value="" hidden>PILIH TYPE</option>';
        if(mysqli_num_rows($views)>0){
            while($row=mysqli_fetch_assoc($views)){
                $views2 = $db->selectTable("type_galeri","id_owner",$owner,"id_merek",$row['id_merek']);
                if(mysqli_num_rows($views2)>0){
                    echo '<optgroup label="'.$db->nameFormater($row['name_merek']).'">';
                    while($row2=mysqli_fetch_assoc($views2)){
                        echo '<option value="'.$row2['id_type'].'">'.$db->nameFormater($row2['name_type']).'</option>';
                    }
                    echo '</optgroup>';
                }
            }
        }
    }
}else{
    if($kategori = "Other"){

    }
    echo '<option value="" hidden>PILIH PRODUK</option>';
    
}

?>