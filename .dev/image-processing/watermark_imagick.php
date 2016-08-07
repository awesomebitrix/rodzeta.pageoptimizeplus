<?php

$image = new Imagick();
$image->readImage($imageSrcPath);

$image->setImageCompressionQuality(100);

if (!empty($config->background)) {
	$image->setImageBackgroundColor($config->background);
}

$iWidth = $image->getImageWidth();
$iHeight = $image->getImageHeight();
// select watermark file
if (!empty($config->watermark->scale)) {
	// use next biggest watermark for scale to image size
	foreach (glob($config->watermark->path . "/*.png") as $fname) {
		$tmp = basename($fname, ".png");
		if ($iWidth <= (int)$tmp) { // check real image size
			break;
		}
	}
} else {
	$prevName = null;
	$tmp = null;
	foreach (glob($config->watermark->path . "/*.png") as $fname) {
		$prevName = $tmp;
		$tmp = basename($fname, ".png");
		if ($iWidth <= (int)$tmp) { // check real image size
			break;
		}
	}
	if ($iWidth < (int)$tmp && !empty($prevName)) {
		$fname = $config->watermark->path . "/$prevName.png";
	}
}
if (defined("APP_DEBUG")) {
	error_log("use $fname");
}

$watermark = new Imagick();
$watermark->readImage($fname);
$wWidth = $watermark->getImageWidth();
$wHeight = $watermark->getImageHeight();
// scale watermark
if (!empty($config->watermark->scale)) {
	if ($iHeight < $wHeight || $iWidth < $wWidth) {
		$watermark->scaleImage($iWidth, $iHeight, true);
		$wWidth = $watermark->getImageWidth();
		$wHeight = $watermark->getImageHeight();
	}
}

// calculate the position
$x = ($iWidth - $wWidth) / 2;
$y = ($iHeight - $wHeight) / 2;

// overlay the watermark on the original image
$image->compositeImage($watermark, imagick::COMPOSITE_OVER, $x, $y);
$image->writeImage($imageDestPath);
$image->clear();
