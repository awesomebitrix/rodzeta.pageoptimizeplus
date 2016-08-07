<?php

if ($ftype == "image/jpeg" || $ftype == "image/pjpeg") {
	$main_img_obj = imagecreatefromjpeg($imageSrcPath);
}
else if ($ftype == "image/gif") {
	$tmp = imagecreatefromgif($imageSrcPath);
	$ar = getimagesize($imageSrcPath);
	$x = $ar[0];
	$y = $ar[1];
	$main_img_obj = imagecreatetruecolor($x,$y);
	$trans = imagecolorallocate($main_img_obj ,0,0,0);
	imagecolortransparent($main_img_obj,$trans);
	imagecopyresized ($main_img_obj, $tmp, 0, 0, 0, 0, $x, $y, $x, $y);
	imagedestroy($tmp);
	//imagetruecolortopalette($main_img_obj, true, 256);
	//imageinterlace($main_img_obj );
}
else if ($ftype == "image/x-png" || $ftype == "image/png") {
	$main_img_obj = imagecreatefrompng($imageSrcPath);
}

// select watermark for image size
$watermark_img_obj = null;
$ar = getimagesize($imageSrcPath);
foreach (glob($config->watermark->path . "/*.png") as $fname) {
	$tmp = basename($fname, ".png");
	if ($ar[0] <= (int)$tmp) { // check real image size
		$watermark_img_obj = imagecreatefrompng($fname);
		break;
	}
}
if ($watermark_img_obj == null) {
	$watermark_img_obj = imagecreatefrompng($fname);
}

// calc coords
$main_img_w = imagesx($main_img_obj);
$main_img_h = imagesy($main_img_obj);
$watermark_img_w = imagesx($watermark_img_obj);
$watermark_img_h = imagesy($watermark_img_obj);
$posX = 0;
$posY = 0;
switch ($halign) {
	case "left":
		$posX = 0;
		break;
	case "center":
		$posX = ($main_img_w / 2) - ($watermark_img_w / 2);
		break;
	case "right":
		$posX = $main_img_w - $watermark_img_w;
		break;
}
switch ($valign) {
	case "top":
		$posY = 0;
		break;
	case "center":
		$posY = ($main_img_h / 2) - ($watermark_img_h / 2);
		break;
	case "bottom":
		$posY = $main_img_h - $watermark_img_h;
		break;
}

// apply watermark
if (1 || $main_img_w > $watermark_img_w) {
	if ($ftype == "image/x-png" || $ftype == "image/png") {
		imagecopymerge(
			$main_img_obj, $watermark_img_obj,
			$posX, $posY,
			0, 0,
			$watermark_img_w, $watermark_img_h,
			5
		);
	}
	else {
		imagecopyresampled(
			$main_img_obj, $watermark_img_obj,
			$posX, $posY,
			0, 0,
			$watermark_img_w, $watermark_img_h,
			$watermark_img_w, $watermark_img_h
		);
	}
}
else {
	imagecopyresampled(
		$main_img_obj,
		$watermark_img_obj,
		$posX,
		$posY,
		0,
		0,
		$main_img_w,
		$main_img_h,
		$main_img_w,
		$main_img_h
	);
}

// save
if ($ftype == "image/jpeg" || $ftype == "image/pjpeg") {
	imagejpeg($main_img_obj, $imageDestPath, 100);
}
else if ($ftype == "image/gif") {
	imagegif($main_img_obj, $imageDestPath);
}
else if ($ftype == "image/x-png" || $ftype == "image/png") {
	imagepng($main_img_obj, $imageDestPath, 9);
}
imagedestroy($main_img_obj);
