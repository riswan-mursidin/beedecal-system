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

$editselect = $db->selectTable("user_galeri","username_user",$edit);
$rowedit = mysqli_fetch_assoc($editselect);

if($edit != ""){
  if(mysqli_num_rows($editselect) == 0){
    header('Location: konfigurasi-pegawaitoko');
    exit();
  }
}

if(isset($_POST['submit_pegawai'])){
  $email = $_POST['email'];
  $jk = $_POST['jk'];
  $username = strtolower($_POST['username']);
  $fullname = $_POST['fullname'];
  $jabatan = $_POST['jabatan'];
  $status = $_POST['status'];
  $pass = $_POST['password'];
  $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
  if($edit == ""){
    $checkusername = $db->selectTable("user_galeri","username_user",$username);
    if(mysqli_num_rows($checkusername) > 0){
      $alert = "3";
    }else{
      $checkemail = $db->selectTable("user_galeri","email_user",$email);
      if(mysqli_num_rows($checkemail) > 0){
        $alert = "4";
      }else{
        $insert = $db->insertUser("pegawai",$email,$jk,$username,$fullname,$jabatan,$status,$pass_hash,$id);
        if($insert){
          if($_POST['foto'] != ""){
            $path = "assets/images/users/";
            $foto = $_POST['foto'];
            $db->SaveFoto($path,$foto,$username);
          }
          $_SESSION['alert'] = "1";
          header('Location: konfigurasi-pegawaitoko');
          exit();
        }
      }
    }
  }else{
    $update = $db->updateUser($edit,"pegawai",$jk,$fullname,$jabatan,$status);
    if($update){
      if($_POST['foto'] != ""){
        $path = "assets/images/users/";
        $foto = $_POST['foto'];
        $db->SaveFoto($path,$foto,$edit);
      }
      $_SESSION['alert'] = "1";
      header('Location: konfigurasi-pegawaitoko');
      exit();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>STIKER | PEGAWAI</title>
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
    <link rel="stylesheet" href="https://fengyuanchen.github.io/cropperjs/css/cropper.css" />
    <script src="https://fengyuanchen.github.io/cropperjs/js/cropper.js"></script> 
    <script>
        $(document).ready(function(){
            var $modal = $('#modal_crop');
            var crop_image = document.getElementById('sample_image');
            var cropper;
            $('#upload_image').change(function(event){
                var files = event.target.files;
                var done = function(url){
                    crop_image.src = url;
                    $modal.modal('show');
                };
                if(files && files.length > 0)
                {
                    reader = new FileReader();
                    reader.onload = function(event)
                    {
                        done(reader.result);
                    };
                    reader.readAsDataURL(files[0]);
                }
            });
            $modal.on('shown.bs.modal', function() {
                cropper = new Cropper(crop_image, {
                    aspectRatio: 1,
                    viewMode: 3,
                    preview:'.preview'
                });
            }).on('hidden.bs.modal', function(){
                cropper.destroy();
                cropper = null;
            });
            $('#crop_and_upload').click(function(){
                canvas = cropper.getCroppedCanvas({
                    width:400,
                    height:400
                });
                canvas.toBlob(function(blob){
                    url = URL.createObjectURL(blob);
                    var reader = new FileReader();
                    reader.readAsDataURL(blob);
                    reader.onloadend = function(){
                        var base64data = reader.result; 
                        document.getElementById("preview").src = base64data
                        document.getElementById("foto").value = base64data
                        $modal.modal('hide');
        //                 $.ajax({
        //                     url:'profile.php',
        //                     method:'POST',
        //                     data:{crop_image:base64data},
        //                     success:function(data)
        //                     {
        //                         $modal.modal('hide');
        //                     }
        //                 });
                    };
                });
            });
        });
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
                  <h4 class="mb-sm-0">Edit Pegawai</h4>

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
                      <div class="row mb-4">
                        <input type="file" name="foto_profile" id="upload_image" accept="image/png,Image/jpeg" hidden>
                        <label for="upload_image" style="cursor: pointer;" class="col-12">
                          <input type="hidden" name="foto" id="foto" value="<?= $_POST['foto']  ?>">
                          <img id="preview" style="border-radius: 50%;display: block;margin-left: auto;margin-right: auto; max-width:180px;" src="<?= $edit != "" ?  $rowedit['foto_user'] : ( $_POST['foto'] != "" ? $_POST['foto'] : "assets/images/users/avatar-2.png" ) ?>" alt="">
                        </label>
                        <label for="upload_image" style="cursor: pointer;" class="col-12 mt-2">
                          <span class="text-info" style="display: block;text-align: center;font-size: 1em;">Ubah Foto</span>
                        </label>
                      </div>
                      <div class="row mb-3">
                          <label for="fullname" class="col-md-2 col-form-label">Nama Lengkap</label>
                          <div class="col-md-10">
                            <input required value="<?= $edit != "" ? $rowedit['fullname_user'] : $_POST['fullname'] ?>" class="form-control" id="fullname" type="text" placeholder="Nama Lengkap" name="fullname">
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="jk" class="col-md-2 col-form-label">Jenis Kelamin</label>
                          <div class="col-md-10">
                            <select required name="jk" id="jk" class="form-select">
                              <option value="">Pilih</option>
                              <?php  
                              $a = array("L","P");
                              $value = $edit != "" ? $rowedit['jk_user'] : $_POST['jk'];
                              foreach($a as $as){
                                $select = $value == $as ? 'selected="selected"' : "";
                              ?>
                              <option value="<?= $as ?>" <?= $select ?>><?= $as == "L" ? "Laki-laki" : "Perempuan" ?></option>
                              <?php } ?>
                            </select>
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label class="col-md-2 col-form-label">Username</label>
                          <div class="col-md-10">
                            <input required class="form-control" type="text" value="<?= $edit != "" ? $rowedit['username_user'] : $_POST['username'] ?>" placeholder="Username" id="username" style="text-transform: lowercase;" name="username" <?= $readonly ?>>
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="email" class="col-md-2 col-form-label">Email</label>
                          <div class="col-md-10">
                            <input required class="form-control" type="email" placeholder="example@mail.com" value="<?= $edit != "" ? $rowedit['email_user'] : $_POST['email'] ?>" name="email" <?= $readonly ?>>
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="role" class="col-md-2 col-form-label">Jabatan</label>
                          <div class="col-md-10">
                          <select required name="jabatan" id="role" class="form-select">
                              <option value="">Pilih</option>
                              <?php  
                              $a = array("Admin","Desainer","Produksi","Pemasang","Logistik");
                              $value = $edit != "" ? $rowedit['level_user'] : $_POST['jabatan'];
                              foreach($a as $as){
                                $select = $value == $as ? 'selected="selected"' : "";
                              ?>
                              <option value="<?= $as ?>" <?= $select ?>><?= $as ?></option>
                              <?php } ?>
                            </select>
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="statuss" class="col-md-2 col-form-label">Status</label>
                          <div class="col-md-10">
                            <select required name="status" id="statuss" class="form-select">
                              <option value="">Pilih</option>
                              <?php  
                              $a = array("Aktif","Tidak Aktif");
                              $value = $edit != "" ? $rowedit['status_user'] : $_POST['status'];
                              foreach($a as $as){
                                $select = $value == $as ? 'selected="selected"' : "";
                              ?>
                              <option value="<?= $as ?>" <?= $select ?>><?= $as ?></option>
                              <?php } ?>
                            </select>
                          </div>
                      </div>
                      <?php if($edit == ""){ ?>
                      <div class="row mb-3">
                          <label class="col-md-2 col-form-label" for="passsword">Password</label>
                          <div class="col-md-10">
                            <input required class="form-control" type="password" placeholder="password" id="password" name="password">
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label class="col-md-2 col-form-label" for="passswordconf">Konfirmasi Password</label>
                          <div class="col-md-10">
                            <input required class="form-control" type="password" placeholder="password" id="passswordconf">
                          </div>
                      </div>
                      <?php } ?>
                      <button class="btn btn-primary" type="submit" name="submit_pegawai">Submit</button>
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
                  var confirm_password = document.getElementById("passswordconf");

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