<?php  

require_once "action/DbClass.php";

$db = new ConfigClass();

$conn = $db->conn;

$id_order = $_GET['id_order'];
$id_user = $_GET['id_user'];

if($id_order == "" || $id_user == ""){
    header('Location: menunggu_designer');
    exit();
}

// $

?>