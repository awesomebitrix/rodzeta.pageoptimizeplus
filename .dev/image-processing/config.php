<?php

return (object)[
	//"background" => "#ffffff", // set background for image
	"extensions" => [
		"jpg" => 1,
		"png" => 1,
		"gif" => 1,
	],
	"watermark" => (object)[
		"min_width" => 200, // min width for apply watermark
		"min_height" => 120, // min height for apply watermark
		"path" => __DIR__ . "/watermark",
		"ignore" => [ // ignore watermark for files with prefix
			"-watermark"
		],
		"scale" => true, // scale watermark to image size
	]
];
