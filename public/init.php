<?php
define('ROOT', realpath(__DIR__ . '/../'));
define('VIEW',  ROOT . '/views');
define('CTRL',  ROOT . '/controllers');
define('CACHE', ROOT . '/cached');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

global $config;
$config = json_decode(file_get_contents(ROOT.'/config.json'));

if (!empty($config->post_dir)) {
	$post_dir = $config->post_dir;
}else {
	$post_dir = ROOT . '/posts';
}

define('POST',  $post_dir);
unset($post_dir);

include ROOT . '/vendor/autoload.php';
include ROOT.'/helpers/commons.php';

init($config);
