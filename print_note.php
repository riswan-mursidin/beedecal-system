<?php  
$spk = $_GET['spk'];
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

// DATA STORE
$store = $db->selectTable("store_galeri","id_owner",$id);
$rowtoko = mysqli_fetch_assoc($store);
$addresstoko = addressShow($rowtoko['prov_id'],$rowtoko['kab_id'],$rowtoko['kec_id']);

// DATA ORDER
$order = $db->selectTable("data_pemesanan","code_order",$spk,"id_owner",$id);
$roworder = mysqli_fetch_assoc($order);

// DATA CUSTOMER
$customer = $db->selectTable("customer_stiker","id_customer",$roworder['id_customer']);
$rowcustomer = mysqli_fetch_assoc($customer);
$addresscus = addressShow($rowcustomer['prov_customer'],$rowcustomer['kota_kab_customer'],$rowcustomer['kec_customer']);

function showProduk($id_produk){
    global $db;
    $querydb = $db->selectTable("type_galeri","id_type",$id_produk);
    $rowdb=mysqli_fetch_assoc($querydb);
    $result = $rowdb['name_type'];
    return $result;
}

function addressShow($provid,$kabid,$kecid){
    global $db;

    $provname = "";
    $provs = $db->dataIndonesia("prov",null);
    foreach($provs as $prov){
        if($prov['province_id'] == $provid){
            $provname = $prov['province'];
        }else{continue;}
    }

    $kabname = "";
    $kab_kota = $db->dataIndonesia("kab_kota",$provid);
    foreach ($kab_kota as $key => $kab){
        if($kab['city_id'] == $kabid){
            $kabname = $kab["city_name"];
        }else{continue;}
    }

    $kecname = "";
    $kecamatan = $db->dataIndonesia("kec",$kabid);
    foreach ($kecamatan as $key => $kec){
        if($kec["subdistrict_id"] == $kecid){
            $kecname = $kec["subdistrict_name"];
        }else{continue;}
    }

    return array($provname,$kabname,$kecname);
}



?>

<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="shortcut icon" href="assets/images/favicon.ico" />
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@600&display=swap" rel="stylesheet">
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
        <title>STIKER | NOTA</title>
    </head>
    <body>
        <div class="container">
            <img class="mt-3 mb-2" src="assets/images/logo-dark.png" style="width: 10rem;" alt="">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 30%">
                        <b>Addres:</b> <?= $rowtoko['address_store'] ?><br>
                        <?= $addresstoko[0]." - ".$addresstoko[1] ?>
                    </td>
                    <td style="width: 30%;"></td>
                    <td rowspan="2" valign="top">
                        <b>Order Date: </b><?= $db->dateFormatter($roworder['tgl_order']) ?>
                        <p><b>Order ID:</b> <?= $spk ?></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        Kec: <?= $addresstoko[2] ?>
                    </td>
                    <td></td>
                </tr>

                <tr>
                    <td>
                        Kode Pos: <?= $rowtoko['kode_pos'] ?>
                    </td>
                </tr>
                <tr style="height: 20px;">
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <b>No. Telpn/WA: </b> 62<?= $rowtoko['telpn_store'] ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Email: </b><?= $rowtoko['email_store'] ?>
                    </td>
                </tr>
                <tr style="height: 20px;">
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <b>CUSTOMER</b>
                    </td>
                    <td style=""></td>
                    <td>
                        <b>PRODUCTION</b>
                    </td>
                </tr>
                <tr id="name">
                    <td>
                        <?= $db->nameFormater($rowcustomer['name_customer']) ?>
                    </td>
                    <td style=""></td>
                    <td rowspan="5" valign="top">
                        Desain/Costume: <?= $roworder['status_desain_order'] ?><br>
                        Pemasangan: <?= $roworder['status_pasang_order'] ?><br>
                        Laminating: <?= $roworder['laminating_order'] ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= $rowcustomer['address_customer'] ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= $addresscus[0] ?> - <?= $addresscus[1] ?> 
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= $addresscus[2] ?> (<?= $rowcustomer['kode_pos_customer'] ?>) 
                    </td>
                </tr>
                <tr>
                    <td>
                        62<?= $rowcustomer['telpn_customer'] ?>
                    </td>
                </tr>
            </table>

            <h5 class="mt-4">PAYMENT</h5>
            <table class="table" style="width: 100%;">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Description</th>
                        <th scope="col">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td scope="row">1</td>
                        <td>
                            <?= $roworder['jenis_produk_order'] == 'Custom' ? $db->nameFormater(showProduk($roworder['produk_order'])) : '' ?> - 
                            <?= $roworder['model_stiker_order'] ?><br>
                        </td>
                        <td>
                            <?php  
                            $harga = intval($roworder['harga_produk_order']);
                            $diskon =  intval($roworder['diskon_order']) / 100;
                            $potongan = $harga * $diskon;
                            $hasil = $harga - $potongan;
                            ?>
                            <?= $diskon != "" ? "Rp.".number_format($hasil) : "Rp.".number_format($harga); ?>
                        </td>
                    </tr>
                    <tr>
                        <td scope="row">2</td>
                        <td>Pemasangan</td>
                        <td><?= $roworder['status_pasang_order'] == "Ya" ? "Rp.".number_format($roworder['harga_pasang_order']) : 'Tidak Ada' ?></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" scope="row">
                            Total:
                        </th>
                        <th>
                            <?php  
                                $total = $hasil + $roworder['harga_pasang_order'];
                                echo "Rp.".number_format($total)
                            ?>
                        </th>
                    </tr>
                </tfoot>
            </table>

        </div>

        <!-- Optional JavaScript; choose one of the two! -->

        <!-- Option 1: Bootstrap Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
        <script>
            document.title = "<?= strtoupper($rowtoko['name_store']) ?> - <?= $spk ?>";
            window.print();
        </script>
        <!-- Option 2: Separate Popper and Bootstrap JS -->
        <!--
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
        -->
    </body>
</html>