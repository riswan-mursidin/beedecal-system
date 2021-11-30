<?php  

$input = "assets/images/b.png";
$output = "assets/images/new.jpg";

// $img = imagecreatefrompng($input);
// imagepng($img,$output,0);

$new = "assets/images/new.jpg";

$img = imagecreatefrompng($input);
imagejpeg($img,$new,20);

?>