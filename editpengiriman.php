<?php  
require_once "action/DbClass.php";

if($_SESSION['login_stiker_admin'] != true ){
    header('Location: auth-login');
    exit();
}

$db = new ConfigClass();

$userselect = $db->selectTable("user_galeri","id_user",$_SESSION['login_stiker_id']);
$row = mysqli_fetch_assoc($userselect);
$usernamelogin = $row['username_user'];
$id = $row['id_owner'];
// jika yang login buka owner ambil data owner dari id owner
if($row['id_owner'] == "0"){
    $id = $row['id_user'];
}

$store = $db->selectTable("store_galeri","id_owner",$id);
$rowstore = mysqli_fetch_assoc($store);
$asal = $rowstore['kab_id'];

$spk = $_GET['spk'];
$from = $_GET['from'];
$order = $db->selectTable("data_pemesanan","code_order",$spk,"id_owner",$id);
if($from == "" || $spk == "" || mysqli_num_rows($order) == 0){
  header('Location: ambil-ditoko');
  exit();
}

function showCustomer($id_customer, $pengiriman=null, $id_order=null){
  global $db;
  // name customer
  $querydb = $db->selectTable("customer_stiker","id_customer",$id_customer);
  $rowdb=mysqli_fetch_assoc($querydb);

  // alamat customer
  $result['prov'] = $rowdb['prov_customer'];
  $result['kab'] = $rowdb['kota_kab_customer'];
  $result['kec'] = $rowdb['kec_customer'];
  $result['kodepos'] = $rowdb['kode_pos_customer'];
  $result['alamat'] = $rowdb['address_customer'];
  return $result;
}



