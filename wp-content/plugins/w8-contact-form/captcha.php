<?php
$strings = '123456789';
$i = 0;
$characters = 6;
$code = '';
while ($i < $characters)
{ 
    $code .= substr($strings, mt_rand(0, strlen($strings)-1), 1);
    $i++;
} 

setcookie("cfs-cap", md5($code), time()+60, "/", $_SERVER['HTTP_HOST']);
//generate image
$im = imagecreatetruecolor(100, 35);
imagealphablending($im, false);
$foreground = imagecolorallocate($im, 0, 0, 0);
$shadow = imagecolorallocate($im, 173, 172, 168);
$background = imagecolorallocatealpha($im, 0, 0, 0, 127);
imagefill($im, 0, 0, $background);
imagesavealpha($im, true);

imagefilledrectangle($im, 25, 25, 25, 25, $foreground);
// use your own font!
$font = 'Comfortaa-Regular.ttf';
imagecolortransparent($im, $background);

//draw text:
imagettftext($im, 20, 0, 9, 28, $shadow, $font, $code);
imagettftext($im, 16, 0, 2, 32, $foreground, $font, $code);     

// prevent client side  caching
header("Expires: Wed, 1 Jan 1997 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

//send image to browser
header ("Content-type: image/png");
imagepng($im);
imagedestroy($im);
?>