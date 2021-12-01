<?php  
require_once "action/DbClass.php";

if($_SESSION['login_stiker_admin'] != true ){
  header('Location: auth-login');
  exit();
}

$db = new ConfigClass();

$userselect = $db->selectTable("user_galeri","id_user",$_SESSION['login_stiker_id']);
$row = mysqli_fetch_assoc($userselect);
$usernamelogin = $row['username_user']
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>STIKER | PRODUCT</title>
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
                  <h4 class="mb-sm-0">Tambah Produk</h4>

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
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-body">
                    <form action="" method="post" autocomplete="off" style="padding: 20px;">
                      <div class="card-title mb-3">Upload Produk</div>
                      <div class="row mb-4">
                        <input type="file" onchange="previewImage()" name="foto_produk" id="upload_image" accept="image/png,Image/jpeg" class="d-none">
                        <label for="upload_image" style="cursor: pointer;" class="col-12">
                          <img id="preview" style="display: block;margin-left: auto;margin-right: auto; max-width:180px;" src="assets/images/produk/img.jpg" alt="">
                        </label>
                        <label for="upload_image" style="cursor: pointer;" class="col-12 mt-2">
                          <span class="text-info" style="display: block;text-align: center;font-size: 1em;">Ubah Foto</span>
                        </label>
                      </div>
                      <hr>
                      <div class="card-title mb-3">Informasi Produk</div>
                      <div class="row g-3">
                        <div class="col-sm-3">
                          <label for="name" class="form-label">Nama Produk</label>
                          <p style="font-size: 12px;">
                            Cantumkan min. 40 karakter agar semakin<br>menarik dan mudah ditemukan oleh<br>pembeli, terdiri dari jenis produk, merek,<br>dan keterangan seperti warna, bahan, atau tipe.
                          </p>
                        </div>
                        <div class="col-sm-9">
                          <input type="text" class="form-control">
                        </div>
                        <div class="col-sm-3">
                          <label for="" class="form-label">Kategori</label>
                        </div>
                        <div class="col-sm-9">
                          <select name="" id="" class="form-select">
                            <option value="">--PILIH kATEGORI--</option>
                          </select>
                        </div>
                      </div>
                      <hr>
                      <div class="card-title mb-3">Detail Produk</div>
                      <div class="row g-3">
                        <div class="col-sm-3">
                          <label for="" class="form-label">Deskripsi Produk</label>
                            <p style="font-size: 12px;">
                              Cantumkan min. 40 karakter agar semakin<br>menarik dan mudah ditemukan oleh<br>pembeli, terdiri dari jenis produk, merek,<br>dan keterangan seperti warna, bahan, atau tipe.
                            </p>
                        </div>
                        <div class="col-sm">
                          <textarea name="" id="" rows="4" class="form-control"></textarea>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <!-- end page title -->
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
    <script>
        function previewImage() {
          var oFReader = new FileReader();
          oFReader.readAsDataURL(document.getElementById("upload_image").files[0]);
        
          oFReader.onload = function(oFREvent) {
            // document.getElementById("image-preview").style.display = "block";
            document.getElementById("preview").src = oFREvent.target.result;
          };
        };
    </script>
  </body>
</html>
<?php mysqli_close($db->conn) ?>