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

$edit = $_GET['order'];

// $readonly = $edit != "" ? "readonly" : "";

$editselect = $db->selectTable("data_pemesanan","code_order",$edit,"id_owner",$id);
$rowedit = mysqli_fetch_assoc($editselect);

if($edit != ""){
  if(mysqli_num_rows($editselect) == 0){
    header('Location: data-pesanan');
    exit();
  }
}



$jum_trans = 0;
if($edit != ""){
  $trr = $db->selectTable("detail_transaksi","code_order",$edit,"id_owner",$id); 
  while($rowtrr=mysqli_fetch_assoc($trr)){
    $jum_trans += $rowtrr['jumlah_transaksi'];
  }
}

if(isset($_POST['create_spk'])){
  $date = $_POST['date_order'];
  // create spk
  $spk = $db->createSpk($id,$date);

  $user = $_SESSION['login_stiker_id'];
  
  $produk_id = $_POST['produk'];
  $varian = $_POST['varian_harga'];

  $array_varian = explode(" - ",$varian);
  $varian_harga = $array_varian[0];
  $varian_model = end(explode(" - ",$varian));


  // informasi Pemesanan
  $kategori_produk = $_POST['kategori_produk'];
  $jenis_produk = $_POST['jenis_produk'];
  if($kategori_produk == "Other" && $jenis_produk == "Custom"){
    $varian_harga = $_POST['varian_harga2'];
    $varian_model = "Stiker Custom";
    $produk_id = $_POST['produk2'];
  }

  $desain_status = $_POST['desain_status'];
  $cetak_status = $_POST['cetak_status'];
  $laminating = ucfirst($_POST['laminating']);
  $pemasangan_status = $_POST['pemasangan_status'];


  // detail desain
  $dbfoto = array();
  if($desain_status == "Ya" && count($_FILES['contoh_desain']['name']) != 0){
    for($index = 0; $index < count($_FILES['contoh_desain']['name']); $index++){
      if($_FILES['contoh_desain']['name'] != ""){
        $foto_path = $_FILES['contoh_desain']['tmp_name'][$index];
        $foto_name = basename($_FILES['contoh_desain']['name'][$index]);
        $folder = "assets/images/contoh_desain";
        $save_file = $db->saveFoto2($folder, $foto_name, $foto_path);
        array_push($dbfoto,$save_file);
      }
    }
  }
  
  $desk_desain = $desain_status == "Ya" ? $_POST['desk_desain'] : '';

  // detail pasang
  // $harga_pasang = $pemasangan_status == "Ya" ? $_POST['harga_pasang'] : '0';
  // $status_bayar_pasang = $_POST['status_bayar_pasang'];
  // $kategori_pemasang = $pemasangan_status == "Ya" ? $_POST['kategori_pemasang'] : '';

  // detail pemesanan
  $customer_id = $_POST['pelanggan'];
  $keterangan = $_POST['keterangan'];
  $satuan_potongan = $_POST['satuan_potongan'];
  $diskon = $_POST['diskon'];
  $sumber = $_POST['sumber'];
  $order_date = $date;
  $status_pengiriman = $pemasangan_status == "Ya" ? "Tidak" : $_POST['pengiriman'];

  // detail pengiriman
  $kurir = $status_pengiriman == "Ya" ? $_POST['kurir'] : '';

  $prov_desti = "";
  if($status_pengiriman == "Ya" && $_POST['prov'] != ""){
    $func_prov = $db->dataIndonesia("prov",null);
    foreach($func_prov as $key => $prov){
      if($prov['province_id'] == $_POST['prov']){
        $prov_desti .= $prov['province'];
      }
    }
  }
  $kabkota_desti = "";
  if($status_pengiriman == "Ya" && $_POST['kabkota'] != ""){
    $func_kab = $db->dataIndonesia("kab_kota",$_POST['prov']);
    foreach($func_kab as $key => $kab){
      if($kab['city_id'] == $_POST['kabkota']){
        $kabkota_desti .= $kab['city_name'];
      }
    }
  }
  $kec_desti = "";
  if($status_pengiriman == "Ya" && $_POST['kec'] != ""){
    $func_kec = $db->dataIndonesia("kec",$_POST['kabkota']);
    foreach($func_kec as $key => $kec){
      if($kec['subdistrict_id'] == $_POST['kec']){
        $kec_desti .= $kec['subdistrict_name'];
      }
    }
  }

  $alamat_lengkap = $status_pengiriman == "Ya" ? $_POST['alamat_lengkap'] : '';
  $kode_pos = $_POST['kode_pos'];
  $berat = $status_pengiriman == "Ya" ? $_POST['berat'] : '';
  $paket_ongkir = explode(" - ",$_POST['resultcost']);

  // detail pengiriman
  $cost = $status_pengiriman == "Ya" ? $paket_ongkir[0] : '';
  $name_paket = $status_pengiriman == "Ya" ? $paket_ongkir[1] : '';
  $etd = $status_pengiriman == "Ya" ? $paket_ongkir[2] : '';
  $cod = '';
  if($status_pengiriman == "Ya"){
    if($_POST['cod'] == "on"){
      $cod = "COD";
    }else{
      $cod = "Cash";
    }
  }

  // detail pembayaran
  $sisabayar = $_POST['sisa_pembayaran'];
  $status_pay = $sisabayar > 0 ? "Belum Lunas" : "Lunas";
  $total_pembayaran = $_POST['total_pembayaran'];

  // biaya tambahan
  $ket_biaya_tambahan = $_POST['ket_biaya_tambhahan'];
  $biaya_tambahan = $_POST['biaya_tambahan'];

  // status order
  $status_order = ""; 
  if($desain_status == "Ya"){
    $status_order = "Menunggu Designer";
  }elseif($cetak_status == "Ya"){
    $status_order = "Siap Cetak";
  }elseif($laminating != ""){
    $status_order = "Menunggu Finishing";
  }elseif($pemasangan_status == "Ya"){
    $status_order = "Siap Dipasang";
  }else{
    $status_order = "Selesai Dicetak";
  }

  

  if($edit != ""){

    $fotocontoh = $db->selectTable("contoh_desain","code_order",$rowedit['coder_order'],"id_owner",$id);
    while($rowcontoh=mysqli_fetch_assoc($fotocontoh)){
      if($_FILES['foto'.$rowcontoh['id_contoh']]['name'] != ""){
        $foto_path = $_FILES['foto'.$rowcontoh['id_contoh']]['tmp_name'];
        $foto_name = basename($_FILES['foto'.$rowcontoh['id_contoh']]['name']);
        $folder = "assets/images/contoh_desain";
        $save_file = $db->saveFoto2($folder, $foto_name, $foto_path);
        unlink($rowcontoh['foto_contoh']);
        $queryupdatee = "UPDATE contoh_Desain SET foto_contoh='$save_file' WHERE id_contoh='".$rowcontoh['id_contoh']."'";
        $result = mysqli_query($db->conn, $queryupdatee);
      }else{continue;}
    }


    $query = $db->updateOrder(
        $id,
        $edit,
        $jenis_produk,
        $customer_id,
        $status_pay,
        $status_order,
        $kategori_produk,
        $produk_id,
        $varian_harga,
        $varian_model,
        $desain_status,
        $cetak_status,
        $laminating,
        $pemasangan_status,
        $desk_desain,
        $keterangan,
        $diskon,  
        $status_pengiriman,
        $kurir,
        $prov_desti,
        $kabkota_desti,
        $kec_desti,
        $kode_pos,
        $alamat_lengkap,
        $berat,
        $cost,
        $name_paket,
        $etd,
        $sisabayar,
        $sumber,
        $satuan_potongan,
        $total_pembayaran,
        $cod
    );
    if($query){
      if(count($dbfoto) > 0){
        for($index = 0; $index < count($dbfoto); $index++){
          if($dbfoto[$index] != ""){
            $query = "INSERT INTO contoh_desain (foto_contoh,code_order,id_owner) VALUES('$dbfoto[$index]','$edit','$id')";
            $result = mysqli_query($db->conn, $query);
          }
        }
      }

      $tr = mysqli_query($db->conn, "DELETE FROM biaya_tambahan_order WHERE id_owner='$id' AND code_order='$edit'");

      if(count($ket_biaya_tambahan) > 0 && $tr){
        foreach($ket_biaya_tambahan as $index => $ket){
          if($biaya_tambahan[$index] != ""){
            $keterangan = $ket;
            $biaya = $biaya_tambahan[$index];
            $query = "INSERT INTO biaya_tambahan_order (keterangan_biaya,harga_ketbiaya,id_owner,code_order) VALUES('$keterangan','$biaya','$id','$edit')";
            $result = mysqli_query($db->conn, $query);
          }
        }
      }

      $tp = mysqli_query($db->conn, "DELETE FROM detail_transaksi WHERE id_owner='$id' AND code_order='$edit'");
      if($total_pembayaran != "" && $tp){
        $transaksi = $db->insertDetailTr($id,$edit,$order_date,$total_pembayaran);
        if($transaksi){

          $_SESSION['alert'] = "1";
          header('Location:data-pesanan.php');
        }
      }else{
        $_SESSION['alert'] = "1";
        header('Location:data-pesanan.php');
      }
    }
  }else{
    $query = $db->insertOrder(
        $id,
        $jenis_produk,
        $spk,
        $customer_id,
        $status_pay,
        $order_date,
        $status_order,
        $kategori_produk,
        $produk_id,
        $varian_harga,
        $varian_model,
        $desain_status,
        $cetak_status,
        $laminating,
        $pemasangan_status,
        $desk_desain,
        $keterangan,
        $diskon,
        $status_pengiriman,
        $kurir,
        $prov_desti,
        $kabkota_desti,
        $kec_desti,
        $kode_pos,
        $alamat_lengkap,
        $berat,
        $cost,
        $name_paket,
        $etd,
        $sisabayar,
        $sumber,
        $user,
        $satuan_potongan,
        $total_pembayaran,
        $cod
    );
  
    if($query){
      if(count($dbfoto) > 0){
        for($index = 0; $index < count($dbfoto); $index++){
          if($dbfoto[$index] != ""){
            $query = "INSERT INTO contoh_desain (foto_contoh,code_order,id_owner) VALUES('$dbfoto[$index]','$edit','$id')";
            $result = mysqli_query($db->conn, $query);
          }
        }
      }
      if(count($ket_biaya_tambahan) > 0){
        foreach($ket_biaya_tambahan as $index => $ket){
          if($biaya_tambahan[$index] != ""){
            $keterangan = $ket;
            $biaya = $biaya_tambahan[$index];
            $query = "INSERT INTO biaya_tambahan_order (keterangan_biaya,harga_ketbiaya,id_owner,code_order) VALUES('$keterangan','$biaya','$id','$spk')";
            $result = mysqli_query($db->conn, $query);
          }
        }
      }
      if($total_pembayaran != ""){
        $transaksi = $db->insertDetailTr($id,$spk,$order_date,$total_pembayaran);
        if($transaksi){

          $_SESSION['alert'] = "1";
          header('Location:data-pesanan.php');
        }
      }else{
        $_SESSION['alert'] = "1";
        header('Location:data-pesanan.php');
      }
    }
  }
}


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

    <!-- Sweet Alert-->
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

    <script src="assets/select2/dist/js/jquery.min.js"></script>
    <link href="assets/select2/dist/css/select2.min.css" rel="stylesheet" />
    <script src="assets/select2/dist/js/select2.min.js"></script>

    <!-- Kab -->
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
    <!-- end Kab -->

    <!-- Kec -->
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
    <!-- end Kec -->

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

    <!-- alamat -->
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
    <script>
      function detailPengiriman(str){
        var detail = document.getElementById("detailpengiriman");
        var id = document.getElementById("pelanggan").value;
        if(str == "Ya"){
          detail.style.display = "block";
          $("#kurir").attr("required","");
          $("#berat").attr("required","");
          $("#resut_pengiriman").attr("required","");
          if(id != ""){
            $.ajax({
              type:'post',
              url:'select_address.php?id='+id,
              success:function(hasil_address){
                $("div[id=data_customer]").html(hasil_address);
              }
            })
          }
        }else{
          detail.style.display = "none";
          $("#kurir").removeAttr("required","");
          $("#berat").removeAttr("required","");
          $("#resut_pengiriman").removeAttr("required","");
        }
      }
    </script>
    <!-- end alamat -->


    <!-- show pembayaran -->

    <!-- change satuan potongan -->
    <script>
      function changeSatuan(str){
        var rupiah = document.getElementById("rupiah").style;
        var persen = document.getElementById("persen").style;
        if(str == "persen"){
          rupiah.display = "none";
          persen.display = ""
        }else{
          rupiah.display = "";
          persen.display = "none"
        }
      }
    </script>
    <!-- end change satuan potongan -->

    <!-- biaya tambahan -->
    <script>
      function showfeeby(str){
        var fee = document.getElementById("harga").value;
        if(document.getElementById("jenis").value ==  "Custom" && document.getElementById("kategori").value == "Other"){
          var fee = document.getElementById("harga2").value;
        }
        var diskon = document.getElementById("diskon").value;
        var debit = document.getElementById("debit").value;
        var tamby = document.getElementsByName("biaya_tambahan[]");
        var satuan = document.getElementById("satuan").value;

        // jika harga produk tidak kosong
        if(fee != ""){
          var hasil = 0;
          // jika biaya tambahan tidak kosong
          if(tamby.length > 0){
            var count = 0;
            // loop array input biaya tambahan
            for(var index = 0; index < tamby.length; index++){
              // jika nilai input index kosong (null)
              if(tamby[index].value == ""){
                count += 0;
              }
              // jika nilai input index tdak kosong (nulll)
              else{
                count += parseFloat(tamby[index].value);
              }
            }
            // jika diskon tidak kosong
            if(diskon != ""){
              // potongan dengan persen
              $potongan = (parseFloat(fee) + parseFloat(count)) * (parseFloat(diskon)/100);
              // potongan dengan rupiah
              if(satuan == "rupiah"){
                $potongan = parseFloat(diskon);
              }
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = (parseFloat(fee) + parseFloat(count) - $potongan) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee) + parseFloat(count) - $potongan
              }
            }
            // jika diskon kosong
            else{
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = (parseFloat(fee) + parseFloat(count)) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee) + parseFloat(count)
              }
            }
          }
          // jika biaya tambahan kosong
          else{
            // jika diskon tidak kosong
            if(diskon != ""){
              // potongan dengan persen
              $potongan = parseFloat(fee) * (parseFloat(diskon)/100);
              // potongan dengan rupiah
              if(satuan == "rupiah"){
                $potongan = parseFloat(diskon);
              }
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = (parseFloat(fee) - $potongan) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee) - $potongan
              }
            }
            // jika diskon kosong
            else{
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = parseFloat(fee) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee);              
              }
            }
          }
          // sisa atau hasil dari yang dibayar
          document.getElementById("fee").value = hasil;
        }
        // jika harga produk kosong
        else{
          // jika biaya tambahan tidak kosong
          if(tamby.length > 0){
            var count = 0;
            // loop array input biaya tambahan
            for(var index = 0; index < tamby.length; index++){
              // jika nilai input index kosong (null)
              if(tamby[index].value == ""){
                count += 0;
              }
              // jika nilai input index tdak kosong (nulll)
              else{
                count += parseFloat(tamby[index].value);
              }
            }
            // jika diskon tidak kosong
            if(diskon != ""){
              // potongan dengan persen
              $potongan = parseFloat(count) * (parseFloat(diskon)/100);
              // potongan dengan rupiah
              if(satuan == "rupiah"){
                $potongan = parseFloat(diskon);
              }
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = parseFloat(count) - $potongan - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(count) - $potongan;
              }
            }
            // jika diskon kosong
            else{
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = parseFloat(count) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(count)
              }
            }
          }
          // jika biaya tambahan kosong
          else{
            // jika debit customer tidak kosong
            if(debit != ""){
              hasil = 0 - debit;
            }
            // jika debit customer kosong
            else{
              hasil = 0;
            }
          }
          // sisa atau hasil dari yang dibayar
          document.getElementById("fee").value = hasil;
        }
      }
    </script>
    <!-- end biaya tambahan -->

    <!-- onkeyup and onchange harga produk -->
    <script>
      function showFee(fee){
        var diskon = document.getElementById("diskon").value;
        var debit = document.getElementById("debit").value;
        var tamby = document.getElementsByName("biaya_tambahan[]");
        var satuan = document.getElementById("satuan").value;
        // jika harga produk tidak kosong
        if(fee != ""){
          var hasil = 0;
          // jika biaya tambahan tidak kosong
          if(tamby.length > 0){
            var count = 0;
            // loop array input biaya tambahan
            for(var index = 0; index < tamby.length; index++){
              // jika nilai input index kosong (null)
              if(tamby[index].value == ""){
                count += 0;
              }
              // jika nilai input index tdak kosong (nulll)
              else{
                count += parseFloat(tamby[index].value);
              }
            }
            // jika diskon tidak kosong
            if(diskon != ""){
              // potongan dengan persen
              $potongan = (parseFloat(fee) + parseFloat(count)) * (parseFloat(diskon)/100);
              // potongan dengan rupiah
              if(satuan == "rupiah"){
                $potongan = parseFloat(diskon);
              }
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = (parseFloat(fee) + parseFloat(count) - $potongan) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee) + parseFloat(count) - $potongan
              }
            }
            // jika diskon kosong
            else{
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = (parseFloat(fee) + parseFloat(count)) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee) + parseFloat(count)
              }
            }
          }
          // jika biaya tambahan kosong
          else{
            // jika diskon tidak kosong
            if(diskon != ""){
              // potongan dengan persen
              $potongan = parseFloat(fee) * (parseFloat(diskon)/100);
              // potongan dengan rupiah
              if(satuan == "rupiah"){
                $potongan = parseFloat(diskon);
              }
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = (parseFloat(fee) - $potongan) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee) - $potongan
              }
            }
            // jika diskon kosong
            else{
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = parseFloat(fee) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee);              
              }
            }
          }
          // sisa atau hasil dari yang dibayar
          document.getElementById("fee").value = hasil;
        }
        // jika harga produk kosong
        else{
          // jika biaya tambahan tidak kosong
          if(tamby.length > 0){
            var count = 0;
            // loop array input biaya tambahan
            for(var index = 0; index < tamby.length; index++){
              // jika nilai input index kosong (null)
              if(tamby[index].value == ""){
                count += 0;
              }
              // jika nilai input index tdak kosong (nulll)
              else{
                count += parseFloat(tamby[index].value);
              }
            }
            // jika diskon tidak kosong
            if(diskon != ""){
              // potongan dengan persen
              $potongan = parseFloat(count) * (parseFloat(diskon)/100);
              // potongan dengan rupiah
              if(satuan == "rupiah"){
                $potongan = parseFloat(diskon);
              }
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = parseFloat(count) - $potongan - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(count) - $potongan;
              }
            }
            // jika diskon kosong
            else{
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = parseFloat(count) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(count)
              }
            }
          }
          // jika biaya tambahan kosong
          else{
            // jika debit customer tidak kosong
            if(debit != ""){
              hasil = 0 - debit;
            }
            // jika debit customer kosong
            else{
              hasil = 0;
            }
          }
          // sisa atau hasil dari yang dibayar
          document.getElementById("fee").value = hasil;
        }
      }
    </script>
    <!-- end onkeyup and onchange harga produk -->

    <!-- onkeyup diskon -->
    <script>
      function showFee3(diskon){
        var fee = document.getElementById("harga").value;
        if(document.getElementById("jenis").value ==  "Custom" && document.getElementById("kategori").value == "Other"){
          var fee = document.getElementById("harga2").value;
        }
        var debit = document.getElementById("debit").value;
        var tamby = document.getElementsByName("biaya_tambahan[]");
        var satuan = document.getElementById("satuan").value;
        // jika harga produk tidak kosong
        if(fee != ""){
          var hasil = 0;
          // jika biaya tambahan tidak kosong
          if(tamby.length > 0){
            var count = 0;
            // loop array input biaya tambahan
            for(var index = 0; index < tamby.length; index++){
              // jika nilai input index kosong (null)
              if(tamby[index].value == ""){
                count += 0;
              }
              // jika nilai input index tdak kosong (nulll)
              else{
                count += parseFloat(tamby[index].value);
              }
            }
            // jika diskon tidak kosong
            if(diskon != ""){
              // potongan dengan persen
              $potongan = (parseFloat(fee) + parseFloat(count)) * (parseFloat(diskon)/100);
              // potongan dengan rupiah
              if(satuan == "rupiah"){
                $potongan = parseFloat(diskon);
              }
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = (parseFloat(fee) + parseFloat(count) - $potongan) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee) + parseFloat(count) - $potongan
              }
            }
            // jika diskon kosong
            else{
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = (parseFloat(fee) + parseFloat(count)) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee) + parseFloat(count)
              }
            }
          }
          // jika biaya tambahan kosong
          else{
            // jika diskon tidak kosong
            if(diskon != ""){
              // potongan dengan persen
              $potongan = parseFloat(fee) * (parseFloat(diskon)/100);
              // potongan dengan rupiah
              if(satuan == "rupiah"){
                $potongan = parseFloat(diskon);
              }
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = (parseFloat(fee) - $potongan) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee) - $potongan
              }
            }
            // jika diskon kosong
            else{
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = parseFloat(fee) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee);              
              }
            }
          }
          // sisa atau hasil dari yang dibayar
          document.getElementById("fee").value = hasil;
        }
        // jika harga produk kosong
        else{
          // jika biaya tambahan tidak kosong
          if(tamby.length > 0){
            var count = 0;
            // loop array input biaya tambahan
            for(var index = 0; index < tamby.length; index++){
              // jika nilai input index kosong (null)
              if(tamby[index].value == ""){
                count += 0;
              }
              // jika nilai input index tdak kosong (nulll)
              else{
                count += parseFloat(tamby[index].value);
              }
            }
            // jika diskon tidak kosong
            if(diskon != ""){
              // potongan dengan persen
              $potongan = parseFloat(count) * (parseFloat(diskon)/100);
              // potongan dengan rupiah
              if(satuan == "rupiah"){
                $potongan = parseFloat(diskon);
              }
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = parseFloat(count) - $potongan - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(count) - $potongan;
              }
            }
            // jika diskon kosong
            else{
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = parseFloat(count) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(count)
              }
            }
          }
          // jika biaya tambahan kosong
          else{
            // jika debit customer tidak kosong
            if(debit != ""){
              hasil = 0 - debit;
            }
            // jika debit customer kosong
            else{
              hasil = 0;
            }
          }
          // sisa atau hasil dari yang dibayar
          document.getElementById("fee").value = hasil;
        }
      }
    </script>
    <!-- end onkeyup diskon -->

    <!-- onkeyup pembayaran customer -->
    <script>
      function sisaDari(debit){
        var fee = document.getElementById("harga").value;
        if(document.getElementById("jenis").value ==  "Custom" && document.getElementById("kategori").value == "Other"){
          var fee = document.getElementById("harga2").value;
        }
        var diskon = document.getElementById("diskon").value;
        var tamby = document.getElementsByName("biaya_tambahan[]");
        var satuan = document.getElementById("satuan").value;
        // jika harga produk tidak kosong
        if(fee != ""){
          var hasil = 0;
          // jika biaya tambahan tidak kosong
          if(tamby.length > 0){
            var count = 0;
            // loop array input biaya tambahan
            for(var index = 0; index < tamby.length; index++){
              // jika nilai input index kosong (null)
              if(tamby[index].value == ""){
                count += 0;
              }
              // jika nilai input index tdak kosong (nulll)
              else{
                count += parseFloat(tamby[index].value);
              }
            }
            // jika diskon tidak kosong
            if(diskon != ""){
              // potongan dengan persen
              $potongan = (parseFloat(fee) + parseFloat(count)) * (parseFloat(diskon)/100);
              // potongan dengan rupiah
              if(satuan == "rupiah"){
                $potongan = parseFloat(diskon);
              }
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = (parseFloat(fee) + parseFloat(count) - $potongan) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee) + parseFloat(count) - $potongan
              }
            }
            // jika diskon kosong
            else{
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = (parseFloat(fee) + parseFloat(count)) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee) + parseFloat(count)
              }
            }
          }
          // jika biaya tambahan kosong
          else{
            // jika diskon tidak kosong
            if(diskon != ""){
              // potongan dengan persen
              $potongan = parseFloat(fee) * (parseFloat(diskon)/100);
              // potongan dengan rupiah
              if(satuan == "rupiah"){
                $potongan = parseFloat(diskon);
              }
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = (parseFloat(fee) - $potongan) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee) - $potongan
              }
            }
            // jika diskon kosong
            else{
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = parseFloat(fee) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee);              
              }
            }
          }
          // sisa atau hasil dari yang dibayar
          document.getElementById("fee").value = hasil;
        }
        // jika harga produk kosong
        else{
          // jika biaya tambahan tidak kosong
          if(tamby.length > 0){
            var count = 0;
            // loop array input biaya tambahan
            for(var index = 0; index < tamby.length; index++){
              // jika nilai input index kosong (null)
              if(tamby[index].value == ""){
                count += 0;
              }
              // jika nilai input index tdak kosong (nulll)
              else{
                count += parseFloat(tamby[index].value);
              }
            }
            // jika diskon tidak kosong
            if(diskon != ""){
              // potongan dengan persen
              $potongan = parseFloat(count) * (parseFloat(diskon)/100);
              // potongan dengan rupiah
              if(satuan == "rupiah"){
                $potongan = parseFloat(diskon);
              }
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = parseFloat(count) - $potongan - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(count) - $potongan;
              }
            }
            // jika diskon kosong
            else{
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = parseFloat(count) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(count)
              }
            }
          }
          // jika biaya tambahan kosong
          else{
            // jika debit customer tidak kosong
            if(debit != ""){
              hasil = 0 - debit;
            }
            // jika debit customer kosong
            else{
              hasil = 0;
            }
          }
          // sisa atau hasil dari yang dibayar
          document.getElementById("fee").value = hasil;
        }
      }
    </script>
    <!-- onkeyup pembayaran customer -->

    <!-- end show pembayaran -->

    <!-- show produk -->
    <script>
      function showSubJenis1(kategori) {
        var jenis = document.getElementById("jenis").value;
        if(kategori != "" && jenis != ""){
          if(kategori == "Other" && jenis == "Custom"){
            document.getElementById("produk").style.display = "none";
            document.getElementById("kategori_type").removeAttribute("required");
            document.getElementById("harga").removeAttribute("required");
            document.getElementById("produk2").style.display = "";
            $("#kategori_type2").attr("required", "");
            $("#harga2").attr("required", "");
          }else{
            document.getElementById("produk").style.display = "";
            $("#kategori_type").attr("required", "");
            $("#harga").attr("required", "");
            document.getElementById("produk2").style.display = "none";
            document.getElementById("kategori_type2").removeAttribute("required");
            document.getElementById("harga2").removeAttribute("required");
            // $('#cus').remove();
            $.ajax({
              type:'post',
              url:'data_produk_kategori.php?jenisp='+kategori+'&jenispr='+jenis+'&id='+<?= $id ?>,
              success:function(hasil_views){
                $("select[name=produk]").html(hasil_views);
              }
            })
          }
        }
      }
    </script>
    <script>
      function showSubJenis2(jenis) {
        var kategori = document.getElementById("kategori").value;
        if(kategori != "" && jenis != ""){
          if(kategori == "Other" && jenis == "Custom"){
            document.getElementById("produk").style.display = "none";
            document.getElementById("kategori_type").removeAttribute("required");
            document.getElementById("harga").removeAttribute("required");
            document.getElementById("produk2").style.display = "";
            $("#kategori_type2").attr("required", "");
            $("#harga2").attr("required", "");
          }else{
            document.getElementById("produk").style.display = "";
            $("#kategori_type").attr("required", "");
            $("#harga").attr("required", "");
            document.getElementById("produk2").style.display = "none";
            document.getElementById("kategori_type2").removeAttribute("required");
            document.getElementById("harga2").removeAttribute("required");
            // $('#cus').remove();
            $.ajax({
              type:'post',
              url:'data_produk_kategori.php?jenisp='+kategori+'&jenispr='+jenis+'&id='+<?= $id ?>,
              success:function(hasil_views){
                $("select[name=produk]").html(hasil_views);
              }
            })
          }
        }
      }
    </script>
    <!-- end show produk -->

    <!-- show varian harga produk -->
    <script>
      function showVarian(){
        var str = document.getElementById("kategori_type").value;
        var pr = document.getElementById("jenis").value;
        $.ajax({
          type:'post',
          url:'data_varian_type.php?type='+str+'&id='+<?= $id ?>+'&jenispr='+pr,
          success:function(hasil_views){
            $("select[name=varian_harga").html(hasil_views);
          }
        })
      }
    </script>
    <!-- end show varian harga produk -->

    <!-- show input detail desain -->
    <script>
      function showDetailDesain(str){
        var detailDesain = document.getElementById("desain_detail");
        if(str == "Ya"){
          detailDesain.style.display = "block";
        }else{
          detailDesain.style.display = "none";
        }
      }
    </script>
    <!-- end show input detail desain -->

    <!-- show input detail pemasangan -->
    <script>
      function showDetailPasang(str){
        var pengiriman = document.getElementById("pengiriman_statuss");
        var pengirimandetail = document.getElementById("detailpengiriman");
        if(str == "Ya"){
          pengiriman.disabled = true;
          pengiriman.value = "Tidak";
          pengiriman.style.cursor = "not-allowed";
          pengirimandetail.style.display = "none";
        }else{
          pengiriman.disabled = false;
          pengiriman.style.cursor = "";
        }
      }
    </script>
    <!-- end show input detail pemasangan -->

    <!-- time -->
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
    <!-- end time -->

  </head>

  <body data-sidebar="dark" >
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

            <!-- form -->
            <form action="" method="post" enctype="multipart/form-data">
              <div class="row">

                <!-- informasi pemesanan -->
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title">Informasi Pemesanan</div>
                      <div class="row g-3 mt-3">

                        <!-- kategori produk -->
                        <div class="col-md-3">
                          <label for="jenisp" class="form-label">Kategori Produk</label>
                          <select name="kategori_produk" onchange="showSubJenis1(this.value)" id="kategori" class="form-select" required>
                            <option value="" hidden>PILIH KATEGORI</option>
                            <?php  
                            $array_kategori = array("Mobil","Motor","Other");
                            $val = $edit != "" ? $rowedit['kategori_produk_order'] : '';
                            foreach($array_kategori as $kat){
                              $select = $kat == $val ? 'selected="selected"' : '';
                            ?>
                            <option value="<?= $kat ?>" <?= $select ?>><?= $kat ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <!-- end kategori produk -->

                        <!-- jenis produk -->
                        <div class="col-md-3">
                          <label for="jenispr" class="form-label">Jenis Produk</label>
                          <select name="jenis_produk" onchange="showSubJenis2(this.value)" id="jenis" class="form-select" required>
                            <option value="" hidden>PILIH JENIS</option>
                            <?php  
                            $array_jenis = array("Custom", "No Custom");
                            $val = $edit != "" ? $rowedit['jenis_produk_order'] : "";
                            foreach($array_jenis as $jen){
                              $select = $val == $jen ? 'selected="selected"' : '';
                            ?>
                            <option <?= $select ?> value="<?= $jen ?>"><?= $jen == "No Custom" ? "Produk ".$db->nameFormater($rowstore['name_store']) : $jen ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <!-- end jenis produk -->

                        <!-- produk -->
                        <div class="col-md-6">
                          <?php  
                          // jika edit
                          if($edit != ""){
                            // jika jenis custom dan ketegori other
                            if($rowedit['jenis_produk_order'] == "Custom" && $rowedit['kategori_produk_order'] == "Other"){

                          ?>

                          <div id="produk2" class="row">
                            <div class="col-md-6">
                              <label for="" class="form-label">Nama Produk</label>
                              <input type="text" value="<?= $rowedit['produk_order'] ?>" id="kategori_type2" name="produk2" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                              <label for="" class="form-label">Harga</label>
                              <input type="number" value="<?= $rowedit['harga_produk_order'] ?>" name="varian_harga2" id="harga2" class="form-control" onkeyup="showFee(this.value)" required>
                            </div>
                          </div>

                          <?php 
                            }
                            // jika buka custom dan other
                            else{ 
                          ?>

                          <div class="row" id="produk">
                            <div class="col-md-6">
                              <label for="kategori_type" class="form-label">Produk Tersedia</label>
                              <select onchange="showVarian()" name="produk" id="kategori_type" class="form-control js-example-basic-single" required>
                                <?php 
                                if($edit != ""){ 
                                  if($rowedit['jenis_produk_order'] == "Custom"){
                                    if($rowedit['kategori_produk_order'] == "Other"){
                                
                                    }else{
                                        $views = $db->selectTable("merek_galeri","id_owner",$id,"jenis_merek",$rowedit['kategori_produk_order']);
                                        echo '<option value="" hidden>PILIH TYPE</option>';
                                        if(mysqli_num_rows($views)>0){
                                            while($row=mysqli_fetch_assoc($views)){
                                                $views2 = $db->selectTable("type_galeri","id_owner",$id,"id_merek",$row['id_merek']);
                                                if(mysqli_num_rows($views2)>0){
                                                    echo '<optgroup label="'.$db->nameFormater($row['name_merek']).'">';
                                                    $val = $rowedit['produk_order'];
                                                    while($row2=mysqli_fetch_assoc($views2)){
                                                        $select = $val == $row2['id_type'] ? 'selected="selected"' : '';
                                                        echo '<option '.$select.' value="'.$row2['id_type'].'">'.$db->nameFormater($row2['name_type']).'</option>';
                                                    }
                                                    echo '</optgroup>';
                                                }
                                            }
                                        }
                                    }
                                  }else{
                                      echo '<option value="" hidden>PILIH PRODUK</option>';
                                  }
                                }else{
                                ?>
                                <option value="" hidden>PRODUK</option>
                                <?php } ?>
                              </select>
                            </div>
                            <div class="col-md-6">
                              <label for="harga" class="form-label">Harga/Varian Produk</label>
                              <select name="varian_harga" id="harga" class="form-select" onchange="showFee(this.value)" required>
                                <?php  
                                if($edit != ""){
                                  $val = $rowedit['model_stiker_order'];
                                  if($rowedit['jenis_produk_order'] == "Custom" && $rowedit['kategori_produk_order'] != "Other"){
                                    $views = $db->selectTable("type_galeri","id_type",$rowedit['produk_order'],"id_owner",$id);
                                    $row=mysqli_fetch_assoc($views);
                                    $type = array("fulldash","fullbody","lite");
                                    echo '<option value="">PILIH</option>';
                                    foreach($type as $ty){
                                      $select = $ty == strtolower($rowedit['model_stiker_order']) ? 'selected="selected"' : '';
                                      $fee = $ty == "fulldash" ? $row['fullbodydash_harga_type'] : ($ty == "fullbody" ? $row['fullbody_harga_type'] : $row['lite_harga_type'] );
                                      if($fee != 0){
                                        echo '<option '.$select.' value="'.$fee.' - '.$ty.'">'.number_format($fee,2,",",".").' ('.preg_replace('/\s+/','',$db->nameFormater($ty)).')</option>';
                                      }
                                      
                                    }
                                  }
                                }else{
                                ?>
                                <option value="">PILIH</option>
                                <?php } ?>
                              </select>
                            </div>
                          </div> 

                          <?php 
                            }
                          }
                          // bukan edit
                          else{
                          ?>
                          
                          <div class="row" id="produk">
                            <div class="col-md-6">
                              <label for="kategori_type" class="form-label">Produk Tersedia</label>
                              <select onchange="showVarian()" name="produk" id="kategori_type" class="form-control js-example-basic-single" required>
                                <?php 
                                if($edit != ""){ 
                                  if($rowedit['jenis_produk_order'] == "Custom"){
                                    if($rowedit['kategori_produk_order'] == "Other"){
                                
                                    }else{
                                        $views = $db->selectTable("merek_galeri","id_owner",$id,"jenis_merek",$rowedit['kategori_produk_order']);
                                        echo '<option value="" hidden>PILIH TYPE</option>';
                                        if(mysqli_num_rows($views)>0){
                                            while($row=mysqli_fetch_assoc($views)){
                                                $views2 = $db->selectTable("type_galeri","id_owner",$id,"id_merek",$row['id_merek']);
                                                if(mysqli_num_rows($views2)>0){
                                                    echo '<optgroup label="'.$db->nameFormater($row['name_merek']).'">';
                                                    $val = $rowedit['produk_order'];
                                                    while($row2=mysqli_fetch_assoc($views2)){
                                                        $select = $val == $row2['id_type'] ? 'selected="selected"' : '';
                                                        echo '<option '.$select.' value="'.$row2['id_type'].'">'.$db->nameFormater($row2['name_type']).'</option>';
                                                    }
                                                    echo '</optgroup>';
                                                }
                                            }
                                        }
                                    }
                                  }
                                  else{
                                      echo '<option value="" hidden>PILIH PRODUK</option>';
                                  }
                                }else{
                                ?>
                                <option value="" hidden>PRODUK</option>
                                <?php } ?>
                              </select>
                            </div>
                            <div class="col-md-6">
                              <label for="harga" class="form-label">Harga/Varian Produk</label>
                              <select name="varian_harga" id="harga" class="form-select" onchange="showFee(this.value)" required>
                                <?php  
                                if($edit != ""){
                                  $val = $rowedit['model_stiker_order'];
                                  if($rowedit['jenis_produk_order'] == "Custom" && $rowedit['kategori_produk_order'] != "Other"){
                                    $views = $db->selectTable("type_galeri","id_type",$rowedit['id_type'],"id_owner",$id);
                                    $row=mysqli_fetch_assoc($views);
                                    $type = array("fulldash","fullbody","lite");
                                    echo '<option value="">PILIH</option>';
                                    foreach($type as $ty){
                                      $select = $ty == strtolower($rowedit['model_stiker_order']) ? 'selected="selected"' : '';
                                      $fee = $ty == "fulldash" ? $row['fullbodydash_harga_type'] : ($ty == "fullbody" ? $row['fullbody_harga_type'] : $row['lite_harga_type'] );
                                      if($fee != 0){
                                        echo '<option '.$select.' value="'.$fee.' - '.$ty.'">'.number_format($fee,2,",",".").' ('.preg_replace('/\s+/','',$db->nameFormater($ty)).')</option>';
                                      }
                                      
                                    }
                                  }
                                }else{
                                ?>
                                <option value="">PILIH</option>
                                <?php } ?>
                              </select>
                            </div>
                          </div>

                          <div style="display: none;" id="produk2" class="row">
                            <div class="col-md-6">
                              <label for="" class="form-label">Nama Produk</label>
                              <input type="text" name="produk2" id="kategori_type2" class="form-control">
                            </div>
                            <div class="col-md-6">
                              <label for="" class="form-label">Harga</label>
                              <input type="number" name="varian_harga2" id="harga2" class="form-control" onkeyup="showFee(this.value)">
                            </div>
                          </div>
                          <?php 
                          } 
                          ?>
                        </div>
                        <!-- end produk -->

                        <!-- status desain -->
                        <div class="col-md-3">
                          <label for="desain_status" class="form-label">Desain</label>
                          <select name="desain_status" id="desain_status" class="form-select" onchange="showDetailDesain(this.value)">
                            <?php
                            $v = $edit != "" ? $rowedit['status_desain_order'] : "Tidak";  
                            $d = array("Ya","Tidak");
                            foreach($d as $x){
                              $select = $v == $x ? 'selected="selected"' : '';
                            ?>
                            <option value="<?= $x ?>" <?= $select ?>><?= $x ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <!-- end status desain -->

                        <!-- status cetak -->
                        <div class="col-md-3">
                          <label for="cetak_status" class="form-label">Cetak</label>
                          <select name="cetak_status" id="cetak_status" class="form-select">
                          <?php
                            $v = $edit != "" ? $rowedit['status_cetak_order'] : "Tidak";  
                            $d = array("Ya","Tidak");
                            foreach($d as $x){
                              $select = $v == $x ? 'selected="selected"' : '';
                            ?>
                            <option value="<?= $x ?>" <?= $select ?>><?= $x ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <!-- end status cetak -->

                        <!-- bahan laminating -->
                        <div class="col-md-3">
                          <label for="laminating" class="form-label">Laminating</label>
                          <select name="laminating" id="" class="form-select">
                            <option value="">Tidak</option>
                            <?php  
                            $v = $edit != "" ? $rowedit['laminating_order'] : '' ; 
                            $laminatt = $db->selectTable("bahan_laminating","id_owner",$id);
                            while($rowminat=mysqli_fetch_assoc($laminatt)){
                              $select = $rowminat['nama_laminating'] == $v ? 'selected="selected"' : '' ;
                            ?>
                            <option value="<?= $rowminat['nama_laminating'] ?>" <?= $select ?>><?= $rowminat['nama_laminating'] ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <!-- end bahan laminating -->

                        <!-- status pemasangan -->
                        <div class="col-md-3">
                          <label for="pemasangan_status" class="form-label">Pemasangan</label>
                          <select name="pemasangan_status" id="pemasangan_status" onchange="showDetailPasang(this.value)" class="form-select">
                          <?php
                            $v = $edit != "" ? $rowedit['status_pasang_order'] : "Tidak";  
                            $d = array("Ya","Tidak");
                            foreach($d as $x){
                              $select = $v == $x ? 'selected="selected"' : '';
                            ?>
                            <option value="<?= $x ?>" <?= $select ?>><?= $x ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <!-- end status pemasangan -->
                        

                      </div>
                    </div>
                  </div>
                </div>
                <!-- end informasi pemesanan -->

                <?php  
                if($edit != ""){
                ?>
                <!-- detail desain -->
                <div class="col-12" id="desain_detail" <?= $rowedit['status_desain_order'] == "Tidak" ? 'style="display: none;"' : 'style="display: block;"' ?>>
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title">Detail Desain</div>

                      <div class="row g-3">
                        <label for="" class="col-sm-2 col-form-label">
                          Upload Contoh Desain 
                        </label>
                        
                        <div class="col-sm-10">
                          <div class="row">
                            
                            <?php  
                            $no = 0;
                            $fotocontoh = $db->selectTable("contoh_desain","code_order",$rowedit['code_order'],"id_owner",$id);
                            while($rowcontoh=mysqli_fetch_assoc($fotocontoh)){
                            ?>
                            <div class="col col-sm-3">
                              <span><a target="_blank" href="<?= $rowcontoh['foto_contoh'] ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="View">Contoh <?= ++$no ?></a></span>
                              <label for="foto<?= $rowcontoh['id_contoh'] ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit" style="cursor: pointer;">
                                <i class="mdi mdi-camera-outline"></i>
                              </label>
                              <input hidden type="file" name="foto<?= $rowcontoh['id_contoh'] ?>" id="foto<?= $rowcontoh['id_contoh'] ?>" class="form-control" multiple>
                            </div>
                            <?php } ?>
                            <div class="col-sm-12 mt-2">
                              <input type="file" name="contoh_desain[]" id="" class="form-control" multiple>
                            </div>
                          </div>
                        </div>
                        <label for="" class="col-sm-2 col-form-label">Deskripsi Desain</label>
                        <div class="col-sm-10">
                          <textarea name="desk_desain" id="" rows="3" class="form-control"><?= $rowedit['desk_desain_order'] ?></textarea>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
                <?php  
                }
                else{
                ?>
                <div class="col-12" id="desain_detail" style="display: none;">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title">Detail Desain</div>
                      <div class="row g-3">
                        <label for="" class="col-sm-2 col-form-label">Upload Contoh Desain</label>
                        <div class="col-sm-10">
                          <input type="file" name="contoh_desain[]" id="" class="form-control" multiple>
                        </div>
                        <label for="" class="col-sm-2 col-form-label">Deskripsi Desain</label>
                        <div class="col-sm-10">
                          <textarea name="desk_desain" id="" rows="3" class="form-control"></textarea>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- end detail desain -->
                <?php 
                } 
                ?>

                <!-- detail pemesanan -->
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title">Detail Pemesanan</div>
                      <div class="row g-3">

                        <!-- label pelanggan -->
                        <label for="select2" class="col-sm-2 col-form-label">Pelanggan</label>

                        <!-- pelanggan -->
                        <div class="col-sm-4">
                          <select name="pelanggan" onchange="addressCustomer(this.value)" class="form-control js-example-basic-single" id="pelanggan" style="width: 100%;" required>
                            <option value="" hidden>PILIH PELANGGAN</option>
                            <?php  
                              $val = $edit != "" ? $rowedit['id_customer'] : '';
                              $cs = $db->selectTable("customer_stiker","id_owner",$id);
                              while($rowcs=mysqli_fetch_assoc($cs)){
                                $select = $val == $rowcs['id_customer'] ? 'selected="selected"' : '';
                            ?>
                            <option value="<?= $rowcs['id_customer'] ?>" <?= $select ?>><?= $db->nameFormater($rowcs['name_customer']) ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <!-- endpelanggan -->

                        <!-- keterangan -->
                        <div class="col-sm-3">
                          <input type="text" name="keterangan" value="<?= $edit != "" ? $rowedit['keterangan_order'] : ''; ?>" placeholder="Keterangan" id="" class="form-control">
                        </div>
                        <!-- end keterangan -->

                        <!-- diskon -->
                        <div class="col-sm-3">
                          <div class="row">
                            <div class="col-sm-6">
                              <select name="satuan_potongan" class="form-select" id="satuan" onchange="changeSatuan(this.value)">
                                <?php  
                                $v = $edit != "" ? $rowedit['satuan_potongan'] : '' ;
                                $option = array("persen","rupiah");
                                $ico = array("%","Rp.");
                                foreach($option as $key => $ops){
                                  $select = $v == $ops ? 'selected="selected"' : ''
                                ?>
                                <option value="<?= $ops ?>" <?= $select ?> ><?= strtoupper($ops)." (".$ico[$key].")" ?></option>
                                <?php } ?>
                              </select>
                            </div>
                            <div class="col-sm-6">
                              <div class="input-group">
                              <span class="input-group-text" id="rupiah" style="display: none;">Rp</span>
                                <input type="number" step="0.01" name="diskon" value="<?= $edit != "" ? $rowedit['diskon_order'] : '' ?>" placeholder="Diskon" id="diskon" class="form-control" onkeyup="showFee3(this.value)">
                                <span class="input-group-text" id="persen">%</span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- end diskon -->

                        <!-- label suber -->
                        <label for="" class="col-sm-2 col-form-label">Sumber</label>

                        <!-- sumber -->
                        <div class="col-sm-10">
                          <select name="sumber" class="form-select" required>
                            <option value="" hidden>PILIH SUMBER</option>
                            <?php
                            $v = $edit != "" ? $rowedit['id_sumber'] : '' ;
                            $sumber = $db->selectTable("sumber_pemesanan","id_owner",$id);
                            while($rowsumber=mysqli_fetch_assoc($sumber)){
                              $select = $v == $rowsumber['id_sumber'] ? 'selected="selected"' : '';
                            ?>
                            <option value="<?= $rowsumber['id_sumber'] ?>" <?= $select ?>><?= $rowsumber['name_sumber'] ?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <!-- end sumber -->

                        <!-- tgl pemesanan label -->
                        <label for="" class="col-sm-2 col-form-label">Tanggal Pemesanan</label>
                        
                        <!-- tanggal order -->
                        <div class="col-sm-10">
                          <!-- <div class="input-group"> -->
                            <!-- <span class="input-group-text" id="basic-addon1"><i class="ri-calendar-todo-fill"></i></span> -->
                            <input name="date_order" type="date" class="form-control" value="<?= $edit != "" ? date('Y-m-d',strtotime($rowedit["tgl_order"])) : '' ?>" required>
                          <!-- </div> -->
                        </div>
                        <!-- end tanggal order -->
                        
                        <!-- label pengiriman -->
                        <label for="pengiriman" class="col-sm-2 col-form-label">Pengiriman</label>

                        <!-- status pengiriman -->
                        <div class="col-sm-10">
                          <select name="pengiriman" id="pengiriman_statuss" onchange="detailPengiriman(this.value)" id="pengiriman" class="form-select" required>
                          <?php
                            $v = $edit != "" ? $rowedit['status_pengiriman_order'] : "Tidak";  
                            $d = array("Ya","Tidak");
                            foreach($d as $x){
                              $select = $v == $x ? 'selected="selected"' : '';
                            ?>
                            <option value="<?= $x ?>" <?= $select ?>><?= $x ?></option>
                          <?php } ?>
                          </select>
                        </div>
                        <!-- end status pengiriman -->

                      </div>
                    </div>
                  </div>
                </div>
                <!-- enddetail pemesanan -->

                <!-- detail pengiriman -->
                <?php  
                if($edit != ""){
                ?>
                <div class="col-12" id="detailpengiriman" <?= $rowedit['status_pengiriman_order'] == "Tidak" ? 'style="display: none;"' : 'style="display: block;"' ?>>
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title">Detail Pengiriman</div>
                      
                      <!-- kurir -->
                      <div class="row g-3 mb-3">
                        <!-- label kurir -->
                        <label for="" class="col-sm-2 col-form-label">Kurir</label>

                        <!-- kurir -->
                        <div class="col-sm-10">
                          <select name="kurir" id="kurir" class="form-select" <?= $rowedit['status_pengiriman_order'] == "Ya" ? 'required' : '' ?>>
                              <option value="" hidden>PILIH KURIR</option>
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
                                $select = $k == $rowedit['kurir_pengiriman_order'] ? 'selected="selected"' : '';
                              ?>
                              <option value="<?= $k ?>" <?= $select ?>><?= nameKurir($k) ?></option>
                              <?php } ?>
                          </select>
                        </div>
                        <!-- end kurir -->
                      </div>
                      <!-- end kurir -->
                      
                      <!-- data customer -->
                      <div class="row g-3 mb-3" id="data_customer">
                          <label for="" class="col-sm-2 col-form-label">Tujuan Pengiriman</label>
                          <div class="col-sm-3">
                            <select name="prov" id="prov" class="form-select" onchange="viewKab(this.value)">
                              <option value="" hidden>PROVINSI</option>
                              <?php $idprov = ""; 
                              $provs = $db->dataIndonesia("prov",null);
                              foreach($provs as $prov){
                                $select = $prov['province'] == $rowedit['prov_send_order'] ? 'selected="selected"' : '';
                                $idprov .= $rowedit['prov_send_order'] == $prov['province'] ? $prov["province_id"] : "";
                                echo '<option value="'.$prov['province_id'].'" '.$select.'>'.$prov['province'].'</option>';
                              }
                              ?>
                            </select>
                          </div>
                          <div class="col-sm-3">
                            <select name="kabkota" id="kabkota" class="form-select" onchange="viewkec(this.value)" >
                              <option value="" hidden>KABUPATEN/KOTA</option>
                              <?php $idkab = "";
                              $kab_kota = $db->dataIndonesia("kab_kota",$idprov);
                              foreach ($kab_kota as $key => $kab){
                                $select = $kab['city_name'] == $rowedit['kab_send_order'] ? 'selected="selected"' : '';
                                $idkab .= $rowedit['kab_send_order'] == $kab["city_name"] ? $kab["city_id"] : "";
                                echo '<option value="'.$kab["city_id"].'" '.$select.'>'.$kab["city_name"].'</option>';
                              }
                              ?>
                            </select>
                          </div>
                          <div class="col-sm-3">
                            <select name="kec" id="kec" class="form-select" >
                              <option value="" hidden>KECAMATAN</option>
                              <?php  $idkec = "";
                              $kecamatan = $db->dataIndonesia("kec",$idkab);
                              foreach ($kecamatan as $key => $kec){
                                $select = $kec["subdistrict_name"] == $rowedit['kec_send_order'] ? 'selected="selected"' : '';
                                $idkec .= $kec["subdistrict_name"] == $rowedit['kec_send_order'] ? $kec['subdistrict_id'] : '';
                                echo '<option value="'.$kec["subdistrict_id"].'" '.$select.'>'.$kec["subdistrict_name"].'</option>';
                              }
                              ?>
                            </select>
                          </div>
                          <div class="col-sm-1">
                            <input type="number" name="kode_pos" id="kode_pos" class="form-control" placeholder="Kode Pos" value="<?= $rowedit['kode_pos_send_order'] ?>">
                          </div>
                          <label for="" class="col-sm-2 col-form-label">Alamat Lengkap</label>
                          <div class="col-sm">
                            <textarea name="alamat_lengkap" id="" rows="3" class="form-control"><?= $rowedit['alamat_lengkap_send_order'] ?></textarea>
                          </div>
                      </div>
                      <!-- end data customer -->

                      <!-- raja ongkir -->
                      <div class="row g-3 mb-3">
                        <label for="" class="col-sm-2 col-form-label">Berat</label>
                        <div class="col-sm-10">
                          <div class="input-group">
                            <input type="number" name="berat" step="0.01" id="berat" class="form-control" value="<?= $rowedit['berat_send_order'] ?>" <?= $rowedit['status_pengiriman_order'] == "Ya" ? 'required' : '' ?>>
                            <span class="input-group-text">gram</span>
                          </div>
                        </div>
                        <label for="" class="col-sm-2 col-form-label">Ongkos Kirim</label>
                        <div class="col-sm-10">
                          <div class="row">
                            <div class="col-sm-12">
                              <div class="input-group">
                                <select name="resultcost" id="resut_pengiriman" onchange="showFee4(this.value)" class="form-control" <?= $rowedit['status_pengiriman_order'] == "Ya" ? 'required' : '' ?>>
                                  <option value="">PILIH PAKET</option>
                                  <?php  
                                  require_once "action/rajaOngkir.php";
                                  $rajaongkir = new RajaOngkir();
    
                                  $data = $rajaongkir->checkOngkir($rowedit['kurir_pengiriman_order'], $asal, $idkec, $rowedit['berat_send_order']);
                                  foreach($data->costs as $d){
                                    $select = $d->service == $rowedit['nama_paket_send_order'] ? 'selected="selected"' : '' ;
                                    echo '<option '.$select.' value="'.$d->cost[0]->value.' - '.$d->service.' - '.$d->cost[0]->etd.'">Rp.'.number_format($d->cost[0]->value,2,",",".").' (Paket: '.$d->service.' Estimasi: '.$d->cost[0]->etd. ')</option>';
                                  }
                                  ?>
                                </select>
                                
                                <button class="btn btn-warning" type="button" id="button-addon2" onclick="showOngkir()">Cek</button>
                                
                              </div>
                            </div>
                            <div class="col-sm-12 mt-2">
                              <div class="form-check">
                                <?php  
                                $checked = $rowedit['ongkir_cod_order'] == "COD" ? "checked" : '';
                                ?>
                                <input class="form-check-input" name="cod" type="checkbox" value="on" id="flexCheckChecked" <?= $checked ?>>
                                <label class="form-check-label" for="flexCheckChecked">
                                  Cash on delivery
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- end raja ongkir -->

                    </div>
                  </div>
                </div>
                <?php 
                }else{ 
                ?>
                <div class="col-12" id="detailpengiriman" style="display: none;">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title">Detail Pengiriman</div>

                      <!-- kurir -->
                      <div class="row g-3 mb-3">
                        <!-- label kurir -->
                        <label for="" class="col-sm-2 col-form-label">Kurir</label>

                        <!-- kurir -->
                        <div class="col-sm-10">
                          <select name="kurir" id="kurir" class="form-select">
                            <option value="" hidden>PiLIH KURIR</option>
                            <option value="pos">POS Indonesia (POS)</option>
                            <option value="lion">Lion Parcel (LION)</option>
                            <option value="jne">Jalur Nugraha Ekakurir (JNE)</option>
                            <option value="jnt">J&T Express (J&T)</option>
                          </select>
                        </div>
                        <!-- end kurir -->
                      </div>
                      <!-- end kurir -->

                      <!-- data customer -->
                      <div class="row g-3 mb-3" id="data_customer">
                        <label for="" class="col-sm-2 col-form-label">Tujuan Pengiriman</label>

                        <div class="col-sm-3">
                          <select name="prov" id="prov" class="form-select" onchange="viewKab(this.value)">
                            <option value="" hidden>PROVINSI</option>
                            <?php  
                            $provs = $db->dataIndonesia("prov",null);
                            foreach($provs as $prov){
                              echo '<option value="'.$prov['province_id'].'">'.$prov['province'].'</option>';
                            }
                            ?>
                          </select>
                        </div>

                        <div class="col-sm-3">
                          <select name="kabkota" id="kabkota" class="form-select" onchange="viewkec(this.value)">
                            <option value="" hidden>KABUPATEN/KOTA</option>
                          </select>
                        </div>

                        <div class="col-sm-3">
                          <select name="kec" id="kec" class="form-select">
                            <option value="" hidden>KECAMATAN</option>
                          </select>
                        </div>

                        <div class="col-sm-1">
                          <input type="number" name="kode_pos" id="kode_pos" class="form-control" placeholder="Kode Pos">
                        </div>

                        <label for="" class="col-sm-2 col-form-label">Alamat Lengkap</label>
                        <div class="col-sm">
                          <textarea name="alamat_lengkap" id="" rows="3" class="form-control"></textarea>
                        </div>

                      </div>
                      <!-- end data customer -->

                      <!-- raja ongkir -->
                      <div class="row g-3 mb-3">

                        <label for="" class="col-sm-2 col-form-label">Berat</label>
                        <div class="col-sm-10">
                          <div class="input-group">
                            <input type="number" name="berat" step="0.01" id="berat" class="form-control">
                            <span class="input-group-text">gram</span>
                          </div>
                        </div>
                        
                        <label for="" class="col-sm-2 col-form-label">Ongkos Kirim</label>
                        <div class="col-sm-10">
                          <div class="row">
                            <div class="col-sm-12">
                              <div class="input-group">
                                <select name="resultcost" id="resut_pengiriman" onchange="showFee4(this.value)" class="form-control">
                                  <option value="">PILIH PAKET</option>
                                </select>
                                
                                <button class="btn btn-warning" type="button" id="button-addon2" onclick="showOngkir()">Cek</button>
                              </div>
                            </div>
                            <div class="col-sm-12 mt-2">
                              <div class="form-check">
                                <input class="form-check-input" name="cod" type="checkbox" value="on" id="flexCheckChecked">
                                <label class="form-check-label" for="flexCheckChecked">
                                  Cash on delivery
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- end raja ongkir -->

                    </div>
                  </div>
                </div>
                <?php 
                } 
                ?>
                <!-- end detail pengiriman -->

                <!-- biaya tambahan -->
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title">Biaya Tambahan <button type="button" id="addbiaya" class="btn btn-outline-primary btn-sm">+</button></div>
                      <div id="field">
                        <?php  
                        if($edit != ""){
                          $biayaa = $db->selectTable("biaya_tambahan_order","id_owner",$id,"code_order",$edit);
                          if(mysqli_num_rows($biayaa) > 0){
                            while($rowby=mysqli_fetch_assoc($biayaa)){
                        ?>
                            <div class="row mt-3 hapus">
                              <div class="col-md-5">
                                <input type="text" name="ket_biaya_tambhahan[]" value="<?= $rowby['keterangan_biaya'] ?>" class="form-control" placeholder="Keterangan">
                              </div>
                              <div class="col-md-6">
                                <input type="number" onkeyup="showfeeby(this.value)" name="biaya_tambahan[]" value="<?= $rowby['harga_ketbiaya'] ?>" class="form-control" placeholder="Harga">
                              </div>
                              <div class="col col-md-1">
                                <button id="remove_button" class="btn btn-danger"><i class="mdi mdi-delete-outline"></i></button>
                              </div>
                            </div>
                        <?php
                            } 
                          }
                        } 
                        ?>
                      </div>
                    </div>

                  </div>
                </div>
                <!-- end biaya tambahan -->

                <!-- detail pembayaran -->
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title">Detail Pembayaran</div>
                      <div class="row g-3">
                        <label for="" class="col-sm-2 col-form-label">Pembayaran</label>
                        <div class="col-sm-5">
                          <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="number" id="fee" placeholder="Jumlah Pembayaran" step="0.01" class="form-control" name="sisa_pembayaran" value="<?= $edit != "" ? $rowedit['sisa_pembayaran_order'] : '' ; ?>" readonly>
                          </div>  
                          <span style="font-size:0.8rem"><strong>*jika pembayaran tidak memenuhi harga produk, maka berstatus DP</strong></span>  
                        </div>
                        <div class="col-sm-5">
                          <div class="input-group">
                            <span class="input-group-text">Rp.</span>
                            <input type="number" name="total_pembayaran" id="debit" value="<?= $edit != "" ? $rowedit['pembayaran_customer_order'] : '' ?>" placeholder="Enter Pembayaran" onkeyup="sisaDari(this.value)" class="form-control" required >
                          </div>
                        </div>
                      </div>
                      <button type="submit" name="create_spk" class="btn btn-primary mt-3"><?= $edit != "" ? "Update" : "Buat SPK" ; ?></button>
                    </div>
                  </div>
                </div>
                <!-- end detail pembayaran -->

              </div>
              <!-- end row -->
            </form>
            <!-- end form -->
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

    <!-- biaya tambahan -->
    <script type="text/javascript">
      $(document).ready(function(){
          var maxField = 10; //Input fields increment limitation
          var addButton = $('#addbiaya'); //Add button selector
          var wrapper = $('#field'); //Input field wrapper
          // var fieldHTML = 
          var x = 1; //Initial field counter is 1
          var j = 0;
          //Once add button is clicked
          $(addButton).click(function(){
              //Check maximum number of input fields
              if(x < maxField){ 
                  j++;
                  x++; //Increment field counter
                  $(wrapper).append('<div class="row mt-3 hapus">\
                          <div class="col col-md-5">\
                            <input type="text" name="ket_biaya_tambhahan[]" class="form-control" placeholder="Keterangan" required>\
                          </div>\
                          <div class="col col-md-6">\
                            <input type="number" id="biaya'+j+'" onkeyup="showfeeby(this.value)" name="biaya_tambahan[]" class="form-control" placeholder="Harga" required>\
                          </div>\
                          <div class="col col-md-1">\
                            <button id="remove_button" class="btn btn-danger"><i class="mdi mdi-delete-outline"></i></button>\
                          </div>\
                        </div>'); //Add field html
              }
              // document.getElementById("jumby").value = j;
            });


          
          //Once remove button is clicked
          $(wrapper).on('click', '#remove_button', function(e){
              e.preventDefault();
              $(this).closest('.hapus').remove(); //Remove field html
              x--; //Decrement field counter
              var fee = document.getElementById("harga").value;
              if(document.getElementById("jenis").value ==  "Custom" && document.getElementById("kategori").value == "Other"){
                var fee = document.getElementById("harga2").value;
              }
              var diskon = document.getElementById("diskon").value;
              var debit = document.getElementById("debit").value;
              var tamby = document.getElementsByName("biaya_tambahan[]");
              var count = 0
              // jika harga produk tidak kosong
        if(fee != ""){
          var hasil = 0;
          // jika biaya tambahan tidak kosong
          if(tamby.length > 0){
            var count = 0;
            // loop array input biaya tambahan
            for(var index = 0; index < tamby.length; index++){
              // jika nilai input index kosong (null)
              if(tamby[index].value == ""){
                count += 0;
              }
              // jika nilai input index tdak kosong (nulll)
              else{
                count += parseFloat(tamby[index].value);
              }
            }
            // jika diskon tidak kosong
            if(diskon != ""){
              // potongan dengan persen
              $potongan = (parseFloat(fee) + parseFloat(count)) * (parseFloat(diskon)/100);
              // potongan dengan rupiah
              if(satuan == "rupiah"){
                $potongan = parseFloat(diskon);
              }
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = (parseFloat(fee) + parseFloat(count) - $potongan) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee) + parseFloat(count) - $potongan
              }
            }
            // jika diskon kosong
            else{
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = (parseFloat(fee) + parseFloat(count)) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee) + parseFloat(count)
              }
            }
          }
          // jika biaya tambahan kosong
          else{
            // jika diskon tidak kosong
            if(diskon != ""){
              // potongan dengan persen
              $potongan = parseFloat(fee) * (parseFloat(diskon)/100);
              // potongan dengan rupiah
              if(satuan == "rupiah"){
                $potongan = parseFloat(diskon);
              }
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = (parseFloat(fee) - $potongan) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee) - $potongan
              }
            }
            // jika diskon kosong
            else{
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = parseFloat(fee) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(fee);              
              }
            }
          }
          // sisa atau hasil dari yang dibayar
          document.getElementById("fee").value = hasil;
        }
        // jika harga produk kosong
        else{
          // jika biaya tambahan tidak kosong
          if(tamby.length > 0){
            var count = 0;
            // loop array input biaya tambahan
            for(var index = 0; index < tamby.length; index++){
              // jika nilai input index kosong (null)
              if(tamby[index].value == ""){
                count += 0;
              }
              // jika nilai input index tdak kosong (nulll)
              else{
                count += parseFloat(tamby[index].value);
              }
            }
            // jika diskon tidak kosong
            if(diskon != ""){
              // potongan dengan persen
              $potongan = parseFloat(count) * (parseFloat(diskon)/100);
              // potongan dengan rupiah
              if(satuan == "rupiah"){
                $potongan = parseFloat(diskon);
              }
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = parseFloat(count) - $potongan - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(count) - $potongan;
              }
            }
            // jika diskon kosong
            else{
              // jika debit customer tidak kosong
              if(debit != ""){
                hasil = parseFloat(count) - debit;
              }
              // jika debit customer kosong
              else{
                hasil = parseFloat(count)
              }
            }
          }
          // jika biaya tambahan kosong
          else{
            // jika debit customer tidak kosong
            if(debit != ""){
              hasil = 0 - debit;
            }
            // jika debit customer kosong
            else{
              hasil = 0;
            }
          }
          // sisa atau hasil dari yang dibayar
          document.getElementById("fee").value = hasil;
        }
          });
      });
    </script>

      <!-- select 2 -->
    <script>
      $(document).ready(function() {
          $('#pelanggan').select2({
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

    <!-- Sweet Alerts js -->
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <script>
      function bataltr(str){
        document.getElementById("view2"+str).style.display = "";
        document.getElementById("view"+str).style.display = "none";
        document.getElementById("editt"+str).style.display = "";
        document.getElementById("batall"+str).style.display = "none";
      }
    </script>
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
      }
    </script>
    <script>
      $(document).on('click', '#hpsbiaya', function(e){
        e.preventDefault();
        var link = $(this).attr('href');
        Swal.fire({
          title:"Hapus Biaya!",
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
<?php mysqli_close($db->conn); $_SESSION['alert'] = "" ?>