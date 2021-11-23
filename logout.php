<?php  
session_start();
$_SESSION['login_stiker_admin'] = false;
$_SESSION['login_stiker_id'] = "";
header('Location: auth-login');
exit();
?>