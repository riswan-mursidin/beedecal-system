<?php  

require_once "action/DbClass.php";
$db = new ConfigClass();

$id_order = $_GET['id_order'];
$prov = $_GET['prov'];
$kab = $_GET['kab'];
$kec = $_GET['kec'];
?>
                        <div class="mb-3">
                          <label for="" class="form-label">Kurir</label>
                          <select name="kurir" id="kurir" class="form-select">
                            <optgroup label="PILIH KURIR">
                              <option value="pos">POS Indonesia (POS)</option>
                              <option value="lion">Lion Parcel (LION)</option>
                              <option value="jne">Jalur Nugraha Ekakurir (JNE)</option>
                              <option value="jnt">J&T Express (J&T)</option>
                            </optgroup>
                          </select>
                        </div>
                        <div class="mb-3">
                          <label for="" class="form-label">Provinsi</label>
                            <select name="prov" id="prov" class="form-select" onchange="viewKab(this.value)">
                              <option value="" hidden>PROVINSI</option>
                              <?php  $idprov = "";
                              $provs = $db->dataIndonesia("prov",null);
                              foreach($provs as $prov){
                                $select = $prov['province'] == $customer['prov'] ? 'selected="selected"' : '';
                                $idprov .= $prov['province'] == $customer['prov'] ? $prov['province_id'] : '';
                                echo '<option value="'.$prov['province_id'].'" '.$select.' >'.$prov['province'].'</option>';
                              }
                              ?>
                            </select>
                        </div>
                        <div class="mb-3">
                          <label for="" class="form-label">KABUPATEN/KOTA</label>
                          <select name="kabkota" id="kabkota" class="form-select" onchange="viewkec(this.value)" >
                              <option value="" hidden>KABUPATEN/KOTA</option>
                              <?php $idkab = "";
                              $kab_kota = $db->dataIndonesia("kab_kota",$idprov);
                              foreach ($kab_kota as $key => $kab){
                                $select = $kab['city_name'] == $customer['kab'] ? 'selected="selected"' : '';
                                $idkab .= $customer['kab'] == $kab["city_name"] ? $kab["city_id"] : "";
                                echo '<option value="'.$kab["city_id"].'" '.$select.'>'.$kab["city_name"].'</option>';
                              }
                              ?>
                            </select>
                        </div>
                        <div class="mb-3">
                          <label for="" class="form-label">KECAMATAN</label>
                          <select name="kec" id="kec" class="form-select" >
                            <option value="" hidden>KECAMATAN</option>
                            <?php  
                            $kecamatan = $db->dataIndonesia("kec",$idkab);
                            foreach ($kecamatan as $key => $kec){
                              $select = $kec["subdistrict_name"] == $customer['kec'] ? 'selected="selected"' : '';
                              echo '<option value="'.$kec["subdistrict_id"].'" '.$select.'>'.$kec["subdistrict_name"].'</option>';
                            }
                            ?>
                          </select>
                        </div>
                        <div class="mb-3">
                          <label for="" class="form-label">Kode Pos</label>
                          <input type="number" name="kode_pos" id="kode_pos" class="form-control" placeholder="Kode Pos">
                        </div>
                        <div class="mb-3">
                          <label for="" class="form-label">Alamat Lengkap</label>
                          <textarea name="alamat_lengkap" id="" rows="3" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                          <label for="" class="form-label">Berat</label>
                          <<input type="number" name="berat" step="0.01" id="berat" class="form-control">
                        </div>
                        <div class="mb-3">
                          <label for="" class="form-label">Ongkir</label>
                          <div class="input-group ">
                            <select name="resultcost" id="resut_pengiriman" onchange="showFee4(this.value)" class="form-control">
                              <option value="">PILIH PAKET</option>
                            </select>
                            <button class="btn btn-warning" type="button" id="button-addon2" onclick="showOngkir()">Cek</button>
                          </div>
                        </div>