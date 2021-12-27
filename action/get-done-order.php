<?php  

require_once "DbClass.php";

$db = new ConfigClass();
$conn = $db->conn;

$id_order = $_GET['id'];
$param = $_GET['param']; 

if($id_order == ""){
    header('Location: ../pengiriman');
    exit();
}

$checkorder = $db->selectTable("data_pemesanan","id_order",$id_order);
if(mysqli_num_rows($checkorder) == 0){
    header('Location: ../pengiriman');
    exit();
}

$done = getDone($id_order);
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

function getDone($id){
    global $conn;

    $query = "UPDATE data_pemesanan SET status_order='Selesai' WHERE id_order='$id'";
    $result = mysqli_query($conn, $query);
    return $result;
}

mysqli_close($conn);
?>