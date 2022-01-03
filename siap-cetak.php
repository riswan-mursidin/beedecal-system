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

if($row['level_user'] == "Desainer" || $row['level_user'] == "Pemasang"){
  if($row['level_user'] == "Desainer"){
    header('Location: menunggu_designer');
    exit();
  }elseif($row['level_user'] == "Pemasang"){
    header('Location: siap-dipasang');
    exit();
  }
}

if(isset($_POST['aksi_cetak'])){
  $id_percetakan = $_POST['percetakan'];
  $id_bahan = $_POST['bahan'];
  $lebar = $_POST['lebar'];
  $panjang = $_POST['panjang'];
  $id_orderr = $_POST['id_order'];

  $order = $db->selectTable("data_pemesanan","id_order",$id_orderr);
  $roworderr = mysqli_fetch_assoc($order);
  $spk = $roworderr['code_order'];
  
  $next = 'Proses Cetak';

  $updateecetak = "UPDATE data_pemesanan SET status_order='$next', produksi_status='Ya' WHERE id_order='$id_orderr'";
  $result = mysqli_query($db->conn,$updateecetak);
  if($result){
    $insetcetak = "INSERT INTO data_cetakan (code_order,id_percetakan,id_bahan,lebar_bahan,panjang_bahan,id_owner) VALUES('$spk','$id_percetakan','$id_bahan','$lebar','$panjang',$id)";
    $resultcetak = mysqli_query($db->conn, $insetcetak);
    if($resultcetak){
      $alert = "1";
    }
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
    <title>STIKER | AREA PRODUKSI</title>
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
                  <h4 class="mb-sm-0">Siap Dicetak</h4>

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
                          <th>Desain</th>
                          <th>Status</th>
                          <?= $role == "Produksi" ? '<th>Aksi</th>' : '' ?>
                        </tr>
                      </thead>
                      <tbody>
                        <?php  
                        $order = $db->selectTable("data_pemesanan","id_owner",$id,"status_order","Siap Cetak");
                        while($roworder=mysqli_fetch_assoc($order)){
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
                              echo $roworder['keterangan_order'];
                              // echo 'Kab/Kota: '.$customer['kab'].'<br>';
                              // echo 'Kec: '.$customer['kec'].'<br>';
                              // echo 'Kode Pos: '.$customer['kodepos'].'<br>';
                            ?>
                          </td>
                          <td>
                            <?= showDesigner($roworder['id_designer']) ?>
                          </td>
                          <td><?= $db->dateFormatter($roworder['tgl_order']) ?></td>
                          
                          <td>
                            <a target="_blank" href="<?= $roworder['hasil_desain_order'] ?>">View Desain</a>
                          </td>

                          <td><?= '<h5><span class="badge bg-warning">'.$roworder['status_order'].'</span></h5>' ?></td>
                          <?php if($role == "Produksi"){ ?>
                          <td>
                          <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#proses_cetakan<?= $roworder['id_order'] ?>"  data-bs-placement="top" title="Cetak">
                              <i class="ri-printer-line"></i>
                            </button>
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
        <!-- Modal -->
        <?php  
        $order = $db->selectTable("data_pemesanan","id_owner",$id,"status_order","Siap Cetak");
        while($roworder=mysqli_fetch_assoc($order)){
          $spkdb = $roworder['code_order']
        ?>
        <div class="modal fade" id="proses_cetakan<?= $roworder['id_order'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <form action="" method="post" class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Cetak Orderan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row g-3">
                  <input type="hidden" name="id_order" value="<?= $roworder['id_order'] ?>">
                  <div class="col-sm-12">
                    <label class="form-label">Link Google Drive</label>
                      <div class="input-group">
                        <input type="text" id="link" value="<?= $roworder['link_google_drive'] ?>" class="form-control" disabled readonly>
                        <button type="button" class="btn btn-outline-secondary" onclick="copylink()">
                          <i class="ri-clipboard-line"></i>
                        </button>
                      </div>
                  </div>
                  <div class="col-sm-12">
                    <select name="percetakan" class="form-select">
                      <?php  
                      $queryper = $db->selectTable("data_percetakan","id_owner",$id);
                      while($rowper=mysqli_fetch_assoc($queryper)){
                      ?>
                      <option value="<?= $rowper['id_percetakan'] ?>"><?= $rowper['nama_percetakan'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <label for="bahan" class="col-sm-2 col-form-label">Bahan</label>
                  <div class="col-sm-10">
                    <select name="bahan" id="bahan" class="form-select">
                      <?php  
                        $chooce = $db->selectTable("bahan_stiker","id_owner",$id);
                        while($rowchooce = mysqli_fetch_assoc($chooce)){
                      ?>
                      <option value="<?= $rowchooce['id_bahan'] ?>"><?= $db->formatJenis("",$rowchooce['id_bahan'],null,$id) ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <label for="lebar" class="col-sm-2 col-form-label">Lebar</label>
                  <div class="col-sm-10">
                    <div class="input-group">
                      <input type="number" name="lebar" id="lebar" class="form-control">
                      <span class="input-group-text">CM</span>
                    </div>
                  </div>
                  <label for="panjang" class="col-sm-2 col-form-label">Panjang</label>
                  <div class="col-sm-10">
                    <div class="input-group">
                      <input type="number" name="panjang" id="panjang" class="form-control">
                      <span class="input-group-text">CM</span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="aksi_cetak" class="btn btn-primary">Cetak</button>
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
      function copylink() {
        /* Get the text field */
        var copyText = document.getElementById("link");

        /* Select the text field */
        copyText.select();
        copyText.setSelectionRange(0, 99999); /* For mobile devices */

        /* Copy the text inside the text field */
        navigator.clipboard.writeText(copyText.value);
        
          Swal.fire({
            title:"Berhasil!",
            text:"Copy Link "+copyText.value,
            icon:"success",
          })
      }
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