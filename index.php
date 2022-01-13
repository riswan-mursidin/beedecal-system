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

function resultDiskon($owner,$spk,$harga,$disk,$satuan){
  global $db;
  $count = 0;
  $tamby = $db->selectTable("biaya_tambahan_order","id_owner",$owner,"code_order",$spk);
  if(mysqli_num_rows($tamby) > 0){
    while($rowtamby=mysqli_fetch_assoc($tamby)){
      $count += $rowtamby['harga_ketbiaya'];
    }
    $diskon = ($harga + $count) * ($disk/100);
    $result['hasil'] = ($harga + $count) - $diskon;
    $result['tamby'] = $count;
    if($satuan == "rupiah"){
      $result['hasil'] = ($harga + $count) - $disk;
    }
    return $result;
  }else{
    $diskon = $harga * ($disk/100);
    $result['hasil'] = $harga - $diskon;
    $result['tamby'] = 0;
    if($satuan == "rupiah"){
      $result['hasil'] = $harga - $disk;
    }
    return $result;
  }
}


// penjualan hari ini
$total_ordee = 0; $total_by = 0;
$ordertoday = $db->selectTable("data_pemesanan","id_owner",$id,"tgl_order",date("Y-m-d"));
while($roworder=mysqli_fetch_assoc($ordertoday)){
  $counttam = 0;
  $resultdisk = resultDiskon($id,$roworder['code_order'],$roworder['harga_produk_order'],$roworder['diskon_order'],$roworder['satuan_potongan']);
  // $hasil = ($roworder['harga_produk_order'] + $counttam) - $potongan;
  $total_ordee += $resultdisk['hasil'];
  
}
// penjualan bulan ini
$total_ordeer_bulan_ini = 0; $pemasangan_bln = 0;
$date = date("m");
$year = date("Y");
$queribulan = "SELECT * FROM data_pemesanan WHERE month(tgl_order)='$date' AND year(tgl_order)='$year' AND id_owner='$id'";
$orderbulan = mysqli_query($db->conn, $queribulan);
while($roworder_bulan=mysqli_fetch_assoc($orderbulan)){
  $counttam = 0;
  $resultdisk = resultDiskon($id,$roworder_bulan['code_order'],$roworder_bulan['harga_produk_order'],$roworder_bulan['diskon_order'],$roworder_bulan['satuan_potongan']);
  $total_ordeer_bulan_ini += $resultdisk['hasil'];


  $code_spk = $roworder_bulan['code_order'];
}
$rata_rata = $total_ordeer_bulan_ini / intval(date("d"));
$rata_rata_pasang = $pemasangan_bln / intval(date("d"));

$kalender = CAL_GREGORIAN;
$bulan = $date;
$tahun = date("Y");
$hari = cal_days_in_month($kalender,$bulan,$tahun);

$target = $rata_rata * $hari;
$target_pasang = $rata_rata_pasang * $hari;

// pemasukan hari ini
$countincome_d = 0;
$incometoday = $db->selectTable("detail_transaksi","id_owner",$id,"tgl_transaksi",date("Y-m-d"));
while($rowincometoday=mysqli_fetch_assoc($incometoday)){
  $countincome_d += $rowincometoday['jumlah_transaksi'];
}

// pemasangan bulan ini

function produkStaff($param,$owner){
  global $db; $jum = "";
  $month = date("m");
  $year = date("Y");
  if($param == "selesai"){
    $queryselesai = mysqli_query($db->conn,"SELECT id_order FROM data_pemesanan WHERE month(tgl_order)='$month' AND year(tgl_order)='$year' AND status_order='Selesai' AND id_owner='$owner'");
    $jum = mysqli_num_rows($queryselesai);
    
  }elseif($param == "pending"){
    $queryselesai = mysqli_query($db->conn,"SELECT id_order FROM data_pemesanan WHERE month(tgl_order)='$month' AND year(tgl_order)='$year' AND produksi_status='Tidak' AND id_owner='$owner' AND status_order<>'Selesai'");
    $jum = mysqli_num_rows($queryselesai);
  }elseif($param == "proses"){
    $queryselesai = mysqli_query($db->conn,"SELECT id_order FROM data_pemesanan WHERE month(tgl_order)='$month' AND year(tgl_order)='$year' AND produksi_status='Ya' AND status_order<>'Selesai' AND id_owner='$owner'");
    $jum = mysqli_num_rows($queryselesai);
  }
  $query = mysqli_query($db->conn,"SELECT id_order FROM data_pemesanan WHERE month(tgl_order)='$month' AND year(tgl_order)='$year' AND id_owner='$owner'");
  $total = mysqli_num_rows($query);
  $result = ($jum/$total) * 100; 
  return $result;
}


