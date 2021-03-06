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

$delete = $_GET['delete'];

$checkdata = $db->selectTable("bank_galeri","id_bank",$delete);
if(mysqli_num_rows($checkdata) != 0 && $delete != 0){
  $deletel = $db->deleteTable("bank_galeri",$delete,"id_bank");
  if($deletel){
    $_SESSION['alert'] = "1";
    header('Location: konfigurasi-rek');
    exit();
  }else{
    $alert = "2";
  }
}

$edit = $_GET['edit'];

$editselect = $db->selectTable("bank_galeri","id_bank",$edit);
$rowselect = mysqli_fetch_assoc($editselect);
if($edit != ""){
  if(mysqli_num_rows($editselect) == 0){
    header('Location: konfigurasi-rek');
    exit();
  }
}

if(isset($_POST['add_rek'])){
    $bank = strtolower($_POST['bank']);
    $pemilik = strtolower($_POST['nama']);
    $rek = $_POST['rek'];

  if($edit == ""){
    $check = $db->selectTable("bank_galeri","id_owner",$id,"name_bank",$bank,"rek_bank",$rek,"pemilik_bank",$pemilik);
    if(mysqli_num_rows($check) > 0){
      $alert = "4";
    }else{
      $insert = $db->InsertBank($id,$bank,$pemilik,$rek);
      if($insert){
        $_SESSION['alert'] = "1";
        header('Location: konfigurasi-rek');
        exit();
      }else{
        $alert = "3";
      }
    }
  }else{
    $oldbank = $rowselect['name_bank'];
    $oldpemilik = $rowselect['pemilik_bank'];
    $oldrek = $rowselect['rek_bank'];
    $update = $db->updateBank($edit," "," "," ");
    $check = $db->selectTable("bank_galeri","id_owner",$id,"name_bank",$bank,"rek_bank",$rek,"pemilik_bank",$pemilik);
    if(mysqli_num_rows($check) > 0){
      $update = $db->updateBank($edit,$oldbank,$oldpemilik,$oldrek);
      $alert = "4";
    }else{
      $update = $db->updateBank($edit,$bank,$pemilik,$rek);
      $_SESSION['alert'] = "1";
      header('Location: konfigurasi-rek');
      exit();
    }
  }
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>STIKER | REKENING TOKO</title>
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
                  <h4 class="mb-sm-0">Rekening Toko</h4>

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
                    <div class="card-title">Tambah Rekening</div>
                    <form method="post" action="">
                      <div class="mb-3">
                        <label for="bank" class="form-label">Bank</label>
                        <input type="text" class="form-control" id="bank" value="<?= $edit != "" ? $rowselect['name_bank'] : $_POST['bank'] ?>" name="bank" required>
                      </div>
                      <div class="mb-3">
                        <label for="nama" class="form-label">Atas Nama</label>
                        <input type="text" class="form-control" id="nama" value="<?= $edit != "" ? $rowselect['pemilik_bank'] : $_POST['nama'] ?>" name="nama" required>
                      </div>
                      <div class="mb-3">
                        <label for="rek" class="form-label">No. Rek</label>
                        <input type="number" class="form-control" id="rek" value="<?= $edit != "" ? $rowselect['rek_bank'] : $_POST['rek'] ?>" name="rek" required>
                      </div>
                      <button type="submit" name="add_rek" class="btn btn-primary">Submit</button>
                    </form>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-9">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title">Rekening Bank</div>
                      <table id="datatable" class="table table-bordered table-hover dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Bank</th>
                            <th>Atas Nama</th>
                            <th>No. Rek</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php  
                          $no = 0;
                          $viewbank = $db->selectTable("bank_galeri","id_owner",$id);
                          while($rowbank = mysqli_fetch_assoc($viewbank)){
                          ?>
                          <tr>
                            <td><?= ++$no ?></td>
                            <td><?= strtoupper($rowbank['name_bank']) ?></td>
                            <td><?= $db->nameFormater($rowbank['pemilik_bank']) ?></td>
                            <td><?= $rowbank['rek_bank'] ?></td>
                            <td>
                              <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                <a href="konfigurasi-rek?edit=<?= $rowbank['id_bank']; ?>" class="btn btn-primary btn-sm"><i class="ri-pencil-line"></i></a>
                                <a href="konfigurasi-rek?delete=<?= $rowbank['id_bank']; ?>" class="btn btn-danger btn-sm" id="delete"><i class="ri-delete-bin-line"></i></a>
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
                ?? BEEDECAL
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

<?php $_SESSION['alert'] = ""; ?>
<?php mysqli_close($db->conn) ?>