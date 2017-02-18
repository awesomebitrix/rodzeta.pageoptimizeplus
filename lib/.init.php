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
		"/bitrix/templates/",
		"/local/templates/",
	);
	$basePath = dirname(dirname(dirname(dirname(__DIR__))));
	foreach ($srcFolders as $path) {
		if (!is_dir($basePath . $path)) {
			continue;
		}
		$it = new RegexIterator(
			new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($basePath . $path)),
			'/^.+\.css$/i',
			RecursiveRegexIterator::GET_MATCH
		);
		foreach ($it as $name => $f) {
			echo "$name\n";
		}
	}
}
