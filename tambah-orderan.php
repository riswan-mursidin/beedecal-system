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
$alert = $_SESSION['alert'];
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>STIKER | PEMESANAN BARU</title>
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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script>
      function showSubJenis() {
        var p = document.getElementById("jenisp").value;
        var pr = document.getElementById("jenispr").value;
        $.ajax({
          type:'post',
          url:'data_produk_kategori.php?jenisp='+p+'&jenispr='+pr+'&id='+<?= $id ?>,
          success:function(hasil_views){
            $("select[name=kategori_produk").html(hasil_views);
          }
        })
      }
    </script>
    <script>
      function showVarian(){
        var str = document.getElementById("kategori_type").value;
        var pr = document.getElementById("jenispr").value;
        $.ajax({
          type:'post',
          url:'data_varian_type.php?type='+str+'&id='+<?= $id ?>+'&jenispr='+pr,
          success:function(hasil_views){
            $("select[name=varian_harga").html(hasil_views);
          }
        })
      }
    </script>

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
                  <h4 class="mb-sm-0">Tambah Pemesanan</h4>

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
            <!-- card order -->
            <form action="">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title">Informasi Pemesanan</div>
                      <div class="row g-3 mt-3">
                        <div class="col-md-3">
                          <label for="jenisp" class="form-label">Jenis Pesanan</label>
                          <select name="jenis_pesanann" id="jenisp" class="form-select">
                            <option value="Mobil">Mobil</option>
                            <option value="Motor">Motor</option>
                            <option value="Other">Lainnya</option>
                          </select>
                        </div>
                        <div class="col-md-3">
                          <label for="jenispr" class="form-label">Jenis Produk</label>
                          <select name="Jenis_produk" id="jenispr" class="form-select">
                            <option value="Custom">Custom</option>
                            <option value="No Custom">Produk <?= $db->nameFormater($rowstore['name_store']) ?></option>
                          </select>
                        </div>
                        <div class="col-md-3">
                          <label for="kategori_type" class="form-label">Produk</label>
                          <div class="input-group">
                            <select onchange="showVarian()" name="kategori_produk" id="kategori_type" class="form-select" required>
                              <option value="" hidden>Empty</option>
                            </select>
                            <button class="btn btn-warning" onclick="showSubJenis()" data-bs-toggle="tooltip" data-bs-placement="top" title="Views" type="button"><i class="ri-eye-line"></i></button>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="harga" class="form-label">Harga Varian</label>
                          <select name="varian_harga" id="harga" class="form-select">
                            <option value="">PILIH VARIAN</option>
                          </select>
                        </div>
                        <div class="col-md-3">
                          <label for="" class="form-label">Desain</label>
                          <select name="" id="" class="form-select">
                            <option value="Ya">YA</option>
                            <option value="Tidak">Tidak</option>
                          </select>
                        </div>
                        <div class="col-md-3">
                          <label for="" class="form-label">Desain</label>
                          <select name="" id="" class="form-select">
                            
                          </select>
                        </div>
                        <div class="col-md-3">
                          <label for="" class="form-label">Desain</label>
                          <select name="" id="" class="form-select">
                            
                          </select>
                        </div>
                        <div class="col-md-3">
                          <label for="" class="form-label">Desain</label>
                          <select name="" id="" class="form-select">
                            
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title">Detail Pemesanan</div>
                      <div class="row g-3">
                        <label for="pelanggan" class="col-sm-2 col-form-label">Pelanggan</label>
                        <div class="col-sm-10">
                          <select name="pangggan" style="height: 100px; font-sixe: 20px" class="js-example-placeholder-single form-control">
                            <?php  
                              $cs = $db->selectTable("customer_stiker","id_owner",$id);
                              while($rowcs=mysqli_fetch_assoc($cs)){
                            ?>
                            <option value="<?= $rowcs['id_customer'] ?>"><?= $rowcs['username_customer'] ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </form>
            <!-- end card order -->
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

    <script src="assets/js/app.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
      $(".js-example-placeholder-single").select2({
          placeholder: "PILIH PELANGGAN",
          allowClear: true
      });
    </script>
  </body>
</html>
<?php mysqli_close($db->conn) ?>