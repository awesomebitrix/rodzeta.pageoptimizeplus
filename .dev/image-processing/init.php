<?php

ini_set("display_errors", 0);
if (defined("_APP_DEBUG")) {
	error_reporting(E_ALL);
	ini_set("log_errors", 1);
	ini_set("error_log", __DIR__ . "/watermark.log");
}

$config = require __DIR__ . "/config.php";

// validate extension
$u = parse_url($_SERVER["REQUEST_URI"]);
$p = pathinfo($u["path"]);
if (!isset($config->extensions[$p["extension"]])) {
	header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", false, 404);
	echo "Error file extension: " . $_SERVER["REQUEST_URI"];
	die;
}

$imageDir = dirname($_SERVER["SCRIPT_NAME"]);
// need if using redirect from other dir
$tmpPath = substr($u["path"], 0, strlen($imageDir)) == $imageDir?
	substr($u["path"], strlen($imageDir)) : $u["path"];
$imagePath = str_replace("../", "", $tmpPath);

$imagePath = urldecode($imagePath);
$imageSrcPath = $_SERVER["DOCUMENT_ROOT"] . "/" . $imagePath;
if (defined("_APP_DEBUG")) {
	error_log("$tmpPath, $imageDir, $imagePath");
}

// validate filename
if (substr($imagePath, 0, 5) == "/img-"
			|| !file_exists($imageSrcPath)
			|| preg_match("{[^a-zA-Z0-9\_\-\/\.\s]+}si", $imagePath)) {
	header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found", false, 404);
	echo "Error filename: " . $imageSrcPath;
	die;
}

// parse for processing commands from images dirname
if (empty($imageCmd) || !is_array($imageCmd)) {
	$tmp = explode("-", substr($imageDir, 5)); // dirname start with "img-"
	$imageCmd = [];
	$imageSize = explode("x", $tmp[0]);
	// if first - is image size
	if (count($imageSize) == 2) {
		$imageCmd["resize"] = $imageSize;
		array_shift($tmp);
	}
	foreach ($tmp as $cmd) {
		switch ($cmd) {
			case "c":
				$imageCmd["crop"] = 1; // works only with resize
				break;
			case "w":
				$imageCmd["watermark"] = 1;
				break;
		}
	}
}
if (defined("_APP_DEBUG")) {
	error_log(print_r($imageCmd, true));
}

// first dest path
$imageDestPath = $_SERVER["DOCUMENT_ROOT"] . $imageDir . $imagePath;

// need if using redirect from other dir
if (file_exists($imageDestPath)) {
	header("Location: " . $imageDir . $imagePath);
	die;
}

mkdir(dirname($imageDestPath), 0777, true);

if (isset($imageCmd["resize"])) {
	$imageDestPath .= "_resize";
	require __DIR__ . "/resize.php";
	$imageSrcPath = $imageDestPath;
}

if (!empty($config->watermark->ignore)) {
	if (is_array($config->watermark->ignore)) {
		foreach ($config->watermark->ignore as $v) {
			if (strpos(basename($imageSrcPath), $v) !== false) {
				if (defined("_APP_DEBUG")) {
					error_log("watermark ignored: $v - $imageSrcPath");
				}
				unset($imageCmd["watermark"]);
				break;
			}
		}
	}
	/*
	else if (is_callable($config->watermark->ignore)) {
		// TODO
	}
	*/
}
if (!empty($imageCmd["watermark"])) {
	$imageDestPath .= "_watermark";
	require __DIR__ . "/watermark.php";
	$imageSrcPath = $imageDestPath;
}

// restore dest path
$imageDestPath = $_SERVER["DOCUMENT_ROOT"] . $imageDir . $imagePath;
rename($imageSrcPath, $imageDestPath);
if (defined("_APP_DEBUG")) {
	error_log("move $imageSrcPath to $imageDestPath");
}

header("Location: " . $imageDir . $imagePath . "?new");
die;
// or readfile($imageDestPath); // for test - unlink($imageDestPath);
// or header("Content-Type: image/jpeg"); header("Content-Disposition: inline; filename=" . $imageDestPath);
