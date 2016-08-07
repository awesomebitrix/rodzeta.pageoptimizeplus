<?php

ini_set("display_errors", 1);

$config = require __DIR__ . "/config.php";

foreach (glob($_SERVER["DOCUMENT_ROOT"] . "/img-*") as $dir) {
	$d = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
	foreach (new RecursiveIteratorIterator($d) as $fname => $f) {
		if (!isset($config->extensions[$f->getExtension()])) {
			continue;
		}
		echo "remove: $fname\n";
		unlink($fname);
	}
}
