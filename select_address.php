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
$kode_pos = $rowcustomer['kode_pos_customer'];
$alamat_lengkap = $rowcustomer['address_customer'];

?>
<label for="" class="col-sm-2 col-form-label">Tujuan Pengiriman</label>
<div class="col-sm-3">
    <select name="prov" id="prov" class="form-select" onchange="viewKab(this.value)">
        <option value="">--PILIH PROVINSI--</option>
        <?php  $idprov = "";
        $provs = $db->dataIndonesia("prov",null);
        foreach($provs as $prov){
            $select = $prov['province'] == $prov_post ? 'selected="selected"' : '';
            $idprov .= $prov['province'] == $prov_post ? $prov['province_id'] : '';
            echo '<option value="'.$prov['province_id'].'"'.$select.'>'.$prov['province'].'</option>';
        }
        ?>
    </select>
</div>
<div class="col-sm-3">
    <select name="kabkota" id="kabkota" class="form-select" onchange="viewkec(this.value)" required>
        <option value="">--PILIH KAB/KOTA--</option>
        <?php  $idkab = "";
        $kot_kab = $db->dataIndonesia("kab_kota",$idprov);
        foreach($kot_kab as $k){
            $select = $k['city_name'] == $kab_kota_post ? 'selected="selected"' : '';
            $idkab .= $k['city_name'] == $kab_kota_post ? $k['city_id'] : '';
            echo '<option value="'.$k['city_id'].'" '.$select.'>'.$k['city_name'].'</option>';
        }
        ?>
    </select>
</div>
<div class="col-sm-3">
    <select name="kec" id="kec" class="form-select" required>
        <option value="">--PILIH KECAMATAN--</option>
        <?php  
        $kec = $db->dataIndonesia("kec",$idkab);
        foreach($kec as $j){
            $select = $j['subdistrict_name'] == $kec_post ? 'selected="selected"' : '';
            echo '<option value="'.$j['subdistrict_id'].'" '.$select.'>'.$j['subdistrict_name'].'</option>';
        }
        ?>
    </select>
</div>
<div class="col-sm-1">
    <input type="number" name="kode_pos" id="kode_pos" class="form-control" value="<?= $kode_pos ?>" placeholder="Kode Pos">
</div>
<label for="" class="col-sm-2 col-form-label">Alamat Lengkap</label>
<div class="col-sm">
    <textarea name="alamat_lengkap" id="" rows="3" class="form-control"><?= $alamat_lengkap ?></textarea>
</div>