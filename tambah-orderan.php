<?php  
require_once "action/DbClass.php";

if($_SESSION['login_stiker_admin'] != true ){
  header('Location: auth-login');
  exit();
}

$db = new ConfigClass();

if(isset($_POST['create_spk'])){
  $d = date('d');
  $m = date('m');
  $y = date('Y');
  $niq = 1;
  $spk = "SPK-".$d.$m.$y.$niq;
  // $db->insertOrder($id, $spk);
}

$userselect = $db->selectTable("user_galeri","id_user",$_SESSION['login_stiker_id']);
$row = mysqli_fetch_assoc($userselect);
$usernamelogin = $row['username_user'];
$id = $row['id_owner'];
// jika yang login buka owner ambil data owner dari id owner
if($row['id_owner'] == "0"){
  $id = $row['id_user'];
}
$store = $db->selectTable("store_galeri","id_owner",$id);
$rowstore = mysqli_fetch_assoc($store);
$alert = $_SESSION['alert'];
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>STIKER | PEMESANAN BARU</title>
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

    
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script>
      function viewKab(str) {
        $.ajax({
          type:'post',
          url:'api_kab_kota.php?prov_id='+str,
          success:function(hasil_kab){
            $("select[name=kabkota]").html(hasil_kab);
          }
        })
      }
    </script>
    <script>
      function viewkec(str) {
        $.ajax({
          type:'post',
          url:'api_kecamatan.php?city_id='+str,
          success:function(hasil_kec){
            $("select[name=kec]").html(hasil_kec);
          }
        })
      }
    </script>
    <script>
      function showOngkir(){
        var kurir = document.getElementById("kurir").value;
        var asal = "254";
        var tujuan = document.getElementById("kec").value;
        var berat = document.getElementById("berat").value;
        $.ajax({
          type:'post',
          url:'count_ongkir.php?kurir='+kurir+'&asal='+asal+'&tujuan='+tujuan+'&berat='+berat,
          success:function(hasil_costs){
            $("select[name=resultcost]").html(hasil_costs);
          }
        })
      }
    </script>
    <script>
      function addressCustomer(id){
        if(id != ""){
          $.ajax({
            type:'post',
            url:'select_address.php?id='+id,
            success:function(hasil_address){
              $("div[id=data_customer]").html(hasil_address);
            }
          })
        }
      }
    </script>

    <script src="assets/select2/dist/js/jquery.min.js"></script>
    <link href="assets/select2/dist/css/select2.min.css" rel="stylesheet" />
    <script src="assets/select2/dist/js/select2.min.js"></script>

    <script>
      function showSubJenis() {
        var p = document.getElementById("jenisp").value;
        var pr = document.getElementById("jenispr").value;
        $.ajax({
          type:'post',
          url:'data_produk_kategori.php?jenisp='+p+'&jenispr='+pr+'&id='+<?= $id ?>,
          success:function(hasil_views){
            $("select[name=produk").html(hasil_views);
          }
        })
      }
    </script>
    <script>
      function showVarian(){
        var str = document.getElementById("kategori_type").value;
        var pr = document.getElementById("jenispr").value;
        $.ajax({
          type:'post',
          url:'data_varian_type.php?type='+str+'&id='+<?= $id ?>+'&jenispr='+pr,
          success:function(hasil_views){
            $("select[name=varian_harga").html(hasil_views);
          }
        })
      }
    </script>

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
                  <h4 class="mb-sm-0">Tambah Pemesanan</h4>

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
            <!-- card order -->
            <form action="" method="post">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title">Informasi Pemesanan</div>
                      <div class="row g-3 mt-3">
                        <div class="col-md-3">
                          <label for="jenisp" class="form-label">Kategori Produk</label>
                          <select name="kategori_produk" id="jenisp" class="form-select" required>
                            <option value="">PILIH KATEGORI</option>
                            <option value="Mobil">Mobil</option>
                            <option value="Motor">Motor</option>
                            <option value="Other">Lainnya</option>
                          </select>
                        </div>
                        <div class="col-md-3">
                          <label for="jenispr" class="form-label">Jenis Produk</label>
                          <select name="Jenis_produk" id="jenispr" class="form-select" required>
                            <option value="">PILIH JENIS</option>
                            <option value="Custom">Custom</option>
                            <option value="No Custom">Produk <?= $db->nameFormater($rowstore['name_store']) ?></option>
                          </select>
                        </div>
                        <div class="col-md-3">
                          <label for="kategori_type" class="form-label">Produk Tersedia</label>
                          <div class="input-group" style="width: 100%;">
                            <select onchange="showVarian()" name="produk" id="kategori_type" class="form-control js-example-basic-single" required>
                              <option value="" hidden>Empty</option>
                            </select>
                            <button class="btn btn-secondary" onclick="showSubJenis()" data-bs-toggle="tooltip" data-bs-placement="top" title="Views" type="button"><i class="ri-eye-line"></i></button>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <label for="harga" class="form-label">Harga/Varian Produk</label>
                          <select name="varian_harga" id="harga" class="form-select">
                            <option value="">PILIH</option>
                          </select>
                        </div>
                        <div class="col-md-3">
                          <label for="desain_status" class="form-label">Desain</label>
                          <select name="desain_status" id="desain_status" class="form-select">
                            <option value="Ya">YA</option>
                            <option value="Tidak">Tidak</option>
                          </select>
                        </div>
                        <div class="col-md-3">
                          <label for="cetak_status" class="form-label">Cetak</label>
                          <select name="cetak_status" id="cetak_status" class="form-select">
                            <option value="Ya">YA</option>
                            <option value="Tidak">Tidak</option>
                          </select>
                        </div>
                        <div class="col-md-3">
                          <label for="laminating" class="form-label">Laminating</label>
                          <select name="laminating" id="laminating" class="form-select">
                            <option value="Ya">YA</option>
                            <option value="Tidak">Tidak</option>
                          </select>
                        </div>
                        <div class="col-md-3">
                          <label for="pemasangan_status" class="form-label">Pemasangan</label>
                          <select name="pemasangan_status" id="pemasangan_status" class="form-select">
                            <option value="Ya">YA</option>
                            <option value="Tidak">Tidak</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title">Detail Pemesanan</div>
                      <div class="row g-3">
                        <label for="select2" class="col-sm-2 col-form-label">Pelanggan</label>
                        <div class="col-sm-4">
                          <select name="palanggan" onchange="addressCustomer(this.value)" class="form-control js-example-basic-single" id="select2" style="width: 100%;">
                            <option value="" hidden>PILIH PELANGGAN</option>
                            <?php  
                              $cs = $db->selectTable("customer_stiker","id_owner",$id);
                              while($rowcs=mysqli_fetch_assoc($cs)){
                            ?>
                            <option value="<?= $rowcs['id_customer'] ?>"><?= $rowcs['username_customer'] ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <div class="col-sm-3">
                          <input type="text" name="keterangan" placeholder="Keterangan" id="" class="form-control">
                        </div>
                        <div class="col-sm-3">
                          <div class="input-group">
                            <input type="text" name="diskon" placeholder="Diskon" id="diskon" class="form-control">
                            <span class="input-group-text">%</span>
                          </div>
                        </div>
                        <label for="" class="col-sm-2 col-form-label">Tanggal Pemesanan</label>
                        <div class="col-sm-10">
                          <div class="input-group">
                            <span class="input-group-text" id="basic-addon1"><i class="ri-calendar-todo-fill"></i></span>
                            <input type="text" style="cursor: not-allowed;" class="form-control" value="<?= $db->formatHari(date("D")).", ".$db->dateFormatter(date("Y-m-d")) ?>" disabled>
                          </div>
                        </div>
                        <label for="pengiriman" class="col-sm-2 col-form-label">Pengiriman</label>
                        <div class="col-sm-10">
                          <select name="pengiriman" id="pengiriman" class="form-select">
                            <option value="Ya">YA</option>
                            <option value="Tidak">Tidak</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title">Detail Pengiriman</div>
                      <div class="row g-3 mb-3">
                        <label for="" class="col-sm-2 col-form-label">Kurir</label>
                        <div class="col-sm-10">
                          <select name="kurir" id="kurir" class="form-select">
                            <optgroup label="PILIH KURIR">
                              <option value="pos">POS Indonesia (POS)</option>
                              <option value="lion">Lion Parcel (LION)</option>
                              <option value="jne">Jalur Nugraha Ekakurir (JNE)</option>
                              <option value="j&t">J&T Express (J&T)</option>
                            </optgroup>
                          </select>
                        </div>
                      </div>
                      <div class="row g-3 mb-3" id="data_customer">
                          <label for="" class="col-sm-2 col-form-label">Tujuan Pengiriman</label>
                          <div class="col-sm-4">
                            <select name="prov" id="prov" class="form-select" onchange="viewKab(this.value)">
                              <option value="">--PILIH PROVINSI--</option>
                              <?php  
                              $provs = $db->dataIndonesia("prov",null);
                              foreach($provs as $prov){
                                echo '<option value="'.$prov['province_id'].'">'.$prov['province'].'</option>';
                              }
                              ?>
                            </select>
                          </div>
                          <div class="col-sm-3">
                            <select name="kabkota" id="kabkota" class="form-select" onchange="viewkec(this.value)" required>
                              <option value="">--PILIH KAB/KOTA--</option>
                            </select>
                          </div>
                          <div class="col-sm-3">
                            <select name="kec" id="kec" class="form-select" required>
                              <option value="">--PILIH KECAMATAN--</option>
                            </select>
                          </div>
                      </div>
                      <div class="row g-3 mb-3">
                        <label for="" class="col-sm-2 col-form-label">Berat</label>
                        <div class="col-sm-10">
                          <input type="number" name="berat" step="0.01" id="berat" class="form-control">
                        </div>
                        <label for="" class="col-sm-2 col-form-label">Ongkos Kirim</label>
                        <div class="col-sm-10">
                          <div class="input-group">
                            <select required name="resultcost" id="resut_pengiriman" class="form-control">
                              <option value="">PILIH PAKET</option>
                            </select>
                            <button class="btn btn-warning" type="button" id="button-addon2" onclick="showOngkir()">Cek</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title">Detail Pembayaran</div>
                      <div class="row g-3">
                        <label for="" class="col-sm-2 col-form-label">Pembayaran</label>
                        <div class="col-sm-5">
                          <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="number" id="" placeholder="Jumlah Pembayaran" class="form-control" readonly>
                          </div>
                          <span style="font-size:0.8rem"><strong>*jika pembayaran tidak memenuhi harga produk, maka berstatus DP</strong></span>
                        </div>
                        <div class="col-sm-5">
                          <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="number" name="pembayaran" id="" placeholder="Enter Pembayaran" class="form-control">
                          </div>
                        </div>
                      </div>
                      <button type="submit" name="create_spk" class="btn btn-primary mt-3">Buat SPK</button>
                    </div>
                  </div>
                </div>
              </div>
            </form>
            <!-- end card order -->
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

    <script>
      $(document).ready(function() {
          $('#select2').select2({
            // maximumSelectionLength: 1,
            placeholder: 'Pelanggan Toko',
            allowClear: true,
            minimumInputLength: 0
          });
      });
    </script>
    <script>
      $(document).ready(function() {
          $('#kategori_type').select2({
            minimumInputLength: 0
          });
      });
    </script>

    <!-- JAVASCRIPT -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>

    <script src="assets/js/app.js"></script>
    
  </body>
</html>
<?php mysqli_close($db->conn) ?>