function showClossing($id_user,$lvl,$owner){
  global $db;
  $count = 0;
  $month = date("m");
  $year = date("Y");
  if($lvl == "Admin"){
    $check = mysqli_query($db->conn,"SELECT user_editor FROM data_pemesanan WHERE id_owner='$owner' AND month(tgl_order)='$month' AND year(tgl_order)='$year' AND user_editor='$id_user'");
    $count = mysqli_num_rows($check);
  }elseif($lvl == "Desainer"){
    $check = mysqli_query($db->conn,"SELECT user_editor FROM data_pemesanan WHERE id_owner='$owner' AND month(tgl_order)='$month' AND year(tgl_order)='$year' AND id_designer='$id_user'");
    $count = mysqli_num_rows($check);
  }elseif($lvl == "Pemasanng"){
    $check = mysqli_query($db->conn,"SELECT user_editor FROM data_pemesanan WHERE id_owner='$owner' AND month(tgl_order)='$month' AND year(tgl_order)='$year' AND pemasang_order='$id_user'");
    $count = mysqli_num_rows($check);
  }elseif($lvl == "Produksi"){
    $check = mysqli_query($db->conn,"SELECT user_editor FROM data_pemesanan WHERE id_owner='$owner' AND month(tgl_order)='$month' AND year(tgl_order)='$year' AND id_produksi='$id_user'");
    $count = mysqli_num_rows($check);
  }
  return $count;
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>STIKER | DASHBOARD</title>
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

    <!-- card-home -->
    <link href="assets/css/card-home.css" rel="stylesheet" type="text/css" />

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
                  <h4 class="mb-sm-0">Dashboard</h4>

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

            <!-- contant -->
            <div class="row">
              <div class="col-sm-6 col-12">
                <div class="card green">
                  <div class="card-body">
                      <div class="d-flex text-white">
                          <div class="flex-shrink-0  me-3 align-self-center">
                              <div class="avatar-sm">
                                  <div class="avatar-title bg-light rounded-circle text-primary font-size-20">
                                      <i class="mdi mdi-cart-outline"></i>
                                  </div>
                              </div>
                          </div>
                          <div class="flex-grow-1 overflow-hidden">
                              <p class="mb-1">Pesanan /Hari</p>
                              <h5 class="mb-3 text-white">Rp.<?= number_format($total_ordee,0,".",",") ?></h5>
                              <p class="text-truncate mb-0">
                                <span class="text-white me-2">
                                  <?= number_format($rata_rata) ?>
                                  <i class="ri-arrow-right-up-line align-bottom ms-1"></i>
                                </span> 
                                Avg
                              </p>
                          </div>
                      </div>
                  </div>
                  <!-- end card-body -->
                </div>
              </div>
              <div class="col-sm-6 col-12">
                <div class="card orange">
                  <div class="card-body">
                      <div class="d-flex text-white">
                          <div class="flex-shrink-0  me-3 align-self-center">
                              <div class="avatar-sm">
                                  <div class="avatar-title bg-light rounded-circle text-warning font-size-20">
                                      <i class="ri-calendar-todo-fill"></i>
                                  </div>
                              </div>
                          </div>
                          <div class="flex-grow-1 overflow-hidden">
                              <p class="mb-1">Pesanan /Bulan</p>
                              <h5 class="mb-3 text-white">Rp.<?= number_format($total_ordeer_bulan_ini) ?></h5>
                              <p class="text-truncate mb-0">
                                <span class="text-white me-2" >
                                  <?= number_format($target - $total_ordeer_bulan_ini) ?> - <?= number_format($target) ?>
                                  <i class="ri-arrow-right-up-line align-bottom ms-1"></i>
                                </span> 
                                Target
                              </p>
                          </div>
                      </div>
                  </div>
                  <!-- end card-body -->
                </div>
              </div>
              <div class="col-sm-6 col-12">
                <div class="card blue">
                  <div class="card-body">
                      <div class="d-flex text-white">
                          <div class="flex-shrink-0  me-3 align-self-center">
                              <div class="avatar-sm">
                                  <div class="avatar-title bg-light rounded-circle text-blue font-size-20">
                                      <i class="ri-money-euro-box-line"></i>
                                  </div>
                              </div>
                          </div>
                          <div class="flex-grow-1 overflow-hidden">
                              <p class="mb-1">Pemasukan /Hari</p>
                              <h5 class="mb-3 text-white">Rp.<?= number_format($countincome_d) ?></h5>
                              <p class="text-truncate mb-0">
                                <span class="text-white me-2">
                                  Dompet
                                </span> 
                              </p>
                          </div>
                      </div>
                  </div>
                  <!-- end card-body -->
                </div>
              </div>
              <div class="col-sm-6 col-12">
                <div class="card purple ">
                  <div class="card-body">
                      <div class="d-flex text-white">
                          <div class="flex-shrink-0  me-3 align-self-center">
                              <div class="avatar-sm">
                                  <div class="avatar-title bg-light rounded-circle text-purple font-size-20">
                                      <i class="ri-list-settings-fill"></i>
                                  </div>
                              </div>
                          </div>
                          <div class="flex-grow-1 overflow-hidden">
                              <p class="mb-1">Pemasangan /Bulan</p>
                              <h5 class="mb-3 text-white">Rp.<?= number_format($pemasangan_bln) ?></h5>
                              <p class="text-truncate mb-0">
                                <span class="text-white me-2">
                                  0 - <?= number_format($target_pasang) ?>
                                  <i class="ri-arrow-right-up-line align-bottom ms-1"></i>
                                </span> 
                                Prediksi
                              </p>
                          </div>
                      </div>
                  </div>
                  <!-- end card-body -->
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 col-sm-12">
                <h5>Produksi</h5>
                <hr>
                <div class="card">
                  <div class="card-body">
                    <?php 
                      $selesai = produkStaff("selesai",$id); 
                      $proses = produkStaff("proses",$id); 
                      $pending = produkStaff("pending",$id); 
                    ?>
                    <div class="card-title">Presentasi Produksi</div>
                    <div>
                      <ul class="list-unstyled">
                          <li class="py-3">
                              <div class="d-flex">
                                  <div class="avatar-xs align-self-center me-3">
                                      <div class="avatar-title rounded-circle bg-light text-primary font-size-18">
                                          <i class="ri-checkbox-circle-line"></i>
                                      </div>
                                  </div>
                                  <div class="flex-grow-1">
                                      <p class="text-muted mb-2">Selesai</p>
                                      <div class="progress progress-sm animated-progess">
                                          <div class="progress-bar bg-success" role="progressbar" style="width: <?= number_format($selesai,2) ?>%" aria-valuenow="<?= number_format($selesai,2) ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                  </div>
                              </div>
                          </li>
                          <li class="py-3">
                              <div class="d-flex">
                                  <div class="avatar-xs align-self-center me-3">
                                      <div class="avatar-title rounded-circle bg-light text-primary font-size-18">
                                          <i class="ri-loader-2-line"></i>
                                      </div>
                                  </div>
                                  <div class="flex-grow-1">
                                      <p class="text-muted mb-2">Proses</p>
                                      <div class="progress progress-sm animated-progess">
                                          <div class="progress-bar bg-warning" role="progressbar" style="width: <?= number_format($proses,2) ?>%" aria-valuenow="<?= number_format($proses,2)?>" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                  </div>
                              </div>
                          </li>
                          <li class="py-3">
                              <div class="d-flex">
                                  <div class="avatar-xs align-self-center me-3">
                                      <div class="avatar-title rounded-circle bg-light text-primary font-size-18">
                                          <i class="ri-close-circle-line"></i>
                                      </div>
                                  </div>
                                  <div class="flex-grow-1">
                                      <p class="text-muted mb-2">Pending</p>
                                      <div class="progress progress-sm animated-progess">
                                          <div class="progress-bar bg-danger" role="progressbar" style="width: <?= number_format($pending,2) ?>%" aria-valuenow="<?= number_format($pending,2) ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                      </div>
                                  </div>
                              </div>
                          </li>
                      </ul>
                    </div>
                    <hr>
                      
                    <div class="text-center">
                        <div class="row">
                            <div class="col-4">
                                <div class="mt-2">
                                    <p class="text-muted mb-2">Selesai</p>
                                    <h5 class="font-size-16 mb-0"><?= number_format($selesai,2) ?>%</h5>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mt-2">
                                    <p class="text-muted mb-2">Proses</p>
                                    <h5 class="font-size-16 mb-0"><?= number_format($proses,2)?>%</h5>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mt-2">
                                    <p class="text-muted mb-2">Pending</p>
                                    <h5 class="font-size-16 mb-0"><?= number_format($pending,2) ?>%</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
                <h5 class="">Aktivitas Karyawan</h5>
                <hr>
                <div class="card">
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-centered table-nowrap mb-0">
                          <thead>
                              <tr>
                                  <th scope="col"  style="width: 60px;"></th>
                                  <th scope="col">Nama</th>
                                  <th scope="col">Level</th>
                                  <th scope="col">Closing</th>
                              </tr>
                          </thead>
                          <tbody>
                            <?php  
                            $karya = $db->selectTable("user_galeri","id_owner",$id);
                            while($rowkarya=mysqli_fetch_assoc($karya)){
                            ?>
                              <tr>
                                  <td>
                                      <img src="<?= $rowkarya["foto_user"] != "" ? $rowkarya["foto_user"] : "assets/images/users/avatar-2.png" ?>" alt="user" class="avatar-xs rounded-circle" />
                                  </td>
                                  <td>
                                      <h5 class="font-size-15 mb-0"><?= $db->nameFormater($rowkarya["fullname_user"]) ?></h5>
                                  </td>
                                  <td><?= strtoupper($rowkarya["level_user"]) ?></td>
                                  <td>
                                    <?= showClossing($rowkarya["id_user"],$rowkarya["level_user"],$id) ?>
                                  </td>
                              </tr>
                            <?php } ?>
                          </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col col-md-6 col-sm-12">
                <h5 class="mb-sm-0">Sumber</h5>
                <hr>
                <div class="row">
                  <?php  
                  $sumber = $db->selectTable("sumber_pemesanan","id_owner",$id);
                  while($rowsumber=mysqli_fetch_assoc($sumber)){
                    $date = date("m");
                    $year = date("Y");
                    $orderan = mysqli_query($db->conn,"SELECT * FROM data_pemesanan WHERE month(tgl_order)='$date' AND year(tgl_order)='$year' AND id_owner='$id' AND id_sumber='".$rowsumber['id_sumber']."'");
                    $countorder = mysqli_num_rows($orderan);
                    $fee = 0;
                    while($rowworder=mysqli_fetch_assoc($orderan)){
                      $resultdisk = resultDiskon($id,$rowworder['code_order'],$rowworder['harga_produk_order'],$rowworder['diskon_order'],$rowworder['satuan_potongan']);
                      $fee += $resultdisk['hasil'];
                    }
                    if($countorder!=0){
                  ?>
                  <div class="col-md-6 col-sm-6">
                    <div class="card">
                      <div class="card-body">
                          <div class="d-flex">
                              <div class="flex-shrink-0 me-3 align-self-center">
                                  <div id="radialchart-<?= $rowsumber['id_sumber'] ?>" class="apex-charts" dir="ltr"></div>
                              </div>
                              <div class="flex-grow-1 overflow-hidden">
                                  <p class="mb-1"><?= $rowsumber['name_sumber'] ?></p>
                                  <h5 class="mb-3"><?= $countorder ?> Pesanan</h5>
                                  <p class="text-truncate mb-0"><span class="text-success me-2"> Rp.<?= number_format($fee) ?> <i class="ri-arrow-right-up-line align-bottom ms-1"></i></span> Pendapatan</p>
                              </div>
                          </div>
                      </div>
                      <!-- end card-body -->
                      </div>
                  </div>
                  <?php }} ?>
                  <div class="col-md-12 col-sm-12">
                  <div class="card">
                                    <div class="card-body">
                                        <h4 class="card-title mb-4">Donut Chart</h4>
                                        
                                        <div id="donut_chart" class="apex-charts"  dir="ltr"></div>
                                    </div>
                                </div>
                  </div>
                  </div>
                </div>
              </div>
            <!-- end contant -->
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

    <!-- JAVASCRIPT -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>

    <!-- apexcharts js -->
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- jquery.vectormap map -->
    <script src="assets/libs/jqvmap/jquery.vmap.min.js"></script>
    <script src="assets/libs/jqvmap/maps/jquery.vmap.usa.js"></script>

    <!-- Plugin Js-->
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>

    <script>
      var randomColor = [];
      var colors = [];
      for(var i = 0; i < 5; i++){
        colors.push("#"+Math.floor(Math.random()*16777215).toString(16));
        // randomColor[i] = "#"+Math.floor(Math.random()*16777215).toString(16);
      }
      $("#donut_chart").length&&(options={
        chart:{
            height:320,type:"donut"
        },
        series:[44,55,41,17,15],
        labels:[
            "Series 1",
            "Series 2",
            "Series 3",
            "Series 4",
            "Series 5"
        ],
        colors,
        legend:{
            show:!0,
            position:"bottom",
            horizontalAlign:"center",
            verticalAlign:"middle",
            floating:!1,
            fontSize:"14px",
            offsetX:0,
            offsetY:-10
        },
        responsive:[
            {
                breakpoint:600,
                options:{
                    chart:{
                        height:240
                    },
                    legend:{
                        show:!1
                    }
                }
            }
        ]
      },(chart=new ApexCharts(document.querySelector("#donut_chart"),options)).render());
    </script>

    <?php  
    $date = date("m");
    $year = date("Y");
    $allorder = mysqli_query($db->conn,"SELECT * FROM data_pemesanan WHERE month(tgl_order)='$date' AND year(tgl_order)='$year' AND id_owner='$id'");
    $countall = mysqli_num_rows($allorder);
    $sumber = $db->selectTable("sumber_pemesanan","id_owner",$id);
    while($rowsumber=mysqli_fetch_assoc($sumber)){
      $orderan = mysqli_query($db->conn,"SELECT * FROM data_pemesanan WHERE month(tgl_order)='$date' AND year(tgl_order)='$year' AND id_owner='$id' AND id_sumber='".$rowsumber['id_sumber']."'");
      $countorder = mysqli_num_rows($orderan);
      $persen = ($countorder/$countall) * 100;
    ?>
    <script>
      var persen = parseInt("<?= $persen ?>");
      radialoptions={
        series:[persen],
        chart:{
            type:"radialBar",
            width:72,
            height:72,
            sparkline:{
                enabled:!0
            }
        },
        dataLabels:{
            enabled:!1
        },
        colors:["#0ab39c"],
        stroke:{
            lineCap:"round"
        },
        plotOptions:{
            radialBar:{
                hollow:{
                    margin:0,size:"70%"
                },
                track:{
                    margin:0
                },
                dataLabels:{
                    name:{
                        show:!1
                    },
                    value:{
                        offsetY:5,
                        show:!0
                    }
                }
            }
        }
      };
      (radialchart=new ApexCharts(document.querySelector("#radialchart-<?= $rowsumber['id_sumber'] ?>"),radialoptions)).render();
    </script>
    <?php } ?>

    <script src="assets/js/app.js"></script>
  </body>
</html>
<?php mysqli_close($db->conn) ?>