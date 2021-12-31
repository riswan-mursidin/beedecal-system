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

if($row['level_user'] == "Produksi" || $row['level_user'] == "Pemasang"){
  if($row['level_user'] == "Produksi"){
    header('Location: siap-cetak');
    exit();
  }elseif($row['level_user'] == "Pemasang"){
    header('Location: siap-dipasang');
    exit();
  }
}

if(isset($_POST['aksi_upload'])){
  $nameproduk = $_POST['namefile'];
  $id_order = $_POST['id_order'];

  $order = $db->selectTable("data_pemesanan","id_order",$id_order);
  $roworder = mysqli_fetch_assoc($order);
  if($roworder['hasil_desain_order'] != ""){
    unlink($roworder['hasil_desain_order']);
  }

  $foto_path = $_FILES['hasil_desain']['tmp_name'];
  $foto_name = basename($_FILES['hasil_desain']['name']);
  $folder = "assets/images/hasil_desain";
  $save_file = $db->saveFoto2($folder, $foto_name, $foto_path, $nameproduk);
  $dbfoto = $save_file;

  $query = "UPDATE data_pemesanan SET hasil_desain_order='$dbfoto' WHERE id_order='$id_order'";
  $result = mysqli_query($db->conn, $query);
  if($result){
    $alert = "1";
  }
}

if(isset($_POST['aksi_input_link'])){
  $id_order = $_POST['id_order'];
  $link = $_POST['link_file'];

  $check = $db->selectTable("data_pemesanan","id_order",$id_order,"status_order","Selesai Didesain");
  $row = mysqli_fetch_assoc($check);

  $next = '';
  if($row['status_cetak_order'] == "Ya"){
      $next = "Siap Cetak";
  }elseif($row["laminating_order"] != ""){
      $next = "Menunggu Finishing";
  }elseif($row["status_pasang_order"] == "Ya"){
      $next = "Siap Dipasang";
  }else{
      $next = "Selesai";
  }

  $query = "UPDATE data_pemesanan SET link_google_drive='$link', status_order='$next' WHERE id_order='$id_order'";
  $result = mysqli_query($db->conn, $query);
  if($result){
    $_SESSION['alert'] = "1";
    header('Location: proses-desain');
    exit();
  }
}

function showProduk($id_produk){
  global $db;
  $querydb = $db->selectTable("type_galeri","id_type",$id_produk);
  $rowdb=mysqli_fetch_assoc($querydb);
  $result = $rowdb['name_type'];
  return $result;
}

function showCustomer($id_customer, $pengiriman, $id_order=null){
  global $db;
  // name customer
  $querydb = $db->selectTable("customer_stiker","id_customer",$id_customer);
  $rowdb=mysqli_fetch_assoc($querydb);

  // alamat customer
  $nameprov = "";
  $namekabkot = "";
  $namekec = "";
  $kodepos = "";
  if($pengiriman == "Ya"){
    $order = $db->selectTable("data_pemesanan","id_order",$id_order);
    $roworder=mysqli_fetch_assoc($order);
    $kodepos = $roworder['kode_pos_send_order'];
    $provs = $db->dataIndonesia("prov",null);
    foreach($provs as $prov){
      if($prov['province_id'] == $roworder['prov_send_order']){
        $nameprov = $prov['province'];
      }
    }
    $kabkot = $db->dataIndonesia("kab_kota",$roworder['prov_send_order']);
    foreach($kabkot as $kab){
      if($kab['city_id'] == $roworder['kab_send_order']){
        $namekabkot = $kab['city_name'];
      }
    }
    $kecs = $db->dataIndonesia("kec",$roworder['kab_send_order']);
    foreach($kecs as $kec){
      if($kec['subdistrict_id'] == $roworder['kec_send_order']){
        $namekec = $kec['subdistrict_name'];
      }
    }
  }else{
    $kodepos = $rowdb['kode_pos_customer'];
    $provs = $db->dataIndonesia("prov",null);
    foreach($provs as $prov){
      if($prov['province_id'] == $rowdb['prov_customer']){
        $nameprov = $prov['province'];
      }
    }
    $kabkot = $db->dataIndonesia("kab_kota",$rowdb['prov_customer']);
    foreach($kabkot as $kab){
      if($kab['city_id'] == $rowdb['kota_kab_customer']){
        $namekabkot = $kab['city_name'];
      }
    }
    $kecs = $db->dataIndonesia("kec",$rowdb['kota_kab_customer']);
    foreach($kecs as $kec){
      if($kec['subdistrict_id'] == $rowdb['kec_customer']){
        $namekec = $kec['subdistrict_name'];
      }
    }
  }
  $result['name'] = $rowdb['name_customer'];
  $result['prov'] = $nameprov;
  $result['kab'] = $namekabkot;
  $result['kec'] = $namekec;
  $result['kodepos'] = $kodepos;
  return $result;
}

