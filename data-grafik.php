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
    if($satuan == "rupiah"){
      $result['hasil'] = ($harga + $count) - $disk;
    }
    return $result;
  }else{
    $diskon = $harga * ($disk/100);
    $result['hasil'] = $harga - $diskon;
    if($satuan == "rupiah"){
      $result['hasil'] = $harga - $disk;
    }
    return $result;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>STIKER | DATA GRAFIK</title>
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
                  <h4 class="mb-sm-0">Grafik</h4>

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
              <div class="col-12">
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-12">
                        <div id="column_chart_datalabel" class="apex-charts" dir="ltr"></div>
                      </div>
                    </div>
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

    <!-- Plugin Js-->
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
        <!-- demo js-->
    <?php  
    
    ?>
    <script>
          const rupiah = (number)=>{
            return new Intl.NumberFormat("id-ID", {
              style: "currency",
              currency: "IDR"
            }).format(number);
          }
          $("#column_chart_datalabel").length&&(options={
            chart:{
                height:350,
                type:"bar",
                toolbar:{show:!1}
            },
            plotOptions:{
                bar:{
                    dataLabels:{position:"top"}
                }
            },
            dataLabels:{
                enabled:!0,formatter:function(e){
                    return rupiah(e);
                },
                offsetY:-21,
                style:{
                    fontSize:"12px",
                    colors:["#304758"]
                }
            },
            series:[
                {
                    name:"Penjualan",
                    data:[
                      <?php
                      $year = date("Y"); $month = 1;
                      $count = 0;
                      $omset_query = "SELECT code_order,harga_produk_order,diskon_order,satuan_potongan FROM data_pemesanan WHERE month(tgl_order)='$month' AND year(tgl_order)='$year' AND id_owner='$id'";
                      $result_omset = mysqli_query($db->conn, $omset_query);
                      if(mysqli_num_rows($result_omset) >0){
                        while($rowomset = mysqli_fetch_assoc($result_omset)){
                          $counttam = 0;
                          $resultdisk = resultDiskon($id,$rowomset['code_order'],$rowomset['harga_produk_order'],$rowomset['diskon_order'],$rowomset['satuan_potongan']);
                          $count += $resultdisk['hasil'];
                        }
                      }
                      echo $count
                      ?>,
                      <?php
                      $year = date("Y"); $month = 2;
                      $count = 0;
                      $omset_query = "SELECT harga_produk_order,diskon_order,satuan_potongan FROM data_pemesanan WHERE month(tgl_order)='$month' AND year(tgl_order)='$year' AND id_owner='$id'";
                      $result_omset = mysqli_query($db->conn, $omset_query);
                      if(mysqli_num_rows($result_omset) >0){
                        while($rowomset = mysqli_fetch_assoc($result_omset)){
                          $counttam = 0;
                          $resultdisk = resultDiskon($id,$rowomset['code_order'],$rowomset['harga_produk_order'],$rowomset['diskon_order'],$rowomset['satuan_potongan']);
                          $count += $resultdisk['hasil'];
                        }
                      }
                      echo $count
                      ?>,
                      <?php
                      $year = date("Y"); $month = 3;
                      $count = 0;
                      $omset_query = "SELECT harga_produk_order,diskon_order,satuan_potongan FROM data_pemesanan WHERE month(tgl_order)='$month' AND year(tgl_order)='$year' AND id_owner='$id'";
                      $result_omset = mysqli_query($db->conn, $omset_query);
                      if(mysqli_num_rows($result_omset) >0){
                        while($rowomset = mysqli_fetch_assoc($result_omset)){
                          $counttam = 0;
                          $resultdisk = resultDiskon($id,$rowomset['code_order'],$rowomset['harga_produk_order'],$rowomset['diskon_order'],$rowomset['satuan_potongan']);
                          $count += $resultdisk['hasil'];
                        }
                      }
                      echo $count
                      ?>,
                      <?php
                      $year = date("Y"); $month = 4;
                      $count = 0;
                      $omset_query = "SELECT harga_produk_order,diskon_order,satuan_potongan FROM data_pemesanan WHERE month(tgl_order)='$month' AND year(tgl_order)='$year' AND id_owner='$id'";
                      $result_omset = mysqli_query($db->conn, $omset_query);
                      if(mysqli_num_rows($result_omset) >0){
                        while($rowomset = mysqli_fetch_assoc($result_omset)){
                          $counttam = 0;
                          $resultdisk = resultDiskon($id,$rowomset['code_order'],$rowomset['harga_produk_order'],$rowomset['diskon_order'],$rowomset['satuan_potongan']);
                          $count += $resultdisk['hasil'];
                        }
                      }
                      echo $count
                      ?>,
                      <?php
                      $year = date("Y"); $month = 5;
                      $count = 0;
                      $omset_query = "SELECT harga_produk_order,diskon_order,satuan_potongan FROM data_pemesanan WHERE month(tgl_order)='$month' AND year(tgl_order)='$year' AND id_owner='$id'";
                      $result_omset = mysqli_query($db->conn, $omset_query);
                      if(mysqli_num_rows($result_omset) >0){
                        while($rowomset = mysqli_fetch_assoc($result_omset)){
                          $counttam = 0;
                          $resultdisk = resultDiskon($id,$rowomset['code_order'],$rowomset['harga_produk_order'],$rowomset['diskon_order'],$rowomset['satuan_potongan']);
                          $count += $resultdisk['hasil'];
                        }
                      }
                      echo $count
                      ?>,
                      <?php
                      $year = date("Y"); $month = 6;
                      $count = 0;
                      $omset_query = "SELECT harga_produk_order,diskon_order,satuan_potongan FROM data_pemesanan WHERE month(tgl_order)='$month' AND year(tgl_order)='$year' AND id_owner='$id'";
                      $result_omset = mysqli_query($db->conn, $omset_query);
                      if(mysqli_num_rows($result_omset) >0){
                        while($rowomset = mysqli_fetch_assoc($result_omset)){
                          $counttam = 0;
                          $resultdisk = resultDiskon($id,$rowomset['code_order'],$rowomset['harga_produk_order'],$rowomset['diskon_order'],$rowomset['satuan_potongan']);
                          $count += $resultdisk['hasil'];
                        }
                      }
                      echo $count
                      ?>,
                      <?php
                      $year = date("Y"); $month = 7;
                      $count = 0;
                      $omset_query = "SELECT harga_produk_order,diskon_order,satuan_potongan FROM data_pemesanan WHERE month(tgl_order)='$month' AND year(tgl_order)='$year' AND id_owner='$id'";
                      $result_omset = mysqli_query($db->conn, $omset_query);
                      if(mysqli_num_rows($result_omset) >0){
                        while($rowomset = mysqli_fetch_assoc($result_omset)){
                          $counttam = 0;
                          $resultdisk = resultDiskon($id,$rowomset['code_order'],$rowomset['harga_produk_order'],$rowomset['diskon_order'],$rowomset['satuan_potongan']);
                          $count += $resultdisk['hasil'];
                        }
                      }
                      echo $count
                      ?>,
                      <?php
                      $year = date("Y"); $month = 8;
                      $count = 0;
                      $omset_query = "SELECT harga_produk_order,diskon_order,satuan_potongan FROM data_pemesanan WHERE month(tgl_order)='$month' AND year(tgl_order)='$year' AND id_owner='$id'";
                      $result_omset = mysqli_query($db->conn, $omset_query);
                      if(mysqli_num_rows($result_omset) >0){
                        while($rowomset = mysqli_fetch_assoc($result_omset)){
                          $counttam = 0;
                          $resultdisk = resultDiskon($id,$rowomset['code_order'],$rowomset['harga_produk_order'],$rowomset['diskon_order'],$rowomset['satuan_potongan']);
                          $count += $resultdisk['hasil'];
                        }
                      }
                      echo $count
                      ?>,
                      <?php
                      $year = date("Y"); $month = 9;
                      $count = 0;
                      $omset_query = "SELECT harga_produk_order,diskon_order,satuan_potongan FROM data_pemesanan WHERE month(tgl_order)='$month' AND year(tgl_order)='$year' AND id_owner='$id'";
                      $result_omset = mysqli_query($db->conn, $omset_query);
                      if(mysqli_num_rows($result_omset) >0){
                        while($rowomset = mysqli_fetch_assoc($result_omset)){
                          $counttam = 0;
                          $resultdisk = resultDiskon($id,$rowomset['code_order'],$rowomset['harga_produk_order'],$rowomset['diskon_order'],$rowomset['satuan_potongan']);
                          $count += $resultdisk['hasil'];
                        }
                      }
                      echo $count
                      ?>,
                      <?php
                      $year = date("Y"); $month = 10;
                      $count = 0;
                      $omset_query = "SELECT harga_produk_order,diskon_order,satuan_potongan FROM data_pemesanan WHERE month(tgl_order)='$month' AND year(tgl_order)='$year' AND id_owner='$id'";
                      $result_omset = mysqli_query($db->conn, $omset_query);
                      if(mysqli_num_rows($result_omset) >0){
                        while($rowomset = mysqli_fetch_assoc($result_omset)){
                          $counttam = 0;
                          $resultdisk = resultDiskon($id,$rowomset['code_order'],$rowomset['harga_produk_order'],$rowomset['diskon_order'],$rowomset['satuan_potongan']);
                          $count += $resultdisk['hasil'];
                        }
                      }
                      echo $count
                      ?>,
                      <?php
                      $year = date("Y"); $month = 11;
                      $count = 0;
                      $omset_query = "SELECT harga_produk_order,diskon_order,satuan_potongan FROM data_pemesanan WHERE month(tgl_order)='$month' AND year(tgl_order)='$year' AND id_owner='$id'";
                      $result_omset = mysqli_query($db->conn, $omset_query);
                      if(mysqli_num_rows($result_omset) >0){
                        while($rowomset = mysqli_fetch_assoc($result_omset)){
                          $counttam = 0;
                          $resultdisk = resultDiskon($id,$rowomset['code_order'],$rowomset['harga_produk_order'],$rowomset['diskon_order'],$rowomset['satuan_potongan']);
                          $count += $resultdisk['hasil'];
                        }
                      }
                      echo $count
                      ?>,
                      <?php
                      $year = date("Y"); $month = 12;
                      $count = 0;
                      $omset_query = "SELECT harga_produk_order,diskon_order,satuan_potongan FROM data_pemesanan WHERE month(tgl_order)='$month' AND year(tgl_order)='$year' AND id_owner='$id'";
                      $result_omset = mysqli_query($db->conn, $omset_query);
                      if(mysqli_num_rows($result_omset) >0){
                        while($rowomset = mysqli_fetch_assoc($result_omset)){
                          $counttam = 0;
                          $resultdisk = resultDiskon($id,$rowomset['code_order'],$rowomset['harga_produk_order'],$rowomset['diskon_order'],$rowomset['satuan_potongan']);
                          $count += $resultdisk['hasil'];
                        }
                      }
                      echo $count
                      ?>
                    ]
                }
            ],
            colors:["#0db4d6"],
            grid:{
                borderColor:"#f1f1f1"
            },
            xaxis:{
                categories:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
                position:"top",
                labels:{
                    offsetY:-18
                },
                axisBorder:{
                    show:!1
                },
                axisTicks:{
                    show:!1
                },
                crosshairs:{
                    fill:{
                        type:"gradient",
                        gradient:{
                            colorFrom:"#D8E3F0",
                            colorTo:"#BED1E6",
                            stops:[0,100],
                            opacityFrom:.4,
                            opacityTo:.5
                        }
                    }
                },
                tooltip:{
                    enabled:!0,offsetY:-35
                }
            },
            fill:{
                gradient:{
                    shade:"light",
                    type:"horizontal",
                    shadeIntensity:.25,
                    gradientToColors:void 0,
                    inverseColors:!0,
                    opacityFrom:1,
                    opacityTo:1,
                    stops:[50,0,100,100]
                }
            },
            yaxis:{
                axisBorder:{
                    show:!1
                },
                axisTicks:{
                    show:!1
                },
                labels:{
                    show:!1,
                    formatter:function(e){
                        return rupiah(e)
                    }
                }
            },
            title:{
                text:"OMSET TAHUN <?= date("Y") ?>",
                floating:!0,
                offsetY:320,
                align:"center",
                style:{
                    color:"#444"
                }
            }
          },
          (chart=new ApexCharts(document.querySelector("#column_chart_datalabel"),options)).render())
    </script>

    <script src="assets/js/app.js"></script>
  </body>
</html>
<?php mysqli_close($db->conn) ?>