<?php  

require_once "DbClass.php";

$db = new ConfigClass();

$conn = $db->conn;

$id_order = $_GET['id_order'];
$id_user = $_GET['user'];
$param = $_GET['param'];

if($id_order == "" || $id_user == ""){
    header('Location: ../menunggu_designer');
    exit();
}

// designer
$checkdesigner = $db->selectTable("user_galeri","id_user",$id_user,"level_user","Desainer");

// orderan desain
$checkorder = "";
if($param == "batal selesai"){
    $checkorder = $db->selectTable("data_pemesanan","id_order",$id_order,"status_order","Selesai Didesain");
}elseif($param == "batal" || $param == "selesai"){
    $checkorder = $db->selectTable("data_pemesanan","id_order",$id_order,"status_order","Proses Desain");
}else{
    $checkorder = $db->selectTable("data_pemesanan","id_order",$id_order,"status_order","Menunggu Designer");
}

if(mysqli_num_rows($checkorder) == 0 || mysqli_num_rows($checkdesigner) == 0){
    header('Location: ../menunggu_designer');
    exit();
}

if($param == "batal" || $param == "selesai" || $param == "batal selesai"){
    $bataldesain = getDesain($id_order,$id_user,$param);
    if($bataldesain){
        $_SESSION['alert'] = "1";
        header('Location: ../proses-desain');
        exit();
    }else{
        $_SESSION['alert'] = "3";
        header('Location: ../proses-desain');
        exit();
    }
}else{
    $getdesain = getDesain($id_order,$id_user);
    if($getdesain){
        $_SESSION['alert'] = "1";
        header('Location: ../menunggu_designer');
        exit();
    }else{
        $_SESSION['alert'] = "3";
        header('Location: ../menunggu_designer');
        exit();
    }
}


function getDesain($id, $user, $param=null){
    global $conn;
    if($param == "batal"){
        $query = "UPDATE data_pemesanan SET status_order='Menunggu Designer', id_designer='', admin_konfirm='' WHERE id_order='$id'";
        $result = mysqli_query($conn, $query);
        return $result;
    }elseif($param == "batal selesai"){
        $query = "UPDATE data_pemesanan SET status_order='Proses Desain', admin_konfirm='' WHERE id_order='$id'";
        $result = mysqli_query($conn, $query);
        return $result;
    }elseif($param == "selesai"){
        $query = "UPDATE data_pemesanan SET status_order='Selesai Didesain', admin_konfirm='Belum Disetujui' WHERE id_order='$id'";
        $result = mysqli_query($conn, $query);
        return $result;
    }else{
        $query = "UPDATE data_pemesanan SET status_order='Proses Desain', id_designer='$user', produksi_status='Ya' WHERE id_order='$id'";
        $result = mysqli_query($conn, $query);
        return $result;
    }
}

mysqli_close($conn)

?>