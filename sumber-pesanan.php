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

// edit check
$edit = $_GET['id']; $rowedit = "";
if($edit != ""){
  $check = $db->selectTable("sumber_pemesanan","id_sumber",$edit);
  if(mysqli_num_rows($check) == 0){
    header('Location: sumber-pesanan');
    exit();
  }else{
    $rowedit = mysqli_fetch_assoc($check);
  }
}
$alert = $_SESSION['alert'];
if(isset($_POST['save_sumber'])){
  $name = ucfirst($_POST['sumber_name']);
  if($edit != ""){
    if($rowedit['name_sumber'] != $name){
      $check = $db->selectTable("sumber_pemesanan","id_owner",$id,"name_sumber",$name);
      if(mysqli_fetch_assoc($check) > 0){
        $alert = "3";
      }else{
        $query = "UPDATE sumber_pemesanan SET name_sumber='$name' WHERE id_sumber='$edit'";
        $result = mysqli_query($db->conn, $query);
        if($result){
          $_SESSION['alert'] = "1";
          header('Location: sumber-pesanan');
          exit();
        }else{
          $_SESSION['alert'] = "2";
          header('Location: sumber-pesanan');
          exit();
        }
      }
    }else{
      $_SESSION['alert'] = "1";
      header('Location: sumber-pesanan');
      exit();
    }
  }else{
    $check = $db->selectTable("sumber_pemesanan","id_owner",$id,"name_sumber",$name);
    if(mysqli_fetch_assoc($check) > 0){
      $alert = "3";
    }else{
      $query = "INSERT INTO sumber_pemesanan (name_sumber,id_owner) VALUES('$name','$id')";
      $result = mysqli_query($db->conn, $query);
      if($result){
        $alert = "1";
      }else{
        $alert = "2";
      }
    }
  }
}

if(isset($_POST['id_sumber'])){
  $id_sumber = $_POST['id_sumber'];
  $deletesumber = $db->deleteTable("sumber_pemesanan",$id_sumber,"id_sumber");
  if($deletesumber){

    // order tabel
    $order = $db->selectTable("data_pemesanan","id_sumber",$id_sumber);
    if(mysqli_num_rows($order) > 0){
      while($roworder=mysqli_fetch_assoc($order)){
        $code_order = $roworder['code_order'];

        // contoh desain tabel
        $contohdesain = $db->selectTable("contoh_desain","code_order",$code_order,"id_owner",$id);
        if(mysqli_num_rows($contohdesain) > 0){
          while($rowcontohdesain=mysqli_fetch_assoc($contohdesain)){
            $foto = $rowcontohdesain['foto_contoh'];
            $id_contoh = $rowcontohdesain['id_contoh'];
            if(file_exists($foto)){
              $removefile = unlink($foto);
              if($removefile){
                $db->deleteTable("contoh_desain",$id_contoh,"id_contoh");
              }
            }else{
              $db->deleteTable("contoh_desain",$id_contoh,"id_contoh");
            }
          }
        }
        // end contoh desain tabel

        // detail transaksi tabel
        $detailtr = $db->selectTable("detail_transaksi","code_order",$code_order,"id_owner",$id);
        if(mysqli_num_rows($detailtr) > 0){
          while($rowtr=mysqli_fetch_assoc($detailtr)){
            $id_tr = $rowtr['id'];
            $db->deleteTable("detail_transaksi",$id_tr,"id");
          }
        }
        // end detail transaksi tabel

        // biaya tambahan tabel
        $biayatam = $db->selectTable("biaya_tambahan_order","code_order",$code_order,"id_owner",$id);
        if(mysqli_num_rows($biayatam) > 0){
          while($rowby=mysqli_fetch_assoc($biayatam)){
            $idtr = $rowby['id'];
            $db->deleteTable("biaya_tambahan_order",$idtr,"id");
          }
        }
        // end biaya tambahan tabel
      
      }
    }
    $deleteorder = $db->deleteTable("data_pemesanan",$id_sumber,"id_sumber");
    if($deleteorder){
      $alert = "1";
    }else{
      $alert = "2";
    }
    // end order tabel
  }
}

function showTotal($id_sumber){
  global $db;
  $count = 0;
  $order = $db->selectTable("data_pemesanan","id_sumber",$id_sumber);
  while($roworder=mysqli_fetch_assoc($order)){
    $count += $roworder['harga_produk_order'];
  }
  return $count;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>STIKER | SUMBER</title>
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
                  <h4 class="mb-sm-0">Data Sumber</h4>

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
            <?php 
            
            ?>
            <div id="flash" data-flash="<?= $alert ?>"></div>
            <!-- content -->
            <div class="row">
              <div class="col-12 col-md-4">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title">Input/Edit Sumber</div>
                    <form action="" method="post">
                      <div class="mb-3">
                        <label for="name" class="form-label">Nama Sumber</label>
                        <?php  
                        $value = $edit != "" ? $rowedit['name_sumber'] : ''; 
                        ?>
                        <input type="text" name="sumber_name" value="<?= $value ?>" class="form-control" id="name">
                      </div>
                      <button type="submit" name="save_sumber" class="btn btn-primary">Submit</button>
                    </form>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-8">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title">Sumber</div>
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Sumber</th>
                          <th scope="col">Jumlah Pesanan</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php  $no = 0;
                        $sumber = $db->selectTable("sumber_pemesanan","id_owner",$id);
                        while($rowsumber=mysqli_fetch_assoc($sumber)){
                        ?>
                        <tr>
                          <td scope="row"><?= ++$no ?></td>
                          <td><?= $rowsumber['name_sumber'] ?></td>
                          <td>
                            Rp.<?= number_format(showTotal($rowsumber['id_sumber'])) ?>
                          </td>
                          <td>
                            <form method="post" action="" class="btn-group" role="group" aria-label="Basic mixed styles example">
                              <a href="sumber-pesanan?id=<?= $rowsumber['id_sumber'] ?>" class="btn btn-primary btn-sm" ata-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                <i class="ri-pencil-line"></i>
                              </a>
                              <input type="hidden" name="id_sumber" value="<?= $rowsumber['id_sumber'] ?>">
                              <button type="submit" id="delete_sumber" name="delete_sumber" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="delete">
                                <i class="ri-delete-bin-line"></i>
                              </button>
                            </form>
                          </td>
                        </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- end content -->
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

    <script src="assets/js/app.js"></script>
    <script>
      $(document).on('click', '#delete_sumber', function(e){
        e.preventDefault();
        var form = $(this).parents('form');
        Swal.fire({
          title:"Hapus Sumber!",
          text:"Apakah Anda yakin?",
          icon:"warning",
          showCancelButton: true,
          confirmButtonColor: '#00a65a',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Ya'
        }).then((result) => {
          if(result.isConfirmed){
            form.submit();
          }
        });
      });
    </script>
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
          text:"Sumber Sudah Ada!",
          icon:"question",
        })
      }
    </script>
  </body>
</html>
<?php mysqli_close($db->conn); $_SESSION['alert'] = ""; ?>