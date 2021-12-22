<?php  

require_once "DbClass.php";

$db = new ConfigClass();

$conn = $db->conn;

$id_order = $_GET['id'];
$param = $_GET['param'];

if($id_order == "" && $param == ""){
    header('Location: ../proses-desain');
    exit();
}

$check = $db->selectTable("data_pemesanan","id_order",$id_order,"status_order","Selesai Didesain");

if(mysqli_num_rows($check) == 0){
    header('Location: ../proses-desain');
    exit();
}
$row = mysqli_fetch_assoc($check);
$status_proses = array($row['status_cetak_order'],$row['laminating_order'],$row['status_pasang_order ']);

$update = updateOrder($id_order,$param,$status_proses);
if($update){
    $_SESSION['alert'] = "1";
    header('Location: ../proses-desain');
    exit();
}else{
    $_SESSION['alert'] = "3";
    header('Location: ../proses-desain');
    exit();
}

function updateOrder($id,$param,$proses){
    $next = '';
    if($proses[0] == "Ya"){
        $next = "Siap Cetak";
    }elseif($proses[1] == "Ya"){
        $next = "Menunggu Finishing";
    }elseif($proses[2] == "Ya"){
        $next = "Siap Dipasang";
    }else{
        $next = "Selesai";
    }
    global $conn;
    if($param == "terima"){
        $query = "UPDATE data_pemesanan SET status_order='$next', admin_konfirm='Disetujui' WHERE id_order='$id'";
        $result = mysqli_query($conn, $query);
        return $result;
    }else{
        $query = "UPDATE data_pemesanan SET status_order='Proses Desain', admin_konfirm='Tidak Disetujui' WHERE id_order='$id'";
        $result = mysqli_query($conn, $query);
        return $result;
    }
}


mysqli_close($conn);

?>