<?php
/*******************************************************************************
 * rodzeta.pageoptimizeplus - Additional page optimizations
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Pageoptimizeplus;

define(__NAMESPACE__ . "\ID", "rodzeta.pageoptimizeplus");
define(__NAMESPACE__ . "\APP", dirname(__DIR__) . "/");
define(__NAMESPACE__ . "\LIB", APP  . "lib/");

define(__NAMESPACE__ . "\SITE", substr($_SERVER["SERVER_NAME"], 0, 4) == "www."?
	substr($_SERVER["SERVER_NAME"], 4) : $_SERVER["SERVER_NAME"]);

define(__NAMESPACE__ . "\CONFIG",
	$_SERVER["DOCUMENT_ROOT"] . "/upload/." . ID . "." . SITE);

define(__NAMESPACE__ . "\FILE_OPTIONS", CONFIG . ".php"); // example: /upload/.rodzeta.pageoptimizeplus.localhost.php

require LIB . "encoding/php-array.php";

class ctx {
	static $styles;
}

function Options() {
	$result = is_readable(FILE_OPTIONS)? include FILE_OPTIONS : array(
		"move_css" => "Y",
		"src_folders" => array(
			"/bitrix/templates/",
			"/local/templates/",
			"/upload/",
		),
		"src_files" => array(
			"/upload/company.jpg",
		)
	);
	return $result;
}

function OptionsUpdate($options) {
	$tmp = array();
	foreach (explode("\n", $options["src_folders"]) as $v) {
		$v = trim($v);
		if ($v == "") {
			continue;
		}
		$tmp[] = $v;
	}
	$options["src_folders"] = $tmp;

	$tmp = array();
	foreach (explode("\n", $options["src_files"]) as $v) {
		$v = trim($v);
		if ($v == "") {
			continue;
		}
		$tmp[] = $v;
	}
	$options["src_files"] = $tmp;

	\Encoding\PhpArray\Write(FILE_OPTIONS, $options);
}

function ReplaceStyles($m) {
	// ignore with attr data-skip-moving
	if (strpos($m[1], 'data-skip-moving="true"') !== false) {
		return $m[0];
	}
	// ignore other types
	if (strpos($m[1], "stylesheet") === false) {
		return $m[0];
	}
	ctx::$styles[] = $m[0];
	return "";
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

function OptimizeImages($restore = false) {
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
				if ($restore) {
					if (file_exists($name . ".original")) {
						echo "restore $name\n";
						rename($name . ".original", $name);
					}
				} else {
					$tmp = $pngCmd . escapeshellarg($name);
					echo "$tmp\n";
					if (!file_exists($name . ".original")) {
						copy($name, $name . ".original");
					}
					exec($tmp);
				}
			} else if (strtolower(substr($name, -4)) == ".jpg" || strtolower(substr($name, -5) == ".jpeg")) {
				if ($restore) {
					if (file_exists($name . ".original")) {
						echo "restore $name\n";
						rename($name . ".original", $name);
					}
				} else {
					$tmp = sprintf($jpgCmd, escapeshellarg($name));
					echo "$tmp\n";
					if (!file_exists($name . ".original")) {
						copy($name, $name . ".original");
					}
					exec($tmp);
				}
			}
		}
	}
}