function showStatus($status,$admin){
  if($status == "Proses Desain"){
    if($admin == "Tidak Disetujui"){
      return '<span class="badge bg-danger">'.$admin.'</span>';
    }else{
      return '<span class="badge bg-danger">'.$status.'</span>';
    }
  }else{
    if($admin == "Belum Disetujui"){
      return '<span class="badge bg-warning">'.$admin.'</span>';
    }else{
      return '<span class="badge bg-success">'.$admin.'</span>';
    }
  }
}

function showDesigner($id){
  global $db;
  $query = $db->selectTable("user_galeri","id_user",$id,"level_user","Desainer");
  if(mysqli_num_rows($query) > 0){
    $row = mysqli_fetch_assoc($query);
    return $db->nameFormater($row['fullname_user']);
  }
}

?> 
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>STIKER | AREA DESIGNER</title>
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
                <div
                  class="
                    page-title-box
                    d-sm-flex
                    align-items-center
                    justify-content-between
                  "
                >
                  <h4 class="mb-sm-0">Siap Didesain</h4>

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
            <!-- page card -->
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-body">
                    <table id="datatable" class="table table-bordered table-hover dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Pelanggan</th>
                          <th>Tanggal Pesan</th>
                          <th>Keterangan Desain</th>
                          <th>Jenis Desain</th>
                          <th>Contoh Desain</th>
                          <th>Hasil Desain</th>
                          <?= $role == "Admin" || $role == "Owner" ? "<th>Designer</th>" : '' ?>
                          <th>Status</th>
                          <?= $role == "Desainer" || $role == "Admin" || $role == "Owner" ? "<th>Aksi</th>" : '' ?>
                        </tr>
                      </thead>
                      <tbody>
                        <?php  
                        $order = $db->selectTable("data_pemesanan","id_owner",$id,"id_designer",$row['id_user']);
                        if($role == "Admin" || $role == "Owner"){
                          $order = $db->selectTable("data_pemesanan","id_owner",$id);
                        }
                        while($roworder=mysqli_fetch_assoc($order)){
                          if($roworder['status_order'] == "Proses Desain" || $roworder['status_order'] == "Selesai Didesain"){
                        ?>
                        <tr>
                          <td>
                            <?= $roworder['code_order'] ?><br>
                            <?= $roworder['jenis_produk_order'] == 'Custom' ? $db->nameFormater(showProduk($roworder['produk_order'])) : '' ?><br>
                            <?= $roworder['model_stiker_order'] ?><br>
                          </td>
                          <td>
                            <?php 
                              $status = $roworder['jenis_produk_order'] == 'Custom' ? '<span class="badge bg-light">Custom</span>' : 'No Custom';
                              $customer = showCustomer($roworder['id_customer'],$roworder['status_pengiriman_order'],$roworder['id_order']);
                              echo "<b>".$db->nameFormater($customer['name'])."</b>"." ".$status."<br>"; 
                            ?>
                            <?php
                              echo $roworder['keterangan_order'];
                            ?>
                          </td>
                          <td><?= $db->dateFormatter($roworder['tgl_order']) ?></td>
                          <td><?= $roworder['desk_desain_order'] ?></td>
                          <td><?= $roworder['kategori_produk_order'] ?></td>
                          <td>
                            <?php  
                              $no = 0; $spasi = 1;
                              $fotocontoh = $db->selectTable("contoh_desain","code_order",$roworder['code_order'],"id_owner",$id);
                              while($rowcontoh=mysqli_fetch_assoc($fotocontoh)){
                                $br = $spasi == 2 ? '<br>' : '';
                                $spasi = $spasi == 2 ? 1 : $spasi+1;
                            ?>
                            <a target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="View" href="<?= $rowcontoh['foto_contoh'] ?>">
                              Contoh <?= ++$no; ?>
                            </a><?= $br ?>
                            <?php } ?>
                          </td>
                          <td>
                            <span id="viewsdesain">
                              <?= $roworder['hasil_desain_order'] == "" ? "Tidak Ada Desain" : '<a target="_blank" href="'.$roworder['hasil_desain_order'].'">View Desain</a>' ?>
                              </span>
                          </td>
                          <?php  
                          if($role == "Admin" || $role == "Owner"){
                          ?>
                          <td>
                            <?= showDesigner($roworder['id_designer']); ?>
                          </td>
                          <?php } ?>
                          <td>
                            <?= showStatus($roworder['status_order'],$roworder['admin_konfirm']) ?>
                          </td>
                          <?php if($role == "Desainer"){ ?>
                          <td>
                            <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                              <?php if($roworder['admin_konfirm'] != 'Belum Disetujui'){ ?>
                                <?php if($roworder['admin_konfirm'] == 'Disetujui'){ ?>
                                  <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#input_link<?= $roworder['id_order'] ?>" data-bs-placement="top" title="Masukkan Link" class="btn btn-danger btn-sm">
                                    <i class="ri-attachment-2"></i>
                                  </button>
                                <?php }else{ ?>
                                  <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#upload_hasil_desain<?= $roworder['id_order'] ?>" data-bs-placement="top" title="Upload Desain" class="btn btn-danger btn-sm">
                                    <i class="ri-pencil-line"></i>
                                </button>
                              <?php }
                              }
                              if($roworder['status_order'] != "Selesai Didesain"){
                                if($roworder['hasil_desain_order'] == ""){
                              ?>
                              <a href="" class="btn btn-warning btn-sm disabled" aria-disabled="true"><i class="mdi mdi-check"></i></a>
                              <?php }else{?>
                              <a href="action/get-orderdesain.php?id_order=<?= $roworder['id_order'] ?>&user=<?= $roworder['id_designer'] ?>&param=selesai" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Selesai"><i class="mdi mdi-check"></i></a>
                              <?php }} ?>
                            </div>
                          </td>
                          <?php }elseif($role == "Admin" || $role == "Owner"){ ?>
                          <td>
                            <?php  
                            if($roworder['admin_konfirm'] == "Belum Disetujui"){
                            ?>
                            <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                              <a href="action/confirm-desain.php?param=terima&id=<?= $roworder['id_order'] ?>" class="btn btn-success btn-sm" id="terimadesain" data-bs-toggle="tooltip" data-bs-placement="top" title="Setujui">
                                <i class="mdi mdi-check"></i>
                              </a>
                              <a href="action/confirm-desain.php?param=tolak&id=<?= $roworder['id_order'] ?>" class="btn btn-danger btn-sm" id="tolakdesain" data-bs-toggle="tooltip" data-bs-placement="top" title="Tolak">
                                <i class="ri-close-fill"></i>
                              </a>
                            </div>
                            <?php }elseif($roworder['admin_konfirm'] == "" || $roworder['admin_konfirm'] == "Tidak Disetujui"){ ?>
                              <?php $param = $roworder['status_order'] != "Selesai Didesain" ? 'batal' : 'batal selesai'; ?> 
                            <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                              <a href="action/get-orderdesain.php?id_order=<?= $roworder['id_order'] ?>&user=<?= $roworder['id_designer'] ?>&param=<?= $param ?>" id="batal" data-bs-toggle="tooltip" data-bs-placement="top" title="Batal" class="btn btn-danger btn-sm">
                                <i class="ri-close-fill"></i>
                              </a>
                            </div>
                      
                          <?php } ?>
                          </td>
                          <?php } ?>
                        </tr>
                        <?php }else{continue;}} ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <!-- end page card -->
          </div>
          <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

        <!-- Modal upload hasil desan -->
        <!-- Modal -->
        <?php  
        $order = $db->selectTable("data_pemesanan","id_owner",$id,"status_order","Proses Desain","id_designer",$row['id_user']);
        while($roworder=mysqli_fetch_assoc($order)){
        ?>
        <div class="modal fade" id="upload_hasil_desain<?= $roworder['id_order'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <form action="" method="post" class="modal-content" enctype="multipart/form-data">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Upload Hasil Desain</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="namefile" value="<?= $roworder['jenis_produk_order'] == 'Custom' ? showProduk($roworder['produk_order']) : '' ?>">
                <input type="hidden" name="id_order" value="<?= $roworder['id_order'] ?>">
                <input type="file" name="hasil_desain" id="" class="form-control" required>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="aksi_upload" class="btn btn-primary">Upload Desain</button>
              </div>
            </form>
          </div>
        </div>
        <?php } 
        $order = $db->selectTable("data_pemesanan","id_owner",$id,"status_order","Selesai Didesain","admin_konfirm","Disetujui","id_designer",$row['id_user']);
        while($roworder=mysqli_fetch_assoc($order)){
        ?>
        
        <div class="modal fade" id="input_link<?= $roworder['id_order'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <form action="" method="post" class="modal-content" enctype="multipart/form-data">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Link Google Drive</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="id_order" value="<?= $roworder['id_order'] ?>">
                <input type="text" name="link_file" id="" class="form-control" required>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="aksi_input_link" class="btn btn-primary">Selesai</button>
              </div>
            </form>
          </div>
        </div>
        <?php } ?>
        <!-- End Modal upload hasil desan -->

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
          text:"Data tidak Berubah!",
          icon:"error",
        })
      }else if(flash == "5"){
        Swal.fire({
          title:"Berbahaya",
          text:"Ini bukan Area Anda",
          icon:"question",
          confirmButtonColor:"#5664d2"
        }).then((result) => {
          if(result.isConfirmed){
            window.location = '<?= $link ?>';
          }else{
            window.location = '<?= $link ?>';
          }
        })
      }
    </script>
    <script>
      $(document).on('click', '#batal', function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        Swal.fire({
          title:"Batal Desain!",
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
    <script>
      $(document).on('click', '#terimadesain', function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        Swal.fire({
          title:"Setujui Desain!",
          text:"Apakah Anda yakin?",
          icon:"success",
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
    <script>
      $(document).on('click', '#tolakdesain', function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        Swal.fire({
          title:"Tolak Desain!",
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
    <script>
      $(document).on('click', '#ambilorder', function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        Swal.fire({
          title:"Ambil Orderan!",
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
<?php mysqli_close($db->conn) ?>
<?php $_SESSION['alert'] = "";  ?>