<?php  

require_once "action/DbClass.php";

if($_SESSION['login_stiker_admin'] != true ){
    header('Location: auth-login');
    exit();
}
$db = new ConfigClass();

$id_customer = $_GET['id'];

$customer = $db->selectTable("customer_stiker","id_customer",$id_customer);
$rowcustomer = mysqli_fetch_assoc($customer);
$prov_post = $rowcustomer['prov_customer'];
$kab_kota_post = $rowcustomer['kota_kab_customer'];
$kec_post = $rowcustomer['kec_customer'];

?>
<label for="" class="col-sm-2 col-form-label">Tujuan Pengiriman</label>
<div class="col-sm-4">
    <select name="prov" id="prov" class="form-select" onchange="viewKab(this.value)">
        <option value="">--PILIH PROVINSI--</option>
        <?php  
        $provs = $db->dataIndonesia("prov",null);
        foreach($provs as $prov){
            $select = $prov['province_id'] == $prov_post ? 'selected="selected"' : '';
            echo '<option value="'.$prov['province_id'].'"'.$select.'>'.$prov['province'].'</option>';
        }
        ?>
    </select>
</div>
<div class="col-sm-3">
    <select name="kabkota" id="kabkota" class="form-select" onchange="viewkec(this.value)" required>
        <option value="">--PILIH KAB/KOTA--</option>
        <?php  
        $kot_kab = $db->dataIndonesia("kab_kota",$prov_post);
        foreach($kot_kab as $k){
            $select = $k['city_id'] == $kab_kota_post ? 'selected="selected"' : '';
            echo '<option value="'.$k['city_id'].'" '.$select.'>'.$k['city_name'].'</option>';
        }
        ?>
    </select>
</div>
<div class="col-sm-3">
    <select name="kec" id="kec" class="form-select" required>
        <option value="">--PILIH KECAMATAN--</option>
        <?php  
        $kec = $db->dataIndonesia("kec",$kab_kota_post);
        foreach($kec as $j){
            $select = $j['subdistrict_id'] == $kec_post ? 'selected="selected"' : '';
            echo '<option value="'.$j['subdistrict_id'].'" '.$select.'>'.$j['subdistrict_name'].'</option>';
        }
        ?>
    </select>
</div>