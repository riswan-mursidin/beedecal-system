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
    if($_POST['action'] == "batalfinishing"){
        $sql = "UPDATE data_pemesanan SET status_order='Proses Cetak' WHERE id_order='$id_order'";
        $result = mysqli_query($conn, $sql);
        if($result){
            $getorder = $db->selectTable("data_pemesanan","id_order",$id_order);
            $roworder = mysqli_fetch_assoc($getorder);
            $spkorder = $roworder['code_order'];
            $update_finishing = "UPDATE data_cetakan SET status_cetak='Proses' WHERE code_order='$spkorder' AND id_owner='$id' AND status_cetak='Berhasil'";
            $result_update = mysqli_query($conn, $update_finishing);
            if($result_update){
                $data = "Berhasil";
            }
        }
    }
    echo json_encode($data);
}
?>