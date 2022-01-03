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
$emaillogin = $row['email_user'];
$fullnamelogin = $row['fullname_user'];
$role = $row['level_user'];
$alert = "";

// if(isset($_POST['crop_image'])){
//   $path = "assets/images/users/";
//   $foto = $_POST['crop_image'];
//   $react = $db->SaveFoto($path,$foto,$usernamelogin);
// }

if(isset($_POST['submit_profile'])){
  $fullname = $_POST['fullname'];
  $jk = $_POST['jk'];
  $update = $db->updateUser($usernamelogin,"profil",$fullname,$jk);
  if($update){
    if($_POST['foto'] != ""){
      $path = "assets/images/users/";
      $foto = $_POST['foto'];
      $db->SaveFoto($path,$foto,$usernamelogin);
    }
    $userselect = $db->selectTable("user_galeri","id_user",$_SESSION['login_stiker_id']);
    $row = mysqli_fetch_assoc($userselect);
    $fullnamelogin = $row['fullname_user'];
    $alert = "1";
  }else{
    $alert = "2";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>STIKER | PROFIL</title>
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
                  <h4 class="mb-sm-0">Profil</h4>

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
                    <form action="profile" method="post" autocomplete="off">
                      <div class="row mb-4">
                        <input type="hidden" name="foto" id="foto">
                        <input type="file" name="foto_profile" id="upload_image" accept="image/png,Image/jpeg" hidden>
                        <label for="upload_image" style="cursor: pointer;" class="col-12">
                          <img id="preview" style="border-radius: 50%;display: block;margin-left: auto;margin-right: auto; max-width:180px;" src="<?= $row['foto_user'] == '' ? 'assets/images/users/avatar-2.png' : $row['foto_user'] ?>" alt="">
                        </label>
                        <label for="upload_image" style="cursor: pointer;" class="col-12 mt-2">
                          <span class="text-info" style="display: block;text-align: center;font-size: 1em;">Ubah Foto</span>
                        </label>
                      </div>
                      <div class="row mb-3">
                          <label for="fullname" class="col-md-2 col-form-label">Nama Lengkap</label>
                          <div class="col-md-10">
                            <input class="form-control" id="fullname" type="text" value="<?= $fullnamelogin ?>" name="fullname">
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="jk" class="col-md-2 col-form-label">Jenis Kelamin</label>
                          <div class="col-md-10">
                            <select name="jk" id="jk" class="form-select">
                              <option value="" disabled>Pilih</option>
                              <?php  
                              $a = array("L","P");
                              foreach($a as $as){
                                $select = $row['jk_user'] == $as ? 'selected="selected"' : "";
                              ?>
                              <option value="<?= $as ?>" <?= $select ?>><?= $as == "L" ? "Laki-laki" : "Perempuan" ?></option>
                              <?php } ?>
                            </select>
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label class="col-md-2 col-form-label">Username</label>
                          <div class="col-md-10">
                            <input class="form-control" type="text" value="<?= $usernamelogin ?>" readonly>
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="email" class="col-md-2 col-form-label">Email</label>
                          <div class="col-md-10">
                            <input class="form-control" type="email" value="<?= $emaillogin ?>" readonly>
                          </div>
                      </div>
                      <div class="row mb-3">
                          <label for="role" class="col-md-2 col-form-label">Jabatan</label>
                          <div class="col-md-10">
                            <input class="form-control" type="text" value="<?= $role ?>" readonly>
                          </div>
                      </div>
                      <button class="btn btn-primary" type="submit" name="submit_profile">Submit</button>
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
    <?php include_once "rightside.php" ?>
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
      }
    </script>

    <script src="assets/js/app.js"></script>
  </body>
</html>

<?php mysqli_close($db->conn) ?>