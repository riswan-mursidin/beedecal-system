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

if($row['level_user'] == "Desainer" || $row['level_user'] == "Produksi"){
  if($row['level_user'] == "Desainer"){
    header('Location: menunggu_designer');
    exit();
  }elseif($row['level_user'] == "Produksi"){
    header('Location: siap-cetak');
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
  
  $result['name'] = $rowdb['name_customer'];
  $result['prov'] = $rowdb['prov_customer'];
  $result['kab'] = $rowdb['kota_kab_customer'];
  $result['kec'] = $rowdb['kec_customer'];
  $result['kodepos'] = $rowdb['kode_pos_customer'];
  return $result;
}

function showDesigner($id){
  global $db;
  $query = $db->selectTable("user_galeri","id_user",$id,"level_user","Desainer");
  if(mysqli_num_rows($query) > 0){
    $row = mysqli_fetch_assoc($query);
    return $db->nameFormater($row['fullname_user']);
  }
}

function showPemasang($id){
  global $db;
  $query = $db->selectTable("user_galeri","id_user",$id,"level_user","Pemasang");
  $row = mysqli_fetch_assoc($query);
  return $db->nameFormater($row['fullname_user']);
}

?> 
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>STIKER | AREA PEMASANG</title>
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
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                  <h4 class="mb-sm-0">Proses Pemasangan</h4>
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
                          <th>Designer</th>
                          <th>Tanggal Pesan</th>
                          <th>Tanggal Pemasangan</th>
                          <th>Harga Pemasangan</th>
                          <th>Biaya Tambahan</th>
                          <th>Desain</th>
                          <?= $role == "Owner" || $role == "Admin" ? '<th>Pemasang</th>' : '' ?>
                          <th>Status</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php  

                        $order = $db->selectTable("data_pemesanan","id_owner",$id,"status_order","Proses Pemasangan");
                        while($roworder=mysqli_fetch_assoc($order)){
                        ?>
                        <tr>

                          <td>
                            <?= $roworder['code_order'] ?><br>
                            <?= 
                            $roworder['jenis_produk_order'] == 'Custom' && $roworder['kategori_produk_order'] == "Other" ? 
                              $db->nameFormater($roworder['produk_order']) : 
                                $db->nameFormater(showProduk($roworder['produk_order'])) 
                            ?><br>
                            <?= $roworder['model_stiker_order'] ?><br>
                            <?= $roworder['laminating_order'] ?>
                          </td>
                          
                          <td>
                            <?php 
                              $status = $roworder['jenis_produk_order'] == 'Custom' ? '<span class="badge bg-light">Custom</span>' : 'No Custom';
                              $customer = showCustomer($roworder['id_customer'],$roworder['status_Pengiriman_order'],$roworder['id_order']);
                              echo "<b>".$db->nameFormater($customer['name'])."</b>"." ".$status."<br>"; 
                            ?>
                            <?php
                              echo $roworder['keterangan_order'];
                              // echo 'Kab/Kota: '.$customer['kab'].'<br>';
                              // echo 'Kec: '.$customer['kec'].'<br>';
                              // echo 'Kode Pos: '.$customer['kodepos'].'<br>';
                            ?>
                          </td>
                          <td>
                            <?= $roworder['id_designer'] != "" ? showDesigner($roworder['id_designer']) : '' ?>
                          </td>
                          <td><?= $db->dateFormatter($roworder['tgl_order']) ?></td>
                          <td><?= $db->dateFormatter($roworder['tgl_pasang_order']); ?></td>
                          <td>Rp.<?= number_format($roworder['harga_pasang_order'],2,",",".") ?></td>
                          <td>Rp.<?= number_format($roworder['biaya_tambah_pemasangan_order'],2,",",".") ?></td>
                          <td>
                          <?php  
                            if($roworder['hasil_desain_order'] == ""){
                            ?>
                            <a href="#">Tidak Didesain</a>
                            <?php }else{ ?>
                            <a target="_blank" href="<?= $roworder['hasil_desain_order'] ?>">View Desain</a>
                            <?php } ?>
                          </td>
                          <?php if($role == "Owner" || $role == "Admin"){ ?>
                          <td>
                            <span class="badge bg-light"><?= $roworder['kategori_pemasang_order'] ?></span>
                          </td>
                          <?php } ?>
                          <td><?= '<h5><span class="badge bg-warning">'.$roworder['status_order'].'</span></h5>' ?></td>
                          <?php if($role == "Pemasang"){ ?>
                          <td>
                            <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                              <a id="pasang" href="action/get-pasang.php?param=selesai&id=<?= $roworder['id_order'] ?>&pemasang=<?= $_SESSION['login_stiker_id'] ?>" class="btn btn-success btn-sm"  data-bs-placement="top" title="Selesai Pasang">
                                <i class="ri-check-line"></i>
                              </a>
                            </div>
                          </td>
                          <?php }elseif($role == "Owner" || $role == "Admin"){ ?>
                          <td>
                            <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                              <a id="batal" href="action/get-pasang.php?param=batal&id=<?= $roworder['id_order'] ?>&pemasang=<?= explode("-",$roworder['pemasang_order'])[0] ?>" class="btn btn-danger btn-sm"  data-bs-placement="top" title="Batal Pasang">
                                <i class="ri-close-line"></i>
                              </a>
                            </div>
                          </td>
                          <?php } ?>
                        </tr>
                        <?php } ?>
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
          title:"Batal Pasang!",
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
      $(document).on('click', '#pasang', function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        Swal.fire({
          title:"Selesai Memasang!",
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