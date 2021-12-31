<?php 
require_once "DbClass.php";

if($_SESSION['login_stiker_admin'] != true ){
    header('Location: auth-login');
    exit();
}

$db = new ConfigClass();

$id_biaya = $_GET['page']; $spk = $_GET['spk'];

$query = "DELETE FROM biaya_tambahan_order WHERE id='$id_biaya'";
$result = mysqli_query($db->conn, $query);
if($result){
    header('Location: ../tambah-orderan.php?order='.$spk);
    exit();
}



mysqli_close($db->conn)
?>