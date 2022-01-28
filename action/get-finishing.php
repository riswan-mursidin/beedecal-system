<?php  

require_once "DbClass.php";

$db = new ConfigClass();

$conn = $db->conn;

$id_order = $_GET['id'];
$param = $_GET['param'];

if($id_order == "" || $param == ""){
    header('Location: ../menunggu-finishing');
    exit();
}

$checkorder = "";
if($param == "get"){
    $checkorder = $db->selectTable("data_pemesanan","id_order",$id_order,"status_order","Menunggu Finishing");
}elseif($param == "selesai" || $param == "batal"){
    $checkorder = $db->selectTable("data_pemesanan","id_order",$id_order,"status_order","Proses Finishing");
}

if(mysqli_num_rows($checkorder) == 0){
    header('Location: ../menunggu-finishing');
    exit();
}

$row = mysqli_fetch_assoc($checkorder);
$pasang = $row['status_pasang_order'];

$next = $pasang == "Ya" ? 'Siap Dipasang' : 'Selesai Finishing';

$finishing = finishingAction($id_order,$param);
if($param == "get"){
    if($finishing){
        $_SESSION['alert'] = "1";
        header('Location: ../menunggu-finishing');
        exit();
    }else{
        $_SESSION['alert'] = "3";
        header('Location: ../menunggu-finishing');
        exit();
    }
}elseif($param == "batal" || $param == "selesai"){
    if($finishing){
        $_SESSION['alert'] = "1";
        header('Location: ../proses-finishing');
        exit();
    }else{
        $_SESSION['alert'] = "3";
        header('Location: ../proses-finishing');
        exit();
    }
}

function finishingAction($id,$param=null){
    global $conn; global $next;
    if($param == "get"){
        $query = "UPDATE data_pemesanan SET status_order='Proses Finishing', produksi_status='Ya' WHERE id_order='$id'";
        $result = mysqli_query($conn, $query);
        return $result;
    }elseif($param == "batal"){
        $query = "UPDATE data_pemesanan SET status_order='Menunggu Finishing', id_produksI='NULL' WHERE id_order='$id'";
        $result = mysqli_query($conn, $query);
        return $result;
    }elseif($param == "selesai"){
        if($next == "Siap Dipasang"){
            $query = "UPDATE data_pemesanan SET status_order='$next', admin_konfirm='Belum Disetujui' WHERE id_order='$id'";
            $result = mysqli_query($conn, $query);
            return $result;
        }else{
            $query = "UPDATE data_pemesanan SET status_order='$next' WHERE id_order='$id'";
            $result = mysqli_query($conn, $query);
            return $result;
        }
    }
    // else{
    //     $query = "UPDATE data_pemesanan SET status_order='Proses Desain', id_designer='$user' WHERE id_order='$id'";
    //     $result = mysqli_query($conn, $query);
    //     return $result;
    // }
}

?>