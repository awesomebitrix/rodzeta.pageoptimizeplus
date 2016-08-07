<?php

$imageCmd = [
	"crop" => 1,
	"watermark" => 1,
	"resize" => [120, 120] // width, height
];

require $_SERVER["DOCUMENT_ROOT"] . "/api/image-processing/init.php";
