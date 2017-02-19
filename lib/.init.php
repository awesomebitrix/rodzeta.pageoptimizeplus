<?php
/*******************************************************************************
 * rodzeta.pageoptimizeplus - Additional page optimizations
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Pageoptimizeplus;

function Options() {
	// TODO get from module settings
	return array(
		"src_folders" => array(
			"/bitrix/templates/",
			"/local/templates/",
			"/upload/",
		),
		"src_files" => array(
			"/upload/company.jpg",
		)
	);
}

function OptimizeCss() {
	set_time_limit(30 * 60);
	$options = Options();
	$basePath = dirname(dirname(dirname(dirname(__DIR__))));
	$cmd = "java -jar "
		. dirname(__DIR__) . "/bin/yuicompressor-2.4.8.jar"
		. " --type css";
	foreach (array_merge($options["src_folders"], $options["src_files"]) as $path) {
		if (is_dir($basePath . $path)) {
			$it = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($basePath . $path));
		} else if (is_file($basePath . $path)) {
			$it = array($basePath . $path);
		}
		foreach ($it as $name) {
			if (substr($name, -4) != ".css" || substr($name, -8) == ".min.css") {
				continue;
			}
			$dest = substr($name, 0, -4) . ".min.css";
			$tmp = $cmd . " " . escapeshellarg($name) . " > " . escapeshellarg($dest);
			echo "$tmp\n";
			exec($tmp);
		}
	}
}

function OptimizeJs() {
	set_time_limit(30 * 60);
	$options = Options();
	$basePath = dirname(dirname(dirname(dirname(__DIR__))));
	$cmd = "java -jar "
		. dirname(__DIR__) . "/bin/closure-compiler.jar"
		. " --js %s --js_output_file %s";
	foreach (array_merge($options["src_folders"], $options["src_files"]) as $path) {
		if (is_dir($basePath . $path)) {
			$it = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($basePath . $path));
		} else if (is_file($basePath . $path)) {
			$it = array($basePath . $path);
		}
		foreach ($it as $name) {
			if (substr($name, -3) != ".js" || substr($name, -7) == ".min.js") {
				continue;
			}
			$dest = substr($name, 0, -3) . ".min.js";
			$tmp = sprintf($cmd, escapeshellarg($name), escapeshellarg($dest));
			echo "$tmp\n";
			exec($tmp);
		}
	}
}

function OptimizeImages() {
	// https://developers.google.com/speed/docs/insights/OptimizeImages?hl=ru
	set_time_limit(30 * 60);
	$options = Options();
	$basePath = dirname(dirname(dirname(dirname(__DIR__))));
	if (strtoupper(substr(PHP_OS, 0, 3)) === "WIN") {
		$pngCmd = dirname(__DIR__) . "/bin/optipng.exe -o7 ";
		$jpgCmd = dirname(__DIR__) . "/bin/jpegoptim %s --strip-all";
	} else {
		$pngCmd = "optipng -o7 ";
		$jpgCmd = "jpegoptim %s --strip-all";
	}
	foreach (array_merge($options["src_folders"], $options["src_files"]) as $path) {
		if (is_dir($basePath . $path)) {
			$it = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($basePath . $path));
		} else if (is_file($basePath . $path)) {
			$it = array($basePath . $path);
		}
		foreach ($it as $name) {
			if (strtolower(substr($name, -4)) == ".png") {
				$tmp = $pngCmd . escapeshellarg($name);
				echo "$tmp\n";
				copy($name, $name . ".original");
				exec($tmp);
			} else if (strtolower(substr($name, -4)) == ".jpg" || strtolower(substr($name, -5) == ".jpeg")) {
				$tmp = sprintf($jpgCmd, escapeshellarg($name));
				echo "$tmp\n";
				copy($name, $name . ".original");
				exec($tmp);
			}
		}
	}
}
