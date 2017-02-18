<?php
/*******************************************************************************
 * rodzeta.pageoptimizeplus - Additional page optimizations
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Pageoptimizeplus;

function Options() {
	return array(
		"src_folders" => array(
			"/bitrix/templates/furniture_pale-blue",
			"/local/templates/furniture_pale-blue",
			"/upload/",
		),
	);
}

function OptimizeCss() {
	set_time_limit(30 * 60);
	$options = Options();
	$basePath = dirname(dirname(dirname(dirname(__DIR__))));
	$cmd = "java -jar "
		. dirname(__DIR__) . "/bin/yuicompressor-2.4.8.jar"
		. " --type css";
	foreach ($options["src_folders"] as $path) {
		if (!is_dir($basePath . $path)) {
			continue;
		}
		$it = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($basePath . $path));
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
	foreach ($options["src_folders"] as $path) {
		if (!is_dir($basePath . $path)) {
			continue;
		}
		$it = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($basePath . $path));
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
	set_time_limit(30 * 60);
	$options = Options();
	$basePath = dirname(dirname(dirname(dirname(__DIR__))));
	foreach ($options["src_folders"] as $path) {
		if (!is_dir($basePath . $path)) {
			continue;
		}
		$it = new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($basePath . $path));
		foreach ($it as $name) {
			if (strtolower(substr($name, -4)) == ".png") {
				$cmd = dirname(__DIR__) . "/bin/optipng " . escapeshellarg($name);
				echo "$cmd\n";
				//exec($tmp);
			} else if (strtolower(substr($name, -4)) == ".jpg" || strtolower(substr($name, -5) == ".jpeg")) {
				$cmd = dirname(__DIR__) . "/bin/jpegoptim " . escapeshellarg($name) . " --strip-all";
				echo "$cmd\n";
				//exec($tmp);
			}
		}
	}
}
