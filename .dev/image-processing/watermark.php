<?php

// init
$halign = "center";
$valign = "center";
$size = getimagesize($imageSrcPath);
$ftype = $size["mime"];

// small size images - copy without watermark
if ($size[0] < $config->watermark->min_width
			|| $size[1] < $config->watermark->min_height) {
	copy($imageSrcPath, $imageDestPath);
}
// use Imagick
else if (extension_loaded("imagick")) {
	require "watermark_imagick.php";
}
// use GD
else {
	require "watermark_gd.php";
}

if (defined("APP_DEBUG")) {
	error_log("$imageSrcPath to $imageDestPath");
}