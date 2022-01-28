<?php  
require_once "DbClass.php";

if($_SESSION['login_stiker_admin'] != true ){
    header('Location: auth-login');
    exit();
}

$db = new ConfigClass();
$conn = $db->conn;
$userselect = $db->selectTable("user_galeri","id_user",$_SESSION['login_stiker_id']);
$row = mysqli_fetch_assoc($userselect);
$usernamelogin = $row['username_user'];
$id = $row['id_owner'];
// jika yang login buka owner ambil data owner dari id owner
if($row['id_owner'] == "0"){
    $id = $row['id_user'];
}

if(isset($_POST['action'])){
    $id_order = $_POST['id_order'];
    if($_POST['action'] == "backtodesain"){
        $sql = "UPDATE data_pemesanan SET status_order='Proses Desain', admin_konfirm='Di Kembalikan' WHERE id_order='$id_order'";
        $result = mysqli_query($conn, $sql);
        if($result){
            $data = "Berhasil";
        }
    }elseif($_POST['action'] == "batalcetak"){
        $sql_batal_cetak = "UPDATE data_pemesanan SET status_order='Siap Cetak' WHERE id_order='$id_order'";
        $ext_batal_cetak = mysqli_query($conn,$sql_batal_cetak);
        if($ext_batal_cetak){
            $getorder = $db->selectTable("data_pemesanan","id_order",$id_order);
            $roworder = mysqli_fetch_assoc($getorder);
            $spkorder = $roworder['code_order'];
            $delete_cetakan = "DELETE FROM data_cetakan WHERE code_order='$spkorder' AND id_owner='$id' AND status_cetak='Proses'";
            $result_delete = mysqli_query($conn, $delete_cetakan);
            if($result_delete){
                $data = "Berhasil";
            }
        }
    }
    echo json_encode($data);
}
// echo json_encode("berhasil");

?>