<?php  
require_once "action/DbClass.php";

if($_SESSION['login_stiker_admin'] != true ){
  header('Location: auth-login');
  exit();
}

$db = new ConfigClass();

$userselect = $db->selectTable("user_galeri","id_user",$_SESSION['login_stiker_id']);
$row = mysqli_fetch_assoc($userselect);
$id = $row['id_owner'];
// jika yang login buka owner ambil data owner dari id owner
if($row['id_owner'] == "0"){
  $id = $row['id_user'];
}
$alert = "";

$edit = $_GET['edit'];

$readonly = $edit != "" ? "readonly" : "";

$editselect = $db->selectTable("customer_stiker","username_customer",$edit);
$rowedit = mysqli_fetch_assoc($editselect);

if($edit != ""){
  if(mysqli_num_rows($editselect) == 0){
    header('Location: konfigurasi-pelanggantoko');
    exit();
  }
}

if(isset($_POST['submit_pelanggan'])){
  // var data pribadi
  $username = strtolower($_POST['username']);
  $email = $_POST['email'];
  $fullname = $_POST['fullname'];
  $no = $db->formatNumber($_POST['no']);
  $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

  // var alamat
  $nameprov = "";
  $idprov = $_POST['province'];
  $func_prov = $db->dataIndonesia("prov",null);
  foreach($func_prov as $key => $prov){
    if($prov['province_id'] == $idprov){
      $nameprov = $prov['province_id'];
    }
  }
  $namekab = "";
  $idkab = $_POST['kabkota'];
  $func_kab = $db->dataIndonesia("kab_kota",$idprov);
  foreach($func_kab as $key => $kab){
    if($kab['city_id'] == $idkab){
      $namekab = $kab['city_id'];
    }
  }
  $namekec = "";
  $idkec = $_POST['kec'];
  $func_kec = $db->dataIndonesia("kec",$idkab);
  foreach($func_kec as $key => $kec){
    if($kec['subdistrict_id'] == $idkec){
      $namekec = $kec['subdistrict_id'];
    }
  }
  $kodepos = $_POST['kodepos'];
  $alamat = $_POST['alamat'];

  if($edit == ""){
    $checkusername = $db->selectTable("customer_stiker","username_customer",$username);
    if(mysqli_fetch_assoc($checkusername) > 0){
      $alert = "3";
    }else{
      $put = $db->insertCustomer($fullname,$username,$pass,$email,$no,$nameprov,$namekab,$namekec,$kodepos,$alamat,$id);
      if($put){
        $_SESSION['alert'] = "1";
        header('Location: konfigurasi-pelanggantoko');
        exit();
      }
    }
  }else{
    $status = $_POST['status'];
    $update = $db->updateCustomer($edit,$fullname,$no,$status,$nameprov,$namekab,$namekec,$kodepos,$alamat);
    if($update){
      $_SESSION['alert'] = "1";
      header('Location: konfigurasi-pelanggantoko');
      exit();
    }
  }
}


