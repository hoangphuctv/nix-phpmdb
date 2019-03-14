<?php
define('ROOT', realpath(__DIR__ . '/../'));
define('POST',  ROOT . '/posts');
define('VIEW',  ROOT . '/views');
define('CTRL',  ROOT . '/controllers');
define('CACHE', ROOT . '/cached');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

global $config;
$config = json_decode(file_get_contents(ROOT.'/config.json'));

include ROOT . '/vendor/autoload.php';
include ROOT.'/helpers/commons.php';

init($config);

$uri = parse_uri();

$md_origin = $uri;
$mdfile    = POST. '/'. $uri;

if ($uri == "/") {
	include CTRL . '/home.php';
}else {
	if (is_file($mdfile)) {
		include CTRL . '/single.php';
	}else {
		http_response_code(404);
		echo 'File not found'; exit;
	}
}
