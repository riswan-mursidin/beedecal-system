<?php  
require_once "action/DbClass.php";

if($_SESSION['login_stiker_admin'] != true ){
  header('Location: auth-login');
  exit();
}

$db = new ConfigClass();

$userselect = $db->selectTable("user_galeri","id_user",$_SESSION['login_stiker_id']);
$row = mysqli_fetch_assoc($userselect);
$usernamelogin = $row['username_user']
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
                  <h4 class="mb-sm-0">Grafik Pemesanan</h4>

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
                    <div id="line_chart_dashed" class="apex-charts" dir="ltr"></div>
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

    <!-- Plugin Js-->
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>
        <!-- demo js-->
    <?php  
    
    ?>
    <script>
      const formatRupiah = (money) => {
        return new Intl.NumberFormat('id-ID',
          { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }
        ).format(money);
      }
      $("#line_chart_dashed").length&&(options={
        chart:{
            height:380,
            type:"line",
            zoom:{
                enabled:!1
            },toolbar:{
                show:!1
            }
        },
        colors:["#11c46e","#0db4d6","#fb4d53"],
        dataLabels:{
            enabled:!1
        },
        stroke:{
            width:[3,4,3],
            curve:"straight",
            dashArray:[0,8,5]},
            series:[
                {
                    name:"Total Pendapatan Rp.",
                    data:[
                      parseInt(<?php
                      $month = 01; $year = date("Y");
                      $query = "SELECT harga_produk_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
                      $result = mysqli_query($db->conn, $query);
                      if(mysqli_num_rows($result) != 0){
                        $count = 0;
                        while($rowcount=mysqli_fetch_assoc($result)){
                          $count += $rowcount['harga_produk_order'];
                        }
                        echo $count;
                      }
                      ?>),
                      parseInt(<?php
                      $month = 2; $year = date("Y");
                      $query = "SELECT harga_produk_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
                      $result = mysqli_query($db->conn, $query);
                      if(mysqli_num_rows($result) != 0){
                        $count = 0;
                        while($rowcount=mysqli_fetch_assoc($result)){
                          $count += $rowcount['harga_produk_order'];
                        }
                        echo $count;
                      }
                      ?>),
                      parseInt(<?php
                      $month = 3; $year = date("Y");
                      $query = "SELECT harga_produk_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
                      $result = mysqli_query($db->conn, $query);
                      if(mysqli_num_rows($result) != 0){
                        $count = 0;
                        while($rowcount=mysqli_fetch_assoc($result)){
                          $count += $rowcount['harga_produk_order'];
                        }
                        echo $count;
                      }
                      ?>),
                      parseInt(<?php
                      $month = 4; $year = date("Y");
                      $query = "SELECT harga_produk_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
                      $result = mysqli_query($db->conn, $query);
                      if(mysqli_num_rows($result) != 0){
                        $count = 0;
                        while($rowcount=mysqli_fetch_assoc($result)){
                          $count += $rowcount['harga_produk_order'];
                        }
                        echo $count;
                      }
                      ?>),
                      parseInt(<?php
                      $month = 5; $year = date("Y");
                      $query = "SELECT harga_produk_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
                      $result = mysqli_query($db->conn, $query);
                      if(mysqli_num_rows($result) != 0){
                        $count = 0;
                        while($rowcount=mysqli_fetch_assoc($result)){
                          $count += $rowcount['harga_produk_order'];
                        }
                        echo $count;
                      }
                      ?>),
                      parseInt(<?php
                      $month = 6; $year = date("Y");
                      $query = "SELECT harga_produk_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
                      $result = mysqli_query($db->conn, $query);
                      if(mysqli_num_rows($result) != 0){
                        $count = 0;
                        while($rowcount=mysqli_fetch_assoc($result)){
                          $count += $rowcount['harga_produk_order'];
                        }
                        echo $count;
                      }
                      ?>),
                      parseInt(<?php
                      $month = 7; $year = date("Y");
                      $query = "SELECT harga_produk_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
                      $result = mysqli_query($db->conn, $query);
                      if(mysqli_num_rows($result) != 0){
                        $count = 0;
                        while($rowcount=mysqli_fetch_assoc($result)){
                          $count += $rowcount['harga_produk_order'];
                        }
                        echo $count;
                      }
                      ?>),
                      parseInt(<?php
                      $month = 8; $year = date("Y");
                      $query = "SELECT harga_produk_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
                      $result = mysqli_query($db->conn, $query);
                      if(mysqli_num_rows($result) != 0){
                        $count = 0;
                        while($rowcount=mysqli_fetch_assoc($result)){
                          $count += $rowcount['harga_produk_order'];
                        }
                        echo $count;
                      }
                      ?>),
                      parseInt(<?php
                      $month = 9; $year = date("Y");
                      $query = "SELECT harga_produk_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
                      $result = mysqli_query($db->conn, $query);
                      if(mysqli_num_rows($result) != 0){
                        $count = 0;
                        while($rowcount=mysqli_fetch_assoc($result)){
                          $count += $rowcount['harga_produk_order'];
                        }
                        echo $count;
                      }
                      ?>),
                      parseInt(<?php
                      $month = 10; $year = date("Y");
                      $query = "SELECT harga_produk_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
                      $result = mysqli_query($db->conn, $query);
                      if(mysqli_num_rows($result) != 0){
                        $count = 0;
                        while($rowcount=mysqli_fetch_assoc($result)){
                          $count += $rowcount['harga_produk_order'];
                        }
                        echo $count;
                      }
                      ?>),
                      parseInt(<?php
                      $month = 11; $year = date("Y");
                      $query = "SELECT harga_produk_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
                      $result = mysqli_query($db->conn, $query);
                      if(mysqli_num_rows($result) != 0){
                        $count = 0;
                        while($rowcount=mysqli_fetch_assoc($result)){
                          $count += $rowcount['harga_produk_order'];
                        }
                        echo $count;
                      }
                      ?>),
                      parseInt(<?php
                      $month = 12; $year = date("Y");
                      $query = "SELECT harga_produk_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
                      $result = mysqli_query($db->conn, $query);
                      if(mysqli_num_rows($result) != 0){
                        $count = 0;
                        while($rowcount=mysqli_fetch_assoc($result)){
                          $count += $rowcount['harga_produk_order'];
                        }
                        echo $count;
                      }
                      ?>)
                    ]
                }
            ],
        title:{
            text:"Pendapatan Tahun <?= date("Y") ?>",
            align:"left"
        },
        markers:{
            size:0,
            hover:{
                sizeOffset:6
            }
        },
        xaxis:{
            categories:[
              "<?php
              $month = 01; $year = date("Y");
              $query = "SELECT id_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
              $result = mysqli_query($db->conn, $query);
              if(mysqli_num_rows($result) != 0){
                echo "Jan";
              }
              ?>",
              "<?php
              $month = 2; $year = date("Y");
              $query = "SELECT id_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
              $result = mysqli_query($db->conn, $query);
              if(mysqli_num_rows($result) != 0){
                echo "Feb";
              }
              ?>",
              "<?php
              $month = 3; $year = date("Y");
              $query = "SELECT id_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
              $result = mysqli_query($db->conn, $query);
              if(mysqli_num_rows($result) != 0){
                echo "Mar";
              }
              ?>",
              "<?php
              $month = 4; $year = date("Y");
              $query = "SELECT id_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
              $result = mysqli_query($db->conn, $query);
              if(mysqli_num_rows($result) != 0){
                echo "Apr";
              }
              ?>",
              "<?php
              $month = 5; $year = date("Y");
              $query = "SELECT id_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
              $result = mysqli_query($db->conn, $query);
              if(mysqli_num_rows($result) != 0){
                echo "Mei";
              }
              ?>",
              "<?php
              $month = 6; $year = date("Y");
              $query = "SELECT id_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
              $result = mysqli_query($db->conn, $query);
              if(mysqli_num_rows($result) != 0){
                echo "Jun";
              }
              ?>",
              "<?php
              $month = 7; $year = date("Y");
              $query = "SELECT id_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
              $result = mysqli_query($db->conn, $query);
              if(mysqli_num_rows($result) != 0){
                echo "Jul";
              }
              ?>",
              "<?php
              $month = 8; $year = date("Y");
              $query = "SELECT id_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
              $result = mysqli_query($db->conn, $query);
              if(mysqli_num_rows($result) != 0){
                echo "Agu";
              }
              ?>",
              "<?php
              $month = 9; $year = date("Y");
              $query = "SELECT id_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
              $result = mysqli_query($db->conn, $query);
              if(mysqli_num_rows($result) != 0){
                echo "Sep";
              }
              ?>",
              "<?php
              $month = 10; $year = date("Y");
              $query = "SELECT id_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
              $result = mysqli_query($db->conn, $query);
              if(mysqli_num_rows($result) != 0){
                echo "Okt";
              }
              ?>",
              "<?php
              $month = 11; $year = date("Y");
              $query = "SELECT id_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
              $result = mysqli_query($db->conn, $query);
              if(mysqli_num_rows($result) != 0){
                echo "Nov";
              }
              ?>",
              "<?php
              $month = 12; $year = date("Y");
              $query = "SELECT id_order FROM data_pemesanan WHERE month(tgl_order)='$month' And year(tgl_order)='$year'";
              $result = mysqli_query($db->conn, $query);
              if(mysqli_num_rows($result) != 0){
                echo "Des";
              }
              ?>"
            ]
        },tooltip:{
            y:[{
                title:{
                    formatter:function(e){
                        return " "+e
                    }
                }
            },
            {
                title:{
                    formatter:function(e){
                        return e+" per session"
                    }
                }
            },
            {
                title:{
                    formatter:function(e){
                        return e
                    }
                }
            }]
        },grid:{
            borderColor:"#f1f1f1"
        }
    },(chart=new ApexCharts(document.querySelector("#line_chart_dashed"),options)).render())
    </script>

    <script src="assets/js/app.js"></script>
  </body>
</html>
<?php mysqli_close($db->conn) ?>