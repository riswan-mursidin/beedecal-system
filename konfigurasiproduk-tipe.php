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
$alert = $_SESSION['alert'];

function showMerk($merk){
  global $db;
  $show = $db->selectTable("merek_galeri","id_merek",$merk);
  foreach($show as $views){
    $result[] = $views;
  }
  return $result;
}

$delete = $_GET['delete'];

$checkdata = $db->selectTable("type_galeri","id_type",$delete);
if(mysqli_num_rows($checkdata) != 0 && $delete != 0){
  $deletel = $db->deleteTable("type_galeri",$delete,"id_type");
  if($deletel){
    $_SESSION['alert'] = "1";
    header('Location: konfigurasiproduk-tipe');
    exit();
  }else{
    $alert = "2";
  }
}

$edit = $_GET['edit'];

$editselect = $db->selectTable("type_galeri","id_type",$edit);
$rowselect = mysqli_fetch_assoc($editselect);
if($edit != ""){
  if(mysqli_num_rows($editselect) == 0){
    header('Location: konfigurasiproduk-tipe');
    exit();
  }
}

if(isset($_POST['add_tipe'])){
    $merk = $_POST['merek'];
    $type = strtolower($_POST['tipe']); 
    $fullbody = $_POST['fullbody'];
    $fulldash = $_POST['fullbodydash'];
    $lite = $_POST['lite'];


    $type_foto = $_FILES['foto_type']['name'];
    $dbfoto = "empty";
    if($type_foto != ""){
      $type_path_foto = $_FILES['foto_type']['tmp_name'];
      $type_foto = $_FILES['foto_type']['name'];
      $folder = "assets/images/foto_type";
      $save_file = $db->saveFoto2($folder, $type_foto, $type_path_foto);
      $dbfoto = $save_file;
    }

  if($edit == ""){
    $check = $db->selectTable("type_galeri","id_owner",$id,"name_type",$type,"id_merek",$merk);
    if(mysqli_num_rows($check) > 0){
      $alert = "4";
    }else{
      $insert = $db->insertType($type,$fulldash,$fullbody,$lite,$merk,$dbfoto,$id);
      if($insert){
        $_SESSION['alert'] = "1";
        header('Location: konfigurasiproduk-tipe');
        exit();
      }else{
        $alert = "3";
      }
    }
  }else{
    $oldmerk = $rowselect['id_merek'];
    $oldtype = $rowselect['name_type']; 
    $oldfoto = $rowselect['foto_type']; 
    $oldfullbody = $rowselect['fullbody_harga_type'];
    $oldfulldash = $rowselect['fullbodydash_harga_type'];
    $oldlite = $rowselect['lite_harga_type'];
    $update = $db->updateType(" ",$oldfulldash,$oldfullbody,$oldlite,$oldfoto," ",$edit);
    $check = $db->selectTable("type_galeri","id_owner",$id,"name_type",$type,"id_merek",$merk);
    if(mysqli_num_rows($check) > 0){
      $update = $db->updateType($oldtype,$oldfulldash,$oldfullbody,$oldlite,$oldmerk,$oldfoto,$edit);
      $alert = "4";
    }else{
      if($type_foto == ""){
        $dbfoto = $oldfoto;
      }else{
        if(file_exists($oldfoto)){
          unlink($oldfoto);
        }
      }
      $update = $db->updateType($type,$fulldash,$fullbody,$lite,$merk,$dbfoto,$edit);
      if($update){
        $_SESSION['alert'] = "1";
        header('Location: konfigurasiproduk-tipe');
        exit();
      }
    }
  }
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>STIKER | TIPE PRODUK</title>
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

    <!-- DataTables -->
    <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" /> 

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
                <div class=" page-title-box d-sm-flex align-items-center justify-content-between" >
                  <h4 class="mb-sm-0">Tipe Produk</h4>

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
              <div class="col-12 col-md-3">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title">Tambah Tipe</div>
                    <form method="post" action="" enctype="multipart/form-data">
                      <div class="mb-3">
                        <label for="merek" class="form-label">Merek</label>
                        <select name="merek" id="merek" class="form-select" required>
                          <option value="">--PILIH MEREK--</option>
                          <?php  
                          $val = $edit != "" ? $rowselect['id_merek'] : $_POST['merek'];
                          $mobil = $db->selectTable("merek_galeri","id_owner",$id,"jenis_merek","Mobil"); 
                          if(mysqli_num_rows($mobil) > 0){
                          ?>
                          <optgroup label="Mobil">
                            <?php 
                            $mobil = $db->selectTable("merek_galeri","id_owner",$id,"jenis_merek","Mobil"); 
                            while($rowmobil = mysqli_fetch_assoc($mobil)){
                              $select = $val == $rowmobil['id_merek'] ? 'selected="selected"' : "";
                            ?>
                            <option value="<?= $rowmobil['id_merek'] ?>" <?= $select ?>><?= $db->nameFormater($rowmobil['name_merek']) ?></option>
                            <?php } ?>
                          </optgroup>
                          <?php 
                          } 
                          $motor = $db->selectTable("merek_galeri","id_owner",$id,"jenis_merek","Motor");  
                          if(mysqli_num_rows($motor) > 0){
                          ?>
                          <optgroup label="Motor">
                            <?php 
                            $motor = $db->selectTable("merek_galeri","id_owner",$id,"jenis_merek","Motor"); 
                            while($rowmotor = mysqli_fetch_assoc($motor)){
                              $select = $val == $rowmotor['id_merek'] ? 'selected="selected"' : "";
                            ?>
                            <option value="<?= $rowmotor['id_merek'] ?>" <?= $select ?> ><?= $db->nameFormater($rowmotor['name_merek']) ?></option>
                            <?php } ?>
                          </optgroup>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label for="tipe" class="form-label">Tipe</label>
                        <input type="text" class="form-control" id="tipe" value="<?= $edit != "" ? $rowselect['name_type'] : $_POST['tipe'] ?>" name="tipe" required>
                      </div>
                      <div class="mb-3">
                        <label for="foto_pola" class="form-label">Upload Foto</label>
                        <input type="file" name="foto_type" id="foto_pola" class="form-control" required>
                      </div>
                      <div class="mb-3">
                        <label for="full" class="form-label">Fullbody</label>
                        <div class="input-group">
                          <span class="input-group-text">Rp.</span>
                          <input type="number" class="form-control" placeholder="0.00" id="full" name="fullbody" value="<?= $edit != "" ? $rowselect['fullbody_harga_type'] : $_POST['fullbody'] ?>">
                        </div>
                      </div>
                      <div class="mb-3">
                        <label for="fulldash" class="form-label">Fullbody Dashboard</label>
                        <div class="input-group">
                          <span class="input-group-text">Rp.</span>
                          <input type="number" class="form-control" placeholder="0.00" id="fulldash" name="fullbodydash" value="<?= $edit != "" ? $rowselect['fullbodydash_harga_type'] : $_POST['fullbodydash'] ?>">
                        </div>
                      </div>
                      <div class="mb-3">
                        <label for="lite" class="form-label">Lite</label>
                        <div class="input-group">
                          <span class="input-group-text">Rp.</span>
                          <input type="number" class="form-control" placeholder="0.00" id="lite" name="lite" value="<?= $edit != "" ? $rowselect['lite_harga_type'] : $_POST['lite'] ?>">
                        </div>
                      </div>
                      <button type="submit" name="add_tipe" class="btn btn-primary">Submit</button>
                    </form>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-9">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title">Tipe Produk</div>
                      <table id="datatable" class="table table-bordered table-hover dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Jenis</th>
                            <th>Merek</th>
                            <th>Tipe</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php  
                          $no = 0;
                          $viewtipe = $db->selectTable("type_galeri","id_owner",$id);
                          while($rowtipe = mysqli_fetch_assoc($viewtipe)){
                          ?>
                          <tr>
                            <td><?= ++$no ?></td>
                            <?php 
                            $m = showMerk($rowtipe['id_merek']);
                            foreach($m as $rowmerk){
                            ?>
                            <td><?= $db->nameFormater($rowmerk['jenis_merek']) ?></td>
                            <td><?= $db->nameFormater($rowmerk['name_merek']) ?></td>
                            <?php } ?>
                            <td><?= $db->nameFormater($rowtipe['name_type']) ?></td>
                            <td>
                              <?= $rowtipe['fullbodydash_harga_type'] != "" ? "Fullbody Dash : Rp.". number_format($rowtipe['fullbodydash_harga_type'],0,",",".")."<br>" : "" ?>
                              <?= $rowtipe['fullbody_harga_type'] != "" ? "Fullbody : Rp.". number_format($rowtipe['fullbody_harga_type'],0,",",".")."<br>" : "" ?>
                              <?= $rowtipe['lite_harga_type'] != "" ? "Lite : Rp.". number_format($rowtipe['lite_harga_type'],0,",",".")."<br>" : "" ?>
                            </td>
                            <td>
                              <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                <a href="konfigurasiproduk-tipe?edit=<?= $rowtipe['id_type']; ?>" class="btn btn-primary btn-sm"><i class="ri-pencil-line"></i></a>
                                <a href="konfigurasiproduk-tipe?delete=<?= $rowtipe['id_type']; ?>" class="btn btn-danger btn-sm" id="delete"><i class="ri-delete-bin-line"></i></a>
                              </div>
                            </td>
                          </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                  </div>
                </div>
              </div>
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

    <!-- Required datatable js -->
    <script src="assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <!-- Buttons examples -->
    <script src="assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>

    <script src="assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>
    <!-- Responsive examples -->
    <script src="assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

    <!-- Datatable init js -->
    <script src="assets/js/pages/datatables.init.js"></script>
    
    <!-- Sweet Alerts js -->
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>

    <script src="assets/js/app.js"></script>
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
          text:"Data tidak Terhapus!",
          icon:"error",
        })
      }else if(flash == "3"){
        Swal.fire({
          title:"Gagal!",
          text:"Data tidak Tersimpan!",
          icon:"error",
        })
      }else if(flash == "4"){
        Swal.fire({
          title:"Gagal!",
          text:"Data Sudah Ada!",
          icon:"error",
        })
      }
    </script>
    <script>
      $(document).on('click', '#delete', function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        Swal.fire({
          title:"Hapus Data!",
          text:"Apakah Anda yakin?",
          icon:"warning",
          showCancelButton: true,
          confirmButtonColor: '#00a65a',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ya'
        }).then((result) => {
          if(result.isConfirmed){
            window.location = link;
          }
        });
      });
    </script>
  </body>
</html>

<?php $_SESSION['alert'] = ""; mysqli_close($db->conn) ?>
