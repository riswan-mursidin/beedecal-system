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

if($row['level_user'] == "Desainer" || $row['level_user'] == "Produksi" || $row['level_user'] == "Pemasang"){
  if($row['level_user'] == "Desainer"){
    header('Location: menunggu_designer');
    exit();
  }elseif($row['level_user'] == "Produksi"){
    header('Location: siap-cetak');
    exit();
  }else{
    header('Location: siap-dipasang');
    exit();
  }
}

if(isset($_POST['aksi_tr'])){
  $date = date("Y-m-d");
  $id_edit = $_POST['id'];
  $code_spk = $_POST['code'];
  $sisa = $_POST['sisa'];
  $pembayaran = $_POST['pembayaran'];
  $status_pay = $sisa > 0 ? "Belum Lunas" : "Lunas";

  $query_r = "UPDATE data_pemesanan SET status_pay_order='$status_pay', sisa_pembayaran_order='$sisa' WHERE id_order='$id_edit'";
  $result_r = mysqli_query($db->conn, $query_r);
  if($result_r){
    $query_t = "INSERT INTO detail_transaksi (code_order,tgl_transaksi,jumlah_transaksi,id_owner) VALUES('$code_spk','$date','$pembayaran','$id')";
    $result_t = mysqli_query($db->conn, $query_t);
    if($result_t){
      $alert = "1";
    }
  }
}

if(isset($_POST['input_resi'])){
  $resi = $_POST['resi'];
  $id_order = $_POST['id_order'];

  $resi = "UPDATE data_pemesanan SET resi_pengiriman='$resi' WHERE id_order='$id_order'";
  $resultt = mysqli_query($db->conn, $resi);
}

