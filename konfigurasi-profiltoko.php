<?php  
require_once "action/DbClass.php";

if($_SESSION['login_stiker_admin'] != true ){
  header('Location: auth-login');
  exit();
}

$db = new ConfigClass();

$userselect = $db->selectTable("user_galeri","id_user",$_SESSION['login_stiker_id']);
$row = mysqli_fetch_assoc($userselect);
$usernamelogin = $row['username_user']; $role = $row['level_user'];
$namatoko = $row['toko_user'];
$pemilik = $usernamelogin;
$alamat = "";
$email = "";
$telp = "";
$prov_show = "";
$kab_show = "";
$kec_show = "";
$kode_show = "";
$id = "";


  if($role == "Desainer"){
    header('Location: menunggu_designer');
    exit();
  }elseif($role == "Produksi"){
    header('Location: siap-cetak');
    exit();
  }elseif($role == "Pemasang"){
    header('Location: siap-dipasang');
    exit();
  }


// jika yang login buka owner ambil data owner dari id owner
if($row['id_owner'] == "0"){
  $id = $row['id_user'];
}else{
  $id = $row['id_owner'];
  $owner = $db->selectTable("user_galeri","id_user",$id);
  $rowouwner = mysqli_fetch_assoc($owner);
  $pemilik = $rowouwner['username_user'];
}

$alert = "";
if(isset($_POST['submit_toko'])){
  $namatokopost = $namatoko;
  $pemiliktokopost = $pemilik;
  $emailpost = $_POST['emailtoko'];
  $nopost = $db->formatNumber($_POST['notoko']);
  $alamatpost = $_POST['alamat'];
  $prov = $_POST['prov'];
  $kab = $_POST['kab'];
  $kec = $_POST['kec'];
  $kode_pos = $_POST['kode_pos'];
  $checktoko = $db->selectTable("store_galeri","id_owner",$id);
  if(mysqli_num_rows($checktoko) > 0){
    $update = $db->updateStore($namatokopost,$pemiliktokopost,$emailpost,$nopost,$alamatpost,$id,$prov,$kab,$kec,$kode_pos);
    if($update){
      $alert = "1";
    }else{
      $alert = "2";
    }
  }else{
    $insert = $db->InsertStore($namatokopost,$pemiliktokopost,$emailpost,$nopost,$alamatpost,$id,$prov,$kab,$kec,$kode_pos);
    if($insert){
      $alert = "1";
    }else{
      $alert = "2";
    }
  }
}

// select table toko dgn owner
$checktoko = $db->selectTable("store_galeri","id_owner",$id);
// jika table toko sudah ada tampilkan data table
if(mysqli_num_rows($checktoko) > 0){
  $rowtoko = mysqli_fetch_assoc($checktoko);
  $namatoko = $rowtoko['name_store'];
  $pemilik = $rowtoko['owner_store'];
  $alamat = $rowtoko['address_store'];
  $email = $rowtoko['email_store'];
  $telp = $rowtoko['telpn_store'];
  $prov_show = $rowtoko['prov_id'];
  $kab_show = $rowtoko['kab_id'];
  $kec_show = $rowtoko['kec_id'];
  $kode_show = $rowtoko['kode_pos'];
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>STIKER | PROFIL TOKO</title>
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

    <!-- Sweet Alert-->
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <!-- Kab -->
    <script>
      function viewKab(str) {
        $.ajax({
          type:'post',
          url:'api_kab_kota.php?prov_id='+str,
          success:function(hasil_kab){
            $("select[name=kab]").html(hasil_kab);
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
                  <h4 class="mb-sm-0">Profil Toko</h4>

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
                    <form action="konfigurasi-profiltoko" method="post" autocomplete="off">
                      <!-- <div class="row mb-3">
                          <label for="logo" class="col-md-2 col-form-label">Logo</label>
                          <div class="col-md-10">
                            <input class="form-control" name="logo" type="file" id="logo">
                          </div>
                      </div> -->
                      <div class="row mb-3">
                          <label for="toko" class="col-md-2 col-form-label">Nama</label>
                          <div class="col-md-10">
                            <input class="form-control" name="namatoko" type="text" value="<?= $namatoko ?>" id="toko" readonly>
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="pemilik_toko" class="col-md-2 col-form-label">Pemilik</label>
                          <div class="col-md-10">
                            <input class="form-control" type="text" name="pemiliktoko" value="<?= $pemilik ?>" id="pemilik_toko" readonly>
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="email" class="col-md-2 col-form-label">Email</label>
                          <div class="col-md-10">
                            <input class="form-control" type="email" name="emailtoko" value="<?= $email ?>" id="email" required>
                          </div>
                      </div>
                      <div class="row mb-2">
                          <label for="no" class="col-md-2 col-form-label">No. Telpn</label>
                          <div class="col-md-10">
                            <div class="input-group mb-3">
                              <span class="input-group-text" id="no">+62</span>
                              <input type="number" class="form-control" name="notoko" value="<?= $telp ?>" aria-describedby="no" required>
                            </div>
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="alamat" class="col-md-2 col-form-label">Alamat</label>
                          <div class="col-md-10">
                            <div class="row">
                              <div class="col-sm-3">
                                <select name="prov" id="" class="form-select" onchange="viewKab(this.value)" required>
                                  <option value="" hidden>PROVINSI</option>
                                  <?php  
                                    $provs = $db->dataIndonesia("prov",null);
                                    foreach($provs as $prov){
                                      $select = $prov['province_id'] == $prov_show ? 'selected="selected"' : '';
                                      echo '<option value="'.$prov['province_id'].'" '.$select.'>'.$prov['province'].'</option>';
                                    }
                                  ?>
                                </select>
                              </div>
                              <div class="col-sm-3">
                                <select name="kab" id="kab" class="form-select" onchange="viewkec(this.value)" required>
                                  <option value="" hidden>KABUPATEN/KOTA</option>
                                  <?php  
                                  if($prov_show != ""){
                                    $kab_kota = $db->dataIndonesia("kab_kota",$prov_show);
                                    foreach ($kab_kota as $key => $kab){
                                      $select = $kab['city_id'] == $kab_show ? 'selected="selected"' : '';
                                      echo '<option value="'.$kab["city_id"].'" '.$select.'>'.$kab["city_name"].'</option>';
                                    }
                                  }
                                  ?>
                                </select>
                              </div>
                              <div class="col-sm-3">
                                <select name="kec" id="" class="form-select" required>
                                  <option value="" hidden>KECEMATAN</option>
                                  <?php  
                                  if($kab_show != ""){
                                    $kecamatan = $db->dataIndonesia("kec",$kab_show);
                                    foreach ($kecamatan as $key => $kec){
                                      $select = $kec["subdistrict_id"] == $kec_show ? 'selected="selected"' : '';
                                      echo '<option value="'.$kec["subdistrict_id"].'" '.$select.'>'.$kec["subdistrict_name"].'</option>';
                                    }
                                  }
                                  ?>
                                </select>
                              </div>
                              <div class="col-sm-3">
                                <input type="number" value="<?= $kode_show ?>" name="kode_pos" id="" class="form-control" placeholder="KODE POS">
                              </div>
                              <div class="col-sm-12 mt-3">
                                <textarea class="form-control" name="alamat" id="alamat" rows="3" required><?= $alamat ?></textarea>
                              </div>
                            </div>
                          </div>
                      </div>
                      <button class="btn btn-primary" type="submit" name="submit_toko">Submit</button>
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
      }
    </script>

    <script src="assets/js/app.js"></script>
  </body>
</html>
<?php mysqli_close($db->conn) ?>