<?php

function image_procesing_resize($src, $dst, $width = 0, $height = 0, $crop = false) {
	if ($height <= 0 && $width <= 0) {
		return false;
	}

	$config = require __DIR__ . "/config.php";

	// use Imagick
	if (extension_loaded("imagick")) {
		$img = new Imagick();
		$img->readImage($src);
		$img->setImageCompressionQuality(100);

	  if (!empty($config->background)) {
			$img->setImageBackgroundColor($config->background);
		}

		if ($crop) {
			$img->cropThumbnailImage($width, $height);
		}
		else {
			$img->thumbnailImage($width, $height, true, true);
		}

		$img->writeImage($dst);
		$img->clear();
		return;
	}

	// use GD
	$image = null;
	list($origWidth, $origHeight, $imageType) = getimagesize($src);

	if ($crop) {
		$scaleW = $width / $origWidth;
		$scaleH = $height / $origHeight;
		$scale = max($scaleW, $scaleH);
		$newWidth = ceil($origWidth * $scale);
		$newHeight = ceil($origHeight * $scale);
		$dstX = ceil(($width - $newWidth) / 2);
		$dstY = ceil(($height - $newHeight) / 2);
	}
	else {
		$scaleW = $width / $origWidth;
		$scaleH = $height / $origHeight;
		$scale = min($scaleW, $scaleH);
		$newWidth = $width = ceil($origWidth * $scale);
		$newHeight = $height = ceil($origHeight * $scale);
	}

	// Loading image to memory according to type
	switch ($imageType) {
		case IMAGETYPE_GIF:
			$image = imagecreatefromgif($src);
			break;

		case IMAGETYPE_JPEG:
			$image = imagecreatefromjpeg($src);
			break;

		case IMAGETYPE_PNG:
			$image = imagecreatefrompng($src);
			break;

		default:
			return false;
	}

	// This is the resizing/resampling/transparency-preserving magic
	$image_resized = imagecreatetruecolor($width, $height);
	if ($imageType == IMAGETYPE_GIF || $imageType == IMAGETYPE_PNG) {
		$transparency = imagecolortransparent($image);

		if ($imageType == IMAGETYPE_GIF && $transparency >= 0) {
			list($r, $g, $b) = array_values(imagecolorsforindex($image, $transparency));
			$transparency = imagecolorallocate($image_resized, $r, $g, $b);
			imagefill($image_resized, 0, 0, $transparency);
			imagecolortransparent($image_resized, $transparency);
		}
		else if ($imageType == IMAGETYPE_PNG) {
			imagealphablending($image_resized, false);
			//$color = imagecolorallocate($image_resized, 0, 0, 0, 127);
			$color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
			imagefill($image_resized, 0, 0, $color);
			imagesavealpha($image_resized, true);
		}
	}

	imagecopyresampled(
			$image_resized, $image, $dstX, $dstY, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
	imagedestroy($image);

	//errorlog(implode(", ", [$dstX, $dstY, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight]));
	// Writing image
	switch ($imageType) {
		case IMAGETYPE_GIF:
			imagegif($image_resized, $dst);
			break;

		case IMAGETYPE_JPEG:
			imagejpeg($image_resized, $dst, 100);
			break;

		case IMAGETYPE_PNG:
			imagepng($image_resized, $dst, 9);
			break;

		default:
			return false;
	}
	imagedestroy($image_resized);
	//header("Content-type: " . $imageType);
}

// init
list($width, $height) = $imageCmd["resize"];
image_procesing_resize(
	$imageSrcPath, $imageDestPath,
	$width, $height,
	isset($imageCmd["crop"])
);

if (defined("APP_DEBUG")) {
	error_log("$imageSrcPath to $imageDestPath, crop: " . (int)$imageCmd["crop"]);
}
