<?php  

require_once "DbClass.php";

$db = new ConfigClass();
$conn = $db->conn;

$id_order = $_POST['id_order'];
$param = $_POST['param']; 
$nama_penerima = $_POST['nama_penerima'];


$checkorder = $db->selectTable("data_pemesanan","id_order",$id_order);
if(mysqli_num_rows($checkorder) == 0){
    header('Location: ../pengiriman');
    exit();
}

$done = getDone($id_order,$nama_penerima);
if($done){
    $_SESSION['alert'] == "1";
    if($param == "Ya"){
        header('Location: ../ambil-ditoko');
        exit();
    }else{
        header('Location: ../pengiriman');
        exit();
    }
}else{
    if($param == "Ya"){
        header('Location: ../ambil-ditoko');
        exit();
    }else{
        header('Location: ../pengiriman');
        exit();
    }
}

function getDone($id,$penerima){
    global $conn;
    $timezone = new DateTimeZone('Asia/Makassar');
    $date = new DateTime();
    $date->setTimeZone($timezone);
    $datee = $date->format("Y-m-d H:i:s");
    $query = "UPDATE data_pemesanan SET status_order='Selesai', nama_penerima='$penerima', tgl_selesai='$datee' WHERE id_order='$id'";
    $result = mysqli_query($conn, $query);
    return $result;
}

mysqli_close($conn);
?>