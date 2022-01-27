<?php  
require_once "action/DbClass.php";
$db = new ConfigClass();
$conn = $db->conn;

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

if(isset($_POST['action'])){
    $owner = $_POST["id_owner"];
    if($_POST['action'] == "penjualan"){
        $year = date("Y"); $month = date("m");
        $count_product_custome_order = 0;
        $data = array();
        $get_product_custome = $db->selectTable("type_galeri","id_owner",$owner);
        while($row_product_custome = mysqli_fetch_assoc($get_product_custome)){
            $id_product_custome = $row_product_custome['id_type'];
    
            $sql_order_this_month = "SELECT * FROM data_pemesanan WHERE id_owner='$owner' AND year(tgl_order)='$year' AND month(tgl_order)='$month' AND produk_order='$id_product_custome' AND jenis_produk_order='Custom'";
            $get_order_this_month = mysqli_query($conn, $sql_order_this_month);
            if(mysqli_num_rows($get_order_this_month) > 0){
                $name_product_custome = ucfirst($row_product_custome['name_type']);
                $jumlah_product_custome = mysqli_num_rows($get_order_this_month);
                $color_product_custome = '#'.rand(100000,999999).'';
                $count_product_custome_order += mysqli_num_rows($get_order_this_month);
    
                $data[] = array(
                    'product' => $name_product_custome,
                    'total' => $jumlah_product_custome,
                    'color' => $color_product_custome
                );
            }
        }
        $sqlOrder = "SELECT * FROM data_pemesanan WHERE id_owner='$owner' AND year(tgl_order)='$year' AND month(tgl_order)='$month' AND jenis_produk_order='Custom'";
        $getOrder = mysqli_query($conn, $sqlOrder);
    
        $all_order_this_month = mysqli_num_rows($getOrder);
        $other_product_custome = $all_order_this_month - $count_product_custome_order;
        $color_product_custome = '#'.rand(100000,999999).'';
        $data[] = array(
            'product' => 'Other',
            'total' => $other_product_custome,
            'color' => $color_product_custome
        );
    
        echo json_encode($data);
    }elseif($_POST['action'] == "perbulan"){
        $year = date("Y"); $data = array();
        $months = array("01","02","03","04","05","06","07","08","09","10","11","12",);
        foreach($months as $month){
            $total_penjualan = 0;
            $sqlOrder = "SELECT * FROM data_pemesanan WHERE id_owner='1' AND year(tgl_order)='$year' AND month(tgl_order)='$month'";
            $getOrder = mysqli_query($conn, $sqlOrder);
            if(mysqli_num_rows($getOrder) > 0){
                while($rowOrder = mysqli_fetch_assoc($getOrder)){
                    $spk_product_order = $rowOrder['code_order'];
                    $harga_product_order = $rowOrder['harga_produk_order'];
                    $diskon_product_order = $rowOrder['diskon_order'];
                    $persen_or_rupiah = $rowOrder['satuan_potongan'];
                    $resultDiskon = resultDiskon("1",$spk_product_order,$harga_product_order,$diskon_product_order,$persen_or_rupiah);
        
                    $total_penjualan += $resultDiskon['hasil'];
                }
            }
            $data[] = array(
                'total' => $total_penjualan
            );
        }
        echo json_encode($data);
    }
}
?>