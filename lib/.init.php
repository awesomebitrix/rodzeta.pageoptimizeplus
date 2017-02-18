<?php
/*******************************************************************************
 * rodzeta.pageoptimizeplus - Additional page optimizations
 * Copyright 2016 Semenov Roman
 * MIT License
 ******************************************************************************/

namespace Rodzeta\Pageoptimizeplus;

function OptimizeCss() {
	set_time_limit(30 * 60);

	$srcFolders = array(
		"/bitrix/templates/furniture_pale-blue",
		"/local/templates/furniture_pale-blue",
	);
	$basePath = dirname(dirname(dirname(dirname(__DIR__))));
	$cmd = "java -jar "
		. dirname(__DIR__) . "/bin/yuicompressor-2.4.8.jar"
		. " --type css";
	foreach ($srcFolders as $path) {
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
