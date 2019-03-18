<?php
include __DIR__ . "/init.php";

$uri = parse_uri();

$md_origin = $uri;
$mdfile    = POST. '/'. $uri;

if ($uri == "/") {
	include CTRL . '/home.php';
}else {
	if (is_file($mdfile)) {
		include CTRL . '/single.php';
	}else {
		$static_file = __DIR__ . $uri;
		if (is_file($static_file)) {
			readfile($static_file);
		}else {
			header("Location: /"); exit;
		}
	}
}