// pelanggan
$order = $_GET['order'];
$check = $db->selectTable("data_pemesanan","code_order",$order);
if(mysqli_num_rows($check) != 0 && $order != ""){
    $delete = $db->deleteTable("data_pemesanan",$order,"code_order");
    if($delete){
        $cth = $db->selectTable("contoh_desain","code_order",$order);
        while($rowcth=mysqli_fetch_assoc($cth)){
          unlink($rowcth['foto_contoh']);
        }
        $query = "DELETE FROM contoh_desain WHERE code_order='$order' AND id_owner='$id'";
        $result = mysqli_query($db->conn, $query);
        $_SESSION['alert'] = "1";
        header('Location: data-pesanan');
        exit();
    }else{
        $_SESSION['alert'] = "2";
        header('Location: data-pesanan');
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

function resultDiskon($harga,$disk){
  $diskon = $harga * ($disk/100);
  $result = $harga - $diskon;
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

function statusBadge($txt){
  if($txt == "Belum Lunas"){
    $result = '<h5><span class="badge bg-danger">Belum Lunas</span></h5>';
    return $result;
  }else{
    $result = '<h5><span class="badge bg-success">Lunas</span></h5>';
    return $result;
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

function showPercetakan($id,$owner){
  global $db;
  $percetakan = $db->selectTable("data_percetakan","id_percetakan",$id,"id_owner",$owner);
  $rowpercetakan = mysqli_fetch_assoc($percetakan);
  return $rowpercetakan['nama_percetakan'];
}

function showCetakan($id_order, $owner){
  global $db;
  $data_cetakan = $db->selectTable("data_cetakan","code_order",$id_order,"id_owner",$owner);
  $rowdata = mysqli_fetch_assoc($data_cetakan);
  $id_cetakan = $rowdata['id_percetakan'];
  $id_bahan = $rowdata['id_bahan'];
  $lebar = $rowdata['lebar_bahan'];
  $panjang = $rowdata['panjang_bahan'];

  $result['percetakan'] = showPercetakan($id_cetakan,$owner);
  return $result;
}
?> 

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>STIKER | AREA LOGISTIK</title>
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

    
    

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
                  <h4 class="mb-sm-0">Data Logistik (Ambil DItoko)</h4>

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
            <?php  
              $order = $db->selectTable("data_pemesanan","id_owner",$id,"status_pengiriman_order","Tidak");
              while($roworder=mysqli_fetch_assoc($order)){
                if($roworder['status_order'] == "Selesai Finishing" || $roworder['status_order'] == "Selesai Dicetak" || $roworder['status_order'] == "Selesai Dipasang" || $roworder['status_order'] == "Menunggu Finishing" || $roworder['status_order'] == "Siap Dipasang"){
                  $codee = $roworder['code_order']
            ?>
              <!-- Modal -->
              <div class="modal fade" id="detailsby<?= $roworder['id_order'] ?>" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Biaya Tambahan</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <table class="table">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Keterangan</th>
                            <th>Biaya</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php  
                          $byytam = $db->selectTable("biaya_tambahan_order","id_owner",$id,"code_order",$codee);
                          while($rowbyy=mysqli_fetch_assoc($byytam)){
                          ?>
                          <tr>
                            <td><?= $rowbyy['keterangan_biaya'] ?></td>
                            <td><?= $rowbyy['harga_ketbiaya'] ?></td>
                          </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal fade" id="pelunasan<?= $roworder['id_order'] ?>" data-bs-backdrop="static" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                  <form action="" method="post" class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Pelunasan Pembayaran</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label for="" class="form-label">Sisa Pembayaran</label>
                        <div class="input-group">
                          <span class="input-group-text">Rp.</span>
                          <input type="number" class="form-control" id="sisa" step="0.01" value="<?= $roworder['sisa_pembayaran_order'] ?>" readonly name="sisa">
                        </div>
                      </div>
                      <script>
                        function hitungSisa(pem){
                          var sisa = "<?= $roworder['sisa_pembayaran_order'] ?>";
                          if(pem != ""){
                            var hasil = parseFloat(sisa) - parseFloat(pem);
                            document.getElementById("sisa").value = parseFloat(hasil);
                          }else{
                            document.getElementById("sisa").value = parseFloat(sisa);
                          }
                        }
                      </script>
                      <div class="mb-3">
                        <label class="form-label">Pembayaran</label>
                        <div class="input-group">
                          <span class="input-group-text">Rp.</span>
                          <input type="number" name="pembayaran" onkeyup="hitungSisa(this.value)" class="form-control">
                        </div>
                      </div>
                      <div class="mb-3">
                        <h6>Jejak Transaksi</h6>
                        <hr>
                      </div>
                      <table class="table">
                        <thead>
                          <tr>
                            <th>Tanggal</th>
                            <th>Nominal</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php  
                          $tr = $db->selectTable("detail_transaksi","id_owner",$id,"code_order",$codee);
                          while($rowtr=mysqli_fetch_assoc($tr)){
                          ?>
                          <tr>
                            <td><?= $rowtr['tgl_transaksi'] ?></td>
                            <td>Rp.<?= number_format($rowtr['jumlah_transaksi'],2,",",".") ?></td>
                          </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                    <div class="modal-footer">
                      <input type="hidden" name="id" value="<?= $roworder['id_order'] ?>">
                      <input type="hidden" name="code" value="<?= $roworder['code_order'] ?>">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="submit" name="aksi_tr" class="btn btn-primary">Simpan</button>
                    </div>
                  </form>
                </div>
              </div>
            <?php }} ?>
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
                          <th>Desain</th>
                          <th>Percetakan</th>
                          <th>Pembayaran</th>
                          <th>Biaya Tambahan</th>
                          <th>Produksi</th>
                          <th>Tanggal Pesan</th>
                          <th>Status</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php  
                        $order = $db->selectTable("data_pemesanan","id_owner",$id,"status_pengiriman_order","Tidak");
                        while($roworder=mysqli_fetch_assoc($order)){
                          if($roworder['status_order'] == "Selesai Finishing" || $roworder['status_order'] == "Selesai Dicetak" || $roworder['status_order'] == "Selesai Dipasang" || $roworder['status_order'] == "Menunggu Finishing" || $roworder['status_order'] == "Siap Dipasang"){
                        ?>
                        <tr>
                          <td>
                            <?= $roworder['code_order'] ?><br>
                            <?= $roworder['jenis_produk_order'] == 'Custom' ? $db->nameFormater(showProduk($roworder['produk_order'])) : '' ?><br>
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
                              echo 'Prov: '.$customer['prov'].'<br>';
                              echo 'Kab/Kota: '.$customer['kab'].'<br>';
                              echo 'Kec: '.$customer['kec'].'<br>';
                              echo 'Kode Pos: '.$customer['kodepos'].'<br>';
                            ?>
                          </td>
                          <td>
                            <?= showDesigner($roworder['id_designer']); ?>
                          </td>
                          <td>
                            <?= '<a target="_blank" href="'.$roworder['hasil_desain_order'].'">View Desain</a>' ?>
                          </td>
                          <td>
                            <?= showCetakan($roworder['code_order'],$id)['percetakan'] ?>
                          </td>
                          <td>
                            <?= $roworder['diskon_order'] != "" ? '<span style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Dari Harga Rp.'.number_format($roworder['harga_produk_order'],2,",",".").'" class="badge bg-secondary">disk '.$roworder['diskon_order'].'%</span>' : '' ?><br>
                            Harga Produk: Rp.<?= number_format(resultDiskon($roworder['harga_produk_order'],$roworder['diskon_order']),2,",",".") ?><br>
                            Harga Pasang: <?= $roworder['status_pasang_order'] == "Ya" ? ' Rp.'.number_format($roworder['harga_pasang_order'],2,",",".") : 'Tidak Dipasang' ?><br>
                            <?= statusBadge($roworder['status_pay_order']) ?>
                          </td>
                          <td>
                          <a data-bs-toggle="modal" href="#detailsby<?= $roworder['id_order'] ?>">
                            Details
                          </a>
                          </td>
                          <td>
                            Desain: <b><?= $roworder['status_desain_order'] ?></b><br>
                            Cetak: <b><?= $roworder['status_cetak_order'] ?></b><br>
                            Laminating: <b><?= $roworder['laminating_order'] ?></b><br>
                            Pasang: <b><?= $roworder['status_pasang_order'] ?></b><br>
                          </td>
                          <td><?= $db->dateFormatter($roworder['tgl_order']) ?></td>
                          <td><?= '<h5><span class="badge bg-success">'.$roworder['status_order'].'</span></h5>' ?></td>
                          <td>
                            <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                            <?php  
                            if($roworder['status_pay_order'] == "Belum Lunas"){
                            ?>
                              <a data-bs-toggle="modal" href="#pelunasan<?= $roworder['id_order'] ?>" class="btn btn-info btn-sm">
                                <i class="ri-currency-line"></i>
                              </a>
                            <?php }else{ ?>
                              <?php $id = $roworder['status_order'] == "Menunggu Finishing" || $roworder['status_order'] == "Siap Dipasang" ? "warnig_status" : "doneorder" ?>
                              
                                <a id="<?= $id ?>" href="action/get-done-order?id=<?= $roworder['id_order'] ?>&param=Ya" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Selesai">
                                  <i class="ri-check-line"></i>
                                </a>
                            <?php } ?>
                              <a target="_blank" href="print_note?spk=<?= $roworder['code_order'] ?>" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Print Note"><i class="ri-printer-line"></i></a>
                            </div>
                          </td>
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
      $(document).on('click', '#doneorder', function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        Swal.fire({
          title:"Selesai!",
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
      $(document).on('click', '#warnig_status', function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        Swal.fire({
          title:"Peringatan!",
          text:"Proses Masih Berjalan!",
          icon:"warning",
          showCancelButton: true,
          showConfirmButton: false,
          cancelButtonColor: '#d33'
        })
      });
    </script>
  </body>
</html>
<?php mysqli_close($db->conn) ?>
<?php $_SESSION['alert'] = "";  ?>