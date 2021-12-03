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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    
    <script>
      function namaWarna() {
        var val = document.getElementById("js-example-basic-multiple");
        var warna = "";
        var n = 0;
        for(var i = 0; i<val.options.length; i++){
          if(val.options[i].selected === true){
              n += 1; 
              warna += '<tr>\
                          <td scope="row">'+n+'</td>\
                          <td>'+val.options[i].value+'</td>\
                          <td>\
                            <input type="file" onchange="previewWarna()" name="produk[]" id="upload_image'+n+'" accept="image/png,Image/jpeg" class="d-none">\
                            <label for="upload_image'+n+'" style="cursor: pointer;" class="col-12">\
                              <img id="warna'+n+'" style="max-width:100px;" src="assets/images/produk/img.jpg" alt="">\
                            </label>\
                          </td>\
                        </tr>';
          }
        }
        if(n != 0){
          // document.getElementById("jum_color").value = n;
          document.getElementById("d-warna").style.display = "block";
          document.getElementById("s-warna").innerHTML = warna;
        }else{
          document.getElementById("d-warna").style.display = "none";
        }
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
                          <span class="text-info" style="display: block;text-align: center;font-size: 1em;">Foto Depan</span>
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
                            <option value="1"></option>
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
                        <div class="col-sm-9">
                          <textarea name="" id="" rows="4" class="form-control"></textarea>
                        </div>
                        <div class="col-sm-3">
                          <label for="" class="form-label">Jumlah Stok</label>
                        </div>
                        <div class="col-sm-9">
                          <input type="number" name="stok" id="" class="form-control">
                        </div>
                        <div class="col-sm-3">
                          <label for="" class="form-label">Varian Warna</label>
                        </div>
                        <div class="col-12 col-sm">
                          <div class="input-group">
                            <select class="form-select" id="js-example-basic-multiple" name="warna[]" multiple="multiple">
                              <option value="Merah">Merah</option>
                              <option value="Biru">Biru</option>
                              <option value="Kuning">Kuning</option>
                              <option value="Hijau">Hijau</option>
                              <option value="Putih">Putih</option>
                              <option value="Hitam">Hitam</option>
                            </select>
                            <button type="button" style="height: 2.1rem;" class="btn btn-outline-primary" onclick="namaWarna()">Submit</button>
                          </div>
                          <div style="display: none;" id="d-warna">
                            <!-- <input type="number" id="jum_color" > -->
                            <table class="table table-hover mt-2">
                              <thead>
                                <tr>
                                  <th scope="col">#</th>
                                  <th scope="col">Warna</th>
                                  <th scope="col">Gambar</th>
                                </tr>
                              </thead>
                              <tbody id="s-warna">
                              </tbody>
                            </table>
                          </div>
                        </div>
                        <!-- <div class="col-sm-3">
                          <label for="" class="form-label">Jumlah Warna</label>
                        </div> -->
                        <!-- <div class="col-sm-9">
                          <input type="number" name="warna" onkeyup="namaWarna(this.value)" id="" class="form-control">
                        </div>
                        <div id="show">
                        </div>
                          <div class="col-sm-3">
                            <label for="" class="form-label">Warna</label>
                          </div>
                          <div class="col-sm">
                            
                          </div> -->
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
    <script>
      function previewWarna(){
        var img_name = "upload_image1";
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById(img_name).files[0]);
          
        oFReader.onload = function(oFREvent) {
          var color = "warna1";
          // document.getElementById("image-preview").style.display = "block";
          document.getElementById(color).src = oFREvent.target.result;
        };
        var img_name = "upload_image2";
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById(img_name).files[0]);
          
        oFReader.onload = function(oFREvent) {
          var color = "warna2";
          // document.getElementById("image-preview").style.display = "block";
          document.getElementById(color).src = oFREvent.target.result;
        };
        var img_name = "upload_image3";
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById(img_name).files[0]);
          
        oFReader.onload = function(oFREvent) {
          var color = "warna3";
          // document.getElementById("image-preview").style.display = "block";
          document.getElementById(color).src = oFREvent.target.result;
        };
        var img_name = "upload_image4";
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById(img_name).files[0]);
          
        oFReader.onload = function(oFREvent) {
          var color = "warna4";
          // document.getElementById("image-preview").style.display = "block";
          document.getElementById(color).src = oFREvent.target.result;
        };
        var img_name = "upload_image5";
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById(img_name).files[0]);
          
        oFReader.onload = function(oFREvent) {
          var color = "warna5";
          // document.getElementById("image-preview").style.display = "block";
          document.getElementById(color).src = oFREvent.target.result;
        };
      }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
      $(document).ready(function() {
          $('#js-example-basic-multiple').select2({
            placeholder: "--PILIH WARNA--"
          });
      });
    </script>

  </body>
</html>
<?php mysqli_close($db->conn) ?>