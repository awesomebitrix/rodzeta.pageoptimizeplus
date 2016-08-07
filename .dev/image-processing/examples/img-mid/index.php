<?php

$imageCmd = [
	"crop" => 1,
	"watermark" => 1,
	"resize" => [300, 300] // width, height
];

require $_SERVER["DOCUMENT_ROOT"] . "/api/image-processing/init.php";