?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>STIKER | PELANGGAN</title>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script>
      function viewKab(str) {
        $.ajax({
          type:'post',
          url:'api_kab_kota.php?prov_id='+str,
          success:function(hasil_kab){
            $("select[name=kabkota").html(hasil_kab);
          }
        })
      }
    </script>
    <script>
      function viewkec(str) {
        $.ajax({
          type:'post',
          url:'api_kecamatan.php?city_id='+str,
          success:function(hasil_kec){
            $("select[name=kec").html(hasil_kec);
          }
        })
      }
    </script>
    <!-- Sweet Alert-->
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
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
      <div id="flash" data-flash="<?= $alert ?>"></div>
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
                  <h4 class="mb-sm-0">Edit Pelanggan</h4>

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
            <div class="row">
              <div class="col-xl-12">
                <div class="card">
                  <div class="card-body">
                    <form action="" method="post" autocomplete="off">
                      <div class="row g-3 mb-3">
                        <div class="col-md-12">
                          <label for="" class="form-label">Data Pribadi</label>
                        </div>
                        <hr>
                        <div class="col-md-3">
                          <div class="form-floating">
                            <input placeholder="floot" type="text" <?= $readonly ?> class="form-control" name="username" value="<?= $edit != "" ? $rowedit['username_customer'] : "" ?>" id="username" style="text-transform: lowercase;" required>
                            <label for="username">Username</label>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-floating">
                            <input placeholder="floot" type="email" <?= $readonly ?> class="form-control" value="<?= $edit != "" ? $rowedit['email_customer'] : "" ?>" name="email" id="email">
                            <label for="email">Email</label>
                          </div>
                        </div>
                        <div class="col-md-5">
                          <div class="form-floating">
                            <input placeholder="floot" type="text" value="<?= $edit != "" ? $rowedit['name_customer'] : "" ?>" class="form-control" name="fullname" id="fullname" required>
                            <label for="fullname">Nama Lengkap</label>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-floating">
                            <input placeholder="floot" type="number" value="<?= $edit != "" ? $rowedit['telpn_customer'] : "" ?>" class="form-control" name="no" id="no" required>
                            <label for="no">No. Telpn</label>
                          </div>
                        </div>
                        <?php if($edit == ""){ ?>
                        <div class="col-md-3">
                          <div class="form-floating">
                            <input placeholder="floot" type="password" class="form-control" name="password" value="123456" id="password" required>
                            <label for="password">Password</label>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-floating">
                            <input placeholder="floot" type="password" class="form-control" name="passwordconf" value="123456" id="passwordconf" required>
                            <label for="passwordconf">Konfirmasi Password</label>
                          </div>
                        </div>
                        <?php }else{ ?>
                          <div class="col-md-6">
                          <div class="form-floating">
                            <select name="status" id="statuscus" class="form-select" aria-label="Floating label select example" required>
                              <option value="">--PILIH STATUS--</option>
                              <?php  
                              $value = $edit != "" ? $rowedit['status_customer'] : "";
                              $status = array("Aktif","Tidak Aktif");
                              foreach($status as $stat){
                                $select = $value == $stat ? 'selected="selected"' : "";
                              ?>
                              <option value="<?= $stat ?>" <?= $select ?>><?= $stat ?></option>
                              <?php } ?>
                            </select>
                            <label for="statuscus">Status</label>
                          </div>
                        </div>
                        <?php } ?>
                        <div class="col-md-12 mt-4">
                          <label for="" class="form-label">Data Alamat</label>
                        </div>
                        <hr>
                        <div class="col-md-3">
                          <div class="form-floating">
                            <select class="form-select" name="province" id="province" onchange="viewKab(this.value)" aria-label="Floating label select example" required>
                              <option selected>--PILIH PROVINSI--</option>
                              <?php
                              $idprov = $edit != "" ? "" : 0;
                              $value = $edit != "" ? $rowedit['prov_customer'] : "";
                              $provs = $db->dataIndonesia("prov",null);
                              foreach($provs as $key => $prov){
                                $select = $value == $prov['province'] ? 'selected="selected"' : "";
                                $idprov .= $value == $prov['province'] ? $prov["province_id"] : "";
                                echo '<option value="'.$prov["province_id"].'" '.$select.'>'.$prov["province"].'</option>';
                              }
                              ?>
                            </select>
                            <label for="province">Provinsi</label>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-floating">
                            <select class="form-select" name="kabkota" id="kabkota" onchange="viewkec(this.value)" aria-label="Floating label select example" required>
                              <option value="">--PILIH KAB/KOTA--</option>
                              <?php  
                              $idkab = "";
                              $value = $edit != "" ? $rowedit['kota_kab_customer'] : "";
                              $kab_kota = $db->dataIndonesia("kab_kota",$idprov);
                              foreach ($kab_kota as $key => $kab){
                                $select = $value == $kab["city_name"] ? 'selected="selected"' : "";
                                $idkab .= $value == $kab["city_name"] ? $kab["city_id"] : "";
                                echo '<option value="'.$kab["city_id"].'" '.$select.'>'.$kab["city_name"].'</option>';
                              }
                              ?>
                            </select>
                            <label for="kabkota">Kab/Kota</label>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-floating">
                            <select class="form-select" name="kec" id="kec" aria-label="Floating label select example" required>
                              <option value="">--PILIH KECAMATAN--</option>
                              <?php 
                              $value = $edit != "" ? $rowedit['kec_customer'] : "";
                              $kecamatan = $db->dataIndonesia("kec",$idkab);
                              foreach ($kecamatan as $key => $kec){
                                $select = $value == $kec["subdistrict_name"] ? 'selected="selected"' : "";
                                echo '<option value="'.$kec["subdistrict_id"].'" '.$select.'>'.$kec["subdistrict_name"].'</option>';
                              }
                              ?>
                            </select>
                            <label for="kec">Kecamatan</label>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-floating">
                            <input placeholder="floot" type="number" class="form-control" value="<?= $edit != "" ? $rowedit['kode_pos_customer'] : "" ?>" name="kodepos" id="kodepos" required>
                            <label for="kodepos">Kode Pos</label>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-floating">
                            <textarea class="form-control" placeholder="Leave a comment here" name="alamat" id="alamat" style="height: 100px"><?= $edit != "" ? $rowedit['address_customer'] : "" ?></textarea>
                            <label for="alamat">Alamat Lengkap</label>
                          </div>
                        </div>
                      </div>
                      <button class="btn btn-primary" type="submit" name="submit_pelanggan">Submit</button>
                    </form>
                  </div>
                </div>
              </div> <!-- end col -->
            </div>
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
                  var password = document.getElementById("password")
                  var confirm_password = document.getElementById("passwordconf");

                  function validatePassword(){
                    if(password.value != confirm_password.value) {
                      confirm_password.setCustomValidity("Passwords Tidak Sama");
                    } else {
                      confirm_password.setCustomValidity('');
                    }
                  }

                  password.onchange = validatePassword;
                  confirm_password.onkeyup = validatePassword;
                </script>
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
    <div class="right-bar">
      <div data-simplebar class="h-100">
        <div class="rightbar-title d-flex align-items-center px-3 py-4">
          <h5 class="m-0 me-2">Settings</h5>

          <a href="javascript:void(0);" class="right-bar-toggle ms-auto">
            <i class="mdi mdi-close noti-icon"></i>
          </a>
        </div>

        <!-- Settings -->
        <hr class="mt-0" />
        <h6 class="text-center mb-0">Choose Layouts</h6>

        <div class="p-4">
          <div class="form-check form-switch mb-3">
            <input
              class="form-check-input theme-choice"
              type="checkbox"
              id="light-mode-switch"
              checked
            />
            <label class="form-check-label" for="light-mode-switch"
              >Light Mode</label
            >
          </div>

          <div class="form-check form-switch mb-3">
            <input
              class="form-check-input theme-choice"
              type="checkbox"
              id="dark-mode-switch"
              data-bsStyle="assets/css/bootstrap-dark.min.css"
              data-appStyle="assets/css/app-dark.min.css"
            />
            <label class="form-check-label" for="dark-mode-switch"
              >Dark Mode</label
            >
          </div>

          <div class="form-check form-switch mb-5">
            <input
              class="form-check-input theme-choice"
              type="checkbox"
              id="rtl-mode-switch"
              data-appStyle="assets/css/app-rtl.min.css"
            />
            <label class="form-check-label" for="rtl-mode-switch"
              >RTL Mode</label
            >
          </div>
        </div>
      </div>
      <!-- end slimscroll-menu-->
    </div>
    <!-- /Right-bar -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- modal -->
      <div class="modal" tabindex="-1" id="modal_crop">
        <div class="modal-dialog" style="max-width: 1000px !important;">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title">Crop Foto</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="img-container">
                  <div class="row">
                      <div class="col-md-8 col-11">
                        <img src="" id="sample_image" class="img-fluid mx-auto d-block" style="max-width: 100%; display:block" alt="">
                      </div>
                  </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="crop_and_upload" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
          </div>
        </div>
      </div>

    <!-- JAVASCRIPT -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>

    <!-- Sweet Alerts js -->
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>

    <script>
      var flash = $('#flash').data('flash');
      if(flash == "1"){
        Swal.fire({
          title:"Berhasil!",
          text:"Data Tersimpan!",
          icon:"success",
        })
      }else if(flash == "2"){
        Swal.fire({
          title:"Gagal!",
          text:"Data Tidak Tersimpan!",
          icon:"error",
        })
      }else if(flash == "3"){
        Swal.fire({
          title:"Gagal!",
          text:"Username Sudah Ada!",
          icon:"error",
        })
      }else if(flash == "4"){
        Swal.fire({
          title:"Gagal!",
          text:"Email Sudah Ada!",
          icon:"error",
        })
      }
    </script>
    <script>
      document.querySelector('#username').addEventListener('keydown', function(e) {

      if (e.which === 32) {
          e.preventDefault();
      }
    });
    </script>

    <script src="assets/js/app.js"></script>
  </body>
</html>
<?php mysqli_close($db->conn) ?>