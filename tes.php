<?php  

$input = "assets/images/b.png";
$new = "assets/images/new.jpg";

$img = imagecreatefrompng($input);
imagejpeg($img,$new,20);

?>