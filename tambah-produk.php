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

$alert = "";

if(isset($_POST['input_produk_back'])){
  // path save image
  $folder = "assets/images/produk/";

  // upload
  $old_sampul_path = $_FILES['foto_produk']['tmp_name'];
  $old_sampul_name = $_FILES['foto_produk']['name'];

  // informasi produk
  $nama_produk = $_POST['nama_produk'];
  $kategori_produk = $_POST['kategori_produk'];
  
  // detail produk
  $status = $_POST['status'] == "on" ? "Aktif" : "Tidak Aktif";
  $desk = $_POST['deskripsi'];
  $stok = $_POST['stok'];
  $berat = $_POST['berat']." ".$_POST['satuan'];
  $harga = $_POST['harga'];
  
  // data VARIAN WARNA  
  $warna = $_POST['warna'];
  $foto_warna_path = $_FILES['produk']['tmp_name'];
  $foto_warna_name = $_FILES['produk']['name'];

  $checkproduk = $db->selectTable("produk_stiker",'id_owner',$id,'name_product',$nama_produk);
  if(mysqli_num_rows($checkproduk) > 0){
    $alert = "2";
  }else{
    $exted = pathinfo($old_sampul_name, PATHINFO_EXTENSION);
    if($exted == "jpg" || $exted == "jpeg" || $exted == "png"){
      $rand = md5(rand());
      $new_sampul_name = $rand.".".$exted;
      $new_sampul_path = $folder.$new_sampul_name;
      $upload_sampul = move_uploaded_file($old_sampul_path, $new_sampul_path);
      if($upload_sampul){
        $compress = $db->compress_image($exted, $new_sampul_path, $folder);
        if($compress['bol']){
          unlink($new_sampul_path);
          $new_sampul_path = $compress['db'];
        }
      }
      $insert = $db->insertProduct($id,$nama_produk,$status,$desk,$stok,$berat,$harga,$new_sampul_path,$kategori_produk);
      if($insert){
        if(count($warna) > 0){
          for($i = 0; $i < count($warna); $i++){
            $color = $warna[$i];
            $old_warna_path = $foto_warna_path[$i];
            $old_warna_name = $foto_warna_name[$i];
            $exted = pathinfo($old_warna_name, PATHINFO_EXTENSION);
            if($exted == "jpg" || $exted == "jpeg" || $exted == "png"){
              $rand = md5(rand());
              $new_warna_name = $rand.".".$exted;
              $new_warna_path = $folder.$new_warna_name;
              $upload_warna = move_uploaded_file($old_warna_path, $new_warna_path);
              if($upload_warna){
                $compress = $db->compress_image($exted, $new_warna_path, $folder);
                if($compress['bol']){
                  unlink($new_warna_path);
                  $new_warna_path = $compress['db'];
                }
              }
              $insertvarian = $db->insertVarian($id,$color,$new_warna_path,$nama_produk);
            }else{  
              $_SESSION['faild'] += 1;
              continue;
            }
          }
          $alert = "1";
        }else{
          $alert = "3";
        }
      }

    }else{
      $alert = "4";
    }
  }

}

