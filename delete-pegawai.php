<?php  
require_once "action/DbClass.php";

$db = new ConfigClass();

if($_SESSION['login_stiker_admin'] != true ){
    header('Location: auth-login');
    exit();
}

$edit = $_GET['edit'];
$check = $db->selectTable("user_galeri","username_user",$edit);
if(mysqli_num_rows($check) == 0 || $edit == ""){
    header('Location: konfigurasi-pegawaitoko');
    exit();
}else{
    $delete = $db->deleteTable("user_galeri",$edit,"username_user");
    if($delete){
        $rowfoto = mysqli_fetch_assoc($check);
        $foto = $rowfoto['foto_user'];
        unlink($foto);
        $_SESSION['alert'] = "1";
        header('Location: konfigurasi-pegawaitoko');
        exit();
    }else{
        $_SESSION['alert'] = "2";
        header('Location: konfigurasi-pegawaitoko');
        exit();
    }
}


?>