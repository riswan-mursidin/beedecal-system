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

$store = $db->selectTable("store_galeri","id_owner",$id);
$rowstore = mysqli_fetch_assoc($store);
$asal = $rowstore['kab_id'];
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

if(isset($_POST['input_resi'])){
  $resi = $_POST['resi'];
  $id_order = $_POST['id_order'];

  $resi = "UPDATE data_pemesanan SET resi_pengiriman='$resi' WHERE id_order='$id_order'";
  $resultt = mysqli_query($db->conn, $resi);
}

if(isset($_POST['edit_send'])){
    $id_order = $_POST['id_order'];
    $status_pengiriman = $_POST['send_status'];
    $kurir = $status_pengiriman == "Ya" ? $_POST['kurir'] : '';
    $prov_desti = $status_pengiriman == "Ya" ? $_POST['prov'] : '';
    $kabkota_desti = $status_pengiriman == "Ya" ? $_POST['kabkota'] : '';
    $kec_desti = $status_pengiriman == "Ya" ? $_POST['kec'] : '';
    $alamat_lengkap = $status_pengiriman == "Ya" ? $_POST['alamat_lengkap'] : '';
    $kode_pos = $status_pengiriman == "Ya" ? $_POST['kode_pos'] : '';
    $berat = $status_pengiriman == "Ya" ? $_POST['berat'] : '';
    $paket_ongkir = explode(" - ",$_POST['resultcost']);

    // detail pengiriman
    $cost = $status_pengiriman == "Ya" ? $paket_ongkir[0] : '';
    $name_paket = $status_pengiriman == "Ya" ? $paket_ongkir[1] : '';
    $etd = $status_pengiriman == "Ya" ? $paket_ongkir[2] : '';

    $query = "UPDATE data_pemesanan SET status_pengiriman_order='$status_pengiriman', kurir_pengiriman_order='$kurir', prov_send_order='$prov_desti', kab_send_order='$kabkota_desti', kec_send_order='$kec_desti', kode_pos_send_order='$kode_pos', alamat_lengkap_send_order='$alamat_lengkap', berat_send_order='$berat', ongkir_send_order='$cost', nama_paket_send_order='$name_paket', estimasi_send_order='$etd' WHERE id_order='$id_order'";

    $result = mysqli_query($db->conn, $query);
    if($result){
      $_SESSION['alert'] = "1";
      header('Location: pengiriman');
      exit();
    }
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

function resultDiskon($harga,$disk,$satuan){
  $diskon = $harga * ($disk/100);
  $result = $harga - $diskon;
  if($satuan == "rupiah"){
    $result = $harga - $disk;
  }
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



function statusBadge($txt){
  if($txt == "Belum Lunas"){
    $result = '<h9><span class="badge rounded-pill bg-danger">Belum Lunas</span></h9>';
    return $result;
  }else{
    $result = '<h9><span class="badge rounded-pill bg-success">Lunas</span></h9>';
    return $result;
  }
}
function statusBadge2($txt){
  if($txt == "Tidak"){
    $result = '<h9><span class="badge rounded-pill bg-danger">Belum Lunas</span></h9>';
    return $result;
  }else{
    $result = '<h9><span class="badge rounded-pill bg-success">Lunas</span></h9>';
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

    <script>
      function detailPengiriman(str){
        var detail = document.getElementById("detailpengiriman");
        if(str == "Ya"){
          detail.style.display = "block";
        }else{
          detail.style.display = "none";
        }
      }
    </script>

    <!-- ongkir -->
    <script>
      function showOngkir(){
        var kurir = document.getElementById("kurir").value;
        var asal = "<?= $asal ?>";
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
    <!-- end ongkir -->

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
                  <h4 class="mb-sm-0">Data Logistik (Yang Dikirim)</h4>

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
                          <th>Produksi</th>
                          <th>Tanggal Pesan</th>
                          <th>Pengiriman</th>
                          <th>Status</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php  
                        $order = $db->selectTable("data_pemesanan","id_owner",$id,"status_pengiriman_order","Ya");
                        while($roworder=mysqli_fetch_assoc($order)){
                          if($roworder['status_order'] == "Selesai Finishing" || $roworder['status_order'] == "Selesai Dicetak"){
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
                              $customer = showCustomer($roworder['id_customer'],$roworder['status_pengiriman_order'],$roworder['id_order']);
                              echo "<b>".$db->nameFormater($customer['name'])."</b>"." ".$status."<br>"; 
                            ?>
                            <?php
                              if($roworder['status_pengiriman_order'] == "Ya"){
                                echo 'Prov: '.$roworder['	prov_send_order'].'<br>';
                                echo 'Kab/Kota: '.$roworder['kab_send_order'].'<br>';
                                echo 'Kec: '.$roworder['kec_send_order'].'<br>';
                                echo 'Kode Pos: '.$roworder['kode_pos_send_order'].'<br>';
                              }else{
                                echo 'Prov: '.$customer['prov'].'<br>';
                                echo 'Kab/Kota: '.$customer['kab'].'<br>';
                                echo 'Kec: '.$customer['kec'].'<br>';
                                echo 'Kode Pos: '.$customer['kodepos'].'<br>';
                              }
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
                            <?php if($roworder['satuan_potongan'] == "persen"){ ?>
                            <?= $roworder['diskon_order'] != "" ? '<span style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Dari Harga Rp.'.number_format($roworder['harga_produk_order'],2,",",".").'" class="badge bg-secondary">Diskon '.$roworder['diskon_order'].'%</span><br>' : '' ?>
                            <?php }else{ ?>
                              <?= $roworder['diskon_order'] != "" ? '<span style="cursor: pointer;" data-bs-toggle="tooltip" data-bs-placement="top" title="Dari Harga Rp.'.number_format($roworder['harga_produk_order'],2,",",".").'" class="badge bg-secondary">Diskon Rp.'.number_format($roworder['diskon_order'],2,",",".").'</span><br>' : '' ?>
                            <?php } ?>
                            Harga Produk: Rp.<?= number_format(resultDiskon($roworder['harga_produk_order'],$roworder['diskon_order'],$roworder['satuan_potongan']),2,",",".") ?>
                            <?= statusBadge($roworder['status_pay_order']) ?><br>
                            Harga Pasang: <?= $roworder['status_pasang_order'] == "Ya" ? ' Rp.'.number_format($roworder['harga_pasang_order'],2,",",".") : 'Tidak Dipasang' ?>
                            <?= $roworder['status_pasang_order'] == "Ya" ? statusBadge2($roworder['status_bayar_pasang']) : '' ?><br>
                            Harga Pengiriman: <?= $roworder['status_pengiriman_order'] == "Ya" ? " Rp.".number_format($roworder['ongkir_send_order'],2,",",".") : '-,-' ?>
                            
                          </td>
                          <td>
                            Desain: <b><?= $roworder['status_desain_order'] ?></b><br>
                            Cetak: <b><?= $roworder['status_cetak_order'] ?></b><br>
                            Laminating: <b><?= $roworder['laminating_order'] ?></b><br>
                            Pasang: <b><?= $roworder['status_pasang_order'] ?></b><br>
                          </td>
                          <td><?= $db->dateFormatter($roworder['tgl_order']) ?></td>
                          <td>
                            Kurir: <?= strtoupper($roworder['kurir_pengiriman_order']) ?><br>
                            Paket: <?= $roworder['nama_paket_send_order'] ?><br>
                            Estimasi: <?= $roworder['estimasi_send_order'] ?><br>
                            Ongkir: Rp.<?= number_format($roworder['ongkir_send_order']) ?>
                          </td>
                          <td><?= '<h5><span class="badge bg-success">'.$roworder['status_order'].'</span></h5>' ?></td>
                          <td>
                            <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                              <a href="#editpengiriman<?= $roworder['id_order'] ?>" data-bs-toggle="modal" class="btn btn-secondary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Pengiriman">
                                <i class="ri-pencil-line"></i>
                              </a>
                              <a href="#inputresiorder<?= $roworder['id_order'] ?>" data-bs-toggle="modal" class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Masukkan Resi Pengiriman">
                                <i class="ri-send-plane-line"></i>
                              </a>
                              <?php  
                              if($roworder['status_pay_order'] == "Belum Lunas"){
                              ?>
                              <a data-bs-toggle="modal" href="#pelunasan<?= $roworder['id_order'] ?>" class="btn btn-info btn-sm">
                                <i class="ri-currency-line"></i>
                              </a>
                              <?php }else{ ?>
                              <a id="doneorder" href="action/get-done-order?id=<?= $roworder['id_order'] ?>" class="btn btn-primary btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Selesai">
                                <i class="ri-check-line"></i>
                              </a>
                              <?php } ?>
                              <a target="_blank" href="print_note?spk=<?= $roworder['code_order'] ?>" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Print Note"><i class="ri-printer-line"></i></a>
                              <!-- <a href="<?= $roworder[''] ?>" class="btn btn-warning btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Details"><i class="ri-eye-line"></i></a>
                              <a href="data-pesanan.php?order=" class="btn btn-danger btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" id="delete"><i class="ri-delete-bin-line"></i></a> -->
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

        <!-- Modal -->
        <?php  
        $order = $db->selectTable("data_pemesanan","id_owner",$id,"status_pengiriman_order","Ya");
        while($roworder=mysqli_fetch_assoc($order)){
        ?>
        <div class="modal fade" id="inputresiorder<?= $roworder['id_order'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <form action="" method="post" class="modal-content" enctype="multipart/form-data">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Masukkan Resi Pengiriman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="id_order" value="<?= $roworder['id_order'] ?>">
                <input type="text" name="resi" value="<?= $roworder['resi_pengiriman'] ?>" id="" class="form-control">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="input_resi" class="btn btn-primary">Simpan</button>
              </div>
            </form>
          </div>
        </div>
        <div class="modal fade" id="editpengiriman<?= $roworder['id_order'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <form action="" method="post" class="modal-content" enctype="multipart/form-data">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Pengiriman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <input type="hidden" name="id_order" value="<?= $roworder['id_order'] ?>">
                <div class="mb-3">
                  <label for="" class="form-label">Status Pengiriman</label>
                  <select name="send_status" id="" onchange="detailPengiriman(this.value)" class="form-select">
                    <?php  
                    $options = array("Ya","Tidak");
                    foreach($options as $ops){
                      $select = $ops == $roworder['status_pengiriman_order'] ? 'selected="selected"' : '';
                    ?>
                    <option value="<?= $ops ?>" <?= $select ?>><?= $ops ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="" id="detailpengiriman">
                  <div class="mb-3">
                    <label for="" class="form-label">Kurir</label>
                    <select name="kurir" id="kurir" class="form-select">
                      <optgroup label="PILIH KURIR">
                        <?php 
                        function nameKurir($kode){
                          switch($kode){
                            case "pos":
                              return "POS Indonesia (POS)";
                              break;
                            case "lion":
                              return "Lion Parcel (LION)";
                              break;
                            case "jne":
                              return "Jalur Nugraha Ekakurir (JNE)";
                              break;
                            case "jnt":
                              return "J&T Express (J&T)";
                              break;
                          }
                        }
                        $kur = array("pos","lion","jne","jnt");
                        foreach($kur as $k){
                          $select = $k == $roworder['kurir_pengiriman_order'] ? 'selected="selected"' : '';
                        ?>
                        <option value="<?= $k ?>" <?= $select ?>><?= nameKurir($k) ?></option>
                        <?php } ?>
                      </optgroup>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="" class="form-label">Provinsi</label>
                    <select name="prov" id="prov" class="form-select" onchange="viewKab(this.value)">
                      <option value="" hidden>PROVINSI</option>
                      <?php  
                      $idprov = "";
                      $provs = $db->dataIndonesia("prov",null);
                      foreach($provs as $prov){
                        $select = $roworder['prov_send_order'] == $prov['province'] ? 'selected="selected"' : '';
                        $idprov .= $roworder['prov_send_order'] == $prov['province'] ? $prov["province_id"] : ""; 
                        echo '<option value="'.$prov['province_id'].'" '.$select.'>'.$prov['province'].'</option>';
                      }
                      ?>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="" class="form-label">KABUPATEN/KOTA</label>
                    <select name="kabkota" id="kabkota" class="form-select" onchange="viewkec(this.value)" >
                      <option value="" hidden>KABUPATEN/KOTA</option>
                      <?php $idkab = "";
                      $kab_kota = $db->dataIndonesia("kab_kota",$idprov);
                      foreach ($kab_kota as $key => $kab){
                        $select = $kab['city_name'] == $roworder['kab_send_order'] ? 'selected="selected"' : '';
                        $idkab .= $roworder['kab_send_order'] == $kab["city_name"] ? $kab["city_id"] : "";
                        echo '<option value="'.$kab["city_id"].'" '.$select.'>'.$kab["city_name"].'</option>';
                      }
                      ?>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="" class="form-label">KECAMATAN</label>
                    <select name="kec" id="kec" class="form-select" >
                      <option value="" hidden>KECAMATAN</option>
                      <?php  
                      $kecamatan = $db->dataIndonesia("kec",$idkab);
                      foreach ($kecamatan as $key => $kec){
                        $select = $kec["subdistrict_name"] == $roworder['kec_send_order'] ? 'selected="selected"' : '';
                        echo '<option value="'.$kec["subdistrict_id"].'" '.$select.'>'.$kec["subdistrict_name"].'</option>';
                      }
                      ?>
                    </select>
                  </div>
                  <div class="mb-3">
                    <label for="" class="form-label">Kode Pos</label>
                    <input type="number" name="kode_pos" id="kode_pos" class="form-control" placeholder="Kode Pos" value="<?= $roworder['kode_pos_send_order'] ?>">
                  </div>
                  <div class="mb-3">
                    <label for="" class="form-label">Alamat Lengkap</label>
                    <textarea name="alamat_lengkap" id="" rows="3" class="form-control"><?= $roworder['alamat_lengkap_send_order'] ?></textarea>
                  </div>
                  <div class="mb-3">
                    <label for="" class="form-label">Berat</label>
                    <input type="number" name="berat" step="0.01" id="berat" class="form-control" value="<?= $roworder['berat_send_order'] ?>">
                  </div>
                  <div class="mb-3">
                    <label for="" class="form-label">Ongkir</label>
                    <div class="input-group ">
                      <select name="resultcost" id="resut_pengiriman" class="form-control">
                        <option value="">PILIH PAKET</option>
                        <?php  
                        require_once "action/rajaOngkir.php";
                        $rajaongkir = new RajaOngkir();
  
                        $data = $rajaongkir->checkOngkir($roworder['kurir_pengiriman_order'], $asal, $roworder['kec_send_order'], $roworder['berat_send_order']);
                        foreach($data->costs as $d){
                          $select = $d->service == $roworder['nama_paket_send_order'] ? 'selected="selected"' : '' ;
                          echo '<option '.$select.' value="'.$d->cost[0]->value.' - '.$d->service.' - '.$d->cost[0]->etd.'">Rp.'.number_format($d->cost[0]->value,2,",",".").' (Paket: '.$d->service.' Estimasi: '.$d->cost[0]->etd. ')</option>';
                        }
                        ?>
                      </select>
                      <button class="btn btn-warning" type="button" id="button-addon2" onclick="showOngkir()">Cek</button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                <button type="submit" name="edit_send" class="btn btn-primary">Simpan</button>
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
          text:"Apakah Barang Sudah Sampai?",
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
  </body>
</html>
<?php mysqli_close($db->conn) ?>
<?php $_SESSION['alert'] = "";  ?>