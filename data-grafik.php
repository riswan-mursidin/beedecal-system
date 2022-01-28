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
                        <div id="column_chart" class="apex-charts" dir="ltr"></div>
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
      function showData(){
        const rupiah = (number)=>{
            return new Intl.NumberFormat("id-ID", {
              style: "currency",
              currency: "IDR"
            }).format(number);
        }
        $.ajax({
          url:"datachart.php",
          method:"POST",
          data:{action:"perbulan",id_owner:"<?= $id ?>"},
          dataType:"JSON",
          success:function(data){
            var jum = [];
            var pasang = [];

            for(var index = 0; index < data.length; index++){
              jum.push(data[index].total);
              pasang.push(data[index].pemasangan);
            }
            $("#column_chart").length&&(options={
              chart:{
                  height:350,
                  type:"bar",
                  toolbar:{
                      show:!1
                  }
              },
              plotOptions:{
                  bar:{
                      horizontal:!1,
                      columnWidth:"45%",
                      endingShape:"flat"
                  }
              },
              dataLabels:{
                  enabled:!1
              },
              stroke:{
                  show:!0,
                  width:2,
                  colors:["transparent"]
              },
              series:[
                  {
                      name:"Penjualan",
                      data:jum
                  },
                  {
                      name:"Pemasangan",
                      data:pasang
                  }
              ],
              colors:["#FFC600","#612897"],
              xaxis:{
                  categories:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]
              },
              yaxis:{
                  title:{
                      text:"Rp (rupiah)"
                  }
              },
              grid:{
                  borderColor:"#f1f1f1"
              },
              fill:{
                  opacity:1
              },
              tooltip:{
                  y:{
                      formatter:function(e){
                          return rupiah(e)
                      }
                  }
              }
            },(chart=new ApexCharts(document.querySelector("#column_chart"),options)).render())
          }
        });
      }

      showData();
    </script>

    <script src="assets/js/app.js"></script>
  </body>
</html>
<?php mysqli_close($db->conn) ?>