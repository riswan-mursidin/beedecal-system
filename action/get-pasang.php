<?php  

require_once "DbClass.php";

$db = new ConfigClass();

$conn = $db->conn;



$id_order = $_GET['id'];
$param = $_GET['param'];
$pemasang = $_GET['pemasang'];


if($id_order == "" || $param == "" || $pemasang == ""){
    header('Location: ../siap-dipasang');
    exit();
}

$checkpemasang = $db->selectTable("user_galeri","id_user",$id_user,"level_user","Pemasang");

$checkorder = "";
if($param == "get"){
    $checkorder = $db->selectTable("data_pemesanan","id_order",$id_order,"status_order","Siap Dipasang");
}elseif($param == "selesai" || $param == "batal"){
    $checkorder = $db->selectTable("data_pemesanan","id_order",$id_order,"status_order","Proses Pemasangan");
}

if(mysqli_num_rows($checkorder) == 0 || mysqli_num_rows($checkpemasang) == 0){
    header('Location: ../siap-dipasang');
    exit();
}

$row = mysqli_fetch_assoc($checkorder);

$next = "Selesai Dipasang";

$finishing = pemasanganAction($id_order,$param,$pemasang);
if($param == "get"){
    if($finishing){
        $_SESSION['alert'] = "1";
        header('Location: ../siap-dipasang');
        exit();
    }else{
        $_SESSION['alert'] = "3";
        header('Location: ../siap-dipasang');
        exit();
    }
}elseif($param == "batal" || $param == "selesai"){
    if($finishing){
        $_SESSION['alert'] = "1";
        header('Location: ../proses-pasang');
        exit();
    }else{
        $_SESSION['alert'] = "3";
        header('Location: ../proses-pasang');
        exit();
    }
}

function pemasanganAction($id,$param=null,$pemasang){
    global $conn; global $next; $date = date("Y-m-d");
    if($param == "get"){
        $query = "UPDATE data_pemesanan SET status_order='Proses Pemasangan', pemasang_order='$pemasang', tgl_pasang_order='$date' WHERE id_order='$id'";
        $result = mysqli_query($conn, $query);
        return $result;
    }elseif($param == "batal"){
        $query = "UPDATE data_pemesanan SET status_order='Siap Dipasang', pemasang_order=''  WHERE id_order='$id'";
        $result = mysqli_query($conn, $query);
        return $result;
    }elseif($param == "selesai"){
        $query = "UPDATE data_pemesanan SET status_order='$next' WHERE id_order='$id'";
        $result = mysqli_query($conn, $query);
        return $result;
    }
    // else{
    //     $query = "UPDATE data_pemesanan SET status_order='Proses Desain', id_designer='$user' WHERE id_order='$id'";
    //     $result = mysqli_query($conn, $query);
    //     return $result;
    // }
}

?>