<?php  

require_once "DbClass.php";

$db = new ConfigClass();

$conn = $db->conn;

$id_order = $_GET['id'];

if($id_order == ""){
    header('Location: ../sedang-dicetak');
    exit();
}

$checkorder = $db->selectTable("data_pemesanan","id_order",$id_order,"status_order","Proses Cetak");
if(mysqli_num_rows($checkorder) == 0){
    header('Location: ../sedang-dicetak');
    exit();
}

$row = mysqli_fetch_assoc($checkorder);
$status_proses = array($row['laminating_order'],$row['status_pasang_order']);

$update = updateOrder($id_order,$status_proses);
if($update){
    $_SESSION['alert'] = "1";
    header('Location: ../sedang-dicetak');
    exit();
}else{
    $_SESSION['alert'] = "3";
    header('Location: ../sedang-dicetak');
    exit();
}

function updateOrder($id,$proses){
    $next = '';
    if($proses[1] == "Ya"){
        $next = "Menunggu Finishing";
    }elseif($proses[2] == "Ya"){
        $next = "Siap Dipasang";
    }else{
        $next = "Selesai";
    }
    global $conn;
    $query = "UPDATE data_pemesanan SET status_order='$next' WHERE id_order='$id'";
    $result = mysqli_query($conn, $query);
    return $result;
}

?>