if(isset($_POST['save_pengiriman'])){
  $id_orderr = $_POST['id_orderr'];
  $status_pengiriman = $_POST['status_pengiriman'] == "on" ? "Ya" : "Tidak";
  $pemasangan_status = $status_pengiriman == "Ya" ? "Tidak" : $_POST['status_pasang'];

  // detail pengiriman
  $kurir = $status_pengiriman == "Ya" ? $_POST['kurir'] : '';

  $prov_desti = "";
  if($status_pengiriman == "Ya" && $_POST['prov'] != ""){
    $func_prov = $db->dataIndonesia("prov",null);
    foreach($func_prov as $key => $prov){
      if($prov['province_id'] == $_POST['prov']){
        $prov_desti .= $prov['province'];
      }
    }
  }
  $kabkota_desti = "";
  if($status_pengiriman == "Ya" && $_POST['kabkota'] != ""){
    $func_kab = $db->dataIndonesia("kab_kota",$_POST['prov']);
    foreach($func_kab as $key => $kab){
      if($kab['city_id'] == $_POST['kabkota']){
        $kabkota_desti .= $kab['city_name'];
      }
    }
  }
  $kec_desti = "";
  if($status_pengiriman == "Ya" && $_POST['kec'] != ""){
    $func_kec = $db->dataIndonesia("kec",$_POST['kabkota']);
    foreach($func_kec as $key => $kec){
      if($kec['subdistrict_id'] == $_POST['kec']){
        $kec_desti .= $kec['subdistrict_name'];
      }
    }
  }

  $alamat_lengkap = $status_pengiriman == "Ya" ? $_POST['alamat_lengkap'] : '';
  $kode_pos = $status_pengiriman == "Ya" ? $_POST['kode_pos'] : '';
  $berat = $status_pengiriman == "Ya" ? $_POST['berat'] : '';
  $paket_ongkir = explode(" - ",$_POST['resultcost']);

  // detail pengiriman
  $cost = $status_pengiriman == "Ya" ? $paket_ongkir[0] : '';
  $name_paket = $status_pengiriman == "Ya" ? $paket_ongkir[1] : '';
  $etd = $status_pengiriman == "Ya" ? $paket_ongkir[2] : '';
  $cod = '';
  if($status_pengiriman == "Ya"){
    if($_POST['cod'] == "on"){
      $cod = "COD";
    }else{
      $cod = "Cash";
    }
  }

  if($pemasangan_status == "Ya"){
    $status_order = "Siap Dipasang";
    $next = $status_order == "Selesai Finishing" ? "Siap Dipasang" : $status_order;
    $updatesend = mysqli_query($db->conn, "UPDATE data_pemesanan SET status_pasang_order='$pemasangan_status',status_order='$status_order' WHERE id_order='$id_orderr'"); 
    
    if($updatesend){
      $_SESSION['alert'] = "1";
      header('Location: ambil-ditoko');
      if($from == "pengiriman"){
        header('Location: pengiriman');
  
      }
      exit();
    }
  }else{
    $updatesend = mysqli_query($db->conn, "UPDATE data_pemesanan SET status_pasang_order='$pemasangan_status',status_pengiriman_order='$status_pengiriman', kurir_pengiriman_order='$kurir', prov_send_order='$prov_desti', kab_send_order='$kabkota_desti', kec_send_order='$kec_desti', kode_pos_send_order='$kode_pos', alamat_lengkap_send_order='$alamat_lengkap', berat_send_order='$berat', ongkir_send_order='$cost', nama_paket_send_order='$name_paket', estimasi_send_order='$etd', ongkir_cod_order='$cod' WHERE id_order='$id_orderr'"); 
    
    if($updatesend){
      $_SESSION['alert'] = "1";
      header('Location: ambil-ditoko');
      if($from == "pengiriman"){
        header('Location: pengiriman');
  
      }
      exit();
    }
  }
}


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>STIKER | EDIT PENGIRIMAN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      content="APLIKASI CRM PERCETAKAN DAN STICKERART NO.1 INDONESIA"
      name="description"
    />
    <meta content="BEEDECAL" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico" />

    <!-- Bootstrap Css -->
    <link
      href="assets/css/bootstrap.min.css"
      id="bootstrap-style"
      rel="stylesheet"
      type="text/css"
    />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link
      href="assets/css/app.min.css"
      id="app-style"
      rel="stylesheet"
      type="text/css"
    />

    <!-- Kab -->
    <script>
      function viewKab(str) {
        $.ajax({
          type:'post',
          url:'api_kab_kota.php?prov_id='+str,
          success:function(hasil_kab){
            $("select[name=kabkota]").html(hasil_kab);
          }
        })
      }
    </script>
    <!-- end Kab -->

    <!-- Kec -->
    <script>
      function viewkec(str) {
        $.ajax({
          type:'post',
          url:'api_kecamatan.php?city_id='+str,
          success:function(hasil_kec){
            $("select[name=kec]").html(hasil_kec);
          }
        })
      }
    </script>
    <!-- end Kec -->

    <!-- ongkir -->
    <script>
      function showOngkir(){
        var kurir = document.getElementById("kurir").value;
        var asal = "<?= $asal ?>";
        var tujuan = document.getElementById("kec").value;
        var berat = document.getElementById("berat").value;
        $.ajax({
          type:'post',
          url:'count_ongkir.php?kurir='+kurir+'&asal='+asal+'&tujuan='+tujuan+'&berat='+berat,
          success:function(hasil_costs){
            $("select[name=resultcost]").html(hasil_costs);
          }
        })
      }
    </script>
    <!-- end ongkir -->

    

    <script type="text/javascript">
            function showTime() {
                var a_p = "";
                var today = new Date();
                var curr_hour = today.getHours();
                var curr_minute = today.getMinutes();
                var curr_second = today.getSeconds();
                if (curr_hour < 12) {
                    a_p = "AM";
                } else {
                    a_p = "PM";
                }
                if (curr_hour == 0) {
                    curr_hour = 12;
                }
                if (curr_hour > 12) {
                    curr_hour = curr_hour - 12;
                }
                curr_hour = checkTime(curr_hour);
                curr_minute = checkTime(curr_minute);
                curr_second = checkTime(curr_second);
                document.getElementById('time').innerHTML=curr_hour + ":" + curr_minute + ":" + curr_second + " " + a_p;
            }
            
            function checkTime(i) {
                if (i < 10) {
                    i = "0" + i;
                }
                return i;
            }
            setInterval(showTime, 500);         
    </script>
  </head>

  <body data-sidebar="dark">
    <!-- <body data-layout="horizontal" data-topbar="dark"> -->

    <!-- Begin page -->
    <div id="layout-wrapper">
      <?php require_once "header.php" ?>
      

      <!-- ========== Left Sidebar Start ========== -->
      <?php require_once "side_bar.php" ?>
      <!-- Left Sidebar End -->

      <!-- ============================================================== -->
      <!-- Start right Content here -->
      <!-- ============================================================== -->
      <div class="main-content">
        <div class="page-content">
          <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
              <div class="col-12">
                <div
                  class="
                    page-title-box
                    d-sm-flex
                    align-items-center
                    justify-content-between
                  "
                >
                  <h4 class="mb-sm-0">Edit Pengiriman</h4>

                  <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                      <li class="breadcrumb-item">
                        <span><b><?= date('D').", ".date("Y-m-d") ?></b> | <b id="time"></b></span>  
                      </li>
                    </ol>
                  </div>
                </div>
              </div>
            </div>
            <!-- end page title -->
            <form action="" method="post">
              <?php  
              $order = $db->selectTable("data_pemesanan","code_order",$spk,"id_owner",$id);
              $roworder = mysqli_fetch_assoc($order); 
              $customer = $roworder['id_customer'];
              $showcustomer =  showCustomer($customer);
              if($from == "pengiriman"){
                $showcustomer = [
                  'prov' => $roworder['prov_send_order'],
                  'kab' => $roworder['kab_send_order'],
                  'kec' => $roworder['kec_send_order'],
                  'kodepos' => $roworder['kode_pos_send_order'],
                  'alamat' => $roworder['alamat_lengkap_send_order']
                ];
              }
              ?>
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex">
                        Status Pengiriman&nbsp;<span class="form-check form-switch"><input class="form-check-input" value="on" name="status_pengiriman" type="checkbox" role="switch" id="flexSwitchCheckChecked" onclick="detailPengiriman()" <?= $from == "ambil_ditoko" ? '' : 'checked' ?>></span>
                      </div>
                      <div class="row g-3 mt-3">
                        <label for="" class="col-sm-2 col-form-label">Pemasangan</label>
                        <div class="col-sm-10">
                          <?php $disabled = $from == "pengiriman" ? "disabled" : "" ?>
                          <select name="status_pasang" id="status_pasang" class="form-select" <?= $disabled ?>>
                            <?php 
                            $v = $from == "pengiriman" ? "Tidak" : $roworder['status_pasang_order'];
                            $option = array("Ya","Tidak");
                            foreach($option as $ops){
                              $select = $v == $ops ? 'selected="selected"' : '';
                            ?>
                            <option value="<?= $ops ?>" <?= $select ?>><?= $ops ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title">Detail Pengiriman</div>
                      <div id="detailpengiriman" <?= $from == "ambil_ditoko" ? 'style="display: none;"' : '' ?>>
                        <!-- kurir -->
                        <div class="row g-3 mb-3">
                          <!-- label kurir -->
                          <label for="" class="col-sm-2 col-form-label">Kurir</label>
    
                          <!-- kurir -->
                          <div class="col-sm-10">
                            <select name="kurir" id="kurir" class="form-select">
                              <option value="" hidden>PiLIH KURIR</option>
                              <?php 
                              function nameKurir($kode){
                                switch($kode){
                                  case "pos":
                                    return "POS Indonesia (POS)";
                                    break;
                                  case "lion":
                                    return "Lion Parcel (LION)";
                                    break;
                                  case "jne":
                                    return "Jalur Nugraha Ekakurir (JNE)";
                                    break;
                                  case "jnt":
                                    return "J&T Express (J&T)";
                                    break;
                                }
                              }
                              $kur = array("pos","lion","jne","jnt");
                              foreach($kur as $k){
                                $select = $k == $roworder['kurir_pengiriman_order'] ? 'selected="selected"' : '';
                              ?>
                              <option value="<?= $k ?>" <?= $select ?>><?= nameKurir($k) ?></option>
                              <?php } ?>
                            </select>
                          </div>
                          <!-- end kurir -->
                        </div>
                        <!-- end kurir -->
    
                        <!-- data customer -->
                        <div class="row g-3 mb-3" id="data_customer">
                          <label for="" class="col-sm-2 col-form-label">Tujuan Pengiriman</label>
    
                          <div class="col-sm-3">
                            <select name="prov" id="prov" class="form-select" onchange="viewKab(this.value)">
                              <option value="" hidden>PROVINSI</option>
                              <?php  $idprov = "";
                              $provs = $db->dataIndonesia("prov",null);
                              foreach($provs as $prov){
                                $select = $prov['province'] == $showcustomer['prov'] ? 'selected="selected"' : '';
                                $idprov .= $prov['province'] == $showcustomer['prov'] ? $prov['province_id'] : '';
                                echo '<option value="'.$prov['province_id'].'" '.$select.'>'.$prov['province'].'</option>';
                              }
                              ?>
                            </select>
                          </div>
    
                          <div class="col-sm-3">
                            <select name="kabkota" id="kabkota" class="form-select" onchange="viewkec(this.value)">
                              <option value="" hidden>KABUPATEN/KOTA</option>
                              <?php $idkab = "";
                              $kab_kota = $db->dataIndonesia("kab_kota",$idprov);
                              foreach ($kab_kota as $key => $kab){
                                $select = $kab['city_name'] == $showcustomer['kab'] ? 'selected="selected"' : '';
                                $idkab .= $kab['city_name'] == $showcustomer['kab'] ? $kab["city_id"] : "";
                                echo '<option value="'.$kab["city_id"].'" '.$select.'>'.$kab["city_name"].'</option>';
                              }
                              ?>
                            </select>
                          </div>
    
                          <div class="col-sm-3">
                            <select name="kec" id="kec" class="form-select">
                            <option value="" hidden>KECAMATAN</option>
                              <?php  $idkec = "";
                              $kecamatan = $db->dataIndonesia("kec",$idkab);
                              foreach ($kecamatan as $key => $kec){
                                $select = $kec["subdistrict_name"] == $showcustomer['kec'] ? 'selected="selected"' : '';
                                $idkec .= $kec["subdistrict_name"] == $showcustomer['kec'] ? $kec['subdistrict_id'] : '';
                                echo '<option value="'.$kec["subdistrict_id"].'" '.$select.'>'.$kec["subdistrict_name"].'</option>';
                              }
                              ?>
                            </select>
                          </div>
    
                          <div class="col-sm-1">
                            <input type="number" name="kode_pos" id="kode_pos" class="form-control" placeholder="Kode Pos" value="<?= $showcustomer['kodepos'] ?>">
                          </div>
    
                          <label for="" class="col-sm-2 col-form-label">Alamat Lengkap</label>
                          <div class="col-sm">
                            <textarea name="alamat_lengkap" id="" rows="3" class="form-control"><?= $showcustomer['alamat'] ?></textarea>
                          </div>
    
                        </div>
                        <!-- end data customer -->
    
                        <!-- raja ongkir -->
                        <div class="row g-3 mb-3">
    
                          <label for="" class="col-sm-2 col-form-label">Berat</label>
                          <div class="col-sm-10">
                            <div class="input-group">
                              <input type="number" name="berat" step="0.01" id="berat" class="form-control" value="<?= $roworder['berat_send_order'] ?>">
                              <span class="input-group-text">gram</span>
                            </div>
                          </div>
                            
                          <label for="" class="col-sm-2 col-form-label">Ongkos Kirim</label>
                          <div class="col-sm-10">
                            <div class="row">
                              <div class="col-sm-12">
                                <div class="input-group">
                                  <select name="resultcost" id="resut_pengiriman" onchange="showFee4(this.value)" class="form-control">
                                    <option value="">PILIH PAKET</option>
                                    <?php  
                                    require_once "action/rajaOngkir.php";
                                    $rajaongkir = new RajaOngkir();
      
                                    $data = $rajaongkir->checkOngkir($roworder['kurir_pengiriman_order'], $asal, $idkec, $roworder['berat_send_order']);
                                    foreach($data->costs as $d){
                                      $select = $d->service == $roworder['nama_paket_send_order'] ? 'selected="selected"' : '' ;
                                      echo '<option '.$select.' value="'.$d->cost[0]->value.' - '.$d->service.' - '.$d->cost[0]->etd.'">Rp.'.number_format($d->cost[0]->value,2,",",".").' (Paket: '.$d->service.' Estimasi: '.$d->cost[0]->etd. ')</option>';
                                    }
                                    ?>
                                  </select>
    
                                  <button class="btn btn-warning" type="button" id="button-addon2" onclick="showOngkir()">Cek</button>
                                </div>
                              </div>
                              <div class="col-sm-12 mt-2">
                                <div class="form-check">
                                  <?php  
                                  $checked = $roworder['ongkir_cod_order'] == "COD" ? "checked" : '';
                                  ?>
                                  <input class="form-check-input" name="cod" type="checkbox" value="on" <?= $checked ?> id="flexCheckChecked">
                                  <label class="form-check-label" for="flexCheckChecked">
                                    Cash on delivery
                                  </label>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- end raja ongkir -->
                      </div>
                      <input type="hidden" name="status_order" value="<?= $roworder['status_order'] ?>">
                      <input type="hidden" name="id_orderr" value="<?= $roworder['id_order'] ?>">
                      <button type="submit" name="save_pengiriman" class="btn btn-success">Simpan</button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- end contant -->
            </form>
            <!-- contant -->

          </div>
          <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

        <!-- Footer -->
        <footer class="footer">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-6">
                <script>
                  document.write(new Date().getFullYear());
                </script>
                © BEEDECAL
              </div>
              <div class="col-sm-6">
                <div class="text-sm-end d-none d-sm-block">
                  Crafted with <i class="mdi mdi-heart text-danger"></i> by
                  GALERIIDE
                </div>
              </div>
            </div>
          </div>
        </footer>
        <!-- Footer -->
      </div>
      <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->
    <?php include_once "rightside.php" ?>
    <!-- /Right-bar -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- JAVASCRIPT -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>

    <script src="assets/js/app.js"></script>
    <script>
      function detailPengiriman(){
        var str = document.getElementById("flexSwitchCheckChecked")
        var detail = document.getElementById("detailpengiriman");
        if(str.checked == true){
          detail.style.display = "block";
          $("#status_pasang").val("Tidak");
          $("#status_pasang").attr("disabled","true");
          $("#kurir").attr("required","");
          $("#berat").attr("required","");
          $("#resut_pengiriman").attr("required","");
        }else{
          detail.style.display = "none";
          $("#status_pasang").val("Ya");
          $("#status_pasang").removeAttr("disabled","true")
          $("#kurir").removeAttr("required","");
          $("#berat").removeAttr("required","");
          $("#resut_pengiriman").removeAttr("required","");
        }
      }
    </script>
  </body>
</html>
<?php mysqli_close($db->conn) ?>