if(isset($_POST['input_produk'])){
  // path save image
  $folder = "assets/images/produk/";

  // upload
  $old_sampul_path = $_FILES['foto_produk']['tmp_name'];
  $old_sampul_name = $_FILES['foto_produk']['name'];

  // informasi produk
  $nama_produk = $_POST['nama_produk'];
  $kategori_produk = $_POST['kategori_produk'];
  
  // detail produk
  $status = $_POST['status'] == "on" ? "Aktif" : "Tidak Aktif";
  $desk = $_POST['deskripsi'];
  $stok = $_POST['stok'];
  $berat = $_POST['berat']." ".$_POST['satuan'];
  $harga = $_POST['harga'];
  
  // data VARIAN WARNA  
  $warna = $_POST['warna'];
  $foto_warna_path = $_FILES['produk']['tmp_name'];
  $foto_warna_name = $_FILES['produk']['name'];

  $checkproduk = $db->selectTable("produk_stiker",'id_owner',$id,'name_product',$nama_produk);
  if(mysqli_num_rows($checkproduk) > 0){
    $alert = "2";
  }else{
    $exted = pathinfo($old_sampul_name, PATHINFO_EXTENSION);
    if($exted == "jpg" || $exted == "jpeg" || $exted == "png"){
      $rand = md5(rand());
      $new_sampul_name = $rand.".".$exted;
      $new_sampul_path = $folder.$new_sampul_name;
      $upload_sampul = move_uploaded_file($old_sampul_path, $new_sampul_path);
      if($upload_sampul){
        $compress = $db->compress_image($exted, $new_sampul_path, $folder);
        if($compress['bol']){
          unlink($new_sampul_path);
          $new_sampul_path = $compress['db'];
        }
      }
      $insert = $db->insertProduct($id,$nama_produk,$status,$desk,$stok,$berat,$harga,$new_sampul_path,$kategori_produk);
      if($insert){
        if(count($warna) > 0){
          for($i = 0; $i < count($warna); $i++){
            $color = $warna[$i];
            $old_warna_path = $foto_warna_path[$i];
            $old_warna_name = $foto_warna_name[$i];
            $exted = pathinfo($old_warna_name, PATHINFO_EXTENSION);
            if($exted == "jpg" || $exted == "jpeg" || $exted == "png"){
              $rand = md5(rand());
              $new_warna_name = $rand.".".$exted;
              $new_warna_path = $folder.$new_warna_name;
              $upload_warna = move_uploaded_file($old_warna_path, $new_warna_path);
              if($upload_warna){
                $compress = $db->compress_image($exted, $new_warna_path, $folder);
                if($compress['bol']){
                  unlink($new_warna_path);
                  $new_warna_path = $compress['db'];
                }
              }
              $insertvarian = $db->insertVarian($id,$color,$new_warna_path,$nama_produk);
            }else{
              $alert = "4";
              $_SESSION['faild'] += 1;
              continue;
            }
          }
          $_SESSION['alert'] = "1";
          header('Location: konfigurasiproduk-produk');
          exit();
        }else{
          $alert = "3";
        }
      }

    }else{
      $alert = "4";
    }
  }

}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>STIKER | PRODUCT</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" integrity="sha512-nMNlpuaDPrqlEls3IX/Q56H36qvBASwb3ipuo3MxeWbsQB1881ox0cRv7UPTgBlriqoynt35KjEwgGUeUXIPnw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Sweet Alert-->
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <script>
      function namaWarna() {
        var val = document.getElementById("js-example-basic-multiple");
        var warna = "";
        var n = 0;
        for(var i = 0; i<val.options.length; i++){
          if(val.options[i].selected === true){
              n += 1; 
              warna += '<tr>\
                          <td scope="row">'+n+'</td>\
                          <td>'+val.options[i].value+'</td>\
                          <td>\
                            <input type="file" onchange="previewWarna()" name="produk[]" id="upload_image'+n+'" accept="image/png,Image/jpeg" class="d-none">\
                            <label for="upload_image'+n+'" style="cursor: pointer;" class="col-12">\
                              <img id="warna'+n+'" style="max-width:100px;" src="assets/images/produk/img.jpg" alt="">\
                            </label>\
                          </td>\
                        </tr>';
          }
        }
        if(n != 0){
          // document.getElementById("jum_color").value = n;
          document.getElementById("d-warna").style.display = "block";
          document.getElementById("s-warna").innerHTML = warna;
        }else{
          document.getElementById("d-warna").style.display = "none";
        }
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
                  <h4 class="mb-sm-0">Tambah Produk</h4>

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
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-body">
                    <form action="" method="post" autocomplete="off" style="padding: 20px;" enctype="multipart/form-data">
                      <div class="card-title mb-3">Upload Produk</div>

                      <div class="row mb-4">
                        <input type="file" onchange="previewImage()" name="foto_produk" id="upload_image" accept="image/png,Image/jpeg" class="d-none">
                        <label for="upload_image" style="cursor: pointer;" class="col-12">
                          <img id="preview" style="display: block;margin-left: auto;margin-right: auto; max-width:180px;" src="assets/images/produk/img.jpg" alt="">
                        </label>
                        <label for="upload_image" style="cursor: pointer;" class="col-12 mt-2">
                          <span class="text-info" style="display: block;text-align: center;font-size: 1em;">Foto Depan</span>
                        </label>
                      </div>

                      <hr>

                      <div class="card-title mb-3">Informasi Produk</div>

                      <div class="row g-3">
                        <div class="col-sm-3">
                          <label for="name" class="form-label">Nama Produk <span class="badge bg-secondary">Wajib</span></label>
                          <p style="font-size: 12px;">
                            Cantumkan min. 40 karakter agar semakin<br>menarik dan mudah ditemukan oleh<br>pembeli, terdiri dari jenis produk, merek,<br>dan keterangan seperti warna, bahan, atau tipe.
                          </p>
                        </div>
                        <div class="col-sm-9">
                          <input type="text" class="form-control" id="name" name="nama_produk">
                        </div>

                        <div class="col-sm-3">
                          <label for="kat" class="form-label">Kategori <span class="badge bg-secondary">Wajib</span></label>
                        </div>
                        <div class="col-12 col-sm-9">
                          <select name="kategori_produk" id="kat" class="form-control js-example-placeholder-single">
                            <?php  
                            $val = $edit != "" ? $rowselect['id_parent_kategori'] : $_POST['category'] ; 
                            $chooce = $db->selectTable("kategori_stiker","id_owner",$id);
                            while($rowchooce = mysqli_fetch_assoc($chooce)){
                              $select = $rowchooce['id_kategori'] == $val ? 'selected="selected"' : "";
                            ?>
                            <option value="<?= $rowchooce['id_kategori'] ?>" <?= $select ?>><?= $db->formatKategori("select",$rowchooce['id_kategori'],null,$id) ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>

                      <hr>

                      <div class="card-title mb-3">Detail Produk</div>

                      <div class="row g-3">
                        <div class="col-sm-3">
                          <label for="desk " class="form-label">Deskripsi Produk <span class="badge bg-secondary">Wajib</span></label>
                            <p style="font-size: 12px;">
                            Pastikan deskripsi produk memuat <br>spesifikasi, ukuran, bahan, masa berlaku,<br>dan lainnya. Semakin detail, semakin<br>berguna bagi pembeli, cantumkan min. 260<br>karakter agar pembeli semakin mudah<br>mengerti dan menemukan produk anda
                            </p>
                        </div>
                        <div class="col-sm-9">
                          <textarea name="deskripsi" id="desk" rows="4" class="form-control"></textarea>
                        </div>

                        <div class="col-sm-3">
                          <label for="harga" class="form-label">Harga <span class="badge bg-secondary">Wajib</span></label>
                        </div>
                        <div class="col-sm-9">
                          <div class="input-group mb-3">
                            <span class="input-group-text" id="basic-addon1"><b>Rp</b></span>
                            <input type="number" id="harga" name="harga" class="form-control" placeholder="0.00">
                          </div>
                        </div>

                        <div class="col-sm-3">
                          <label for="stok" class="form-label">Jumlah Stok <span class="badge bg-secondary">Wajib</span></label>
                        </div>
                        <div class="col-sm-9">
                          <input type="number" name="stok" id="stok" class="form-control">
                        </div>

                        <div class="col-sm-3">
                          <label for="berat" class="form-label">Berat Produk <span class="badge bg-secondary">Wajib</span></label>
                          <p>
                            Masukkan berat dengan menimbang produk<br>setelah dikemas.
                          </p>
                        </div>
                        <div class="col-4 col-sm-2">
                          <select name="satuan" class="form-select">
                            <option value="g">Gram (g)</option>
                            <option value="kg">Kilogram (kg)</option>
                          </select>
                        </div>
                        <div class="col-7 col-sm-7">
                          <input type="number" step="0.01" name="berat" id="berat" class="form-control">
                        </div>

                        <div class="col-sm-3">
                          <label for="statuss" class="form-label">Status Produk <span class="badge bg-secondary">Wajib</span></label>
                        </div>
                        <div class="col-sm-9">
                          <div class="form-check form-switch">
                            <input class="form-check-input" name="status" onclick="labelChange()" type="checkbox" role="switch" id="statuss" checked>
                            <label class="form-check-label" for="statuss"><Span id="notiff">Aktif</Span></label>
                          </div>
                        </div>

                        <div class="col-sm-3">
                          <label for="" class="form-label">Varian Warna <span class="badge bg-secondary">Wajib</span></label>
                        </div>
                        <div class="col-12 col-sm">
                          <div class="input-group" >
                            <select class="form-control js-example-basic-multiple" id="js-example-basic-multiple" name="warna[]" multiple="multiple">
                              <option value="Merah">Merah</option>
                              <option value="Biru">Biru</option>
                              <option value="Kuning">Kuning</option>
                              <option value="Hijau">Hijau</option>
                              <option value="Putih">Putih</option>
                              <option value="Hitam">Hitam</option>
                            </select>
                            <button type="button" style="height: 2.1rem;" class="btn btn-outline-primary" onclick="namaWarna()">Submit</button>
                          </div>
                          <div style="display: none;" id="d-warna">
                            <!-- <input type="number" id="jum_color" > -->
                            <table class="table table-hover mt-2">
                              <thead>
                                <tr>
                                  <th scope="col">#</th>
                                  <th scope="col">Warna</th>
                                  <th scope="col">Gambar</th>
                                </tr>
                              </thead>
                              <tbody id="s-warna">
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <button type="submit" name="input_produk" class="btn btn-primary mt-3">Simpan Produk</button>
                      <button type="submit" name="input_produk_back" class="btn btn-secondary mt-3">Simpan & Input Produk</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <!-- end page title -->
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
    
    <script src="assets/js/app.js"></script>
    <script>
      function labelChange(){
        var chck = document.getElementById("statuss");
        var label = document.getElementById("notiff");
        if(chck.checked == false){
          label.innerHTML = "Tidak Aktif";
        }else{
          label.innerHTML = "Aktif";
        }
      }
    </script>
    <script>
        function previewImage() {
          var oFReader = new FileReader();
          oFReader.readAsDataURL(document.getElementById("upload_image").files[0]);
        
          oFReader.onload = function(oFREvent) {
            // document.getElementById("image-preview").style.display = "block";
            document.getElementById("preview").src = oFREvent.target.result;
          };
        };
    </script>
    <script>
      function previewWarna(){
        var img_name = "upload_image1";
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById(img_name).files[0]);
          
        oFReader.onload = function(oFREvent) {
          var color = "warna1";
          // document.getElementById("image-preview").style.display = "block";
          document.getElementById(color).src = oFREvent.target.result;
        };
        var img_name = "upload_image2";
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById(img_name).files[0]);
          
        oFReader.onload = function(oFREvent) {
          var color = "warna2";
          // document.getElementById("image-preview").style.display = "block";
          document.getElementById(color).src = oFREvent.target.result;
        };
        var img_name = "upload_image3";
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById(img_name).files[0]);
          
        oFReader.onload = function(oFREvent) {
          var color = "warna3";
          // document.getElementById("image-preview").style.display = "block";
          document.getElementById(color).src = oFREvent.target.result;
        };
        var img_name = "upload_image4";
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById(img_name).files[0]);
          
        oFReader.onload = function(oFREvent) {
          var color = "warna4";
          // document.getElementById("image-preview").style.display = "block";
          document.getElementById(color).src = oFREvent.target.result;
        };
        var img_name = "upload_image5";
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById(img_name).files[0]);
          
        oFReader.onload = function(oFREvent) {
          var color = "warna5";
          // document.getElementById("image-preview").style.display = "block";
          document.getElementById(color).src = oFREvent.target.result;
        };
      }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
      $(document).ready(function() {
          $('#js-example-basic-multiple').select2({
            placeholder: "Pilih Warna"
          });
      });
    </script>
    <script>
      $(".js-example-placeholder-single").select2({
          placeholder: "Pilih Kategori",
          allowClear: true
      });
    </script>
    <!-- Sweet Alerts js -->
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>


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
          text:"Produk Sudah Ada!",
          icon:"error",
        })
      }else if(flash == "3"){
        Swal.fire({
          title:"Gagal!",
          text:"Data tidak Tersimpan!",
          icon:"error",
        })
      }else if(flash == "4"){
        Swal.fire({
          title:"Gagal!",
          text:"File Gambar Harus Jpeg atau Png!",
          icon:"error",
        })
      }
    </script>
  </body>
</html>
<?php mysqli_close($db->conn) ?>