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
if($param == "batal"){
    $checkorder = $db->selectTable("data_pemesanan","id_order",$id_order,"status_order","Proses Desain");
}else{
    $checkorder = $db->selectTable("data_pemesanan","id_order",$id_order,"status_order","Menunggu Designer");
}

if(mysqli_num_rows($checkorder) == 0 || mysqli_num_rows($checkdesigner) == 0){
    header('Location: ../menunggu_designer');
    exit();
}

if($param == "batal"){
    $bataldesain = getDesain($id_order,$id_user,$param);
    if($bataldesain){
        $_SESSION['alert'] = "1";
        header('Location: ../proses-desain');
        exit();
    }
}else{
    $getdesain = getDesain($id_order,$id_user);
    if($getdesain){
        $_SESSION['alert'] = "1";
        header('Location: ../menunggu_designer');
        exit();
    }
}


function getDesain($id, $user, $param=null){
    global $db;
    if($param == null){
        $query = "UPDATE data_pemesanan SET status_order='Proses Desain', id_designer='$user' WHERE id_order='$id'";
        $result = mysqli_query($db->conn, $query);
        return $result;
    }else{
        $query = "UPDATE data_pemesanan SET status_order='Menunggu Designer', id_designer='' WHERE id_order='$id'";
        $result = mysqli_query($db->conn, $query);
        return $result;
    }
}

mysqli_close($db->conn)

?>