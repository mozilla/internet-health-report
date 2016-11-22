<?php

/* captcha.php file */
error_reporting(0);
if (!session_start()) {
    exit('Cannot start session');
}
if (!function_exists('imagecreatefrompng')) {
    exit('PHP GD2 library is disabled');
}

header("Expires: Tue, 01 Jan 2014 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$key = isset($_GET['key']) ? $_GET['key'] : uniqid('c');
$chars = '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
$randomString = '';
for ($i = 0; $i < 5; $i++) {
    $randomString .= $chars[rand(0, strlen($chars) - 1)];
}

if (($im = @imagecreatefrompng("captcha_bg_easy.png")) === false) {
    exit('Cannot create image file');
}
$_SESSION['wpdiscuzc'][$key] = md5(strtolower($randomString));
$size = 16;
$angle = 0;
$x = 5;
$y = 20;
$font = './consolai.ttf';
for ($i = 0; $i < strlen($randomString); $i++) {
    $color = imagecolorallocate($im, rand(0, 255), 0, rand(0, 255));
    $letter = substr($randomString, $i, 1);
    imagettftext($im, $size, $angle, $x, $y, $color, $font, $letter);
    $x += 13;
}

for ($i = 0; $i < 5; $i++) {
    $color = imagecolorallocate($im, rand(0, 255), rand(0, 200), rand(0, 255));
    imageline($im, rand(0, 20), rand(1, 50), rand(150, 180), rand(1, 50), $color);
}
header('Content-type: image/png');
if (!imagepng($im, null, 5)) {
    exit('PNG image creation disabled');
}
imagedestroy($im);
